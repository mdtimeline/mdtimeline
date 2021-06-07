DROP PROCEDURE IF EXISTS `getControllingHighBloodPressureReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getControllingHighBloodPressureReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR)
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
               IF(EXISTS(
                          SELECT dx.code
                          FROM encounter_dx AS dx
                                   JOIN encounters as dxe ON dx.eid = dxe.eid
                          WHERE dxe.pid = e.pid
                            AND dx.dx_type = 'F'
                            AND dx.code = 'I10'
                            AND dxe.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                            AND dxe.close_date IS NOT NULL),
                  '1', '0') AS has_hypertesion_dx,
               (
                   SELECT vital.bp_systolic
                   FROM encounter_vitals AS vital
                   WHERE pid = e.pid
                     AND vital.eid = e.eid
                     AND vital.bp_systolic IS NOT NULL
                     AND vital.date BETWEEN start_date AND end_date
                   ORDER BY vital.id DESC
                   LIMIT 1
               )as last_bp_systolic,
               (
                   SELECT vital.bp_diastolic
                   FROM encounter_vitals AS vital
                   WHERE pid = e.pid
                     AND vital.eid = e.eid
                     AND vital.bp_diastolic IS NOT NULL
                     AND vital.date BETWEEN start_date AND end_date
                   ORDER BY vital.id DESC
                   LIMIT 1
               )as last_bp_diastolic
        FROM (SELECT enc.eid,
                     enc.pid,
                     YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) as age
              FROM encounters as enc
                       INNER JOIN patient p on enc.pid = p.pid
                       LEFT JOIN patient_insurances as pi ON pi.pid = p.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)
                AND pi.insurance_id = insurance_id) e
        WHERE e.age >= 18
          AND e.age <= 85;

    ELSE
        CREATE TEMPORARY TABLE report_ds
        SELECT e.eid,
               e.pid,
               IF(EXISTS(
                          SELECT dx.code
                          FROM encounter_dx AS dx
                                   JOIN encounters as dxe ON dx.eid = dxe.eid
                          WHERE dxe.pid = e.pid
                            AND dx.dx_type = 'F'
                            AND dx.code = 'I10'
                            AND dxe.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                            AND dxe.close_date IS NOT NULL),
                  '1', '0') AS has_hypertesion_dx,
               (
                   SELECT vital.bp_systolic
                   FROM encounter_vitals AS vital
                   WHERE pid = e.pid
                     AND vital.bp_systolic IS NOT NULL
                     AND vital.date BETWEEN start_date AND end_date
                   ORDER BY vital.id DESC
                   LIMIT 1
               )as last_bp_systolic,
               (
                   SELECT vital.bp_diastolic
                   FROM encounter_vitals AS vital
                   WHERE pid = e.pid
                     AND vital.bp_diastolic IS NOT NULL
                     AND vital.date BETWEEN start_date AND end_date
                   ORDER BY vital.id DESC
                   LIMIT 1
               )as last_bp_diastolic
        FROM (SELECT enc.eid,
                     enc.pid,
                     YEAR(enc.service_date) - YEAR(p.DOB) - (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) as age
              FROM encounters as enc
                       INNER JOIN patient p on enc.pid = p.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)) e
        WHERE e.age >= 18
          AND e.age <= 85;

    END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds where has_hypertesion_dx = '1'  GROUP BY eid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE has_hypertesion_dx = '1' AND last_bp_diastolic < 90 AND last_bp_systolic < 140 GROUP BY eid;

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
