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

Ext.define('App.model.patient.encounter.Addendum', {
	extend: 'Ext.data.Model',
	table: {
		name: 'encounter_addenda'
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
			name: 'source',
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
		},
		{
			name: 'created_by_fname',
			type: 'string',
			store: false
		},
		{
			name: 'created_by_mname',
			type: 'string',
			store: false
		},
		{
			name: 'created_by_lname',
			type: 'string',
			store: false
		},
		{
			name: 'created_by',
			type: 'string',
			convert: function (v,r){
				return Ext.String.format('{0}, {1} {2}', r.get('created_by_lname'), r.get('created_by_fname'), r.get('created_by_mname'))
			},
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'EncounterAddenda.getEncounterAddenda',
			create: 'EncounterAddenda.addEncounterAddendum',
			update: 'EncounterAddenda.updateEncounterAddendum',
			destroy: 'EncounterAddenda.destroyEncounterAddendum'
		}
	}
});
