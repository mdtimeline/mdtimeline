DROP PROCEDURE IF EXISTS `getSupportElectronicReferralLoopsReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSupportElectronicReferralLoopsReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR)
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
        SELECT e.eid,
               e.pid,
               e.send_record as record_exchange_electronically
        FROM (SELECT enc.eid, enc.pid, pr.send_record
              FROM encounters AS enc
                       INNER JOIN patient AS p
                                  ON enc.pid = p.pid
                       INNER JOIN patient_insurances AS pi
                                  ON p.pid = pi.pid
                       INNER JOIN patient_referrals pr
                                  ON enc.eid = pr.eid
                                      AND enc.pid = pr.pid
                                      AND pr.refer_by = provider_id
              WHERE enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)
                AND pi.insurance_id = insurance_id
             ) e;
    ELSE
        CREATE TEMPORARY TABLE report_ds
        SELECT e.eid,
               e.pid,
               e.send_record as record_exchange_electronically
        FROM (SELECT enc.eid, enc.pid, pr.send_record
              FROM encounters AS enc
                       INNER JOIN patient AS p
                                  ON enc.pid = p.pid
                       INNER JOIN patient_referrals pr
                                  ON enc.eid = pr.eid
                                      AND enc.pid = pr.pid
                                      AND pr.refer_by = provider_id
              WHERE enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)) e;
    END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE record_exchange_electronically = '1' GROUP BY pid;

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