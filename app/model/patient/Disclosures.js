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

Ext.define('App.model.patient.Disclosures', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_disclosures',
		comment: 'Disclosures'
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
			name: 'type',
			type: 'string',
			len: 25
		},{
			name: 'recipient',
			type: 'string',
			len: 25
		},
		{
			name: 'status',
			type: 'string',
			len: 25
		},
		{
			name: 'description',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'request_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s',
			useNull: true
		},
		{
			name: 'fulfil_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s',
			useNull: true
		},
		{
			name: 'pickup_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s',
			useNull: true
		},
		{
			name: 'pickup_signature',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'active',
			type: 'bool'
		},
		{
			name: 'document_inventory',
			type: 'string',
			store: false
		},
		{
			name: 'document_inventory_ids',
			type: 'string',
			store: false
		},
		{
			name: 'document_inventory_count',
			type: 'int',
			store: false
		},
		{
			name: 'document_file_paths',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Disclosure.getDisclosures',
			create: 'Disclosure.addDisclosure',
			update: 'Disclosure.updateDisclosure'
		}
	}
});

