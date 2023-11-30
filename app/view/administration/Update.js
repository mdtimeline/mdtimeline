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

Ext.define('App.view.administration.Update', {
	extend: 'App.ux.RenderPanel',
	requires: [

	],
	pageTitle: _('update'),
	itemId: 'AdminUpdatePanel',
	initComponent: function(){
		var me = this;

		me.store = Ext.create('App.store.administration.Update');

		me.pageLayout = {
			type: 'vbox',
			align: 'stretch'
		};
		me.pageBody = [
			{
				xtype: 'grid',
				itemId: 'AdminUpdateGrid',
				title: 'Modules',
				flex: 1,
				store: me.store,
				columns: [
					{
						text: 'Module',
						dataIndex: 'module',
						flex: 1,
					},
					{
						text: 'Version',
						dataIndex: 'module',
						flex: 1,
					},
					{
						text: 'Script Version',
						dataIndex: 'script_version',
						flex: 1,
					},
					{
						text: 'Latest Commit',
						dataIndex: 'latest_commit',
						flex: 1,
					},
					{
						text: 'Actions',
						menuDisabled: true,
						sortable: false,
						xtype: 'actioncolumn',
						flex: 1,
						items: [
							{
								iconCls: 'fas fa-clipboard-list',
								tooltip: 'Git Log',
								handler: function(grid, rowIndex, colIndex) {
									var module_record = grid.getStore().getAt(rowIndex);
									app.getController('App.controller.administration.Update').doAGitLog(module_record);
								}
							},
							{
								iconCls: 'fas fa-not-equal',
								tooltip: 'Git DifF',
								handler: function(grid, rowIndex, colIndex) {
									var module_record = grid.getStore().getAt(rowIndex);
									app.getController('App.controller.administration.Update').doAGitDifF(module_record);
								}
							},
							{
								iconCls: 'fas fa-file-download',
								tooltip: 'Git Pull',
								handler: function(grid, rowIndex, colIndex) {
									var module_record = grid.getStore().getAt(rowIndex);
									app.getController('App.controller.administration.Update').doAGitPull(module_record);
								}
							},
							{
								iconCls: 'fas fa-undo',
								tooltip: 'Git Reset',
								handler: function(grid, rowIndex, colIndex) {
									var module_record = grid.getStore().getAt(rowIndex);
									app.getController('App.controller.administration.Update').doAGitReset(module_record);
								}
							}
						]
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 25,
					store: me.store,
					plugins: Ext.create('Ext.ux.SlidingPager')
				}
			}
		];
		me.callParent(arguments);

	}

});
