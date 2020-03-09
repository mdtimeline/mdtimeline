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

Ext.define('App.model.areas.PoolArea', {
	extend: 'Ext.data.Model',
	table: {
		name:'pool_areas'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'title',
			type: 'string',
			len: 80
		},
		{
			name: 'concept',
			type: 'string',
			len: 80
		},
		{
			name: 'floor_plan_id',
			type: 'int',
			useNull: true
		},
		{
			name: 'facility_id',
			type: 'int',
			useNull: true
		},
		{
			name: 'sequence',
			type: 'int'
		},
		{
			name: 'active',
			type: 'bool'
		},
		{
			name: 'facility_name',
			type: 'string',
			store: false
		},
		{
			name: 'floor_plan_title',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'PoolArea.getPoolAreas',
			create: 'PoolArea.addPoolArea',
			update: 'PoolArea.updatePoolArea',
			destroy: 'PoolArea.destroyPoolArea'
		},
		remoteGroup: false
	},
	remoteGroup: false
});