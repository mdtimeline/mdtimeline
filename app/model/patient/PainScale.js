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

Ext.define('App.model.patient.PainScale', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_pain_scales'
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
			name: 'anatomical_region_code',
			type: 'string',
			len: 40
		},
		{
			name: 'anatomical_region_code_text',
			type: 'string',
			len: 300
		},
		{
			name: 'anatomical_region_code_type',
			type: 'string',
			len: 15
		},
		{
			name: 'pain_scale',
			type: 'int'
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
			name: 'service_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'PainScale.getPainsScales',
			create: 'PainScale.addPainScale',
			update: 'PainScale.addPainScale',
			destroy: 'PainScale.destroyPainScale'
		},
		reader: {
			root: 'data'
		},
		writer: {
			writeAllFields: true
		},
		remoteGroup: false
	}
});