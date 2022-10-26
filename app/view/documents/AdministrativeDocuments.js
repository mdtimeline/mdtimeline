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

Ext.define('App.view.documents.AdministrativeDocuments', {
	extend: 'Ext.panel.Panel',
	requires: [
		'App.ux.grid.LiveSearchGridPanel',
		'App.ux.combo.Templates',
		'Ext.grid.plugin.RowEditing',
		'App.store.documents.AdministrativeDocuments',
		'App.ux.ManagedIframe',
		'Ext.grid.feature.Grouping',
		'Ext.form.ComboBox'
	],
	xtype: 'administrativedocumentspanel',
	title: _('documents'),
	layout: 'border',
	orientation: 'west',

	belongs_to: null,
	belongs_to_id: null,

	initComponent: function(){
		var me = this,
			store = Ext.create('App.store.documents.AdministrativeDocuments', {
				autoLoad: false,
				remoteFilter: true,
				remoteSort: false,
				autoSync: false,
				pageSize: 500,
				groupField: 'docTypeCode',
				sorters: [
					{
						property: 'date',
						direction: 'DESC'
					},
					{
						property: 'docTypeCode',
						direction: 'ASC'
					}
				]
			}),

			// TODO
			docCtrl = app.getController('documents.AdministrativeDocuments');





		me.items = [
			{
				xtype: 'gridlivesearch',
				region: me.orientation,
				split: true,
				flex: 1,
				columnLines: true,
				selType: 'checkboxmodel',
				stateful: true,
				stateId: 'AdministrativeDocumentGridState',
				viewConfig: {
					plugins: {
						ptype: 'gridviewdragdrop',
						dragText: 'Drag and drop to move document to another category'
					}
				},
				features: [
					{
						ftype: 'grouping',
						hideGroupedHeader: true,
						collapsible: false,
						startCollapsed: false,
						groupHeaderTpl: Ext.create('Ext.XTemplate',
							'{children:this.getGroupName}',
							{
								getGroupName: function(children){
									return docCtrl.getGroupName(children[0].store, children[0]);
								}
							}
						)
					}
				],
				itemId: 'AdministrativeDocumentGrid',
				store: store,
				columns: [
					// {
					// 	xtype: 'actioncolumn',
					// 	width: 23,
					// 	icon: 'resources/images/icons/icoLessImportant.png',
					// 	tooltip: _('validate_file_integrity_hash'),
					// 	stateId: 'administrativeDocumentGridStateActionCol',
					// 	handler: function(grid, rowIndex){
					// 		docCtrl.onAdministrativeDocumentHashCheckBtnClick(grid, rowIndex);
					// 	},
					// 	getClass: function(){
					// 		return 'x-grid-icon-padding';
					// 	}
					// },
					{
						header: _('category'),
						dataIndex: 'docTypeCode',
						itemId: 'docTypeCode',
						editor: {
							xtype: 'textfield'
						},
						stateId: 'AdministrativeDocumentGridStateDocTypeCol',
						renderer: function(v, meta, record){
							if(record.get('entered_in_error')){
								meta.tdCls += ' entered-in-error ';
								meta.tdAttr = 'data-qtip="' + _('error_note') + ': ' + record.get('error_note') + '"';
							}
							return record.get('docType');
						}
					},
					{
						xtype: 'datecolumn',
						header: _('date'),
						dataIndex: 'date',
						format: g('date_display_format'),
						itemId: 'groupDate',
						stateId: 'administrativeDocumentGridStateGroupDateCol',
						renderer: function(v, meta, record){
							var val = v != null ? Ext.util.Format.date(v, g('date_display_format')) : '-';

							if(record.get('entered_in_error')){
								meta.tdCls += ' entered-in-error ';
								meta.tdAttr = 'data-qtip="' + _('error_note') + ': ' + record.get('error_note') + '"';
							}
							return val;
						}
					},
					{
						header: _('title'),
						dataIndex: 'title',
						flex: 1,
						editor: {
							xtype: 'textfield',
							action: 'title'
						},
						stateId: 'administrativeDocumentGridStateTitleCol',
						renderer: function(v, meta, record){
							if(record.get('entered_in_error')){
								meta.tdCls += ' entered-in-error ';
								meta.tdAttr = 'data-qtip="' + _('error_note') + ': ' + record.get('error_note') + '"';
							}
							return v.split('|').join('<br>');
						}
					},
					// {
					// 	header: _('encrypted'),
					// 	dataIndex: 'encrypted',
					// 	width: 70,
					// 	stateId: 'patientDocumentGridStateEncryptedCol',
					// 	renderer: function(v, meta, record){
					// 		if(record.get('entered_in_error')){
					// 			meta.tdCls += ' entered-in-error ';
					// 			meta.tdAttr = 'data-qtip="' + _('error_note') + ': ' + record.get('error_note') + '"';
					// 		}
					// 		return app.boolRenderer(v);
					// 	}
					// }
				],
				plugins: Ext.create('Ext.grid.plugin.RowEditing', {
					autoCancel: true,
					errorSummary: false,
					clicksToEdit: 2
				}),
				tbar: [
					_('group_by') + ':',
					{
						xtype: 'button',
						text: _('category'),
						enableToggle: true,
						action: 'docTypeCode',
						pressed: true,
						disabled: true,
						toggleGroup: 'administrativedocumentgridgroup'
					},
					{
						xtype: 'button',
						text: _('date'),
						enableToggle: true,
						action: 'groupDate',
						toggleGroup: 'administrativedocumentgridgroup'
					},
					'->',
					'-',
					{
						icon: 'resources/images/icons/no.png',
						itemId: 'AdministrativeDocumentEnteredInErrorBtn',
						tooltip: _('entered_in_error'),
						acl: a('allow_document_enter_in_error')
					},
					'-',
					{
						icon: 'resources/images/icons/icoScanner.png',
						itemId: 'AdministrativeDocumentScanBtn',
						tooltip: _('scan')
					},
					'-',
					{
						icon: 'resources/images/icons/upload.png',
						itemId: 'AdministrativeDocumentUploadBtn',
						tooltip: _('upload')
					}
				],
				bbar: Ext.create('Ext.PagingToolbar', {
					pageSize: 10,
					store: store,
					displayInfo: true,
					items: [
						{
							// text: 'Show...',
							// destroyMenu: true,
							// itemId: 'AdministrativeDocumentGridGroupBtn',
							// menu: []
						}
					]
				})
			},
			{
				xtype: 'panel',
				region: 'center',
				flex: 2,
				layout: {
					type: 'vbox',
					align: 'stretch'
				},
				frame: true,
				itemId: 'AdministrativeDocumentViewerPanel',
				cls: 'document-viewer-backgroud',
				items: [
					{
						xtype: 'miframe',
						style: 'document-viewer-backgroud',
						autoMask: false,
						flex: 1,
						itemId: 'AdministrativeDocumentViewerFrame'
					}
				],
				bbar: [
					{
						xtype: 'sliderfield',
						value: 1,
						increment: 0.1,
						decimalPrecision: 1,
						minValue: 0.1,
						maxValue: 1,
						flex: 1,
						margin: '0 10',
						stateId: 'AdministrativeDocumentViewerOpacityField',
						stateful: true,
						itemId: 'AdministrativeDocumentViewerOpacityField',
						getState: function(){
							return {"value": this.getValue()};
						},
						applyState: function(state){
							this.setValue(state.value);
						},
						stateEvents: [
							'change'
						]
					}
				]
			}
		];

		me.callParent(arguments);
	}
});