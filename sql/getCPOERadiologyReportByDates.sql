DROP PROCEDURE IF EXISTS `getCPOERadiologyReportByDates`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCPOERadiologyReportByDates`(IN provider_id INT, IN start_date DATE, IN end_date DATE, IN stages VARCHAR(40))
BEGIN
    DROP TABLE IF EXISTS g2_report_ds;
    DROP TABLE IF EXISTS g2_report_ds_first_encounters;
    DROP TABLE IF EXISTS g2_report_denominator_ds;
    DROP TABLE IF EXISTS g2_report_numerator_ds;

    SET @provider = (SELECT CONCAT(title, ' ', lname, ', ', fname, ' ', mname, ' (NPI:', npi, ')') FROM users WHERE id = provider_id);

    CREATE TEMPORARY TABLE g2_report_ds
    SELECT
        o.id,
        o.pid,
        IF(o.priority IS NOT NULL AND o.priority != '', '1','0') AS is_cpoe
    FROM
        (SELECT ord.id, ord.pid, ord.priority FROM patient_orders as ord
             INNER JOIN encounters as e ON e.eid = ord.eid
         WHERE e.provider_uid = provider_id AND ord.order_type = 'rad' AND ord.date_ordered BETWEEN start_date AND end_date
         GROUP BY ord.id) o;

    SET @stage3 = (SELECT FIND_IN_SET('3',stages));

    IF @stage3 > 0 THEN

        CREATE TEMPORARY TABLE g2_report_denominator_ds
        SELECT 1 as `value`, pid FROM g2_report_ds GROUP BY id;

        CREATE TEMPORARY TABLE g2_report_numerator_ds
        SELECT 1 as `value`, pid FROM g2_report_ds WHERE is_cpoe = '1' GROUP BY id;

        SET @denominator = (SELECT sum(`value`) FROM g2_report_denominator_ds);
        SET @numerator = (SELECT sum(`value`) FROM g2_report_numerator_ds);
        SET @denominator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_denominator_ds GROUP BY pid) as d);
        SET @numerator_pids = (SELECT group_concat(pid) FROM (SELECT pid FROM g2_report_numerator_ds GROUP BY pid) as n);

        SELECT
            @provider as provider,
            'Stage 3 Measure' as title,
            @denominator as denominator,
            @denominator_pids as denominator_pids,
            @numerator as numerator,
            @numerator_pids as numerator_pids;

    END IF ;

END$$

DELIMITER ;
