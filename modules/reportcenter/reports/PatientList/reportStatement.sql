SET @Provider = :provider;
SET @StartDate = :begin_date;
SET @EndDate = :end_date;
SET @ProblemCode = :problem_code;
SET @MedicationCode = :medication_code;
SET @MedicationAllergyCode = :allergy_code;
SET @RaceCode = :race;
SET @EthnicityCode = :ethnicity;
SET @SexCode = :sex;
SET @AgeFrom = :ageFrom;
SET @AgeTo = :ageTo;
SET @MaritalCode = :marital;
SET @LanguageCode = :language;
SET @LabOrderCode = :lab_result_code;
SET @LabOrderOperator = :lab_comparison;
SET @LabOrderValue = :lab_value;

SELECT patient.*,
	encounters.service_date,
	CONCAT(patient.fname, ' ', patient.mname, ' ', patient.lname) as patient_name,
	DATE_FORMAT(patient.DOB, '%d %b %y') as DateOfBirth,
	TIMESTAMPDIFF(YEAR, patient.DOB, CURDATE()) AS Age,
	Race.option_name as Race,
	Ethnicity.option_name as Ethnicity,
	CONCAT(Provider.fname,' ',Provider.mname,' ',Provider.lname) as ProviderName,
	GROUP_CONCAT(patient_allergies.allergy SEPARATOR ', <br>') as allergies,
	GROUP_CONCAT(patient_active_problems.code_text SEPARATOR ', <br>') as problems,
	GROUP_CONCAT(patient_medications.STR SEPARATOR ', <br>') as medications,
	GROUP_CONCAT(CONCAT(patient_order_results.code_text,': ', patient_order_results_observations.value, patient_order_results_observations.units) SEPARATOR ', <br>') as laboratories
FROM patient

#
# Encounters
#
INNER JOIN encounters ON patient.pid = encounters.pid AND
CASE
	WHEN @StartDate IS NOT NULL AND @EndDate IS NULL
    THEN encounters.service_date BETWEEN CONCAT(@StartDate, ' ', '00:00:00') AND NOW()
	WHEN @StartDate IS NOT NULL AND @EndDate IS NOT NULL
	THEN encounters.service_date BETWEEN CONCAT(@StartDate, ' ', '00:00:00') AND CONCAT(@EndDate, ' ', '23:00:00')
END

#
# Active Problems
#
LEFT JOIN patient_active_problems ON patient.pid = patient_active_problems.pid AND
CASE
    WHEN @ProblemCode IS NOT NULL
	THEN patient_active_problems.code = @ProblemCode
END

#
# Active Medications
#
LEFT JOIN patient_medications ON patient.pid = patient_medications.pid AND
CASE
	WHEN @MedicationCode IS NOT NULL
	THEN patient_medications.rxcui = @MedicationCode
END

#
# Active Allergies
#
LEFT JOIN patient_allergies ON patient_allergies.pid = patient.pid AND
CASE
    WHEN @MedicationAllergyCode IS NOT NULL
	THEN patient_allergies.allergy_code = @MedicationAllergyCode
END

#
# Race
#
LEFT JOIN combo_lists_options as Race ON Race.option_value = patient.race
AND Race.list_id = 14

#
# Ethnicity
#
LEFT JOIN combo_lists_options as Ethnicity ON Ethnicity.option_value = patient.ethnicity
AND Ethnicity.list_id = 59

#
# Patient's provider
#
LEFT JOIN users as Provider ON Provider.id = encounters.provider_uid AND
CASE
	WHEN @Provider IS NOT NULL
    THEN encounters.provider_uid = @Provider
END

#
# Laboratory Orders
#
LEFT JOIN patient_order_results ON patient.pid = patient_order_results.pid AND
CASE
	WHEN @LabOrderCode IS NOT NULL
    THEN patient_order_results.code = @LabOrderCode
END

#
# Laboratory Results
#
LEFT JOIN patient_order_results_observations ON patient_order_results.id = patient_order_results_observations.result_id AND
CASE
	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '='
	THEN patient_order_results_observations.value = @LabOrderValue

	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '>='
	THEN patient_order_results_observations.value >= @LabOrderValue

	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '<='
	THEN patient_order_results_observations.value <= @LabOrderValue
END

#
# Where Filter
#
WHERE
CASE
    WHEN @EthnicityCode IS NOT NULL
	THEN patient.ethnicity = @EthnicityCode

    WHEN @SexCode IS NOT NULL
	THEN patient.sex = @SexCode

    WHEN @MaritalCode IS NOT NULL
	THEN patient.marital_status = @MaritalCode

    WHEN @LanguageCode IS NOT NULL
	THEN patient.language = @LanguageCode

	WHEN @ProblemCode IS NOT NULL
	THEN patient_active_problems.code = @ProblemCode

	WHEN @Provider IS NOT NULL
    THEN encounters.provider_uid = @Provider

	WHEN @LabOrderCode IS NOT NULL
    THEN patient_order_results.code = @LabOrderCode

	WHEN @MedicationCode IS NOT NULL
	THEN patient_medications.rxcui = @MedicationCode

	WHEN @MedicationAllergyCode IS NOT NULL
	THEN patient_allergies.allergy_code = @MedicationAllergyCode

	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '=' AND @LabOrderCode IS NOT NULL
	THEN patient_order_results_observations.value = @LabOrderValue AND patient_order_results.code = @LabOrderCode

	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '>=' AND @LabOrderCode IS NOT NULL
	THEN patient_order_results_observations.value >= @LabOrderValue AND patient_order_results.code = @LabOrderCode

	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '<=' AND @LabOrderCode IS NOT NULL
	THEN patient_order_results_observations.value <= @LabOrderValue AND patient_order_results.code = @LabOrderCode

ELSE 1=1
END

GROUP BY patient.pid

#
# Having Filter
#
HAVING CASE
    WHEN @AgeFrom IS NOT NULL AND @AgeTo IS NOT NULL
    THEN Age BETWEEN @AgeFrom AND @AgeTo
    ELSE 1=1
END
