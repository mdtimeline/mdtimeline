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

Ext.define('App.model.patient.SOAP', {
	extend: 'Ext.data.Model',
	requires: [
		'App.model.patient.Encounter',
		'App.model.patient.EncounterDx',
		'App.model.patient.encounter.Procedures'
	],
	table: {
		name: 'encounter_soap',
		comment: 'SOAP Data'
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
			type: 'int'
		},
		{
			name: 'date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'chief_complaint',
			type: 'string',
			store: false
		},
		{
			name: 'subjective',
			type: 'string',
			dataType: 'mediumtext'
		},
		{
			name: 'objective',
			type: 'string',
			dataType: 'mediumtext'
		},
		{
			name: 'assessment',
			type: 'string',
			dataType: 'mediumtext'
		},
		{
			name: 'plan',
			type: 'string',
			dataType: 'mediumtext'
		},
		{
			name: 'instructions',
			type: 'string',
			dataType: 'mediumtext'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			update: 'Encounter.updateSoap'
		},
		writer: {
			writeAllFields: true
		}
	},
	hasMany: [
		{
			model: 'App.model.patient.EncounterDx',
			name: 'dxCodes',
			primaryKey: 'eid',
			foreignKey: 'eid'
		},
		{
			model: 'App.model.patient.encounter.Procedures',
			name: 'procedures',
			primaryKey: 'eid',
			foreignKey: 'eid'
		}
	],
	belongsTo: {
		model: 'App.model.patient.Encounter',
		foreignKey: 'eid'
	}

});