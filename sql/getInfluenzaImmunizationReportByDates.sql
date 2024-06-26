DROP PROCEDURE IF EXISTS `getInfluenzaImmunizationReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getInfluenzaImmunizationReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR, IN ethnicity VARCHAR(40), IN race VARCHAR(40))
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
                          SELECT *
                          FROM encounters
                          WHERE pid = e.pid
                            AND service_date BETWEEN CONCAT(YEAR(start_date) - 1, '-10-01') AND CONCAT(YEAR(start_date), '-03-31')
                            AND close_date IS NOT NULL),
                  '1', '0') as has_encounter_within_season,
               IF(EXISTS(
                          SELECT *
                          FROM encounters AS enc
                                   INNER JOIN patient_immunizations AS pi
                                              ON enc.pid = pi.pid
                                                  AND enc.eid = pi.eid
                          WHERE enc.pid = e.pid
                            AND pi.CODE IN ('135', '140', '141', '144', '149', '150', '155', '158', '161', '166', '168',
                                            '171', '185', '186', '197', '205', '88')
                            AND pi.administered_date BETWEEN CONCAT(YEAR(start_date) - 1, '-10-01') AND CONCAT(YEAR(start_date), '-03-31')),
                  '1', '0') as influenza_vaccine_administered
        FROM (SELECT enc.eid, enc.pid
              FROM encounters as enc
                       INNER JOIN patient p ON enc.pid = p.pid
                       LEFT JOIN patient_insurances as pi ON pi.pid = p.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)
                AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                AND (race IS NULL OR p.race = race)
                AND pi.insurance_id = insurance_id
                AND TIMESTAMPDIFF(MONTH, p.DOB, start_date) >= 6
              GROUP BY enc.pid) e;
    ELSE
        CREATE TEMPORARY TABLE report_ds
        SELECT e.eid,
               e.pid,
               IF(EXISTS(
                          SELECT *
                          FROM encounters
                          WHERE pid = e.pid
                            AND service_date BETWEEN CONCAT(YEAR(start_date) - 1, '-10-01') AND CONCAT(YEAR(start_date), '-03-31')
                            AND close_date IS NOT NULL),
                  '1', '0') as has_encounter_within_season,
               IF(EXISTS(
                          SELECT *
                          FROM encounters AS enc
                                   INNER JOIN patient_immunizations AS pi
                                              ON enc.pid = pi.pid
                                                  AND enc.eid = pi.eid
                          WHERE enc.pid = e.pid
                            AND pi.CODE IN ('135', '140', '141', '144', '149', '150', '155', '158', '161', '166', '168',
                                            '171', '185', '186', '197', '205', '88')
                            AND pi.administered_date BETWEEN CONCAT(YEAR(start_date) - 1, '-10-01') AND CONCAT(YEAR(start_date), '-03-31')),
                  '1', '0') as influenza_vaccine_administered
        FROM (SELECT enc.eid, enc.pid, enc.service_date
              FROM encounters as enc
                       INNER JOIN patient p ON enc.pid = p.pid
              WHERE enc.provider_uid = provider_id
                AND enc.service_date BETWEEN start_date AND end_date
                AND enc.close_date IS NOT NULL
                AND (sex IS NULL OR p.sex = sex)
                AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                AND (race IS NULL OR p.race = race)
                AND TIMESTAMPDIFF(MONTH, p.DOB, enc.service_date) >= 6
              GROUP BY enc.pid) e;
    END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE has_encounter_within_season = '1'  GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE has_encounter_within_season = '1' AND influenza_vaccine_administered = '1' GROUP BY pid;

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