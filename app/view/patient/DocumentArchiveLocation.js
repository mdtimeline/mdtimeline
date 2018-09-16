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

Ext.define('App.view.patient.DocumentArchiveLocation', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('document_location'),
	pageLayout: 'border',
	requires: [

	],
	itemId: 'PatientDocumentArchiveLocation',

	initComponent: function(){
		var me = this;


		me.documents_store = Ext.create('App.store.patient.PatientDocumentArchiveLocations', {
			remoteFilter: true,
			remoteSort: true
		});

		me.documents_location_store = Ext.create('App.store.patient.PatientDocumentArchiveLocations', {
			remoteFilter: true,
			remoteSort: true
		});

		me.pageBody = [
			{
				xtype: 'panel',
				region: 'center',
				layout: {
					type: 'hbox',
					align: 'stretch'
				},
				items: [
					{
						xtype: 'grid',
						title: _('documents'),
						flex: 1,
						frame: true,
						itemId: 'PatientDocumentArchiveLocationDocumentsGrid',
						selModel: {
							mode: 'MULTI'
						},
						viewConfig: {
							plugins: {
								ptype: 'gridviewdragdrop',
								dragText: 'Drag and drop to Archive',
								dragGroup: 'DocumentLocationUnArchivedGroup',
								dropGroup: 'DocumentLocationArchivedGroup',
							}
						},
						tools: [
							{
								xtype: 'button',
								text: _('show_archived'),
								itemId: 'PatientDocumentArchiveLocationDocumentArchivedBtn',
								enableToggle: true,
								toggleGroup: 'documentArchivedGroup'
							}
						],
						store: me.documents_store,
						dockedItems: [
							{
								xtype: 'toolbar',
								dock: 'top',
								layout: {
									type: 'hbox',
									align: 'stretch'
								},
								padding: '0 5',
								items: [
									{
										xtype: 'container',
										layout: {
											type: 'vbox',
											align: 'stretch'
										},
										width: 150,
										items: [
											{
												xtype: 'datefield',
												fieldLabel: _('from'),
												labelAlign: 'top',
												flex: 1,
												value: app.getDate(),
												maxValue: app.getDate(),
												allowBank: false,
												itemId: 'PatientDocumentArchiveLocationFromDate',
											},
											{
												xtype: 'datefield',
												fieldLabel: _('to'),
												labelAlign: 'top',
												flex: 1,
												value: app.getDate(),
												minValue: app.getDate(),
												allowBank: false,
												itemId: 'PatientDocumentArchiveLocationToDate',
											}
										]
									},
									{
										xtype: 'container',
										layout: {
											type: 'vbox',
											align: 'stretch'
										},
										flex: 2,
										items: [
											{
												xtype: 'patienlivetsearch',
												fieldLabel: _('patient'),
												labelAlign: 'top',
												flex: 1,
												resetEnabled: true,
												hideLabel: false,
												itemId: 'PatientDocumentArchiveLocationPatientSearch',
											},
											{
												xtype: 'userlivetsearch',
												fieldLabel: _('scanned_by'),
												labelAlign: 'top',
												flex: 1,
												hideLabel: false,
												itemId: 'PatientDocumentArchiveLocationScannedBySearch',
											}
										]
									},
									{
										xtype: 'container',
										layout: {
											type: 'vbox',
											align: 'stretch'
										},
										flex: 2,
										items: [
											{
												xtype: 'mitos.facilitiescombo',
												fieldLabel: _('facility'),
												labelAlign: 'top',
												resetTriggerEnable: true,
												itemId: 'PatientDocumentArchiveLocationFacilitySearch'
											},
											// {
											// 	xtype: 'depatmentscombo',
											// 	fieldLabel: _('department'),
											// 	labelAlign: 'top',
											// 	resetTriggerEnable: true,
											// 	itemId: 'PatientDocumentLocationDepartmentSearch',
											// }
										]
									}
								]
							},
							{
								xtype: 'toolbar',
								dock: 'bottom',
								items: [
									{
										xtype: 'pagingtoolbar',
										pageSize: 10,
										store: me.documents_store,
										displayInfo: true,
										plugins: new Ext.ux.SlidingPager()
									}
								]
							}
						],
						columns: [
							{
								xtype: 'datecolumn',
								text: _('scanned_date'),
								dataIndex: 'scanned_date',
								format: g('date_time_display_format'),
								width: 125,
							},
							{
								text: _('facility'),
								dataIndex: 'facility',
								flex: 1
							},
							{
								text: _('scanned_by'),
								dataIndex: 'scanned_by_lname',
								flex: 1,
								renderer: function (v,meta,rec) {
									return Ext.String.format('{0}, {1} {1}',
										rec.get('scanned_by_lname'),
										rec.get('scanned_by_fname'),
										rec.get('scanned_by_mname')
									);
								}
							},
							{
								text: _('reference_number'),
								dataIndex: 'archive_reference_number',
								flex: 1,
								hidden: true
							},
							{
								text: _('patient'),
								dataIndex: 'patient_lname',
								flex: 1,
								hidden: true,
								renderer: function (v,meta,rec) {
									return Ext.String.format('{0}, {1} {1}',
										rec.get('patient_lname'),
										rec.get('patient_fname'),
										rec.get('patient_mname')
									);
								}
							},
							{
								text: _('record_number'),
								dataIndex: 'patient_record_number',
								flex: 1,
								hidden: true
							},
							{
								text: _('document'),
								dataIndex: 'document_info',
								flex: 2
							}
						]
					},
					{
						xtype: 'splitter'
					},
					{
						xtype: 'grid',
						title: _('location'),
						flex: 1,
						frame: true,
						selModel: {
							mode: 'MULTI'
						},
						viewConfig: {
							plugins: {
								ptype: 'gridviewdragdrop',
								dragText: 'Drag and drop to un Archive',
								dragGroup: 'DocumentLocationArchivedGroup',
								dropGroup: 'DocumentLocationUnArchivedGroup',
							}
						},
						itemId: 'PatientDocumentArchiveLocationDocumentsLocationGrid',
						tools: [
							{
								xtype: 'combobox',
								fieldLabel: _('location'),
								labelAlign: 'right',
								margin: '0 5 0 0',
								width: 350,
								valueField: 'id',
								displayField: 'cmb_text',
								minChars: 1,
								typeAhead: false,
								queryDelay: 1000,
								store: Ext.create('App.store.patient.DocumentArchiveLocations'),
								itemId: 'PatientDocumentArchiveLocationDocumentsLocationSearch',
							},
							{
								xtype: 'button',
								text: _('new_edit'),
								itemId: 'PatientDocumentArchiveLocationDocumentsLocationNewEditBtn',
							}
						],
						store: me.documents_location_store,
						dockedItems: [
							{
								xtype: 'pagingtoolbar',
								dock: 'bottom',
								pageSize: 10,
								store: me.documents_location_store,
								displayInfo: true,
								plugins: new Ext.ux.SlidingPager(),
								items: [
									'-',
									{
										xtype: 'button',
										iconCls: 'icoAdd',
										text: _('entry'),
										itemId: 'PatientDocumentArchiveLocationDocumentsEntryAddBtn'
									},
									'-'
								]
							}
						],
						columns: [
							{
								xtype: 'datecolumn',
								text: _('archived_date'),
								dataIndex: 'create_date',
								format: g('date_display_format')
							},
							{
								text: _('document'),
								dataIndex: 'document_info',
								flex: 1
							},
							{
								text: _('patient'),
								dataIndex: 'patient_lname',
								flex: 1,
								hidden: true,
								renderer: function (v,meta,rec) {
									return Ext.String.format('{0}, {1} {1}',
										rec.get('patient_lname'),
										rec.get('patient_fname'),
										rec.get('patient_mname')
									);
								}
							},
							{
								text: _('record_number'),
								dataIndex: 'patient_record_number',
								flex: 1,
								hidden: true
							},
							{
								text: _('reference_number'),
								dataIndex: 'archive_reference_number',
								flex: 1,
								hidden: true
							},
							{
								text: _('archived_by'),
								dataIndex: 'archived_by_lname',
								flex: 1,
								renderer: function (v,meta,rec) {
									return Ext.String.format('{0} {1}',
										rec.get('archived_by_lname'),
										rec.get('archived_by_fname')
									);
								}
							},
							{
								text: _('notes'),
								dataIndex: 'notes',
								flex: 2
							}
						]
					}
				]
			},
			{
				xtype: 'panel',
				region: 'east',
				width: 550,
				title: _('document_preview'),
				collapsible: true,
				collapsed: true,
				split: true,
				frame: true,
				animCollapse: false,
				layout: 'fit',
				itemId: 'PatientDocumentArchiveLocationDocumentPreview',
				items: [
					{
						xtype: 'miframe',
						itemId: 'PatientDocumentArchiveLocationDocumentPreviewFrame'
					}
				]
			}

		];

		me.callParent(arguments);
	},

});
