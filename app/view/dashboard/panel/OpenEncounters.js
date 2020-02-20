/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2012 Ernesto Rodriguez
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

Ext.define('App.view.dashboard.panel.OpenEncounters', {
	extend: 'Ext.grid.Panel',
	itemId: 'DashboardOpenEncountersGrid',
	requires: [
		'Ext.ux.SlidingPager'
	],
	height: 220,
	// selModel:{
	// 	mode: 'MULTI',
	// 	allowDeselect: true
	// },

	initComponent: function(){
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			fields: [
				{
					name: 'service_date',
					type: 'date',
					dateFormat: 'Y-m-d H:i:s'
				},
				{
					name: 'provider',
					type: 'string'
				},
				{
					name: 'patient',
					type: 'string'
				},
				{
					name: 'pid',
					type: 'int'
				},
				{
					name: 'eid',
					type: 'int'
				}
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'Encounter.getOpenEncounters'
				},
				reader: {
					root: 'data'
				},
				remoteGroup: false
			}
		});

		me.bbar = {
			xtype: 'pagingtoolbar',
			pageSize: 10,
			store: me.store,
			displayInfo: true,
			plugins: Ext.create('Ext.ux.SlidingPager'),
		};

		me.columns = [
			{
				xtype: 'datecolumn',
				text: _('service_date'),
				dataIndex: 'service_date',
				format: 'Y-m-d'
			},
			{
				text: _('patient'),
				dataIndex: 'patient',
				flex: 1
			},
			{
				text: _('provider'),
				dataIndex: 'provider',
				flex: 1
			}
		];

		me.callParent(arguments);
	}
});
