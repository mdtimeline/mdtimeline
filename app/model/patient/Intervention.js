/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-01-20 21:16:17
 */

Ext.define('App.model.patient.Intervention', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_interventions'
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
			name: 'intervention_type',
			type: 'string',
			index: true,
			len: 25
		},
		{
			name: 'code',
			type: 'string',
			len: 80
		},
		{
			name: 'code_text',
			type: 'string',
			len: 300
		},
		{
			name: 'code_type',
			type: 'string',
			len: 20
		},
		{
			name: 'notes',
			type: 'string',
			dataTYpe: 'text'
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
			read: 'Interventions.getPatientInterventions',
			create: 'Interventions.addPatientIntervention',
			update: 'Interventions.updatePatientIntervention',
			destroy: 'Interventions.destroyPatientIntervention'
		},
		reader: {
			root: 'data'
		}
	}
});
