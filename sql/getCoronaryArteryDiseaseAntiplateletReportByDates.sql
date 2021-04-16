DROP PROCEDURE IF EXISTS `getCoronaryArteryDiseaseAntiplateletReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCoronaryArteryDiseaseAntiplateletReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE)
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
                            AND RXCUI IN ('1116635', '1666332', '197622', '199314', '241162', '259081', '309362', '309952',
                                          '309953', '309955', '313406', '392451', '411653', '749196', '855812', '855818')
                            AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active AND m.begin_date BETWEEN start_date AND end_date))),
                    '1', '0') as was_prescribed_or_active
            FROM
                (SELECT enc.eid,
                        enc.pid,
                        YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) as age
                 FROM encounters as enc
                          INNER JOIN patient p ON enc.pid = p.pid
                          LEFT JOIN patient_insurances AS pi ON pi.pid = p.pid
                          INNER JOIN encounter_dx AS dx ON enc.eid = dx.eid
                 WHERE enc.provider_uid = provider_id
                   AND enc.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                   AND pi.insurance_id = insurance_id
                   AND dx.code IN ('I20.0', 'I20.1', 'I20.8', 'I20.9', 'I21.01', 'I21.02', 'I21.09', 'I21.11', 'I21.19',
                     'I21.21', 'I21.29', 'I21.3', 'I21.4', 'I22.0', 'I22.1', 'I22.2', 'I22.8', 'I22.9', 'I24.0', 'I24.1',
                     'I24.8', 'I24.9', 'I25.10', 'I25.110', 'I25.111', 'I25.118', 'I25.119', 'I25.2', 'I25.5', 'I25.6',
                     'I25.700', 'I25.701', 'I25.708', 'I25.709', 'I25.710', 'I25.711', 'I25.718', 'I25.719', 'I25.720',
                     'I25.721', 'I25.728', 'I25.729', 'I25.730', 'I25.731', 'I25.738', 'I25.739', 'I25.750', 'I25.751',
                     'I25.758', 'I25.759', 'I25.760', 'I25.761', 'I25.768', 'I25.769', 'I25.790',  'I25.791', 'I25.798',
                     'I25.799', 'I25.810', 'I25.811', 'I25.812', 'I25.82', 'I25.83', 'I25.89', 'I25.9', 'Z95.1', 'Z95.5', 'Z98.61')
                 GROUP BY enc.pid) e
            WHERE e.age >= 18;

        ELSE

            CREATE TEMPORARY TABLE report_ds
            SELECT
                e.eid,
                e.pid,
                IF( EXISTS (
                            SELECT * FROM patient_medications as m
                            WHERE m.eid = e.eid
                              AND m.pid = e.pid
                              AND RXCUI IN ('1116635', '1666332', '197622', '199314', '241162', '259081', '309362', '309952',
                                            '309953', '309955', '313406', '392451', '411653', '749196', '855812', '855818')
                              AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active AND m.begin_date BETWEEN start_date AND end_date))),
                    '1', '0') as was_prescribed_or_active
            FROM
                (SELECT enc.eid,
                        enc.pid,
                        YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) as age
                 FROM encounters as enc
                          INNER JOIN patient p ON enc.pid = p.pid
                          INNER JOIN encounter_dx AS dx ON enc.eid = dx.eid
                 WHERE enc.provider_uid = provider_id
                   AND enc.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                   AND dx.code IN ('I20.0', 'I20.1', 'I20.8', 'I20.9', 'I21.01', 'I21.02', 'I21.09', 'I21.11', 'I21.19',
                     'I21.21', 'I21.29', 'I21.3', 'I21.4', 'I22.0', 'I22.1', 'I22.2', 'I22.8', 'I22.9', 'I24.0', 'I24.1',
                     'I24.8', 'I24.9', 'I25.10', 'I25.110', 'I25.111', 'I25.118', 'I25.119', 'I25.2', 'I25.5', 'I25.6',
                     'I25.700', 'I25.701', 'I25.708', 'I25.709', 'I25.710', 'I25.711', 'I25.718', 'I25.719', 'I25.720',
                     'I25.721', 'I25.728', 'I25.729', 'I25.730', 'I25.731', 'I25.738', 'I25.739', 'I25.750', 'I25.751',
                     'I25.758', 'I25.759', 'I25.760', 'I25.761', 'I25.768', 'I25.769', 'I25.790',  'I25.791', 'I25.798',
                     'I25.799', 'I25.810', 'I25.811', 'I25.812', 'I25.82', 'I25.83', 'I25.89', 'I25.9', 'Z95.1', 'Z95.5', 'Z98.61')
                 GROUP BY enc.pid) e
            WHERE e.age >= 18;

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
