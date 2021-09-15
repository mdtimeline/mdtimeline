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

Ext.define('App.view.administration.PdfForms', {
	extend: 'App.ux.RenderPanel',
	requires: [

	],
	pageTitle: 'PDF Forms',
	pageLayout: 'border',
	itemId: 'AdministrationPdfForms',
	initComponent: function(){
		var me = this;

		me.store = Ext.create('App.store.administration.DocumentForms', {
			remoteFilter: true,
			remoteSort: true,
			autoSync: false,
			pageSize: 50
		});

		me.pageBody = [
			{
				xtype: 'grid',
				store: me.store,
				region: 'center',
				itemId: 'AdministrationPdfFormsGrid',
				frame: true,
				plugins: [
					{
						ptype: 'rowediting'
					}
				],
				columns: [
					{
						text: 'Facility',
						dataIndex: 'facility_id',
						editor: {
							xtype: 'activefacilitiescombo',
							allowBlank: false
						}
					},
					{
						text: 'Type',
						dataIndex: 'document_type',
						editor: {
							xtype: 'textfield',
							allowBlank: false
						}
					},
					{
						text: 'Title',
						dataIndex: 'document_title',
						flex: 1,
						editor: {
							xtype: 'textfield',
							allowBlank: false
						}
					},
					{
						text: 'Path',
						dataIndex: 'document_path',
						flex: 2,
						editor: {
							xtype: 'textfield',
							allowBlank: false
						}
					},
					{
						text: 'SigX',
						dataIndex: 'signature_x',
						width: 40,
						editor: {
							xtype: 'numberfield'
						}
					},
					{
						text: 'SigY',
						dataIndex: 'signature_y',
						width: 40,
						editor: {
							xtype: 'numberfield'
						}
					},
					{
						text: 'SigW',
						dataIndex: 'signature_w',
						width: 40,
						editor: {
							xtype: 'numberfield'
						}
					},
					{
						text: 'SigH',
						dataIndex: 'signature_h',
						width: 40,
						editor: {
							xtype: 'numberfield'
						}
					},
					{
						text: 'Flatten',
						dataIndex: 'flatten',
						width: 70,
						renderer: app.boolRenderer,
						editor: {
							xtype: 'checkboxfield'
						}
					},
					{
						text: 'Active',
						dataIndex: 'is_active',
						width: 70,
						renderer: app.boolRenderer,
						editor: {
							xtype: 'checkboxfield'
						}
					}
				],
				dockedItems: [
					{
						xtype: 'toolbar',
						dock: 'top',
						items: [
							'->',
							{
								xtype: 'button',
								iconCls: 'icoAdd',
								text: 'PDF Form',
								itemId: 'AdministrationPdfFormsAddBtn'
							}
						]
					},
					{
						xtype: 'pagingtoolbar',
						pageSize: 1000,
						store: me.store,
						displayInfo: true,
						dock: 'bottom',
						plugins: new Ext.ux.SlidingPager()
					}
				]
			},
			{
				xtype: 'panel',
				title: 'Tokens',
				region: 'east',
				width: 260,
				frame: true,
				split: true,
				bodyStyle: 'background-color: white',
				itemId: 'AdministrationPdfFormsTokenPanel'
			}
		];

		me.callParent(arguments);

	},

});
