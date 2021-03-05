DROP PROCEDURE IF EXISTS `getAdvanceCarePlanReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAdvanceCarePlanReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE)
BEGIN
    DROP TABLE IF EXISTS report_ds;
    DROP TABLE IF EXISTS report_ds_first_encounters;
    DROP TABLE IF EXISTS report_denominator_ds;
    DROP TABLE IF EXISTS report_numerator_ds;

    SET @provider = (SELECT CONCAT(title, ' ', lname, ', ', fname, ' ', mname, ' (NPI:', npi, ')') FROM users WHERE id = provider_id);
    SET @insurance = (SELECT CONCAT(name, ' ', ' (CODE:', insurance_companies.code, ')') FROM insurance_companies WHERE id = insurance_id);

    CREATE TEMPORARY TABLE report_ds
    SELECT
        o.id,
        o.pid,
        IF(o.priority IS NOT NULL AND o.priority != '', '1','0') AS is_cpoe
    FROM
        (SELECT enc.eid,
                enc.pid
         FROM encounters as enc
         INNER JOIN patient p on enc.pid = p.pid
         WHERE YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 65 AND enc.provider_uid = provider_id AND enc.service_date BETWEEN start_date AND end_date
         GROUP BY enc.pid) o;

        CREATE TEMPORARY TABLE g2_report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds GROUP BY id;

        CREATE TEMPORARY TABLE g2_report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE is_cpoe = '1' GROUP BY id;

        SET @denominator = (SELECT sum(`value`) FROM g2_report_denominator_ds);
        SET @numerator = (SELECT sum(`value`) FROM g2_report_numerator_ds);
        SET @denominator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_denominator_ds GROUP BY pid) as d);
        SET @numerator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_numerator_ds GROUP BY pid) as n);

        SELECT
            @provider as provider,
            @insurance as insurance,
            'MIPS' as title,
            @denominator as denominator,
            @denominator_pids as denominator_pids,
            @numerator as numerator,
            @numerator_pids as numerator_pids;

END$$

DELIMITER ;
