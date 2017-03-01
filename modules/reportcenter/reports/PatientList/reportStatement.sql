SET @Provider = :provider;
SET @StartDate = :begin_date;
SET @EndDate = :end_date;
SET @ProblemCode = :problem_code;
SET @MedicationCode = :medication_code;
SET @MedicationAllergyCode = :allergy_code;
SET @RaceCode = :race;
SET @EthnicityCode = :ethnicity;
SET @SexCode = :sex;
SET @CommunicationCode = :phone_publicity;
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
	Communication.option_name as Communication,
	Ethnicity.option_name as Ethnicity,
	CONCAT(Provider.fname,' ',Provider.mname,' ',Provider.lname) as ProviderName,
	GROUP_CONCAT(encounters.service_date SEPARATOR ', <br>') as service_dates,

	# Compile Medication Allergies
	(SELECT GROUP_CONCAT(distinct(allergy) SEPARATOR ', <br>') as allergy
	FROM patient_allergies
	INNER JOIN patient ON patient.pid = patient_allergies.pid
	  WHERE
	CASE
	WHEN @MedicationAllergyCode IS NOT NULL
	THEN patient_allergies.allergy_code = @MedicationAllergyCode
	ELSE 1=1
	END) AS allergies,

	# Compile Active Problems
	(SELECT GROUP_CONCAT(distinct(code_text) SEPARATOR ', <br>') as code_text
	FROM patient_active_problems
	INNER JOIN patient ON patient.pid = patient_active_problems.pid
	  WHERE
	CASE
	WHEN @ProblemCode IS NOT NULL
	THEN FIND_IN_SET(patient_active_problems.code, @ProblemCode)
	ELSE 1=1
	END) AS problems,

	# Compile Medications
	(SELECT GROUP_CONCAT(distinct(STR) SEPARATOR ', <br>') as STR
	FROM patient_medications
	INNER JOIN patient ON patient.pid = patient_medications.pid
	  WHERE
	CASE
	WHEN @MedicationCode IS NOT NULL
	THEN patient_medications.rxcui = @MedicationCode
	ELSE 1=1
	END) AS medications,

	# Compile Laboratories Orders/Results/Values
	(SELECT GROUP_CONCAT(CONCAT(patient_order_results.code_text,': ',patient_order_results_observations.value,patient_order_results_observations.units) SEPARATOR ', <br>') as order_result
	FROM patient_orders
	INNER JOIN patient ON patient.pid = patient_orders.pid
    LEFT JOIN patient_order_results ON patient_orders.id = patient_order_results.order_id
    LEFT JOIN patient_order_results_observations ON patient_order_results.id = patient_order_results_observations.result_id
	WHERE
	CASE
		WHEN @LabOrderCode IS NOT NULL
		THEN patient_order_results.code = @LabOrderCode

		WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '=' AND @LabOrderCode IS NOT NULL
		THEN patient_order_results_observations.value = @LabOrderValue AND patient_order_results_observations.code = @LabOrderCode

		WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '>=' AND @LabOrderCode IS NOT NULL
		THEN patient_order_results_observations.value >= @LabOrderValue AND patient_order_results_observations.code = @LabOrderCode

		WHEN @LabOrderValue IS NOT NULL AND @LabOrderOperator = '<=' AND @LabOrderCode IS NOT NULL
		THEN patient_order_results_observations.value <= @LabOrderValue AND patient_order_results_observations.code = @LabOrderCode
		ELSE 1=1
    END) AS laboratories

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
# Phone Publicity (Communication)
#
LEFT JOIN combo_lists_options as Communication ON Communication.option_value = patient.phone_publicity
AND Communication.list_id = 132

#
# Patient provider Join
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
# Laboratory Observations Join
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
    WHEN @CommunicationCode IS NOT NULL
	THEN patient.phone_publicity = @CommunicationCode
    ELSE 1=1
END AND

CASE
	WHEN @ProblemCode IS NOT NULL
	THEN FIND_IN_SET(patient_active_problems.code, @ProblemCode)
    ELSE 1=1
END AND

CASE
	WHEN @Provider IS NOT NULL
    THEN encounters.provider_uid = @Provider
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
    WHEN @StartDate IS NOT NULL AND @EndDate IS NULL
    THEN encounters.service_date BETWEEN CONCAT(@StartDate, ' ', '00:00:00') AND NOW()

	WHEN @StartDate IS NOT NULL AND @EndDate IS NOT NULL
	THEN encounters.service_date BETWEEN CONCAT(@StartDate, ' ', '00:00:00') AND CONCAT(@EndDate, ' ', '23:00:00')

    ELSE 1=1
END AND

CASE
	WHEN @LabOrderCode IS NOT NULL
    THEN patient_order_results.code = @LabOrderCode
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
