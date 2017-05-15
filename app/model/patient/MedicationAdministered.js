/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.model.patient.MedicationAdministered', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_medications_administered'
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
			name: 'uid',
			type: 'int',
			index: true
		},
		{
			name: 'order_id',
			type: 'int',
			index: true
		},
		{
			name: 'rxcui',
			type: 'string',
			len: 40,
			index: true
		},
		{
			name: 'description',
			type: 'string',
			len: 180
		},
		{
			name: 'instructions',
			type: 'string',
			len: 180
		},
		{
			name: 'administered',
			type: 'bool'
		},
		{
			name: 'administered_uid',
			type: 'int',
			index: true
		},
		{
			name: 'administered_amount',
			type: 'string',
			len: 40
		},
		{
			name: 'administered_units',
			type: 'string',
			len: 40
		},
		{
			name: 'administered_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'administered_by',
			type: 'string',
			store: false,
			convert: function(v, record){
				return (record.data.administered_title + ' ' +
					record.data.administered_fname + ' ' +
					(record.data.administered_mname || '') + ' ' +
					record.data.administered_lname).trim();
			}
		},
		{
			name: 'administered_title',
			type: 'string',
			store: false
		},
		{
			name: 'administered_fname',
			type: 'string',
			store: false
		},
		{
			name: 'administered_mname',
			type: 'string',
			store: false
		},
		{
			name: 'administered_lname',
			type: 'string',
			store: false
		},
		{
			name: 'administration_site',
			type: 'string',
			len: 40
		},
		{
			name: 'adverse_reaction_text',
			type: 'string',
			len: 120
		},
		{
			name: 'adverse_reaction_code',
			type: 'string',
			len: 40
		},
		{
			name: 'adverse_reaction_code_type',
			type: 'string',
			len: 15
		},
		{
			name: 'manufacturer',
			type: 'string',
			len: 180
		},
		{
			name: 'exp_date',
			type: 'date',
			dataType: 'date',
			dateFormat: 'Y-m-d'
		},
		{
			name: 'lot_number',
			type: 'string',
			len: 60
		},
		{
			name: 'note',
			type: 'string',
			len: 600
		},
		{
			name: 'created_uid',
			type: 'int'
		},
		{
			name: 'updated_uid',
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
			read: 'Medications.getPatientMedicationsAdministered',
			create: 'Medications.addPatientMedicationAdministered',
			update: 'Medications.updatePatientMedicationAdministered',
			destroy: 'Medications.destroyPatientMedicationAdministered'
		},
        writer: {
            writeAllFields: true
        },
		remoteGroup: false
	}
});

