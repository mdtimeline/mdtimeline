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

Ext.define('App.model.administration.Facility', {
	extend: 'Ext.data.Model',
	table: {
		name: 'facility',
		comment: 'Facilities'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'code',
			type: 'string',
			len: 15
		},
		{
			name: 'name',
			type: 'string',
			len: 60,
			comment: 'Facility Name'
		},
		{
			name: 'legal_name',
			type: 'string',
			len: 60
		},
		{
			name: 'lname',
			type: 'string',
			len: 35
		},
		{
			name: 'fname',
			type: 'string',
			len: 35
		},
		{
			name: 'mname',
			type: 'string',
			len: 35
		},
		{
			name: 'facility_entity',
			type: 'string',
			len: 1
		},
		{
			name: 'attn',
			type: 'string',
			len: 80
		},
		{
			name: 'region',
			type: 'string',
			len: 45
		},
		{
			name: 'phone',
			type: 'string',
			len: 25
		},
		{
			name: 'fax',
			type: 'string',
			len: 25
		},
		{
			name: 'email',
			type: 'string',
			len: 180
		},
		{
			name: 'address',
			type: 'string',
			len: 55
		},
		{
			name: 'address_cont',
			type: 'string',
			len: 55
		},
		{
			name: 'city',
			type: 'string',
			len: 30
		},
		{
			name: 'state',
			type: 'string',
			len: 2
		},
		{
			name: 'postal_code',
			type: 'string',
			len: 15
		},
		{
			name: 'country_code',
			type: 'string',
			len: 3
		},
        {
            name: 'postal_address',
            type: 'string',
            len: 55
        },
        {
            name: 'postal_address_cont',
            type: 'string',
            len: 55
        },
        {
            name: 'postal_city',
            type: 'string',
            len: 30
        },
        {
            name: 'postal_state',
            type: 'string',
            len: 2
        },
        {
            name: 'postal_zip_code',
            type: 'string',
            len: 15
        },
        {
            name: 'postal_country_code',
            type: 'string',
            len: 3
        },
		{
			name: 'billing_location',
			type: 'bool'
		},
		{
			name: 'pos_code',
			type: 'string',
			len: 2
		},
		{
			name: 'service_loc_code',
			type: 'string',
			len: 10
		},
		{
			name: 'ein',
			type: 'string',
			len: 15
		},
		{
			name: 'clia',
			type: 'string',
			len: 15
		},
		{
			name: 'fda',
			type: 'string',
			len: 15
		},
		{
			name: 'npi',
			type: 'string',
			len: 15
		},
		{
			name: 'ess',
			type: 'string',
			len: 15
		},
        {
            name: 'external_id',
            type: 'string',
            len: 25
        },
        {
            name: 'global_id',
            type: 'string',
            len: 50
        },
		{
			name: 'network_cidr',
			type: 'string',
			len: 80
		},
		{
			name: 'network_from',
			type: 'int',
			useNull: true
		},
		{
			name: 'network_to',
			type: 'int',
			useNull: true
		},
		{
			name: 'coordinates',
			type: 'string',
			len: 120
		},
		{
			name: 'active',
			type: 'bool'
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
			read: 'Facilities.getFacilities',
			create: 'Facilities.addFacility',
			update: 'Facilities.updateFacility',
			destroy: 'Facilities.deleteFacility'
		}
	}
});
