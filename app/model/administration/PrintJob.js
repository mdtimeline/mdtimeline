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

Ext.define('App.model.administration.PrintJob', {
	extend: 'Ext.data.Model',
	table: {
		name: 'print_jobs'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'uid',
			type: 'int'
		},
		{
			name: 'document_id',
			type: 'int'
		},
		{
			name: 'printer_id',
			type: 'string'
		},
		{
			name: 'printer_type',
			type: 'string',
			len: 25
		},
		{
			name: 'print_status',
			type: 'string',
			len: 30
		},
		{
			name: 'priority',
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
		},
		{
			name: 'user_username',
			type: 'string',
			store: false
		},
		{
			name: 'user_fname',
			type: 'string',
			store: false
		},
		{
			name: 'user_lname',
			type: 'string',
			store: false
		},
		{
			name: 'user_mname',
			type: 'string',
			store: false
		},
		{
			name: 'document_doc_type',
			type: 'string',
			store: false
		},
		{
			name: 'document_title',
			type: 'string',
			store: false
		},
		{
			name: 'document_note',
			type: 'string',
			store: false
		},
		{
			name: 'number_of_copies',
			type: 'int',
			store: false,
			defaultValue: 1
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'PrintJob.getPrintJobs',
			create: 'PrintJob.addPrintJob',
			update: 'PrintJob.updatePrintJob',
			destroy: 'PrintJob.destroyPrintJob'
		},
		reader: {
			root: 'data'
		},
		writer:{
			writeAllFields: true
		}
	}
});