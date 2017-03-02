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

SELECT patient.pid,
		CONCAT(patient.fname, ' ', patient.mname, ' ', patient.lname) as patient_name,
		DATE_FORMAT(patient.DOB, '%d %b %y') as DateOfBirth,
        TIMESTAMPDIFF(YEAR, patient.DOB, CURDATE()) AS Age,
        Race.option_name as Race,
        Ethnicity.option_name as Ethnicity,
        Communication.option_name as Communication,

        # Encounter Service Dates
        (SELECT
			GROUP_CONCAT(encounters.service_date SEPARATOR ', <br>') as service_dates
		FROM encounters
		WHERE patient.pid = encounters.pid) AS service_dates,

		# Patient providers
		(SELECT
			GROUP_CONCAT(CONCAT(fname, ' ', mname, ' ', lname) SEPARATOR '<br>') as providers
		FROM users
		WHERE
			users.id IN (SELECT encounters.provider_uid FROM encounters WHERE patient.pid = encounters.pid)
		) AS providers,

        # Patient Medications
		(SELECT
			GROUP_CONCAT(distinct(STR) SEPARATOR ', <br>') as STR
		FROM patient_medications
		WHERE patient.pid = patient_medications.pid AND
        CASE
		WHEN @MedicationCode IS NOT NULL
			THEN patient_medications.RXCUI = @MedicationCode
			ELSE 1=1
		END) AS medications,

		# Patient Active Problems
		(SELECT
			GROUP_CONCAT(distinct(code_text) SEPARATOR '<br>') as code_text
		FROM patient_active_problems
		WHERE patient_active_problems.pid = patient.pid AND
        CASE
		WHEN @ProblemCode IS NOT NULL
			THEN FIND_IN_SET(patient_active_problems.code, @ProblemCode)
			ELSE 1=1
		END) AS problems,

		# Patient Medication Allergies
		(SELECT
			GROUP_CONCAT(distinct(allergy) SEPARATOR '<br>') as allergy
		FROM patient_allergies
		WHERE patient_active_problems.pid = patient.pid AND
		CASE
			WHEN @MedicationAllergyCode IS NOT NULL
			THEN FIND_IN_SET(patient_allergies.allergy_code, @MedicationAllergyCode)
			ELSE 1=1
		END) AS allergies

FROM patient

# Encounters Join
LEFT JOIN encounters ON patient.pid = encounters.pid

# Patient Medications Join
LEFT JOIN patient_medications ON encounters.eid = patient_medications.eid

# Active Problems Join
LEFT JOIN patient_active_problems ON encounters.eid = patient_active_problems.eid

# Medication Allergies Join
LEFT JOIN patient_allergies ON encounters.eid = patient_allergies.eid

# Providers Join
LEFT JOIN users as Provider ON Provider.id = encounters.provider_uid

# Race Join
LEFT JOIN combo_lists_options as Race ON Race.option_value = patient.race AND Race.list_id = 14

# Ethnicity Join
LEFT JOIN combo_lists_options as Ethnicity ON Ethnicity.option_value = patient.ethnicity AND Ethnicity.list_id = 59

# Phone Publicity (Communication)
LEFT JOIN combo_lists_options as Communication ON Communication.option_value = patient.phone_publicity AND Communication.list_id = 132

WHERE

# Where StartDate and EndDate
CASE
	WHEN @StartDate IS NOT NULL AND @EndDate IS NOT NULL
	THEN encounters.service_date BETWEEN @StartDate AND @EndDate
    ELSE 1=1
END

AND

# Where MedicationCode
CASE
	WHEN @MedicationCode IS NOT NULL
	THEN FIND_IN_SET(patient_medications.RXCUI, @MedicationCode)
	ELSE 1=1
END

AND

# Where Active Problems
CASE
	WHEN @ProblemCode IS NOT NULL
	THEN FIND_IN_SET(patient_active_problems.code, @ProblemCode)
    ELSE 1=1
END

AND

# Where Race
CASE
    WHEN @RaceCode IS NOT NULL
	THEN patient.race = @RaceCode
    ELSE 1=1
END

AND

# Where Ethnicity
CASE
    WHEN @EthnicityCode IS NOT NULL
	THEN patient.ethnicity = @EthnicityCode
    ELSE 1=1
END

AND

# Where Communication
CASE
    WHEN @CommunicationCode IS NOT NULL
	THEN patient.phone_publicity = @CommunicationCode
    ELSE 1=1
END

AND

# Where Medication Allergies
CASE
	WHEN @MedicationAllergyCode IS NOT NULL
	THEN FIND_IN_SET(patient_allergies.allergy_code, @MedicationAllergyCode)
    ELSE 1=1
END

AND

# Where Provider
CASE
	WHEN @Provider IS NOT NULL
	THEN FIND_IN_SET(encounters.provider_uid, @Provider)
	ELSE 1=1
END

AND

# Where AgeFrom and AgeTo
CASE
    WHEN @AgeFrom IS NOT NULL AND @AgeTo IS NOT NULL
    THEN TIMESTAMPDIFF(YEAR, patient.DOB, CURDATE()) BETWEEN @AgeFrom AND @AgeTo
    ELSE 1=1
END

GROUP BY patient.pid;
