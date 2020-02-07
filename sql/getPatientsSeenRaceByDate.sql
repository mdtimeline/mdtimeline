DROP PROCEDURE IF EXISTS `getPatientsSeenRaceByDate`;

DELIMITER $$
CREATE PROCEDURE `getPatientsSeenRaceByDate` (IN start_date DATE, IN end_date DATE, IN facility_id INT)
BEGIN

    call getPatientsSeenDemographicsByDate(start_date, end_date, facility_id);

    SELECT
        patient_race,
        SUM(patient_ethnicity_is_latino) as latino_count,
        SUM(patient_ethnicity_is_not_latino) as not_latino_count,
        SUM(patient_ethnicity_is_not_reported) as not_reported_count
    FROM patient_seen_demographics_ds
    GROUP BY patient_race
    ORDER BY FIELD(patient_race,'ASIAN','HAWAIIAN','AFRICA_AMERICAN','INDIAN_ALASKA_NATIVE','WHITE','MORE_THAN_ONE','NOT_REPORTED');

END$$

DELIMITER ;

