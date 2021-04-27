DROP PROCEDURE IF EXISTS `getFallsRiskAssessmentReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFallsRiskAssessmentReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE)
BEGIN

    DROP TABLE IF EXISTS report_ds;
    DROP TABLE IF EXISTS report_denominator_ds;
    DROP TABLE IF EXISTS report_numerator_ds;


#     SET @provider_id = 2619;
#     SET @insurance_id = 1;
#     SET @start_date = '2020-01-01';
#     SET @end_date = '2024-01-01';

    SET @provider = (SELECT CONCAT(title, ' ', lname, ', ', fname, ' ', mname, ' (NPI:', npi, ')') FROM users WHERE id = provider_id);
    SET @insurance = (SELECT CONCAT(name, ' ', ' (CODE:', insurance_companies.code, ')') FROM insurance_companies WHERE id = insurance_id);

    IF insurance_id > 0
        THEN
            CREATE TEMPORARY TABLE report_ds
            SELECT
                e.eid,
                e.pid,
                e.was_screened_for_future_fall
            FROM
                (SELECT enc.pid, enc.eid, IF(pcfs.code IS NULL, 0,1) as was_screened_for_future_fall
                FROM encounters as enc
                    INNER JOIN patient p ON enc.pid = p.pid
                    LEFT JOIN patient_insurances pi ON pi.pid = p.pid
                    LEFT JOIN patient_cognitive_functional_status pcfs ON pcfs.eid = enc.eid AND pcfs.pid = p.pid  AND pcfs.code IN ('401196007','408422004','408423009','408589008','414191008','426938003','427206005','443731004','445990009','711054005')
                WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND pi.insurance_id = insurance_id
                AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 65) e;

        ELSE

            CREATE TEMPORARY TABLE report_ds
            SELECT
                e.eid,
                e.pid,
                e.was_screened_for_future_fall
            FROM
                (SELECT enc.pid, enc.eid, IF(pcfs.code IS NULL, 0,1) as was_screened_for_future_fall
                FROM encounters as enc
                    INNER JOIN patient p ON enc.pid = p.pid
                    LEFT JOIN patient_cognitive_functional_status pcfs ON pcfs.eid = enc.eid AND pcfs.pid = p.pid  AND pcfs.code IN ('401196007','408422004','408423009','408589008','414191008','426938003','427206005','443731004','445990009','711054005')
                WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 65) e;
        END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds  GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE was_screened_for_future_fall = '1' GROUP BY pid;

        SET @denominator = (SELECT sum(`value`) FROM report_denominator_ds);
        SET @numerator = (SELECT sum(`value`) FROM report_numerator_ds);
        SET @denominator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM report_denominator_ds GROUP BY pid) as d);
        SET @numerator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM report_numerator_ds GROUP BY pid) as n);

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
