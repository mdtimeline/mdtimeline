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

Ext.define('App.model.patient.EncounterService', {
	extend: 'Ext.data.Model',
	table: {
		name: 'encounter_services'
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
            name: 'module_table',
            type: 'string',
            lenght: 100
        },
        {
            name: 'module_reference_id',
            type: 'int',
            lenght: 11
        },
		{
			name: 'reference_type',
			type: 'string',
			len: 40,
			index: true
		},
		{
			name: 'reference_id',
			type: 'int',
			index: true
		},
		{
			name: 'billing_reference',
			type: 'string',
			len: 20,
			index: true
		},
		{
			name: 'code',
			type: 'string',
			len: 40,
			index: true
		},
		{
			name: 'code_type',
			type: 'string',
			len: 40
		},
		{
			name: 'code_text',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'units',
			type: 'int',
			len: 5
		},
		{
			name: 'modifiers',
			type: 'array'
		},
		{
			name: 'dx_group_id',
			type: 'int'
		},
		{
			name: 'dx_pointers',
			type: 'array'
		},
		{
			name: 'status',
			type: 'string',
			len: 20
		},
        {
            name: 'financial_class',
            type: 'string',
            len: 4
        },
        {
            name: 'financial_name',
            type: 'string',
            len: 4,
            store: false
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
			name: 'date_create',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'date_update',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
        {
            name: 'billing_transfer_date',
            type: 'date',
            dateFormat: 'Y-m-d H:i:s',
            comment: 'When a billing gets transferred to a billing software or to a medical biller person.'
        }
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Services.getEncounterServices',
			create: 'Services.addEncounterService',
			update: 'Services.updateEncounterService',
			destroy: 'Services.removeEncounterService'
		},
		writer: {
			writeAllFields: true
		}
	}
});
