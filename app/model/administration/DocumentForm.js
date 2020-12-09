/**
 GaiaEHR (Electronic Health Records)
 Copyright (C) 2013 Certun, LLC.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.model.administration.DocumentForm', {
	extend: 'Ext.data.Model',
	table: {
		name: 'documents_pdf_forms'
	},
	fields: [
		{
			name: 'id',
			type: 'int',
			index: true
		},
		{
			name: 'facility_id',
			type: 'int',
			index: true
		},
		{
			name: 'document_type',
			type: 'string',
			len: 40,
			index: true
		},
		{
			name: 'document_title',
			type: 'string',
			len: 80
		},
		{
			name: 'document_path',
			type: 'string',
			len: 300
		},
		{
			name: 'document_path',
			type: 'string',
			len: 300
		},
		{
			name: 'signature_x',
			type: 'int',
			len: 6
		},
		{
			name: 'signature_y',
			type: 'int',
			len: 6
		},
		{
			name: 'signature_w',
			type: 'int',
			len: 6
		},
		{
			name: 'signature_h',
			type: 'int',
			len: 6
		},
		{
			name: 'flatten',
			type: 'bool'
		},
		{
			name: 'is_active',
			type: 'bool',
			index: true
		}
	]
});