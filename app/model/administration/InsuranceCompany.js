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

Ext.define('App.model.administration.InsuranceCompany', {
	extend: 'Ext.data.Model',
	table: {
		name: 'insurance_companies',
		comment: 'Insurance Companies'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'code',
			type: 'string',
			len: 80,
			index: true,
			comment: 'use to reference the insurance to another software'
		},
		{
			name: 'ins_name',
			type: 'string',
			len: 120
		},
		{
			name: 'ins_contact',
			type: 'string',
			len: 120
		},
		{
			name: 'ins_address1',
			type: 'string',
			len: 100
		},
		{
			name: 'ins_address2',
			type: 'string',
			len: 100
		},
		{
			name: 'ins_city',
			type: 'string',
			len: 80
		},
		{
			name: 'ins_state',
			type: 'string',
			len: 80
		},
		{
			name: 'ins_zip_code',
			type: 'string',
			len: 15
		},
		{
			name: 'ins_country',
			type: 'string',
			len: 80
		},
		{
			name: 'ins_phone1',
			type: 'string',
			len: 20
		},
		{
			name: 'ins_phone2',
			type: 'string',
			len: 20
		},
		{
			name: 'ins_fax',
			type: 'string',
			len: 20
		},
		{
			name: 'ins_active',
			type: 'bool'
		},
		{
			name: 'ins_dx_type',
			type: 'string',
			len: 5
		},
		{
			name: 'address_full',
			type: 'string',
			convert: function(v, record){
				return record.data.address1 + ' ' +  record.data.address2 + ' ' +  record.data.city + ' ' +  record.data.state + ', ' +  record.data.zip_code;
			},
			store: false

		},
		{
			name: 'combo_text',
			type: 'string',
			convert: function(v, record){
				return record.data.id + ': ' + (record.data.name ? record.data.name : ' * ' ) + ' ' + (!record.data.active ? ('(' +  _('inactive') + ')') : '') ;
			},
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Insurance.getInsuranceCompanies',
			create: 'Insurance.addInsuranceCompany',
			update: 'Insurance.updateInsuranceCompany'
		},
		reader: {
			root: 'data'
		}
	}
});