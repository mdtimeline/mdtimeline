DROP PROCEDURE IF EXISTS `getBreastCancerScreeningReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBreastCancerScreeningReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR)
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
                IF( EXISTS (
                            SELECT * FROM patient_order_results
                            WHERE pid = e.pid
                              AND code IN ('24604-1', '24605-8', '24606-6', '24610-8', '26175-0', '26176-8', '26177-6',
                                           '26287-3', '26289-9', '26291-5', '26346-7', '26347-5', '26348-3', '26349-1',
                                           '26350-9', '26351-7', '36319-2', '36625-2', '36626-0', '36627-8', '36642-7',
                                           '36962-9', '37005-6', '37006-4', '37016-3', '37017-1', '37028-8', '37029-6',
                                           '37030-4', '37037-9', '37038-7', '37052-8', '37053-6', '37539-4', '37542-8',
                                           '37543-6', '37551-9', '37552-7', '37553-5', '37554-3', '37768-9', '37769-7',
                                           '37770-5', '37771-3', '37772-1', '37773-9', '37774-7', '37775-4', '38070-9',
                                           '38071-7', '38072-5', '38090-7', '38091-5', '38807-4', '38820-7', '38854-6',
                                           '38855-3', '39150-8', '39152-4', '39153-2', '39154-0', '42168-5', '42169-3',
                                           '42174-3', '42415-0', '42416-8', '46335-6', '46336-4', '46337-2', '46338-0',
                                           '46339-8', '46342-2', '46350-5', '46351-3', '46354-7', '46355-4', '46356-2',
                                           '46380-2', '48475-8', '48492-3', '69150-1', '69251-7', '69259-0', '72137-3',
                                           '72138-1', '72139-9', '72140-7', '72141-5', '72142-3', '86462-9', '86463-7')
                              AND result_date BETWEEN DATE_SUB(end_date, INTERVAL 27 MONTH) AND end_date),
                    '1', '0') as has_mammograms
            FROM
                (SELECT enc.eid, enc.pid
                 FROM encounters as enc
                 INNER JOIN patient p ON enc.pid = p.pid
                 INNER JOIN patient_insurances AS pi ON p.pid = pi.pid
                 WHERE enc.provider_uid = provider_id
                 AND enc.service_date BETWEEN start_date AND end_date
                 AND (sex IS NULL OR p.sex = sex)
                 AND pi.insurance_id = insurance_id
                 AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 51
                 AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) <= 74) e;
        ELSE

            CREATE TEMPORARY TABLE report_ds
            SELECT
                e.eid,
                e.pid,
                IF( EXISTS (
                            SELECT * FROM patient_order_results
                            WHERE pid = e.pid
                            AND code IN ('24604-1', '24605-8', '24606-6', '24610-8', '26175-0', '26176-8', '26177-6',
                                         '26287-3', '26289-9', '26291-5', '26346-7', '26347-5', '26348-3', '26349-1',
                                         '26350-9', '26351-7', '36319-2', '36625-2', '36626-0', '36627-8', '36642-7',
                                         '36962-9', '37005-6', '37006-4', '37016-3', '37017-1', '37028-8', '37029-6',
                                         '37030-4', '37037-9', '37038-7', '37052-8', '37053-6', '37539-4', '37542-8',
                                         '37543-6', '37551-9', '37552-7', '37553-5', '37554-3', '37768-9', '37769-7',
                                         '37770-5', '37771-3', '37772-1', '37773-9', '37774-7', '37775-4', '38070-9',
                                         '38071-7', '38072-5', '38090-7', '38091-5', '38807-4', '38820-7', '38854-6',
                                         '38855-3', '39150-8', '39152-4', '39153-2', '39154-0', '42168-5', '42169-3',
                                         '42174-3', '42415-0', '42416-8', '46335-6', '46336-4', '46337-2', '46338-0',
                                         '46339-8', '46342-2', '46350-5', '46351-3', '46354-7', '46355-4', '46356-2',
                                         '46380-2', '48475-8', '48492-3', '69150-1', '69251-7', '69259-0', '72137-3',
                                         '72138-1', '72139-9', '72140-7', '72141-5', '72142-3', '86462-9', '86463-7')
                            AND result_date BETWEEN DATE_SUB(end_date, INTERVAL 27 MONTH) AND end_date),
                    '1', '0') as has_mammograms
            FROM
                (SELECT enc.eid, enc.pid
                 FROM encounters as enc
                 INNER JOIN patient p ON enc.pid = p.pid
                 WHERE enc.provider_uid = provider_id
                 AND enc.service_date BETWEEN start_date AND end_date
                 AND (sex IS NULL OR p.sex = sex)
                 AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 51
                 AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) <= 74) e;
        END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds  GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE has_mammograms = '1' GROUP BY pid;

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