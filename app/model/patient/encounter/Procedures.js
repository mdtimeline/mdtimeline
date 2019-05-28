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

Ext.define('App.model.patient.encounter.Procedures', {
	extend: 'Ext.data.Model',
	table: {
		name: 'encounter_procedures',
		comment: 'Patient Encounter Procedures'
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
			name: 'eid',
			type: 'int'
		},
		{
			name: 'performer_id',
			type: 'int'
		},
		{
			name: 'procedure_date',
			type: 'date',
			dataType: 'date',
			dateFormat: 'Y-m-d'
		},
		{
			name: 'code',
			type: 'string',
			len: 40
		},
		{
			name: 'code_text',
			type: 'string',
			len: 300
		},
		{
			name: 'code_type',
			type: 'string',
			len: 15
		},
		{
			name: 'status_code',
			type: 'string',
			len: 40
		},
		{
			name: 'status_code_text',
			type: 'string',
			len: 300
		},
		{
			name: 'status_code_type',
			type: 'string',
			len: 15
		},
		{
			name: 'target_site_code',
			type: 'string',
			len: 40
		},
		{
			name: 'target_site_code_text',
			type: 'string',
			len: 300
		},
		{
			name: 'target_site_code_type',
			type: 'string',
			len: 15
		},
		{
			name: 'encounter_dx_id',
			type: 'int'
		},
		{
			name: 'observation',
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
			read: 'Procedures.loadProcedures',
			create: 'Procedures.saveProcedure',
			update: 'Procedures.saveProcedure',
			destroy: 'Procedures.destroyProcedure'
		}
	}
});
