/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-01-20 21:16:17
 */

Ext.define('App.model.patient.HealthConcern', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_health_concerns'
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
			name: 'health_concern_type',
			type: 'string',
			index: true,
			len: 25
		},
		{
			name: 'description',
			type: 'string',
			len: 300
		},
		{
			name: 'instructions',
			type: 'string',
			dataTYpe: 'text'
		},
		{
			name: 'active_from',
			type: 'date',
			dateFormat: 'Y-m-d'
		},
		{
			name: 'active_to',
			type: 'date',
			dateFormat: 'Y-m-d'
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
			read: 'HealthConcerns.getPatientHealthConcerns',
			create: 'HealthConcerns.addPatientHealthConcern',
			update: 'HealthConcerns.updatePatientHealthConcern',
			destroy: 'HealthConcerns.destroyPatientHealthConcern'
		},
		reader: {
			root: 'data'
		}
	}
});
