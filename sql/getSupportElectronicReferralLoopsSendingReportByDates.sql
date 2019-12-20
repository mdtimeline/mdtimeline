DROP PROCEDURE IF EXISTS `getSupportElectronicReferralLoopsSendingReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSupportElectronicReferralLoopsSendingReportByDates`(IN provider_id INT, IN start_date DATE, IN end_date DATE, IN stages VARCHAR(40))
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
        r.inboud_toc,
        r.outboud_toc,
        r.inbound_referral,
        r.outbound_referral,
        r.number_of_previous_encounters,
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
             IF(a.id IS NOT NULL AND a.event = 'INBOUND_TOC', '1', '0') AS inboud_toc,
             IF(a.id IS NOT NULL AND a.event = 'OUTBOUND_TOC', '1', '0') AS outboud_toc,
             IF(e.referring_physician IS NOT NULL, 1, 0) AS inbound_referral,
             IF(pr.id IS NOT NULL, 1, 0) AS outbound_referral,
             (SELECT count(prev_e.eid) FROM encounters as prev_e WHERE prev_e.pid = e.pid AND prev_e.service_date < e.service_date) AS number_of_previous_encounters
         FROM
             encounters AS e
                 INNER JOIN patient_referrals AS pr on e.eid = pr.eid
                 LEFT JOIN audit_log AS a ON a.pid = e.pid
                     AND a.eid = e.eid
                     AND a.event IN ('INBOUND_TOC' OR 'OUTBOUND_TOC')
         WHERE
                 e.provider_uid = provider_id
           AND e.service_date IS NOT NULL
           AND e.service_date BETWEEN start_date AND end_date

         ) r ORDER BY r.service_date;

    SET @stage3 = (SELECT FIND_IN_SET('3',stages));

    IF @stage3 > 0 THEN

        CREATE TEMPORARY TABLE g2_report_denominator_ds
        SELECT 1 as `value`, pid, outboud_toc, eid FROM g2_report_ds WHERE outbound_referral = '1' GROUP BY eid;

        CREATE TEMPORARY TABLE g2_report_numerator_ds
        SELECT 1 as `value`, pid, eid FROM g2_report_ds WHERE outbound_referral = '1' AND outboud_toc = '1' group by eid;

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
