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
            name: 'pid',
            type: 'int'
        },
        {
            name: 'insurance_id',
            type: 'int'
        },
        {
            name: 'department_id',
            type: 'int'
        },
        {
            name: 'department_title',
            type: 'string',
            store: false
        },
        {
            name: 'patient_insurance_cover',
            type: 'string'
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
