/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-10-25 14:22:10
 */

Ext.define('App.model.patient.PatientAlternateRecordNo',{
    extend: 'Ext.data.Model',
    requires: [
    ],
    table: {
        name: 'patient_alternate_record_no'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'pid',
            type: 'int',
            index: true
        },
        {
            name: 'pubpid',
            type: 'int',
            index: true
        },
        {
            name: 'pubaccount',
            type: 'int',
            index: true
        },
        {
            name: 'facility_id',
            type: 'string',
            len: 10,
            index: true
        },
        {
            name: 'fname',
            type: 'string',
            len: 60
        },
        {
            name: 'mname',
            type: 'string',
            len: 40
        },
        {
            name: 'lname',
            type: 'string',
            len: 60
        },
        {
            name: 'sex',
            type: 'string',
            len: 10
        },
        {
            name: 'DOB',
            type: 'date',
            comment: 'day of birth',
            dateFormat: 'Y-m-d H:i:s',
            defaultValue: '0000-00-00 00:00:00'
        }
    ]
});
