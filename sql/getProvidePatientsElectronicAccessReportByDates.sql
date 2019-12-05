DROP PROCEDURE IF EXISTS `getProvidePatientsElectronicAccessReportByDates`;

DELIMITER $$
CREATE PROCEDURE `getProvidePatientsElectronicAccessReportByDates` (IN provider_id INT, IN start_date DATE, IN end_date DATE)
BEGIN
    DROP TABLE IF EXISTS g2_report_ds;
    DROP TABLE IF EXISTS g2_report_ds_first_encounters;
    DROP TABLE IF EXISTS g2_report_denominator_ds;
    DROP TABLE IF EXISTS g2_report_numerator_ds;

    CREATE TEMPORARY TABLE g2_report_ds
    SELECT
        r.eid,
        r.pid,
        r.provider,
        r.facility,
        r.in_time,
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
             IF(e.service_date IS NOT NULL
                    AND CAST(CONCAT(DATE(a.event_date), ' 23:59:59') AS DATETIME) < DATE_ADD(e.service_date, INTERVAL 48 HOUR), 1, 0) AS in_time
         FROM
             encounters AS e
                 LEFT JOIN audit_log AS a ON a.pid = e.pid AND e.eid = a.eid AND a.event IN ('CCDA_RECEIVED')
         WHERE
                 e.provider_uid = provider_id
           AND e.service_date IS NOT NULL
           AND e.service_date BETWEEN start_date AND end_date

        ) r ORDER BY r.service_date;

    CREATE TEMPORARY TABLE g2_report_ds_first_encounters SELECT eid, pid,provider,facility FROM g2_report_ds GROUP BY pid, provider;

    CREATE TEMPORARY TABLE g2_report_denominator_ds
    SELECT 1 as `value`, pid FROM g2_report_ds GROUP BY pid;

    CREATE TEMPORARY TABLE g2_report_numerator_ds
    SELECT 1 as `value`, pid FROM g2_report_ds WHERE in_time = '1' AND eid IN (SELECT eid FROM g2_report_ds_first_encounters) group by pid;

    DELETE FROM  g2_report_numerator_ds WHERE pid IN (
        SELECT pid FROM g2_report_ds WHERE in_time = '0' AND eid NOT IN (SELECT eid FROM g2_report_ds_first_encounters) group by pid
    );

    SET @denominator = (SELECT sum(`value`) FROM g2_report_denominator_ds);
    SET @numerator = (SELECT sum(`value`) FROM g2_report_numerator_ds);

    SELECT
        @denominator as denominator,
        (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_denominator_ds GROUP BY pid) as d) as denominator_pids,
        @numerator as numerator,
        (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_numerator_ds GROUP BY pid) as n) as numerator_pids;

END$$

DELIMITER ;

