DROP PROCEDURE IF EXISTS `getTobaccoUseScreeningCessationInterventionReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTobaccoUseScreeningCessationInterventionReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR, IN ethnicity VARCHAR(40), IN race VARCHAR(40))
BEGIN



    DROP TABLE IF EXISTS report_ds;
    DROP TABLE IF EXISTS report_denominator_ds;
    DROP TABLE IF EXISTS report_numerator_ds_1;
    DROP TABLE IF EXISTS report_denominator_ds_2;
    DROP TABLE IF EXISTS report_numerator_ds_2;
    DROP TABLE IF EXISTS report_numerator_ds_3;


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
               e.counseling AS counseling_given,
               !ISNULL(e.status_code) AS was_screened_for_tobacco_use,
               IF(e.status_code IN ('449868002', '428041000124106', '77176002', '428071000124103', '428061000124105',
                                    '110483000', '160603005', '43381005', '56578002', '65568007', '81703003', '89765005'),
               '1', '0') AS identified_as_tobacco_user,
            e.service_date
        FROM (SELECT enc.eid,
                     enc.pid,
                     pss.counseling,
                     pss.status_code,
                     enc.service_date
              FROM encounters as enc
                       INNER JOIN patient p on enc.pid = p.pid
                       LEFT JOIN patient_insurances as pi ON pi.pid = p.pid
                       LEFT JOIN patient_smoke_status as pss ON enc.pid = pss.pid
                       LEFT JOIN patient_interventions as pin ON enc.pid = pin.pid
              WHERE YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
                AND enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)
                AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                AND (race IS NULL OR p.race = race)
                AND pi.insurance_id = insurance_id
              GROUP BY enc.pid) e;
    ELSE
        CREATE TEMPORARY TABLE report_ds
        SELECT e.eid,
               e.pid,
               e.counseling AS counseling_given,
               !ISNULL(e.status_code) AS was_screened_for_tobacco_use,
               IF(e.status_code IN ('449868002', '428041000124106', '77176002', '428071000124103', '428061000124105',
                                    '110483000', '160603005', '43381005', '56578002', '65568007', '81703003', '89765005'),
                  '1', '0') AS identified_as_tobacco_user,
               e.service_date
        FROM (SELECT enc.eid,
                     enc.pid,
                     pss.counseling,
                     pss.status_code,
                     enc.service_date
              FROM encounters as enc
                       INNER JOIN patient p on enc.pid = p.pid
                       LEFT JOIN patient_smoke_status as pss ON enc.pid = pss.pid
                       LEFT JOIN patient_interventions as pin ON enc.pid = pin.pid
              WHERE YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
                AND enc.provider_uid = provider_id
                AND enc.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
#                 AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)
                AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                AND (race IS NULL OR p.race = race)
              GROUP BY enc.pid) e;
    END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE service_date BETWEEN start_date AND end_date GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds_1
        SELECT 1 as `value`, ds.pid FROM report_ds AS ds INNER JOIN report_denominator_ds AS d ON d.pid = ds.pid  WHERE ds.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date AND ds.was_screened_for_tobacco_use = '1' GROUP BY d.pid;

        SET @denominator_1 = (SELECT sum(`value`) FROM report_denominator_ds);
        SET @numerator_1 = (SELECT sum(`value`) FROM report_numerator_ds_1);
        SET @denominator_pids_1 = (SELECT group_concat(pid) FROM (SELECT pid FROM report_denominator_ds GROUP BY pid) as d);
        SET @numerator_pids_1 = (SELECT group_concat(pid) FROM (SELECT pid FROM report_numerator_ds_1 GROUP BY pid) as n);

        CREATE TEMPORARY TABLE report_denominator_ds_2
        SELECT 1 as `value`, pid FROM report_ds WHERE service_date BETWEEN start_date AND end_date AND was_screened_for_tobacco_use = '1' AND identified_as_tobacco_user = '1' GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds_2
        SELECT 1 as `value`, pid FROM report_ds WHERE service_date BETWEEN start_date AND end_date AND was_screened_for_tobacco_use = '1' AND identified_as_tobacco_user = '1' AND counseling_given = '1' GROUP BY pid;

        SET @denominator_2 = (SELECT sum(`value`) FROM report_denominator_ds_2);
        SET @numerator_2 = (SELECT sum(`value`) FROM report_numerator_ds_2);
        SET @denominator_pids_2 = (SELECT group_concat(pid) FROM (SELECT pid FROM report_denominator_ds_2 GROUP BY pid) as d);
        SET @numerator_pids_2 = (SELECT group_concat(pid) FROM (SELECT pid FROM report_numerator_ds_2 GROUP BY pid) as n);

        CREATE TEMPORARY TABLE report_numerator_ds_3
        SELECT 1 as `value`, ds.pid FROM report_ds AS ds INNER JOIN report_denominator_ds AS d ON d.pid = ds.pid  WHERE ds.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date AND ds.was_screened_for_tobacco_use = '1' AND identified_as_tobacco_user = '1' AND counseling_given = '1' GROUP BY d.pid;

        SET @denominator_3 = (SELECT sum(`value`) FROM report_denominator_ds);
        SET @numerator_3 = (SELECT sum(`value`) FROM report_numerator_ds_3);
        SET @denominator_pids_3 = (SELECT group_concat(pid) FROM (SELECT pid FROM report_denominator_ds GROUP BY pid) as d);
        SET @numerator_pids_3 = (SELECT group_concat(pid) FROM (SELECT pid FROM report_numerator_ds_3 GROUP BY pid) as n);

    SELECT *
    FROM (
             (SELECT @provider            as provider,
                     @insurance           as insurance,
                     'MIPS - Population 1' as title,
                     @denominator_1         as denominator,
                     @denominator_pids_1    as denominator_pids,
                     @numerator_1           as numerator,
                     @numerator_pids_1      as numerator_pids)
             UNION ALL
             (SELECT @provider            as provider,
                     @insurance           as insurance,
                     'MIPS - Population 2' as title,
                     @denominator_2       as denominator,
                     @denominator_pids_2  as denominator_pids,
                     @numerator_2         as numerator,
                     @numerator_pids_2    as numerator_pids)
             UNION ALL
             (SELECT @provider            as provider,
                     @insurance           as insurance,
                     'MIPS - Population 3' as title,
                     @denominator_3       as denominator,
                     @denominator_pids_3  as denominator_pids,
                     @numerator_3         as numerator,
                     @numerator_pids_3    as numerator_pids)) AS r;

END$$

DELIMITER ;
