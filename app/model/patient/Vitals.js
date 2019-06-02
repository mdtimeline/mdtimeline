// Created dynamically by Matcha::connect
// Create date: 2013-07-28 18:48:17

Ext.define('App.model.patient.Vitals', {
	extend: 'Ext.data.Model',
	table: {
		name: 'encounter_vitals'
	},
	fields: [
		{
			name: 'id',
			type: 'int',
			comment: 'Vital ID'
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
			name: 'uid',
			type: 'int'
		},
		{
			name: 'auth_uid',
			type: 'int'
		},
		{
			name: 'date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'weight_lbs',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'weight_kg',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'height_in',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'height_cm',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'bp_systolic',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'bp_diastolic',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'pulse',
			type: 'int',
			useNull: true,
			len: 10,
			convert: function(v){
				return v > 0 ? v : null;
			}
		},
		{
			name: 'respiration',
			type: 'int',
			useNull: true,
			len: 10,
			convert: function(v){
				return v > 0 ? v : null;
			}
		},
		{
			name: 'temp_f',
			type: 'float',
			useNull: true,
			len: 10,
			convert: function(v){
				return v > 0 ? v : null;
			}
		},
		{
			name: 'temp_c',
			type: 'float',
			useNull: true,
			len: 10,
			convert: function(v){
				return v > 0 ? v : null;
			}
		},
		{
			name: 'temp_location',
			type: 'string',
			len: 40
		},
		{
			name: 'oxygen_saturation',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'oxygen_inhaled_concentration',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'head_circumference_in',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'head_circumference_cm',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'waist_circumference_in',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'waist_circumference_cm',
			type: 'float',
			useNull: true,
			len: 10
		},
		{
			name: 'bmi',
			type: 'float',
			useNull: true,
			len: 10,
			convert: function(v){
				return v > 0 ? v : null;
			}
		},
		{
			name: 'bmi_status',
			type: 'string',
			useNull: true,
			len: 20
		},
		{
			name: 'other_notes',
			type: 'string',
			len: 600
		},
		{
			name: 'bp_systolic_normal',
			type: 'int',
			defaultValue: 120,
			store: false
		},
		{
			name: 'bp_diastolic_normal',
			type: 'int',
			defaultValue: 80,
			store: false
		},
		{
			name: 'group_field',
			type: 'string',
			store: false,
			convert: function(v, record){
				return record.get('eid') === app.patient.eid ? 'Encounter Vitals' : 'History';
			}
		},
		{
			name: 'administer_by',
			type: 'string',
			store: false
		},
		{
			name: 'authorized_by',
			type: 'string',
			store: false
		},
		{
			name: 'patient_record_number',
			type: 'string',
			store: false
		},
		{
			name: 'patient_fname',
			type: 'string',
			store: false
		},
		{
			name: 'patient_mname',
			type: 'string',
			store: false
		},
		{
			name: 'patient_lname',
			type: 'string',
			store: false
		},
		{
			name: 'administer_fname',
			type: 'string',
			store: false
		},
		{
			name: 'administer_mname',
			type: 'string',
			store: false
		},
		{
			name: 'administer_lname',
			type: 'string',
			store: false
		},
		{
			name: 'authorized_fname',
			type: 'string',
			store: false
		},
		{
			name: 'authorized_mname',
			type: 'string',
			store: false
		},
		{
			name: 'authorized_name',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Vitals.getVitals',
			create: 'Vitals.addVitals',
			update: 'Vitals.updateVitals',
			destroy: 'Vitals.removeVitals'
		},
		writer: {
			writeAllFields: true
		}
	}
});
