/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-10-25 14:22:10
 */

Ext.define('App.model.patient.PatientAccount',{
    extend: 'Ext.data.Model',
    requires: [
    ],
    table: {
        name: 'patient_account'
    },
    fields: [
        {
            name: 'pid',
            type: 'int',
            index: true
        },
        {
            name: 'facility_id',
            type: 'int',
            index: true
        },
	    {
		    name: 'account_no',
		    type: 'string',
		    index: true,
		    len: 80
	    },
	    {
		    name: 'account_no_alt',
		    type: 'string',
		    index: true,
		    len: 80
	    }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'Patient.getPatientAccounts',
            create: 'Patient.addPatientAccount',
            update: 'Patient.updatePatientAccount',
            destroy: 'Patient.destroyPatientAccount'
        }
    }
});
