DROP PROCEDURE IF EXISTS `getPrescribingReportByDates`;

DELIMITER $$
CREATE PROCEDURE `getPrescribingReportByDates` (IN provider_id INT, IN start_date DATE, IN end_date DATE, IN stages VARCHAR(40))
BEGIN
    DROP TABLE IF EXISTS g2_report_ds;
    DROP TABLE IF EXISTS g2_report_denominator_ds;
    DROP TABLE IF EXISTS g2_report_numerator_ds;

    SET @provider = (SELECT CONCAT(title, ' ', lname, ', ', fname, ' ', mname, ' (NPI:', npi, ')') FROM users WHERE id = provider_id);

    CREATE TEMPORARY TABLE g2_report_ds
    SELECT med.id, med.pid, IF(erx.id IS NULL, '0', '1') as erx_sent FROM patient_medications as med
                                                                              LEFT JOIN erx_prescriptions as erx ON erx.orderId = med.id
    WHERE med.uid = provider_id AND med.is_controlled = '0' AND med.date_ordered AND med.date_ordered BETWEEN start_date AND end_date;

    SET @stage3 = (SELECT FIND_IN_SET('3',stages));

    IF @stage3 > 0 THEN

        CREATE TEMPORARY TABLE g2_report_denominator_ds
        SELECT pid FROM g2_report_ds GROUP BY id;

        CREATE TEMPORARY TABLE g2_report_numerator_ds
        SELECT pid FROM g2_report_ds WHERE erx_sent = '1' group by id;

        SET @denominator = (SELECT count(*) FROM g2_report_denominator_ds);
        SET @numerator = (SELECT count(*) FROM g2_report_numerator_ds);

        SELECT
            @provider as provider,
            'Stage 3 Measure' as title,
            @denominator as denominator,
            (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_denominator_ds GROUP BY pid) as d) as denominator_pids,
            @numerator as numerator,
            (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_numerator_ds GROUP BY pid) as n) as numerator_pids;

    END IF;

END$$

DELIMITER ;

