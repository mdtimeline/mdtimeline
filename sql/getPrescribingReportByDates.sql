DROP PROCEDURE IF EXISTS `getPrescribingReportByDates`;

DELIMITER $$
CREATE PROCEDURE `getPrescribingReportByDates` (IN provider_id INT, IN start_date DATE, IN end_date DATE)
BEGIN
    DROP TABLE IF EXISTS g2_report_ds;
    DROP TABLE IF EXISTS g2_report_denominator_ds;
    DROP TABLE IF EXISTS g2_report_numerator_ds;

    CREATE TEMPORARY TABLE g2_report_ds
    SELECT med.id, med.pid, IF(erx.id IS NULL, '0', '1') as erx_sent FROM patient_medications as med
                                                                              LEFT JOIN erx_prescriptions as erx ON erx.orderId = med.id
    WHERE med.uid = provider_id AND med.is_controlled = '0' AND med.date_ordered AND med.date_ordered BETWEEN start_date AND end_date;


    CREATE TEMPORARY TABLE g2_report_denominator_ds
    SELECT count(*) as `value`, pid FROM g2_report_ds GROUP BY pid;

    CREATE TEMPORARY TABLE g2_report_numerator_ds
    SELECT count(*) as `value`, pid FROM g2_report_ds WHERE erx_sent = '1' group by pid;

    SET @denominator = (SELECT sum(`value`) FROM g2_report_denominator_ds);
    SET @numerator = (SELECT sum(`value`) FROM g2_report_numerator_ds);

    SELECT
        @denominator as denominator,
        (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_denominator_ds GROUP BY pid) as d) as denominator_pids,
        @numerator as numerator,
        (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_numerator_ds GROUP BY pid) as n) as numerator_pids;

END$$

DELIMITER ;

