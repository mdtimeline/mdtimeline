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

Ext.define('App.model.patient.EncounterDx', {
	extend: 'Ext.data.Model',
	table: {
		name: 'encounter_dx',
		comment: 'Encounter Diagnosis'
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
			name: 'dx_group',
			type: 'int',
			index: true
		},
		{
			name: 'dx_order',
			type: 'int',
			index: true
		},
		{
			name: 'dx_type',
			type: 'string',
			index: true,
			len: 5
		},
		{
			name: 'code',
			type: 'string',
			len: 25
		},
		{
			name: 'code_type',
			type: 'string',
			len: 25
		},
		{
			name: 'code_text',
			type: 'string',
			len: 300
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Encounter.getEncounterDxs',
			create: 'Encounter.createEncounterDx',
			update: 'Encounter.updateEncounterDx',
			destroy: 'Encounter.destroyEncounterDx'
		}
	},
	belongsTo: {
		model: 'App.model.patient.SOAP',
		foreignKey: 'eid'
	}
});