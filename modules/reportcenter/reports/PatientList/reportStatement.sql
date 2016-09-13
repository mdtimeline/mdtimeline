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
    GROUP_CONCAT(encounters.service_date SEPARATOR ', <br>') as service_dates,
	GROUP_CONCAT(patient_allergies.allergy SEPARATOR ', <br>') as allergies,
	GROUP_CONCAT(patient_active_problems.code_text SEPARATOR ', <br>') as problems,
	GROUP_CONCAT(patient_medications.STR SEPARATOR ', <br>') as medications,
	GROUP_CONCAT(CONCAT(
						patient_order_results.code_text,
                        ': ',
                        patient_order_results_observations.value,
                        patient_order_results_observations.units
						) SEPARATOR ', <br>'
				) as laboratories
FROM patient

#
# Encounters Join
#
INNER JOIN encounters ON patient.pid = encounters.pid

#
# Active Problems Join
#
LEFT JOIN patient_active_problems ON encounters.eid = patient_active_problems.eid

#
# Active Medications Join
#
LEFT JOIN patient_medications ON encounters.eid = patient_medications.eid

#
# Active Allergies Join
#
LEFT JOIN patient_allergies ON encounters.eid = patient_allergies.eid

#
# Race Join
#
LEFT JOIN combo_lists_options as Race ON Race.option_value = patient.race
AND Race.list_id = 14

#
# Ethnicity Join
#
LEFT JOIN combo_lists_options as Ethnicity ON Ethnicity.option_value = patient.ethnicity
AND Ethnicity.list_id = 59

#
# Patient's provider Join
#
LEFT JOIN users as Provider ON Provider.id = encounters.provider_uid

#
# Laboratory Orders Join
#
LEFT JOIN patient_orders ON encounters.eid = patient_orders.eid

#
# Laboratory Reults Join
#
LEFT JOIN patient_order_results ON patient_orders.id = patient_order_results.order_id

#
# Laboratory Observtions Join
#
LEFT JOIN patient_order_results_observations ON patient_order_results.id = patient_order_results_observations.result_id

#
# Where Filter
#
WHERE
CASE
    WHEN @EthnicityCode IS NOT NULL
	THEN patient.ethnicity = @EthnicityCode
    ELSE 1=1
END AND

CASE
    WHEN @SexCode IS NOT NULL
	THEN patient.sex = @SexCode
    ELSE 1=1
END AND

CASE
    WHEN @MaritalCode IS NOT NULL
	THEN patient.marital_status = @MaritalCode
    ELSE 1=1
END AND

CASE
    WHEN @RaceCode IS NOT NULL
	THEN patient.race = @RaceCode
    ELSE 1=1
END AND

CASE
    WHEN @LanguageCode IS NOT NULL
	THEN patient.language = @LanguageCode
    ELSE 1=1
END AND

CASE
	WHEN @ProblemCode IS NOT NULL
	THEN patient_active_problems.code = @ProblemCode
    ELSE 1=1
END AND

CASE
	WHEN @Provider IS NOT NULL
    THEN encounters.provider_uid = @Provider
    ELSE 1=1
END AND

CASE
	WHEN @LabOrderCode IS NOT NULL
    THEN patient_order_results.code = @LabOrderCode
    ELSE 1=1
END AND

CASE
	WHEN @MedicationCode IS NOT NULL
	THEN patient_medications.rxcui = @MedicationCode
    ELSE 1=1
END AND

CASE
	WHEN @MedicationAllergyCode IS NOT NULL
	THEN patient_allergies.allergy_code = @MedicationAllergyCode
    ELSE 1=1
END AND

CASE
	WHEN @LabOrderCode IS NOT NULL
    THEN patient_order_results.code = @LabOrderCode
    ELSE 1=1
END AND

CASE
    WHEN @StartDate IS NOT NULL AND @EndDate IS NULL
    THEN encounters.service_date BETWEEN CONCAT(@StartDate, ' ', '00:00:00') AND NOW()

	WHEN @StartDate IS NOT NULL AND @EndDate IS NOT NULL
	THEN encounters.service_date BETWEEN CONCAT(@StartDate, ' ', '00:00:00') AND CONCAT(@EndDate, ' ', '23:00:00')

    ELSE 1=1
END AND

CASE
	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '=' AND @LabOrderCode IS NOT NULL
	THEN patient_order_results_observations.value = @LabOrderValue AND patient_order_results_observations.code = @LabOrderCode

	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '>=' AND @LabOrderCode IS NOT NULL
	THEN patient_order_results_observations.value >= @LabOrderValue AND patient_order_results_observations.code = @LabOrderCode

	WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '<=' AND @LabOrderCode IS NOT NULL
	THEN patient_order_results_observations.value <= @LabOrderValue AND patient_order_results_observations.code = @LabOrderCode

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
