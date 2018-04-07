/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-01-28 12:02:41
 */

Ext.define('App.model.patient.InsuranceCover',{
    extend: 'Ext.data.Model',
    table: {
        name: 'patient_insurance_covers',
        comment: 'Patient Insurance_covers'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'patient_insurance_id',
            type: 'int'
        },
        {
            name: 'department_code',
            type: 'string',
            len: 5
        },
        {
            name: 'cover',
            type: 'string',
            len: 50
        },
        {
            name: 'create_uid',
            type: 'int'
        },
        {
            name: 'update_uid',
            type: 'int'
        },
        {
            name: 'create_date',
            type: 'date',
            dateFormat: 'Y-m-d H:i:s'
        },
        {
            name: 'update_date',
            type: 'date',
            dateFormat: 'Y-m-d H:i:s'
        },
	    {
		    name: 'department_title',
		    type: 'string',
		    store: false
	    }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'Insurance.getInsuranceCovers',
            create: 'Insurance.addInsuranceCover',
            update: 'Insurance.updateInsuranceCover',
            destroy: 'Insurance.destroyInsuranceCover'
        }
    },
    associations: [
        {
            type: 'belongsTo',
            model: 'App.model.patient.Patient',
            associationKey: 'pid',
            foreignKey: 'pid'
        }
    ]
});
