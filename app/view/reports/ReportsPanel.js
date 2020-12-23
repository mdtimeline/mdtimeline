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

Ext.define('App.view.reports.ReportsPanel', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('reports'),
	itemId: 'ReportsPanel',
	requires: [

	],
	initComponent: function () {
		var me = this;

		me.store = Ext.create('App.store.reports.Reports', {
			remoteFilter: true,
			remoteSort: true,
			groupField: 'category'
		});

		me.pageBody = [
			{
				xtype: 'grid',
				frame: true,
				title: _('reports'),
				itemId: 'ReportsGrid',
				store: me.store,
				features: [{ftype:'grouping'}],
				tools: [
					{
						xtype:'button',
						text: _('report'),
						iconCls: 'icoAdd',
						itemId: 'AdministrationReportsAddBtn',
						acl: a('access_admin_reports')
					}
				],
				columns: [
					{
						text: _('category'),
						dataIndex: 'category',
						width: 300
					},
					{
						text: _('title'),
						dataIndex: 'title',
						width: 300
					},
					{
						text: _('description'),
						dataIndex: 'description',
						flex: 1
					},
					{
						text: _('description'),
						dataIndex: 'store_procedure_name',
						width: 300,
						hidden: true
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 10,
					store: me.store,
					displayInfo: true,
					plugins: new Ext.ux.SlidingPager()
				}
			}
		];

		me.callParent(arguments);

	}
});
