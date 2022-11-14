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

Ext.define('App.model.administration.ICD10', {
	extend: 'Ext.data.Model',
	table: {
		name: 'icd10_dx_order_code'
	},
	fields: [
		{
			name: 'dx_id',
			type: 'int'
		},
		{
			name: 'dx_code',
			type: 'string',
			len: 7
		},
		{
			name: 'formatted_dx_code',
			type: 'string',
			len: 10
		},
		{
			name: 'valid_for_coding',
			type: 'string',
			len: 1
		},
		{
			name: 'short_desc',
			type: 'string',
			dataType: 'text',
			len: 300
		},
		{
			name: 'long_desc',
			type: 'string',
			dataType: 'text',
			len: 300
		},
		{
			name: 'active',
			type: 'bool'
		},
		{
			name: 'revision',
			type: 'int'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'ICD10.getICD10s',
			create: 'ICD10.addICD10',
			update: 'ICD10.updateICD10',
			destroy: 'ICD10.deleteICD10'
		},
		reader: {
			root: 'data'
		}
	}
});
