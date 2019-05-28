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

Ext.define('App.model.patient.ProcedureHistory', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_procedure_history'
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
			name: 'performed_date',
			type: 'date',
			dataType: 'date',
			dateFormat: 'Y-m-d'
		},
		{
			name: 'procedure',
			type: 'string',
			len: 300
		},
		{
			name: 'procedure_code',
			type: 'string',
			len: 40
		},
		{
			name: 'procedure_code_type',
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
			name: 'performer',
			type: 'string',
			len: 300
		},
		{
			name: 'performer_id',
			type: 'int',
			index: true
		},
		{
			name: 'service_location',
			type: 'string',
			len: 300
		},
		{
			name: 'service_location_id',
			type: 'int',
			index: true
		},
		{
			name: 'notes',
			type: 'string',
			dataType: 'text'
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
			read: 'ProcedureHistory.getProcedureHistories',
			create: 'ProcedureHistory.addProcedureHistory',
			update: 'ProcedureHistory.updateProcedureHistory',
			destroy: 'ProcedureHistory.destroyProcedureHistory'
		}
	}
});
