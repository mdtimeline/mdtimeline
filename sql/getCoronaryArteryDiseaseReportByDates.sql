DROP PROCEDURE IF EXISTS `getCoronaryArteryDiseaseReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCoronaryArteryDiseaseReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE)
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
                            AND RXCUI IN ('1000001', '1091646', '1091652', '1235144', '1235151', '1299859', '1299871', '1299890', '1299896', '1299897', '1435624',
                                            '153822', '153823', '1600716', '1600724', '1600728', '1656340', '1656349', '1656354', '197436', '197437', '197438', '197439',
                                            '197884', '197885', '197886', '197887', '198188', '198189', '199351', '199352', '199353', '200094', '200095', '200096', '200284',
                                            '200285', '205304', '205305', '205326', '261962', '282755', '283316', '283317', '308962', '308963', '308964', '310140', '310792',
                                            '310793', '310796', '310797', '310809', '311353', '311354', '312748', '312749', '312750', '314076', '314077', '314203', '317173',
                                            '349199', '349200', '349201', '349353', '349373', '349401', '349405', '349483', '351292', '351293', '403853', '403854', '403855',
                                            '477130', '485471', '577776', '578325', '578330', '636042', '636045', '639537', '722126', '722131', '722134', '722137', '730861',
                                            '730866', '730869', '730872', '802749', '845488', '848131', '848135', '848140', '848145', '848151', '854925', '854984', '854988',
                                            '857166', '857169', '857174', '857183', '857187', '858804', '858810', '858813', '858815', '858817', '858824', '858828', '876514', '876519',
                                            '876524', '876529', '897781', '897783', '897844', '897853', '898342', '898346', '898350', '898353', '898356', '898359', '898362',
                                            '898367', '898372', '898378', '898687', '898690', '898719', '898723', '979464', '979468', '979471', '979480', '979485', '979492',
                                            '999967', '999986', '999991', '999996')
                            AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active AND m.begin_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date))),
                    '1', '0') as was_prescribed_or_active,
                IF( EXISTS (
                            SELECT * FROM encounter_dx as edx
                            WHERE edx.pid = e.pid
                              AND edx.dx_type = 'F'
                              AND edx.code = 'I50.1'),
                    '1', '0') as has_or_had_lvef_dx
            FROM
                (SELECT enc.eid,
                        enc.pid,
                        YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) as age,
                        COUNT(*) AS total
                 FROM encounters as enc
                          INNER JOIN patient p ON enc.pid = p.pid
                          LEFT JOIN patient_insurances AS pi ON pi.pid = p.pid
                          INNER JOIN encounter_dx AS dx ON enc.eid = dx.eid
                 WHERE enc.provider_uid = provider_id
                   AND enc.service_date BETWEEN start_date AND end_date
                   AND pi.insurance_id = insurance_id
                   AND dx.code IN ('I11.0', 'I13.0', 'I13.2', 'I50.1', 'I50.20', 'I50.21', 'I50.22', 'I50.23', 'I50.30', 'I50.31',  'I50.32',
                     'I50.33', 'I50.40', 'I50.41', 'I50.42', 'I50.43', 'I50.814', 'I50.82', 'I50.83', 'I50.84', 'I50.89', 'I50.9')
                 GROUP BY enc.pid
                 HAVING  total >= 2) e
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
                              AND RXCUI IN ('1000001', '1091646', '1091652', '1235144', '1235151', '1299859', '1299871', '1299890', '1299896', '1299897', '1435624',
                                            '153822', '153823', '1600716', '1600724', '1600728', '1656340', '1656349', '1656354', '197436', '197437', '197438', '197439',
                                            '197884', '197885', '197886', '197887', '198188', '198189', '199351', '199352', '199353', '200094', '200095', '200096', '200284',
                                            '200285', '205304', '205305', '205326', '261962', '282755', '283316', '283317', '308962', '308963', '308964', '310140', '310792',
                                            '310793', '310796', '310797', '310809', '311353', '311354', '312748', '312749', '312750', '314076', '314077', '314203', '317173',
                                            '349199', '349200', '349201', '349353', '349373', '349401', '349405', '349483', '351292', '351293', '403853', '403854', '403855',
                                            '477130', '485471', '577776', '578325', '578330', '636042', '636045', '639537', '722126', '722131', '722134', '722137', '730861',
                                            '730866', '730869', '730872', '802749', '845488', '848131', '848135', '848140', '848145', '848151', '854925', '854984', '854988',
                                            '857166', '857169', '857174', '857183', '857187', '858804', '858810', '858813', '858815', '858817', '858824', '858828', '876514', '876519',
                                            '876524', '876529', '897781', '897783', '897844', '897853', '898342', '898346', '898350', '898353', '898356', '898359', '898362',
                                            '898367', '898372', '898378', '898687', '898690', '898719', '898723', '979464', '979468', '979471', '979480', '979485', '979492',
                                            '999967', '999986', '999991', '999996')
                              AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active AND m.begin_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date))),
                    '1', '0') as was_prescribed_or_active,
                IF( EXISTS (
                            SELECT * FROM encounter_dx as edx
                            WHERE edx.pid = e.pid
                            AND edx.dx_type = 'F'
                            AND edx.code = 'I50.1'),
                    '1', '0') as has_or_had_lvef_dx
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
                   AND edx.code IN ('I50', 'I50.2', 'I50.20', 'I50.21', 'I50.22', 'I50.23', 'I50.3', 'I50.30', 'I50.31',
                                    'I50.32', 'I50.33', 'I50.4', 'I50.40', 'I50.41', 'I50.42', 'I50.43', 'I50.8', 'I11.0',
                                    'I09.81', 'I13.0', 'I13.2')) e;

        END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE has_or_had_lvef_dx = '1' GROUP BY pid;

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
