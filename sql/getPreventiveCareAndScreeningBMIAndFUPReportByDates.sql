DROP PROCEDURE IF EXISTS `getPreventiveCareAndScreeningBMIAndFUPReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPreventiveCareAndScreeningBMIAndFUPReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR)
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
        SELECT e.pid,
               IF(EXISTS(
                          SELECT *
                          FROM encounters AS en
                                   INNER JOIN encounter_vitals AS ev ON en.eid = ev.eid AND en.pid = ev.pid
                                   INNER JOIN patient_interventions pi ON en.eid = pi.eid AND en.pid = pi.pid
                          WHERE en.pid = e.pid
                            AND en.service_date BETWEEN DATE_SUB(e.service_date, INTERVAL 1 YEAR) AND e.service_date
                            AND en.close_date IS NOT NULL
                            AND ev.bmi IS NOT NULL
                            AND pi.code IN
                                ('304549008', '307818003', '370847001', '386291006', '386292004', '386373004',
                                 '386463000', '386464006', '410177006', '413315001', '418995006', '424753004',
                                 '429095004', '443288003')
                      ), '1', '0') as has_BMI_and_intervention

        FROM (SELECT enc.pid, MAX(enc.service_date) AS service_date
              FROM encounters as enc
                       INNER JOIN patient p ON enc.pid = p.pid
                       LEFT JOIN patient_insurances AS pi ON enc.pid = pi.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND pi.insurance_id = insurance_id
                AND (sex IS NULL OR p.sex = sex)
                AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
              GROUP BY enc.pid) e;
    ELSE
        CREATE TEMPORARY TABLE report_ds
        SELECT e.pid,
               IF(EXISTS(
                          SELECT *
                          FROM encounters AS en
                                   INNER JOIN encounter_vitals AS ev ON en.eid = ev.eid AND en.pid = ev.pid
                                   INNER JOIN patient_interventions pi ON en.eid = pi.eid AND en.pid = pi.pid
                          WHERE en.pid = e.pid
                            AND en.service_date BETWEEN DATE_SUB(e.service_date, INTERVAL 1 YEAR) AND e.service_date
                            AND en.close_date IS NOT NULL
                            AND ev.bmi IS NOT NULL
                            AND pi.code IN
                                ('304549008', '307818003', '370847001', '386291006', '386292004', '386373004',
                                 '386463000', '386464006', '410177006', '413315001', '418995006', '424753004',
                                 '429095004', '443288003')
                      ), '1', '0') as has_BMI_and_intervention
        FROM (SELECT enc.pid, MAX(enc.service_date) AS service_date
              FROM encounters as enc
                       INNER JOIN patient p ON enc.pid = p.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)
                AND YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 18
              GROUP BY enc.pid) e;
    END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds  GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE has_BMI_and_intervention = '1' GROUP BY pid;

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