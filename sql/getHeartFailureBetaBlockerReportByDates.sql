DROP PROCEDURE IF EXISTS `getHeartFailureBetaBlockerReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getHeartFailureBetaBlockerReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE)
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
                            SELECT * FROM patient_medications as m
                            WHERE m.eid = e.eid
                            AND m.pid = e.pid
                            AND RXCUI IN ('200031', '200032', '200033', '686924', '854901', '854905', '854908', '854916',
                                          '854919', '860510', '860516', '860522', '860532', '866412', '866419', '866427',
                                          '866436', '866452', '866461', '866472')
                            AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active AND m.begin_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date))),
                    '1', '0') as was_prescribed_or_active
            FROM
                (SELECT enc.pid, enc.eid
                 FROM encounters as enc
                  JOIN (SELECT enc.pid, COUNT(*) AS total
                        FROM encounters as enc
                             INNER JOIN patient p ON enc.pid = p.pid
                        WHERE enc.provider_uid = provider_id
                          AND enc.service_date BETWEEN start_date AND end_date
                          AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
                        GROUP BY enc.pid
                        HAVING  total >= 2) as t
                               ON enc.pid = t.pid
                          INNER JOIN encounter_dx as edx
                ON enc.eid = edx.eid
                AND enc.pid = edx.pid
             WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND edx.code IN ('I50', 'I50.1', 'I50.2', 'I50.20', 'I50.21', 'I50.22', 'I50.23', 'I50.3', 'I50.30',
                                    'I50.31', 'I50.32', 'I50.33', 'I50.4', 'I50.40', 'I50.41', 'I50.42', 'I50.43', 'I50.8')) e;

        ELSE

            CREATE TEMPORARY TABLE report_ds
            SELECT
                e.eid,
                e.pid,
                IF( EXISTS (
                            SELECT * FROM patient_medications as m
                            WHERE m.eid = e.eid
                              AND m.pid = e.pid
                              AND RXCUI IN ('200031', '200032', '200033', '686924', '854901', '854905', '854908', '854916',
                                            '854919', '860510', '860516', '860522', '860532', '866412', '866419', '866427',
                                            '866436', '866452', '866461', '866472')
                              AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active AND m.begin_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date))),
                    '1', '0') as was_prescribed_or_active
            FROM
                (SELECT enc.pid, enc.eid
                FROM encounters as enc
                JOIN (SELECT enc.pid, COUNT(*) AS total
                    FROM encounters as enc
                        INNER JOIN patient p ON enc.pid = p.pid
                    WHERE enc.provider_uid = provider_id
                        AND enc.service_date BETWEEN start_date AND end_date
                        AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
                    GROUP BY enc.pid
                    HAVING  total >= 2) as t
                ON enc.pid = t.pid
                    INNER JOIN encounter_dx as edx
                    ON enc.eid = edx.eid
                    AND enc.pid = edx.pid
                WHERE enc.provider_uid = provider_id
                    AND enc.service_date BETWEEN start_date AND end_date
                    AND edx.code IN ('I50', 'I50.1', 'I50.2', 'I50.20', 'I50.21', 'I50.22', 'I50.23', 'I50.3', 'I50.30',
                                 'I50.31', 'I50.32', 'I50.33', 'I50.4', 'I50.40', 'I50.41', 'I50.42', 'I50.43', 'I50.8')) e;

        END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds  GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE was_prescribed_or_active = '1' GROUP BY pid;

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
