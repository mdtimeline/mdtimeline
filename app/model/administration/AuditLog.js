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

Ext.define('App.model.administration.AuditLog', {
	extend: 'Ext.data.Model',
	table: {
		name: 'audit_log'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'eid',
			type: 'int',
			index: true
		},
		{
			name: 'pid',
			type: 'int',
			index: true
		},
		{
			name: 'uid',
			type: 'int',
			index: true
		},
		{
			name: 'foreign_id',
			type: 'string',
			index: true
		},
		{
			name: 'foreign_table',
			type: 'string',
			len: 80
		},
		{
			name: 'event',
			type: 'string',
			index: true,
			len: 80
		},
		{
			name: 'event_description',
			type: 'string',
			index: true,
			len: 200
		},
		{
			name: 'event_date',
			type: 'date',
			format: 'Y-m-d H:i:s'
		},
		{
			name: 'user_fname',
			type: 'string',
			store: false
		},
		{
			name: 'user_mname',
			type: 'string',
			store: false
		},
		{
			name: 'user_lname',
			type: 'string',
			store: false
		},
		{
			name: 'user_name',
			type: 'string',
			convert: function(val, rec){
				return rec.data.user_lname + ', ' + rec.data.user_fname + ' ' + rec.data.user_mname
			},
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'AuditLog.getLog'
		},
		reader: {
			root: 'data'
		}
	}
});
