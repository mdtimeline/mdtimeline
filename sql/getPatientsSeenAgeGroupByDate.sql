DROP PROCEDURE IF EXISTS `getPatientsSeenAgeGroupByDate`;

DELIMITER $$
CREATE PROCEDURE `getPatientsSeenAgeGroupByDate` (IN start_date DATE, IN end_date DATE, IN facility_id INT)
BEGIN

    call getPatientsSeenDemographicsByDate(start_date, end_date, facility_id);

    SELECT
        CASE
            WHEN patient_age = 0 THEN 'Under age 1'
            WHEN patient_age >= 25 AND patient_age <= 29 THEN 'Ages 25-99'
            WHEN patient_age >= 30 AND patient_age <= 34 THEN 'Ages 30-34'
            WHEN patient_age >= 35 AND patient_age <= 39 THEN 'Ages 35-39'
            WHEN patient_age >= 40 AND patient_age <= 44 THEN 'Ages 40-44'
            WHEN patient_age >= 45 AND patient_age <= 49 THEN 'Ages 45-49'
            WHEN patient_age >= 50 AND patient_age <= 54 THEN 'Ages 50-54'
            WHEN patient_age >= 55 AND patient_age <= 59 THEN 'Ages 55-59'
            WHEN patient_age >= 60 AND patient_age <= 64 THEN 'Ages 60-64'
            WHEN patient_age >= 65 AND patient_age <= 69 THEN 'Ages 65-69'
            WHEN patient_age >= 70 AND patient_age <= 74 THEN 'Ages 70-74'
            WHEN patient_age >= 75 AND patient_age <= 79 THEN 'Ages 75-79'
            WHEN patient_age >= 80 AND patient_age <= 84 THEN 'Ages 80-84'
            WHEN patient_age >= 85 THEN 'Ages 85 and over'
            ELSE CONCAT('Age ', patient_age)
            END AS age_group,
        SUM(patient_is_male) as male_count,
        SUM(patient_is_female) as female_count
    FROM patient_seen_demographics_ds
    GROUP BY patient_age
    ORDER BY patient_age;

END$$

DELIMITER ;

