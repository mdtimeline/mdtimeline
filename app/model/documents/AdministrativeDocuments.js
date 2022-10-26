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

Ext.define('App.model.documents.AdministrativeDocuments', {
	extend: 'Ext.data.Model',
	table: {
		name: 'administrative_documents',
		comment: 'Administrative Documents Storage'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'belongs_to',
			type: 'string',
			len: 75,
			useNull: true,
			index: true
		},
		{
			name: 'belongs_to_id',
			type: 'int',
			index: true
		},
		{
			name: 'uid',
			type: 'int',
			index: true
		},
		{
			name: 'docType',
			type: 'string',
			index: true
		},
		{
			name: 'docTypeCode',
			type: 'string',
			len: 20,
			index: true
		},
		{
			name: 'filesystem_id',
			type: 'int',
			index: true
		},
		{
			name: 'path',
			type: 'string'
		},
		{
			name: 'name',
			type: 'string'
		},
		{
			name: 'date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s',
			index: true
		},
		{
			name: 'note',
			type: 'string',
			dataType: 'MEDIUMTEXT'
		},
		{
			name: 'title',
			type: 'string'
		},
		{
			name: 'hash',
			type: 'string'
		},
		{
			name: 'encrypted',
			type: 'bool',
			defaultValue: 0
		},
		{
			name: 'entered_in_error',
			type: 'bool',
			defaultValue: 0
		},
		{
			name: 'error_note',
			type: 'string',
			len: 300
		},
		{
			name: 'groupDate',
			type: 'string',
			store: false,
			convert: function(v, record){
				return Ext.util.Format.date(record.get('date'), g('date_display_format'));
			}
		},
		{
			name: 'document_instance',
			type: 'string',
			len: 10
		},
		{
			name: 'document',
			type: 'string',
			dataType: 'longblob'
		},
		{
			name: 'document_id',
			type: 'int'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'DocumentHandler.getAdministrativeDocuments',
			create: 'DocumentHandler.addAdministrativeDocument',
			update: 'DocumentHandler.updateAdministrativeDocument'
		},
		reader: {
			root: 'data'
		},
		remoteGroup: false
	}
});

