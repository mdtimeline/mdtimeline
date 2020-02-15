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

Ext.define('App.model.patient.NursesNote', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_nurses_notes'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'code',
			type: 'string',
			len: 120,
			index: true
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
			name: 'note',
			type: 'string',
			dataType: 'TEXT'
		},
		{
			name: 'in_error',
			type: 'bool'
		},
		{
			name: 'create_uid',
			type: 'int',
			comment: 'create user ID'
		},
		{
			name: 'update_uid',
			type: 'int',
			comment: 'update user ID'
		},
		{
			name: 'create_date',
			type: 'date',
			comment: 'create date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'update_date',
			type: 'date',
			comment: 'last update date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'nurse_fname',
			type: 'string',
			store: false
		},
		{
			name: 'nurse_mname',
			type: 'string',
			store: false
		},
		{
			name: 'nurse_lname',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'NursesNotes.getNursesNotes',
			create: 'NursesNotes.addNursesNote',
			update: 'NursesNotes.updateNursesNote',
			destroy: 'NursesNotes.destroyNursesNote'
		},
		reader: {
			root: 'data'
		},
		remoteGroup: false
	}
});

