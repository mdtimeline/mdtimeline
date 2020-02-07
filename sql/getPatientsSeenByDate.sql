DROP PROCEDURE IF EXISTS `getPatientsSeenDemographicsByDate`;

DELIMITER $$
CREATE PROCEDURE `getPatientsSeenDemographicsByDate` (IN start_date DATE, IN end_date DATE, IN facility_id INT)
BEGIN

    -- Use the individual’s age on June 30 of the reporting period.
    SET @age_from_date = DATE_FORMAT(start_date,'%Y-06-30');


    DROP TABLE IF EXISTS patient_seen_demographics_ds;

    CREATE TEMPORARY TABLE patient_seen_demographics_ds
    SELECT
        r.pid,
        r.service_date as last_service_date,
        r.patient_age,
        r.patient_sex,
        r.patient_is_male,
        r.patient_is_female,
        r.patient_identity,
        r.patient_oriantation,
        r.patient_race,
        r.patient_ethnicity,
        IF(patient_ethnicity = 'LATINO', 1, 0) AS patient_ethnicity_is_latino,
        IF(patient_ethnicity = 'NON_LATINO', 1, 0) AS patient_ethnicity_is_not_latino,
        IF(patient_ethnicity = 'NOT_REPORTED', 1, 0) AS patient_ethnicity_is_not_reported
    FROM
        (SELECT
             e.pid,
             e.service_date,
             (CASE
                 WHEN p.sex = 'M' THEN 'MALE'
                 WHEN p.sex = 'F' THEN 'FEMALE'
                 ELSE 'OTHER'
                 END
             ) as patient_sex,
             (CASE
                 WHEN p.sex = 'M' THEN 1
                 WHEN p.sex = 'F' THEN 0
                 ELSE 'OTHER'
                 END
             ) as patient_is_male,
             (CASE
                 WHEN p.sex = 'M' THEN 0
                 WHEN p.sex = 'F' THEN 1
                 ELSE 'OTHER'
                 END
             ) as patient_is_female,
             (CASE
                 WHEN p.identity = '446151000124109' THEN 'MALE'
                 WHEN p.identity = '446141000124107' THEN 'FEMALE'
                 WHEN p.identity = '407377005' THEN 'FEMALE_TO_MALE'
                 WHEN p.identity = '407376001' THEN 'MALE_TO_FEMALE'
                 WHEN p.identity = 'ASKU' THEN 'NOT_DISCLOSED'
                 ELSE 'OTHER'
                 END
             ) as patient_identity,
             (CASE
                 WHEN p.orientation = '38628009' THEN 'LESBIAN_GAY'
                 WHEN p.orientation = '20430005' THEN 'STRAIGHT'
                 WHEN p.orientation = '42035005' THEN 'BISEXUAL'
                 WHEN p.orientation = 'OTH' THEN 'SOMETHING_ELSE'
                 WHEN p.orientation = 'ASKU' THEN 'NOT_DISCLOSED'
                 ELSE 'DONT_KNOW'
                 END
             ) as patient_oriantation,
             (CASE
                 WHEN p.race = '2135-2' THEN 'ASIAN'
                 WHEN p.race = '2079-2' THEN 'HAWAIIAN'
                 WHEN p.race = '2054-5' THEN 'AFRICA_AMERICAN'
                 WHEN p.race = '1002-5' THEN 'INDIAN_ALASKA_NATIVE'
                 WHEN p.race = '2106-3' THEN 'WHITE'
                 WHEN p.secondary_race IS NOT NULL THEN 'MORE_THAN_ONE'
                 WHEN p.race = 'ASKU' OR p.ethnicity IS NULL OR p.ethnicity = '' THEN 'NOT_REPORTED'
                 ELSE 'UNK'
                 END
             ) as patient_race,
             (CASE
                 WHEN p.ethnicity = '2135-2' THEN 'LATINO'
                 WHEN p.ethnicity = 'ASKU' OR p.ethnicity IS NULL OR p.ethnicity = '' THEN 'NOT_REPORTED'
                 ELSE 'NON_LATINO'
              END
             ) as patient_ethnicity,
             TIMESTAMPDIFF(YEAR, p.DOB, @age_from_date) AS patient_age
         FROM
             encounters AS e
                 INNER JOIN patient AS p ON p.pid = e.pid
         WHERE
               e.facility = facility_id
           AND e.service_date IS NOT NULL
           AND e.service_date BETWEEN start_date AND end_date
         ORDER BY e.service_date DESC
        ) r GROUP BY r.pid;


    -- SELECT * FROM patient_seen_demographics_ds;

END$$

DELIMITER ;

