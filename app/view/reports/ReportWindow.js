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

Ext.define('App.view.reports.ReportWindow', {
	extend: 'Ext.window.Window',
	pageTitle: _('report'),
	itemId: 'ReportWindow',
	layout:{
		type: 'vbox',
		align: 'stretch'
	},
	bodyPadding: 5,
	requires: [
		'App.ux.grid.exporter.Exporter'
	],
	initComponent: function () {
		var me = this;


		me.width = Ext.getBody().getWidth() - 150;
		me.height = Ext.getBody().getHeight() - 150;

		me.items = [
			{
				xtype: 'form',
				itemId: 'ReportWindowForm',
				bodyPadding: 5,
				layout: 'hbox',
				buttons: [
					{
						xtype: 'button',
						text: _('reload'),
						itemId: 'ReportWindowReloadBtn',
					}
				]
			},
			{
				xtype: 'grid',
				frame: true,
				title: _('report'),
				itemId: 'ReportWindowGrid',
				features: [{
					groupHeaderTpl: '{name}',
					ftype: 'groupingsummary'
				}],
				flex: 1,
				tbar: [
					'->',
					{
						xtype:'button',
						iconCls: 'icoPrint',
						text: 'Print',
						itemId: 'ReportWindowGridPrintBtn'
					},
					'-',
					{
						xtype: 'exporterbutton',
						text: 'Save As CSV',
					},
					'-',
					{
						xtype: 'exporterbutton',
						text: 'Save As XLS',
						format: 'excel'
					}
				],
				columns: [
					{
						text: 'ID',
						dataIndex: 'id'
					}
				]
			}
		];

		me.callParent(arguments);

	}
});
