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

Ext.define('App.model.administration.FileSystem', {
	extend: 'Ext.data.Model',
	table: {
		name: 'filesystems'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'next_id',
			type: 'int',
			index: true
		},
		{
			name: 'dir_path',
			type: 'string',
			len: 180
		},
		{
			name: 'status',
			type: 'string',
			len: 15,
			index: true
		},
		{
			name: 'total_space',
			type: 'float',
			len: 11
		},
		{
			name: 'free_space',
			type: 'float',
			len: 11
		},
		{
			name: 'error',
			type: 'string',
			len: 200
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'FileSystem.getFileSystems',
			create: 'FileSystem.addFileSystem',
			update: 'FileSystem.updateFileSystem'
		}
	},
	reader: {
		totalProperty: 'total',
		root: 'data'
	}
});