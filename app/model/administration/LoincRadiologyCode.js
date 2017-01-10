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

Ext.define('App.model.administration.LoincRadiologyCode', {
	extend: 'Ext.data.Model',
	table: {
		name: 'codes_loinc_radiology'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'LoincNumber',
			type: 'string',
			len: 15
		},
		{
			name: 'LongCommonName',
			type: 'string',
			len: 250
		},
		{
			name: 'PartNumber',
			type: 'string',
			len: 80
		},
		{
			name: 'PartTypeName',
			type: 'string',
			len: 180
		},
		{
			name: 'PartName',
			type: 'string',
			len: 180
		},
		{
			name: 'PartSequenceOrder',
			type: 'string',
			len: 15
		},
		{
			name: 'RID',
			type: 'string',
			len: 15
		},
		{
			name: 'PreferredName',
			type: 'string',
			len: 180
		},
		{
			name: 'RPID',
			type: 'string',
			len: 15
		},
		{
			name: 'LongName',
			type: 'string',
			len: 250
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'LoincCodes.getLoincRadiologyCodes',
			create: 'LoincCodes.addLoincRadiologyCode',
			update: 'LoincCodes.updateLoincRadiologyCode',
			destroy: 'LoincCodes.deleteLoincRadiologyCode'
		},
		reader: {
			root: 'data'
		}
	}
});