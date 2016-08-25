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

Ext.define('App.view.administration.AuditLogWindow', {
	extend: 'Ext.window.Window',
	layout: 'fit',
	title: _('audit_log'),
	itemId: 'AuditLogWindow',
	width: 900,
	height: 500,
	bodyPadding: 5,
	modal: true,

	requires: [
		'Ext.toolbar.Paging'
	],

	initComponent: function(){

		var me = this;

		me.gridStore =  Ext.create('App.store.administration.AuditLogs');

		me.items = [
			{
				xtype: 'grid',
				store: me.gridStore,
				itemId: 'AuditLogWindowGrid',
				columns: [
					{
						xtype: 'datecolumn',
						text: _('date'),
						dataIndex: 'event_date',
						width: 150,
						format: 'Y-m-d H:i:s'
					},
					{
						text: _('event'),
						dataIndex: 'event',
						width: 150,
						renderer: function(v){
							return v.toUpperCase();
						}
					},
					{
						text: _('description'),
						dataIndex: 'event_description',
						flex: 1
					},
					{
						text: _('user'),
						dataIndex: 'user_name',
						flex: 1
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 25,
					store: me.gridStore,
					displayInfo: true,
					plugins: Ext.create('Ext.ux.SlidingPager')
				}
			}
		];

		me.callParent(arguments);
	}
});