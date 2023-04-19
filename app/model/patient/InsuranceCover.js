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
            name: 'department_title',
            type: 'string',
            store: false
        },
        {
            name: 'cover_exception',
            type: 'string',
            store: false
        },
        {
            name: 'service_type_id',
            type: 'int'
        },
        {
            name: 'service_type_description',
            type: 'string',
            store: false
        },
        {
            name: 'isDollar',
            type: 'bool'
        },
        {
            name: 'copay',
            type: 'float'
        },
        {
            name: 'exception_isDollar',
            type: 'bool'
        },
        {
            name: 'exception_copay',
            type: 'float'
        },
        {
            name: 'external_id',
            type: 'string'
        },
        {
            name: 'global_id',
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
        },
        {
            name: 'validate_copay',
            type: 'bool',
            store: false
        },
        {
            name: 'validate_ecopay',
            type: 'bool',
            store: false
        },
        {
            name: 'validate_service_type_status',
            type: 'bool',
            store: false
        },
        {
            name: 'load_billing_cover',
            type: 'bool',
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
        },
        writer: {
            writeAllFields: true
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
