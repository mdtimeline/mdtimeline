DROP PROCEDURE IF EXISTS `getMedicationClinicalInformationReconciliationReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMedicationClinicalInformationReconciliationReportByDates`(IN provider_id INT, IN start_date DATE, IN end_date DATE, IN stages VARCHAR(40))
BEGIN
    DROP TABLE IF EXISTS g2_report_ds;
    DROP TABLE IF EXISTS g2_report_ds_first_encounters;
    DROP TABLE IF EXISTS g2_report_denominator_ds;
    DROP TABLE IF EXISTS g2_report_numerator_ds;

    SET @provider = (SELECT CONCAT(title, ' ', lname, ', ', fname, ' ', mname, ' (NPI:', npi, ')') FROM users WHERE id = provider_id);

    CREATE TEMPORARY TABLE g2_report_ds
    SELECT
        r.eid,
        r.pid,
        r.provider,
        r.facility,
        r.patient_reconcile,
        r.inbound_referral,
        r.ccda_available,
        r.ccda_requested,
        r.number_of_previous_encounters,
        r.medications_reconcile,
        r.problems_reconcile,
        r.allergies_reconcile,
        r.service_date,
        r.event_id,
        r.event,
        r.event_date
    FROM
        (SELECT
             e.eid,
             e.provider_uid as provider,
             e.facility,
             e.pid,
             e.service_date,
             a.id AS event_id,
             a.event,
             a.event_date,
             IF(e.referring_physician IS NOT NULL, 1, 0) AS inbound_referral,
             IF(e.summary_care_provided IS NULL, 0, e.summary_care_provided) AS ccda_available,
             IF(e.summary_care_requested IS NULL, 0, e.summary_care_requested) AS ccda_requested,
             IF(a.id IS NOT NULL AND a.event = 'RECONCILE', '1', '0') AS patient_reconcile,
             IF(a.foreign_table = 'patient_medications', 1, 0) AS medications_reconcile,
             IF(a.foreign_table = 'patient_active_problems', 1, 0) AS problems_reconcile,
             IF(a.foreign_table = 'patient_allergies', 1, 0) AS allergies_reconcile,
             (SELECT count(prev_e.eid) FROM encounters as prev_e WHERE prev_e.provider_uid = e.provider_uid AND prev_e.pid = e.pid AND prev_e.service_date < e.service_date) AS number_of_previous_encounters
         FROM
             encounters AS e
                 LEFT JOIN audit_log AS a ON a.pid = e.pid
                     AND a.eid = e.eid
                     AND a.event IN ('INBOUND_TOC' OR 'CCDA_IMPORT', 'RECONCILE')
                     AND a.event_date BETWEEN start_date AND end_date
         WHERE
                 e.provider_uid = provider_id
           AND e.service_date IS NOT NULL
           AND e.service_date BETWEEN start_date AND end_date

         ) r ORDER BY r.service_date;

    SET @stage3 = (SELECT FIND_IN_SET('3',stages));

    IF @stage3 > 0 THEN

        CREATE TEMPORARY TABLE g2_report_denominator_ds
        SELECT 1 as `value`, pid, eid, inbound_referral, number_of_previous_encounters,
               max(medications_reconcile) as medications_reconcile,
               max(problems_reconcile) as problems_reconcile,
               max(allergies_reconcile) as allergies_reconcile
        FROM g2_report_ds
        WHERE  inbound_referral = '1' OR number_of_previous_encounters = '0'
        GROUP BY eid;

        CREATE TEMPORARY TABLE g2_report_numerator_ds
        SELECT 1 as `value`, pid
        FROM g2_report_denominator_ds
        WHERE (inbound_referral = '1' OR number_of_previous_encounters = '0')
             AND medications_reconcile = '1'
             AND problems_reconcile = '1'
             AND allergies_reconcile = '1'
        GROUP BY eid;

        SET @denominator = (SELECT sum(`value`) FROM g2_report_denominator_ds);
        SET @numerator = (SELECT sum(`value`) FROM g2_report_numerator_ds);

        SELECT
            @provider as provider,
            'Stage 3 Measure' as title,
            @denominator as denominator,
            (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_denominator_ds GROUP BY pid) as d) as denominator_pids,
            @numerator as numerator,
            (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_numerator_ds GROUP BY pid) as n) as numerator_pids;

    END IF ;

END$$
DELIMITER ;
