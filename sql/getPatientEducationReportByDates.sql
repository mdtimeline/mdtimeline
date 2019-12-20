DROP PROCEDURE IF EXISTS `getPatientEducationReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPatientEducationReportByDates`(IN provider_id INT, IN start_date DATE, IN end_date DATE, IN stages VARCHAR(40))
BEGIN
    DROP TABLE IF EXISTS g2_report_ds;
    DROP TABLE IF EXISTS g2_report_ds_first_encounters;
    DROP TABLE IF EXISTS g2_report_denominator_ds;
    DROP TABLE IF EXISTS g2_report_numerator_ds;

    SET @provider = (SELECT CONCAT(title, ' ', lname, ', ', fname, ' ', mname, ' (NPI:', npi, ')') FROM users WHERE id = provider_id);
    SET @calendar_start = CAST(CONCAT(DATE(start_date), ' 00:00:00') AS DATETIME);
    SET @calendar_end =  CAST(CONCAT(DATE(end_date), ' 23:59:59') AS DATETIME);

    CREATE TEMPORARY TABLE g2_report_ds
    SELECT
        r.eid,
        r.pid,
        r.provider,
        r.facility,
        r.in_calendar_year,
        r.service_date,
        r.event_id,
        r.event_date
    FROM
        (SELECT
             e.eid,
             e.provider_uid as provider,
             e.facility,
             e.pid,
             e.service_date,
             a.id AS event_id,
             a.event_date,
             IF(a.event_date IS NOT NULL AND a.event_date BETWEEN @calendar_start AND @calendar_end, 1, 0) AS in_calendar_year
         FROM
             encounters AS e
                 LEFT JOIN audit_log AS a ON a.pid = e.pid AND a.eid = e.eid AND a.event IN ('EDUCATION_RESOURCES_PROVIDED')
         WHERE
                 e.provider_uid = provider_id
           AND e.service_date IS NOT NULL
           AND e.service_date BETWEEN start_date AND end_date
           AND e.visit_category_code IN ('99201','99202','99203','99205','99212','99213','99214','99215')
        ) r ORDER BY r.service_date;

    SET @stage3 = (SELECT FIND_IN_SET('3',stages));

    IF @stage3 > 0 THEN

        CREATE TEMPORARY TABLE g2_report_denominator_ds
        SELECT 1 as `value`, pid FROM g2_report_ds GROUP BY pid;

        CREATE TEMPORARY TABLE g2_report_numerator_ds
        SELECT 1 as `value`, pid FROM g2_report_ds WHERE in_calendar_year = '1' group by pid;

        SET @denominator = (SELECT sum(`value`) FROM g2_report_denominator_ds);
        SET @numerator = (SELECT sum(`value`) FROM g2_report_numerator_ds);

        SELECT
            @provider as provider,
            'Stage 3 Measure' as title,
            @denominator as denominator,
            (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_denominator_ds GROUP BY pid) as d) as denominator_pids,
            @numerator as numerator,
            (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_numerator_ds GROUP BY pid) as n) as numerator_pids;

    END IF ;

END$$
DELIMITER ;
