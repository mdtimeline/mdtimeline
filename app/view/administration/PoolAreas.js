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

Ext.define('App.view.administration.PoolAreas', {
	extend: 'App.ux.RenderPanel',
	itemId: 'PoolAreasPanel',
	pageTitle: _('pool_areas'),

	initComponent: function(){
		var me = this;

		var store = Ext.create('App.store.areas.PoolAreas',{
			groupField: 'facility_name',
		});

		me.pageBody = [
			{
				xtype: 'grid',
				itemId: 'PoolAreasGrid',
				store: store,
				features: [{
					ftype:'grouping',
					groupHeaderTpl: '{name}',

				}],
				plugins: [{ ptype: 'rowediting' }],
				columns: [
					{
						text: _('title'),
						dataIndex: 'title',
						flex: 1,
						editor: {
							xtype: 'textfield',
							allowBlank: false
						}
					},
					{
						text: _('concept'),
						dataIndex: 'concept',
						flex: 1,
						editor: {
							xtype: 'combobox',
							queryMode: 'local',
							editable: false,
							allowBlank: false,
							store: [
								'CHECKIN',
								'TRIAGE',
								'PHYSICIAN',
								'TREATMENT',
								'CHECKOUT'
							]
						}
					},
					{
						text: _('facility'),
						dataIndex: 'facility_id',
						flex: 1,
						editor: {
							xtype: 'mitos.facilitiescombo',
							allowBlank: false
						},
						renderer: function (v,m,r) {
							return r.get('facility_name');
						}
					},
					{
						text: _('floor_plan'),
						dataIndex: 'floor_plan_id',
						flex: 1,
						editor: {
							xtype: 'floorplanareascombo'
						},
						renderer: function (v,m,r) {
							return r.get('floor_plan_title');
						}
					},
					{
						text: _('sort'),
						dataIndex: 'sequence',
						editor: {
							xtype: 'numberfield',
							allowBlank: false,
							minValue: 0,
							maxValue: 99
						}
					},
					{
						text: _('active'),
						dataIndex: 'active',
						editor: {
							xtype: 'checkboxfield'
						}
					}
				],
				tbar: [
					'->',
					{
						xtype: 'button',
						text: _('pool_area'),
						iconCls: 'icoAdd',
						itemId: 'PoolAreasPanelAddBtn'
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 10,
					store: store,
					displayInfo: true,
					plugins: new Ext.ux.SlidingPager()
				}
			}
		];

		me.callParent();
	}


});