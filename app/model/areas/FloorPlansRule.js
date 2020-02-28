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

Ext.define('App.model.areas.FloorPlansRule', {
	extend: 'Ext.data.Model',
	table: {
		name: 'pool_areas_rules'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'pool_area_id',
			type: 'int'
		},
		{
			name: 'provider_user_id',
			type: 'int'
		},
		{
			name: 'zone_id',
			type: 'int',
			useNull: true
		},
		{
			name: 'floor_plan_id',
			type: 'int',
			store: false
		},
		{
			name: 'facility',
			type: 'string',
			store: false
		},
		{
			name: 'provider',
			type: 'string',
			store: false
		},
		{
			name: 'pool_area',
			type: 'string',
			store: false
		},
		{
			name: 'zone',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'FloorPlansRules.getFloorPlansRules',
			create: 'FloorPlansRules.addFloorPlansRule',
			update: 'FloorPlansRules.updateFloorPlansRule',
			destroy: 'FloorPlansRules.destroyFloorPlansRule'
		},
		writer: {
			writeAllFields: true
		}
	}
});