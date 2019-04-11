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

Ext.define('App.model.administration.Department', {
	extend: 'Ext.data.Model',
	table: {
		name: 'departments'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'code',
			type: 'string',
			len: 5,
			index: true
		},
		{
			name: 'title',
			type: 'string',
			len: 100
		},
        {
            name: 'taxonomy',
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
			read: 'Facilities.getDepartments',
			create: 'Facilities.addDepartment',
			update: 'Facilities.updateDepartment'
		},
		reader: {
			root: 'data'
		}
	}
});
