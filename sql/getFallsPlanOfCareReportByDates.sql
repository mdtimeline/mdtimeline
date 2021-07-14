DROP PROCEDURE IF EXISTS `getFallsPlanOfCareReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFallsPlanOfCareReportByDates`(IN provider_id INT, IN insurance_id INT, IN start_date DATE, IN end_date DATE, IN sex CHAR, IN ethnicity VARCHAR(40), IN race VARCHAR(40))
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
               IF(e.goal_code IS NOT NULL, '1', '0') AS with_a_plan_of_care
        FROM (SELECT enc.pid, enc.eid, pcpg.goal_code
              FROM encounters as enc
                       INNER JOIN (
                  SELECT pid, COUNT(*) AS total
                  FROM (
                           SELECT enc.pid, enc.eid, pcfs.code
                           FROM encounters AS enc
                                    INNER JOIN patient p
                                               ON enc.pid = p.pid
                                    INNER JOIN patient_insurances AS pi
                                               ON p.pid = pi.pid
                                    INNER JOIN (
                               SELECT *
                               FROM patient_cognitive_functional_status
                               WHERE code IN ('1912002')
                                 AND begin_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                           ) AS pcfs
                                               ON pcfs.pid = enc.pid
                                                   AND pcfs.eid = enc.eid
                           WHERE YEAR(enc.service_date) - YEAR(p.DOB) -
                                 (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 65
                             AND (sex IS NULL OR p.sex = sex)
                             AND (sex IS NULL OR p.sex = sex)
                             AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                             AND pi.insurance_id = insurance_id
                             AND enc.provider_uid = provider_id
                             AND enc.close_date IS NOT NULL
                       ) te
                  GROUP BY pid
                  HAVING total >= 2
              ) AS e
                                  ON enc.pid = e.pid
                       LEFT JOIN patient_care_plan_goals AS pcpg
                                 ON pcpg.pid = enc.pid
                                     AND pcpg.eid = enc.eid
                                     AND pcpg.goal_code IN ('390997009', '408589008', '414191008', '711002000')
              WHERE enc.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                AND enc.close_date IS NOT NULL
                AND enc.provider_uid = provider_id) e;

    ELSE
        CREATE TEMPORARY TABLE report_ds
        SELECT e.eid,
               e.pid,
               IF(e.goal_code IS NOT NULL, '1', '0') AS with_a_plan_of_care
        FROM (SELECT enc.pid, enc.eid, pcpg.goal_code
              FROM encounters as enc
                       INNER JOIN (
                  SELECT pid, COUNT(*) AS total
                  FROM (
                           SELECT enc.pid, enc.eid, pcfs.code
                           FROM encounters AS enc
                                    INNER JOIN patient p
                                               ON enc.pid = p.pid
                                    INNER JOIN (
                               SELECT *
                               FROM patient_cognitive_functional_status
                               WHERE code IN ('1912002')
                                 AND begin_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                           ) AS pcfs
                                               ON pcfs.pid = enc.pid
                                                   AND pcfs.eid = enc.eid
                           WHERE YEAR(enc.service_date) - YEAR(p.DOB) -
                                 (RIGHT(enc.service_date, 5) < RIGHT(p.DOB, 5)) >= 65
                             AND (sex IS NULL OR p.sex = sex)
                             AND (sex IS NULL OR p.sex = sex)
                             AND (ethnicity IS NULL OR p.ethnicity = ethnicity)
                             AND enc.provider_uid = provider_id
                             AND enc.close_date IS NOT NULL
                       ) te
                  GROUP BY pid
                  HAVING total >= 2
              ) AS e
                                  ON enc.pid = e.pid
                       LEFT JOIN patient_care_plan_goals AS pcpg
                                 ON pcpg.pid = enc.pid
                                     AND pcpg.eid = enc.eid
                                     AND pcpg.goal_code IN ('390997009', '408589008', '414191008', '711002000')
              WHERE enc.service_date BETWEEN DATE_SUB(start_date, INTERVAL 1 YEAR) AND end_date
                AND enc.close_date IS NOT NULL
                AND enc.provider_uid = provider_id) e;

        END IF;


        CREATE TEMPORARY TABLE report_denominator_ds
        SELECT 1 as `value`, pid FROM report_ds  GROUP BY pid;

        CREATE TEMPORARY TABLE report_numerator_ds
        SELECT 1 as `value`, pid FROM report_ds WHERE with_a_plan_of_care = '1' GROUP BY pid;

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
