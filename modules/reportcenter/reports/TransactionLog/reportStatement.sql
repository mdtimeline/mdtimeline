SET @StartDate = :begin_date;
SET @EndDate = :end_date;
SET @StartTime = :begin_time;
SET @EndTime = :end_time;

SET @TableName = :table_name;
SET @EventType = :event_type;

SELECT audit_transaction_log.*,
        CONCAT(patient.title, ' ', patient.fname, ' ',patient.mname, ' ',patient.lname) as PatientName,
        patient.pubpid as RecordNumber,
        CONCAT(users.title, ' ', users.fname, ' ',users.mname, ' ',users.lname) as UserName,
        facility.legal_name,
        IF(audit_transaction_log.checksum = sha1(concat(audit_transaction_log.date,audit_transaction_log.pid,audit_transaction_log.eid,audit_transaction_log.uid,audit_transaction_log.fid,audit_transaction_log.event,audit_transaction_log.table_name,audit_transaction_log.sql_string,audit_transaction_log.data,audit_transaction_log.ip)), 'Yes', 'No') as valid
FROM audit_transaction_log
LEFT JOIN patient ON patient.pid = audit_transaction_log.pid
LEFT JOIN encounters ON encounters.eid = audit_transaction_log.eid
LEFT JOIN users ON users.id = audit_transaction_log.uid
LEFT JOIN facility ON facility.id = audit_transaction_log.fid

WHERE

# Start and End date fileter
CASE
 WHEN @StartDate IS NOT NULL AND @EndDate IS NOT NULL
 THEN date BETWEEN CONCAT(@StartDate,' ',@StartTime) AND CONCAT(@EndDate,' ',@EndTime)
 WHEN @StartDate IS NOT NULL AND @EndDate IS NULL
 THEN date BETWEEN CONCAT(@StartDate,' ',@StartTime) AND NOW()
 ELSE 1=1
END

AND

# Table name filter
CASE
  WHEN @TableName IS NOT NULL
  THEN table_name = @TableName
  ELSE 1=1
END

AND

# Event type filter
CASE
  WHEN @EventType IS NOT NULL
  THEN event = @EventType
  ELSE 1=1
END

:ux-sort
:ux-pagination;
