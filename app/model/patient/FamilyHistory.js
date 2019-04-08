/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2017-11-17 11:57:40
 */

Ext.define('App.model.patient.FamilyHistory',{
    extend: 'Ext.data.Model',
    table: {
        name: 'patient_family_history'
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
            name: 'eid',
            type: 'int',
            index: true
        },
        {
            name: 'condition',
            type: 'string',
            len: 60
        },
        {
            name: 'condition_code',
            type: 'string',
            len: 60
        },
        {
            name: 'condition_code_type',
            type: 'string',
            len: 60
        },
        {
            name: 'relation',
            type: 'string',
            len: 60
        },
        {
            name: 'relation_code',
            type: 'string',
            len: 60
        },
        {
            name: 'relation_code_type',
            type: 'string',
            len: 60
        },
        {
            name: 'notes',
            type: 'string',
            dataType: 'TEXT'
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
            read: 'FamilyHistory.getFamilyHistory',
            create: 'FamilyHistory.addFamilyHistory',
            update: 'FamilyHistory.updateFamilyHistory'
        },
        remoteGroup: false
    },
    belongsTo: {
        model: 'App.model.patient.Encounter',
        foreignKey: 'eid'
    },
    parsed_data: {
        primaryKey: 'id',
        fields: [
            'id',
            'pid',
            'eid',
            'condition',
            'condition_code',
            'condition_code_type',
            'relation',
            'relation_code',
            'relation_code_type',
            'notes',
            'create_uid',
            'update_uid',
            'create_date',
            'update_date'
        ],
        encryptedFields: false,
        phantomFields: false,
        arrayFields: false,
        fieldsProperties: {
            id: {
                name: 'id',
                type: 'int'
            },
            pid: {
                name: 'pid',
                type: 'int',
                index: true
            },
            eid: {
                name: 'eid',
                type: 'int',
                index: true
            },
            condition: {
                name: 'condition',
                type: 'string',
                len: 60
            },
            condition_code: {
                name: 'condition_code',
                type: 'string',
                len: 60
            },
            condition_code_type: {
                name: 'condition_code_type',
                type: 'string',
                len: 60
            },
            relation: {
                name: 'relation',
                type: 'string',
                len: 60
            },
            relation_code: {
                name: 'relation_code',
                type: 'string',
                len: 60
            },
            relation_code_type: {
                name: 'relation_code_type',
                type: 'string',
                len: 60
            },
            notes: {
                name: 'notes',
                type: 'string',
                dataType: 'TEXT'
            },
            create_uid: {
                name: 'create_uid',
                type: 'int'
            },
            update_uid: {
                name: 'update_uid',
                type: 'int'
            },
            create_date: {
                name: 'create_date',
                type: 'date',
                dateFormat: 'Y-m-d H:i:s'
            },
            update_date: {
                name: 'update_date',
                type: 'date',
                dateFormat: 'Y-m-d H:i:s'
            }
        }
    }
});
