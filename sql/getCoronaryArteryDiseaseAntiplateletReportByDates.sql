DROP PROCEDURE IF EXISTS `getCoronaryArteryDiseaseAntiplateletReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCoronaryArteryDiseaseAntiplateletReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR, IN ethnicity VARCHAR(40), IN race VARCHAR(40))
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
                            AND RXCUI IN ('1052678', '10594', '108911', '1116632', '1116633', '1116634', '1116635',
                                          '1116636', '1116637', '1116638', '1116639', '1153731', '1153732', '1157089',
                                          '1157090', '1157092', '1157093', '1157699', '1157700', '1158988', '1158990',
                                          '1158991', '1161927', '1162116', '1163067', '1163137', '1163138', '1163766',
                                          '1163767', '1165745', '1165746', '1172779', '1172780', '1172781', '1172793',
                                          '1172794', '1173221', '1173222', '1173433', '1176340', '1176341', '1178211',
                                          '1178212', '1178492', '1178493', '1181334', '1181790', '1181791', '1189780',
                                          '1189781', '1293661', '1293665', '1362082', '152293', '1537034', '1537035',
                                          '1537036', '1537037', '1537038', '1537039', '1537040', '1537041', '1537042',
                                          '1537043', '1537044', '1537045', '1537050', '1600987', '1600991', '1656052',
                                          '1656053', '1656054', '1656055', '1656056', '1656057', '1656058', '1656059',
                                          '1656060', '1656061', '1656683', '1656685', '1666331', '1666332', '1666333',
                                          '1666334', '1722689', '1722691', '1722695', '1736469', '1736470', '1736471',
                                          '1736472', '1736477', '1736478', '1737465', '1737466', '1737467', '1737468',
                                          '1737471', '1737472', '174742', '1812189', '1813034', '1813035', '1813036',
                                          '1813037', '187024', '197622', '199314', '200349', '203015', '206714', '207569',
                                          '208316', '208558', '21107', '211832', '213169', '215060', '226716', '226718',
                                          '227257', '236612', '236991', '241162', '242461', '242462', '243670', '253202',
                                          '259081', '261097', '288688', '308416', '309362', '309952', '309953', '309955',
                                          '313406', '315245', '315413', '315431', '315675', '315676', '315837', '315838',
                                          '315839', '315863', '315864', '317358', '318272', '328741', '329296', '329449',
                                          '32968', '330276', '331837', '3521', '368100', '368301', '369232', '371508',
                                          '371917', '374131', '374583', '564645', '567526', '568326', '569024', '569235',
                                          '572163', '573094', '573208', '574344', '574345', '574548', '596724', '597848',
                                          '597849', '597850', '597851', '597852', '597854', '597856', '597857', '613391',
                                          '724442', '724444', '73137', '749195', '749196', '749197', '749198', '749795',
                                          '75635', '794228', '794229',  '825180', '83929', '847020', '847089', '855810',
                                          '855811', '855812', '855813', '855814', '855815', '855816', '855817', '855818',
                                          '855819', '855820', '97')
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
                   AND enc.close_date IS NOT NULL
                   AND (sex IS NULL OR p.sex = sex)
                   AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                   AND (race IS NULL OR p.race = race)
                   AND pi.insurance_id = insurance_id
                   AND dx.dx_type = 'F'
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
                              AND RXCUI IN ('1052678', '10594', '108911', '1116632', '1116633', '1116634', '1116635',
                                            '1116636', '1116637', '1116638', '1116639', '1153731', '1153732', '1157089',
                                            '1157090', '1157092', '1157093', '1157699', '1157700', '1158988', '1158990',
                                            '1158991', '1161927', '1162116', '1163067', '1163137', '1163138', '1163766',
                                            '1163767', '1165745', '1165746', '1172779', '1172780', '1172781', '1172793',
                                            '1172794', '1173221', '1173222', '1173433', '1176340', '1176341', '1178211',
                                            '1178212', '1178492', '1178493', '1181334', '1181790', '1181791', '1189780',
                                            '1189781', '1293661', '1293665', '1362082', '152293', '1537034', '1537035',
                                            '1537036', '1537037', '1537038', '1537039', '1537040', '1537041', '1537042',
                                            '1537043', '1537044', '1537045', '1537050', '1600987', '1600991', '1656052',
                                            '1656053', '1656054', '1656055', '1656056', '1656057', '1656058', '1656059',
                                            '1656060', '1656061', '1656683', '1656685', '1666331', '1666332', '1666333',
                                            '1666334', '1722689', '1722691', '1722695', '1736469', '1736470', '1736471',
                                            '1736472', '1736477', '1736478', '1737465', '1737466', '1737467', '1737468',
                                            '1737471', '1737472', '174742', '1812189', '1813034', '1813035', '1813036',
                                            '1813037', '187024', '197622', '199314', '200349', '203015', '206714', '207569',
                                            '208316', '208558', '21107', '211832', '213169', '215060', '226716', '226718',
                                            '227257', '236612', '236991', '241162', '242461', '242462', '243670', '253202',
                                            '259081', '261097', '288688', '308416', '309362', '309952', '309953', '309955',
                                            '313406', '315245', '315413', '315431', '315675', '315676', '315837', '315838',
                                            '315839', '315863', '315864', '317358', '318272', '328741', '329296', '329449',
                                            '32968', '330276', '331837', '3521', '368100', '368301', '369232', '371508',
                                            '371917', '374131', '374583', '564645', '567526', '568326', '569024', '569235',
                                            '572163', '573094', '573208', '574344', '574345', '574548', '596724', '597848',
                                            '597849', '597850', '597851', '597852', '597854', '597856', '597857', '613391',
                                            '724442', '724444', '73137', '749195', '749196', '749197', '749198', '749795',
                                            '75635', '794228', '794229',  '825180', '83929', '847020', '847089', '855810',
                                            '855811', '855812', '855813', '855814', '855815', '855816', '855817', '855818',
                                            '855819', '855820', '97')
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
                   AND enc.close_date IS NOT NULL
                   AND (sex IS NULL OR p.sex = sex)
                   AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                   AND (race IS NULL OR p.race = race)
                   AND dx.dx_type = 'F'
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
