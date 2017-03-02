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

Ext.define('App.view.patient.Results', {
	extend: 'Ext.panel.Panel',
	requires: [
		'Ext.grid.plugin.CellEditing',
		'Ext.grid.plugin.RowEditing',
		'Ext.tab.Panel',
		'App.store.patient.PatientsOrders',
		'App.ux.LiveLabsSearch',
		'App.ux.LiveRadsSearch',
		'App.ux.window.voidComment'
	],
	title: _('results'),
	xtype: 'patientresultspanel',
	layout: 'border',
	tbar: [
		'->',
		{
			text: _('new_result'),
			itemId: 'ResultsOrderNewBtn',
			iconCls: 'icoAdd'
		}
	],
	items: [
		{
			xtype: 'grid',
			itemId: 'ResultsOrdersGrid',
			action: 'orders',
			region: 'center',
			columnLines: true,
			allowDeselect: true,
			frame: true,
			store: Ext.create('App.store.patient.PatientsOrders', {
				remoteFilter: true
			}),
			plugins: [
				{
					pluginId: 'ResultsOrdersGridRowEditor',
					ptype: 'rowediting',
					errorSummary: false
				}
			],
			columns: [
				{
					xtype: 'actioncolumn',
					width: 25,
					items: [
						{
							icon: 'resources/images/icons/blueInfo.png',
							tooltip: 'Get Info',
							handler: function(grid, rowIndex, colIndex, item, e, record){
								App.app.getController('InfoButton').doGetInfo(
									record.data.code,
									record.data.code_type,
									record.data.description
								);
							}
						}
					]
				},
				{
					header: _('void'),
					width: 30,
					dataIndex: 'void',
					tooltip: _('void'),
					editor: {
						xtype: 'checkbox',
						itemid: 'ResultsOrdersGridOrderVoidCheckbox'
					},
					renderer: function(v, meta, record){
						return app.voidRenderer(v);
					}
				},
				{
					header: _('type'),
					width: 100,
					dataIndex: 'order_type',
					renderer: function(v, meta, record){
						var style = '';
						if(record.get('void')) style = 'text-decoration: line-through;';

						if(record.data.order_type == 'lab')
							return '<span style="' + style + '">' + _('laboratory') + '</span>';
						if(record.data.order_type == 'rad')
							return '<span style="' + style + '">' + _('radiology') + '</span>';
					},
					editor: {
						xtype: 'combobox',
						itemId: 'ResultsOrdersGridOrderTypeCombo',
						store: Ext.create('Ext.data.Store', {
							fields: ['type', 'order_type'],
							data: [
								{ 'type': 'Laboratory', 'order_type': 'lab' },
								{ 'type': 'Radiology', 'order_type': 'rad' }
							]
						}),
						allowBlank: false,
						editable: false,
						queryMode: 'local',
						displayField: 'type',
						valueField: 'order_type'
					}
				},
				{
					xtype: 'datecolumn',
					format: 'Y-m-d',
					header: _('date_ordered'),
					dataIndex: 'date_ordered',
					menuDisabled: true,
					resizable: false,
					width: 100,
					editor: {
						xtype: 'datefield',
						allowBlank: false
					},
					renderer: function(v, meta, record){
						var dataOrdered = record.data.date_ordered;
						if(record.get('void'))
							return '<span style="text-decoration: line-through;">' + dataOrdered + '</span>';
						return '<span>' + dataOrdered + '</span>';
					}
				},
				{
					header: _('order_description'),
					dataIndex: 'description',
					menuDisabled: true,
					resizable: false,
					flex: 1,
					editor: {
						xtype: 'labslivetsearch',
						itemId: 'ResultsLabsLiveSearchField',
						allowBlank: false
					},
					renderer: function(v, meta, record){
						if(record.get('void'))
							return '<span style="text-decoration: line-through;">' + v + '</span>';
						return '<span>' + v + '</span>';
					}
				},
				{
					header: _('status'),
					dataIndex: 'status',
					menuDisabled: true,
					resizable: false,
					width: 60,
					renderer: function(v, meta, record){
						if(record.get('void'))
							return '<span style="text-decoration: line-through;">' + v + '</span>';
						return '<span>' + v + '</span>';
					}
				}
			]
		},
		{
			/**
			 * Orders Card [ Laboratory or Radiology ]
			 * ---------------------------------------
			 */
			xtype: 'panel',
			border: false,
			region: 'south',
			split: true,
			frame: true,
			itemId: 'ResultsCardPanel',
			height: 350,
			hidden: true,
			layout: 'card',
			activeItem: 0,

			items: [
				{
					/**
					 * Laboratory Order Panel
					 * ---------------------
					 */
					xtype: 'panel',
					frame: false,
					itemId: 'ResultsLaboratoryPanel',
					layout: {
						type: 'border'
					},
					tools: [
						{
							xtype: 'button',
							text: _('view_document'),
							icon: 'resources/images/icons/icoView.png',
							itemId: 'ResultsLaboratoryPanelDocumentViewBtn'
						}
					],
					items: [
						{
							xtype: 'form',
							title: _('report_info'),
							itemId: 'ResultsLaboratoryForm',
							region: 'west',
							collapsible: true,
							autoScroll: true,
							width: 400,
							bodyPadding: 5,
							split: true,
							layout: {
								type: 'vbox',
								align: 'stretch'
							},
							items: [
								{
									xtype: 'fieldset',
									title: _('report_info'),
									defaults: {
										xtype: 'textfield',
										anchor: '100%'
									},
									layout: 'anchor',
									items: [
										{
											xtype: 'datefield',
											fieldLabel: _('report_date'),
											name: 'result_date',
											format: 'Y-m-d',
											allowBlank: false
										},
										{
											fieldLabel: _('report_number'),
											name: 'lab_order_id',
											allowBlank: false
										},
										{
											xtype: 'combobox',
											fieldLabel: _('status'),
											name: 'result_status',
											queryMode: 'local',
											displayField: 'option',
											valueField: 'value',
											store: Ext.create('Ext.data.Store', {
												fields: ['option', 'value'],
												data: [
													{ option: 'Aborted', value: 'aborted'},
													{ option: 'Active', value: 'active'},
													{ option: 'Cancelled', value: 'cancelled'},
													{ option: 'Completed', value: 'completed'},
													{ option: 'Held', value: 'held'},
													{ option: 'Suspended', value: 'suspended'}
												]
											})
										},
										{
											xtype: 'datefield',
											fieldLabel: _('observation_date'),
											name: 'observation_date',
											format: 'Y-m-d',
											allowBlank: false
										},
										{
											fieldLabel: _('specimen'),
											name: 'specimen_text'
										},
										{
											xtype: 'textareafield',
											fieldLabel: _('specimen_notes'),
											name: 'specimen_notes',
											height: 50
										},
										{
											xtype: 'filefield',
											labelAlign: 'top',
											fieldLabel: _('upload_document'),
											action: 'ResultsLaboratoryFormUploadField',
											submitValue: false
										}
									]
								},
								{
									xtype: 'fieldset',
									title: _('laboratory_info'),
									defaults: {
										xtype: 'textfield',
										anchor: '100%'
									},
									layout: 'anchor',
									margin: 0,
									collapsible: true,
									collapsed: true,
									items: [
										{
											fieldLabel: _('name'),
											name: 'lab_name'
										},
										{
											xtype: 'textareafield',
											fieldLabel: _('address'),
											name: 'lab_address',
											height: 50
										}
									]
								}
							]
						},
                        {
							xtype: 'treepanel',
							itemId: 'ResultsLaboratoryObservationsGrid',
							action: 'observations',
                            animate: false,
                            rootVisible: false,
							flex: 1,
							region: 'center',
							split: true,
							border: false,
							columnLines: true,
							plugins: [
								{
									ptype: 'cellediting',
									clicksToEdit: 1
								}
							],
							columns: [
								{
                                    xtype: 'treecolumn',
									text: _('name'),
									menuDisabled: true,
									dataIndex: 'code_text',
									width: 350
								},
								{
									xtype: 'actioncolumn',
									width: 25,
									items: [
										{
											icon: 'resources/images/icons/blueInfo.png',  // Use a URL in the icon config
											tooltip: _('get_info'),
											handler: function(grid, rowIndex, colIndex, item, e, record){
												App.app.getController('InfoButton').doGetInfo(
													record.data.code,
													record.data.code_type,
													record.data.code_text
												);
											}
										}
									]
								},
								{
									text: _('value'),
									menuDisabled: true,
									dataIndex: 'value',
									width: 180,
									editor: {
										xtype: 'textfield'
									},
									renderer: function(v, meta, record){
										var red = ['LL', 'HH', '>', '<', 'AA', 'VS'],
											orange = ['L', 'H', 'A', 'W', 'MS'],
											blue = ['B', 'S', 'U', 'D', 'R', 'I'],
											green = ['N'];

										if(Ext.Array.contains(green, record.data.abnormal_flag)){
											return '<span style="color:green;">' + v + '</span>';
										}
										else if(Ext.Array.contains(blue, record.data.abnormal_flag)){
											return '<span style="color:blue;">' + v + '</span>';
										}
										else if(Ext.Array.contains(orange, record.data.abnormal_flag)){
											return '<span style="color:orange;">' + v + '</span>';
										}
										else if(Ext.Array.contains(red, record.data.abnormal_flag)){
											return '<span style="color:red;">' + v + '</span>';
										}
										else{
											return v;
										}
									}
								},
								{
									text: _('units'),
									menuDisabled: true,
									dataIndex: 'units',
									width: 75,
									editor: {
										xtype: 'textfield'
									}
								},
								{
									text: _('abnormal'),
									menuDisabled: true,
									dataIndex: 'abnormal_flag',
									width: 75,
									editor: {
										xtype: 'textfield'
									},
									renderer: function(v, attr){
										var red = ['LL', 'HH', '>', '<', 'AA', 'VS'],
											orange = ['L', 'H', 'A', 'W', 'MS'],
											blue = ['B', 'S', 'U', 'D', 'R', 'I'],
											green = ['N'];

										if(Ext.Array.contains(green, v)){
											return '<span style="color:green;">' + v + '</span>';
										}
										else if(Ext.Array.contains(blue, v)){
											return '<span style="color:blue;">' + v + '</span>';
										}
										else if(Ext.Array.contains(orange, v)){
											return '<span style="color:orange;">' + v + '</span>';
										}
										else if(Ext.Array.contains(red, v)){
											return '<span style="color:red;">' + v + '</span>';
										}
										else{
											return v;
										}
									}
								},
								{
									text: _('range'),
									menuDisabled: true,
									dataIndex: 'reference_rage',
									width: 150,
									editor: {
										xtype: 'textfield'
									}
								},
								{
									text: _('notes'),
									menuDisabled: true,
									dataIndex: 'notes',
									width: 300,
									editor: {
										xtype: 'textfield'
									}
								},
								{
									text: _('status'),
									menuDisabled: true,
									dataIndex: 'observation_result_status',
									width: 60,
									editor: {
										xtype: 'textfield'
									}
								}
							]
						}
					]
				},
				{
					/**
					 * Radiology Order Panel
					 * ---------------------
					 */
					xtype: 'panel',
					itemId: 'ResultsRadiologyPanel',
					frame: true,
					layout: {
						type: 'border'
					},
					items: [
						{
							xtype: 'form',
							title: _('report'),
							itemId: 'ResultsRadiologyForm',
							region: 'west',
							collapsible: true,
							autoScroll: true,
							width: 400,
							bodyPadding: 5,
							split: true,
							layout: {
								type: 'vbox',
								align: 'stretch'
							},
							items: [
								{
									xtype: 'fieldset',
									title: _('report'),
									defaults: {
										xtype: 'textfield',
										anchor: '100%'
									},
									layout: 'anchor',
									items: [
										{
											xtype: 'datefield',
											fieldLabel: _('report_date'),
											name: 'result_date',
											format: 'Y-m-d',
											allowBlank: false
										},
										{
											fieldLabel: _('report_number'),
											name: 'lab_order_id',
											allowBlank: false
										},
										{
											fieldLabel: _('status'),
											name: 'result_status'
										},
										{
											xtype: 'fileuploadfield',
											fieldLabel: _('report'),
											itemId: 'ResultsRadiologyFormUploadField',
											submitValue: false,
											allowBlank: false
										}
									]
								},
								{
									xtype: 'fieldset',
									title: _('study'),
									defaults: {
										xtype: 'textfield',
										anchor: '100%'
									},
									layout: 'anchor',
									items: [
										{
											xtype: 'button',
											text: _('view'),
											margin: '0 0 8 0',
											itemId: 'ResultsRadiologyFormViewStudyBtn'
										}
									]
								},
								{
									xtype: 'fieldset',
									title: _('radiologist'),
									defaults: {
										xtype: 'textfield',
										anchor: '100%'
									},
									layout: 'anchor',
									margin: 0,
									collapsible: true,
									collapsed: true,
									items: [
										{
											fieldLabel: _('name'),
											name: 'radiologist_name'
										},
										{
											xtype: 'textareafield',
											fieldLabel: _('address'),
											name: 'radiologist_address',
											height: 50
										}
									]
								}
							]
						},
						{
							xtype: 'miframe',
							region: 'center',
							style: 'background-color: white',
							itemId: 'ResultsRadiologyDocumentIframe'
						}
					]
				}
			],
			buttons: [
				{
					text: _('sign'),
					iconCls: 'icoSing',
					disabled: true,
					itemId: 'ResultsOrderSignBtn'
				},
				'->',
				{
					text: _('reset'),
					itemId: 'ResultsOrderResetBtn'
				},
				{
					text: _('save'),
					itemId: 'ResultsOrderSaveBtn'
				}
			]
		}
	]
});
