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

Ext.define('App.model.patient.Notes', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_notes'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'eid',
			type: 'int',
			index: true
		},
		{
			name: 'pid',
			type: 'int',
			index: true
		},
		{
			name: 'uid',
			type: 'int',
			index: true
		},
		{
			name: 'date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s',
			index: true
		},
		{
			name: 'body',
			type: 'string'
		},
		{
			name: 'type',
			type: 'string'
		},
		{
			name: 'list_key',
			type: 'string'
		},
		{
			name: 'user_name',
			type: 'string',
			store: false
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
        }
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Notes.getNotes',
			create: 'Notes.addNote',
			update: 'Notes.updateNote'
		}
	}
});

