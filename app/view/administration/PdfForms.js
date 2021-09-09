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
				columns: [
					{
						text: 'Facility',
						dataIndex: 'facility_id',
						editor: {
							xtype: 'activefacilitiescombo'
						}
					},
					{
						text: 'Type',
						dataIndex: 'document_type',
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: 'Title',
						dataIndex: 'document_title',
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: 'Path',
						dataIndex: 'document_path',
						flex: 1,
						editor: {
							xtype: 'textfield'
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
						width: 40,
						editor: {
							xtype: 'checkboxfield'
						}
					},
					{
						text: 'Active',
						dataIndex: 'is_active',
						width: 40,
						editor: {
							xtype: 'checkboxfield'
						}
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 1000,
					store: me.store,
					displayInfo: true,
					plugins: new Ext.ux.SlidingPager()
				}
			}
		];

		me.callParent(arguments);

	},

});
