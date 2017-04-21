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

Ext.define('App.model.administration.DocumentsPdfTemplate', {
	extend: 'Ext.data.Model',
	table: {
		name: 'documents_pdf_templates'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'facility_id',
			type: 'int',
			index: true
		},
		{
			name: 'template',
			type: 'string',
			len: 250
		},
		{
			name: 'body_margin_left',
			type: 'int',
			len: 3
		},
		{
			name: 'body_margin_top',
			type: 'int',
			len: 3
		},
		{
			name: 'body_margin_right',
			type: 'int',
			len: 3
		},
		{
			name: 'body_margin_bottom',
			type: 'int',
			len: 3
		},
		{
			name: 'body_font_family',
			type: 'string',
			len: 60
		},
		{
			name: 'body_font_style',
			type: 'string',
			len: 3
		},
		{
			name: 'body_font_size',
			type: 'string',
			len: 3
		},
		{
			name: 'header_data',
			type: 'array'
		},
		{
			name: 'active',
			type: 'bool',
			index: true
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'DocumentHandler.getDocumentsTemplates',
			create: 'DocumentHandler.addDocumentsTemplates',
			update: 'DocumentHandler.updateDocumentsTemplates'
		}
	}
});