DROP PROCEDURE IF EXISTS `getCoronaryArteryDiseaseBetaBlockerReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCoronaryArteryDiseaseBetaBlockerReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR, IN ethnicity VARCHAR(40), IN race VARCHAR(40))
BEGIN



    DROP TABLE IF EXISTS report_ds;
    DROP TABLE IF EXISTS report_denominator_ds_1;
    DROP TABLE IF EXISTS report_denominator_ds_2;
    DROP TABLE IF EXISTS report_numerator_ds_1;
    DROP TABLE IF EXISTS report_numerator_ds_2;


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
               IF(EXISTS(
                          SELECT *
                          FROM patient_medications as m
                          WHERE m.pid = e.pid
                            AND RXCUI IN ('1191185', '1297753', '1297757', '1495058', '1593725', '1606347', '1606349',
                                          '1798281', '1923422', '1923424', '1923426', '197379', '197380', '197381',
                                          '197382', '197383', '198000', '198001', '198006', '198007', '198008',
                                          '198104', '198105', '198284', '198285', '198286', '200031', '200032', '200033',
                                          '387013', '686924', '751612', '751618', '827073', '854901', '854905', '854908',
                                          '854916', '854919', '856422', '856429', '856448', '856457', '856460', '856481',
                                          '856519', '856535', '856556', '856569', '856578', '856724', '856733', '860510',
                                          '860516', '860522', '860532', '866412', '866419', '866427', '866436', '866452',
                                          '866461', '866472', '866479', '866482', '866491', '866511', '866514', '866924',
                                          '896758', '896762', '896766', '904589', '998685', '998689')
                            AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active))),
                  '1', '0') as was_prescribed_or_active,
               IF(EXISTS(
                          SELECT dx.code
                          FROM encounter_dx AS dx
                                   JOIN encounters as dxe ON dx.eid = dxe.eid
                          WHERE dxe.pid = e.pid
                            AND dx.dx_type = 'F'
                            AND dx.code IN ('I20.9', 'I21', 'I21.0', 'I21.01', 'I21.02', 'I21.09', 'I21.1', 'I21.11',
                                            'I21.19', 'I12.12', 'I21.21', 'I21.29', 'I21.3', 'I21.4', 'I21.9', 'I21.A',
                                            'I21.A1', 'I21.A9', 'I22', 'I22.0', 'I22.1', 'I22.2', 'I22.8', 'I22.9',
                                            'I23', 'I23.0', 'I23.1', 'I23.2', 'I23.3', 'I23.4', 'I23.5', 'I23.6', 'I23.7',
                                            'I23.8', 'I24', 'I24.0', 'I24.1', 'I24.8', 'I24.9')
                            AND dxe.service_date BETWEEN DATE_SUB(e.service_date, INTERVAL 3 YEAR) AND e.service_date
                            AND dxe.close_date IS NOT NULL),
                  '1', '0') as had_mi_dx,
               IF(EXISTS(
                          SELECT *
                          FROM encounter_dx as edx
                          WHERE edx.pid = e.pid
                            AND edx.dx_type = 'F'
                            AND edx.code = 'I50.1'),
                  '1', '0') as has_or_had_lvsd_dx
        FROM (SELECT enc.pid, enc.eid, enc.service_date
              FROM encounters as enc
                       JOIN (SELECT enc.pid, COUNT(*) AS total
                             FROM encounters as enc
                                      INNER JOIN patient p ON enc.pid = p.pid
                                      INNER JOIN patient_insurances AS pi ON p.pid = pi.pid
                             WHERE enc.provider_uid = provider_id
                               AND enc.service_date BETWEEN start_date AND end_date
                               AND enc.close_date IS NOT NULL
                               AND (sex IS NULL OR p.sex = sex)
                               AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                               AND (race IS NULL OR p.race = race)
                               AND pi.insurance_id = insurance_id
                               AND YEAR(enc.service_date) - YEAR(p.DOB) -
                                   (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
                             GROUP BY enc.pid
                             HAVING total >= 2) as t
                            ON enc.pid = t.pid
                       INNER JOIN encounter_dx as edx
                                  ON enc.eid = edx.eid
                                      AND enc.pid = edx.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND edx.dx_type = 'F'
                AND edx.code IN ('I50', 'I50.1', 'I50.2', 'I50.20', 'I50.21', 'I50.22', 'I50.23', 'I50.3', 'I50.30',
                                 'I50.31', 'I50.32', 'I50.33', 'I50.4', 'I50.40', 'I50.41', 'I50.42', 'I50.43',
                                 'I50.8')) e;
    ELSE
        CREATE TEMPORARY TABLE report_ds
        SELECT e.eid,
               e.pid,
               IF(EXISTS(
                          SELECT *
                          FROM patient_medications as m
                          WHERE m.pid = e.pid
                            AND RXCUI IN ('1191185', '1297753', '1297757', '1495058', '1593725', '1606347', '1606349',
                                          '1798281', '1923422', '1923424', '1923426', '197379', '197380', '197381',
                                          '197382', '197383', '198000', '198001', '198006', '198007', '198008',
                                          '198104', '198105', '198284', '198285', '198286', '200031', '200032', '200033',
                                          '387013', '686924', '751612', '751618', '827073', '854901', '854905', '854908',
                                          '854916', '854919', '856422', '856429', '856448', '856457', '856460', '856481',
                                          '856519', '856535', '856556', '856569', '856578', '856724', '856733', '860510',
                                          '860516', '860522', '860532', '866412', '866419', '866427', '866436', '866452',
                                          '866461', '866472', '866479', '866482', '866491', '866511', '866514', '866924',
                                          '896758', '896762', '896766', '904589', '998685', '998689')
                            AND ((m.date_ordered BETWEEN start_date AND end_date) OR (m.is_active))),
                  '1', '0') as was_prescribed_or_active,
               IF(EXISTS(
                          SELECT dx.code
                          FROM encounter_dx AS dx
                                   JOIN encounters as dxe ON dx.eid = dxe.eid
                          WHERE dxe.pid = e.pid
                            AND dx.dx_type = 'F'
                            AND dx.code IN ('I20.9', 'I21', 'I21.0', 'I21.01', 'I21.02', 'I21.09', 'I21.1', 'I21.11',
                                            'I21.19', 'I12.12', 'I21.21', 'I21.29', 'I21.3', 'I21.4', 'I21.9', 'I21.A',
                                            'I21.A1', 'I21.A9', 'I22', 'I22.0', 'I22.1', 'I22.2', 'I22.8', 'I22.9',
                                            'I23', 'I23.0', 'I23.1', 'I23.2', 'I23.3', 'I23.4', 'I23.5', 'I23.6', 'I23.7',
                                            'I23.8', 'I24', 'I24.0', 'I24.1', 'I24.8', 'I24.9')
                            AND dxe.service_date BETWEEN DATE_SUB(e.service_date, INTERVAL 3 YEAR) AND e.service_date
                            AND dxe.close_date IS NOT NULL),
                  '1', '0') as had_mi_dx,
               IF(EXISTS(
                          SELECT *
                          FROM encounter_dx as edx
                          WHERE edx.pid = e.pid
                            AND edx.dx_type = 'F'
                            AND edx.code = 'I50.1'),
                  '1', '0') as has_or_had_lvsd_dx
        FROM (SELECT enc.pid, enc.eid, enc.service_date
              FROM encounters as enc
                       JOIN (SELECT enc.pid, COUNT(*) AS total
                             FROM encounters as enc
                                      INNER JOIN patient p ON enc.pid = p.pid
                             WHERE enc.provider_uid = provider_id
                               AND enc.service_date BETWEEN start_date AND end_date
                               AND enc.close_date IS NOT NULL
                               AND (sex IS NULL OR p.sex = sex)
                               AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                               AND (race IS NULL OR p.race = race)
                               AND YEAR(enc.service_date) - YEAR(p.DOB) -
                                   (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
                             GROUP BY enc.pid
                             HAVING total >= 2) as t
                            ON enc.pid = t.pid
                       INNER JOIN encounter_dx as edx
                                  ON enc.eid = edx.eid
                                      AND enc.pid = edx.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND edx.dx_type = 'F'
                AND edx.code IN ('I50', 'I50.1', 'I50.2', 'I50.20', 'I50.21', 'I50.22', 'I50.23', 'I50.3', 'I50.30',
                                 'I50.31', 'I50.32', 'I50.33', 'I50.4', 'I50.40', 'I50.41', 'I50.42', 'I50.43',
                                 'I50.8')) e;

        END IF;

        CREATE TEMPORARY TABLE report_denominator_ds_1
        SELECT 1 as `value`, pid FROM report_ds WHERE has_or_had_lvsd_dx = '1' AND had_mi_dx = '0' GROUP BY pid;

        CREATE TEMPORARY TABLE report_denominator_ds_2
        SELECT 1 as `value`, pid FROM report_ds WHERE had_mi_dx = '1' GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds_1
        SELECT 1 as `value`, pid FROM report_ds WHERE was_prescribed_or_active = '1' AND has_or_had_lvsd_dx = '1' AND had_mi_dx = '0' GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds_2
        SELECT 1 as `value`, pid FROM report_ds WHERE was_prescribed_or_active = '1' AND had_mi_dx = '1' GROUP BY pid;

        SET @denominator = (SELECT IFNULL(sum(`value`),0) FROM report_denominator_ds_1) + (SELECT IFNULL(sum(`value`),0) FROM report_denominator_ds_2);
        SET @numerator = (SELECT IFNULL(sum(`value`),0) FROM report_numerator_ds_1) + (SELECT IFNULL(sum(`value`),0) FROM report_numerator_ds_2);
        SET @denominator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM report_denominator_ds_1 UNION SELECT pid FROM report_denominator_ds_2 GROUP BY pid) as d);
        SET @numerator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM report_numerator_ds_1 UNION SELECT pid FROM report_numerator_ds_2 GROUP BY pid) as n);

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