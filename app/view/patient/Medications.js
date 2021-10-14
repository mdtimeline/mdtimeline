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

Ext.define('App.view.patient.Medications', {
	extend: 'Ext.panel.Panel',
	requires: [
		'App.store.patient.Medications',
		'App.store.administration.Medications',
		'Ext.form.field.Trigger',
		'App.ux.LiveRXNORMSearch',
		'App.ux.combo.PrescriptionHowTo',
		'App.ux.combo.PrescriptionTypes',
		'App.ux.LiveSigsSearch',
		'App.ux.LiveUserSearch',
		'App.ux.form.fields.DateTime'
	],
	xtype: 'patientmedicationspanel',
	title: _('meds'),
	layout: 'border',
	border: false,
	items: [
		{
			xtype: 'grid',
			region: 'center',
			action: 'patientMedicationsListGrid',
			itemId: 'patientMedicationsGrid',
			columnLines: true,
            features: [{ftype:'grouping'}],
			store: Ext.create('App.store.patient.Medications', {
				autoSync: false,
                startCollapsed: true
			}),
			columns: [
				{
					xtype: 'griddeletecolumn',
					acl: a('remove_patient_medication'),
					width: 25
				},
				{
					xtype: 'actioncolumn',
					width: 25,
                    groupable: false,
					items: [
						{
							icon: 'resources/images/icons/blueInfo.png',  // Use a URL in the icon config
							tooltip: 'Get Info',
							handler: function(grid, rowIndex, colIndex, item, e, record){
								App.app.getController('InfoButton').doGetInfo(
                                    record.data.RXCUI,
                                    'RXCUI',
                                    record.data.STR
                                );
							}
						}
					]
				},
				{
					header: _('medication'),
					flex: 1,
                    groupable: true,
                    hidden: false,
					minWidth: 200,
					dataIndex: 'STR',
					editor: {
						xtype: 'rxnormlivetsearch',
						itemId: 'PatientMedicationLiveSearch',
						displayField: 'STR',
						valueField: 'STR',
						action: 'medication',
						allowBlank: false
					},
					renderer: function(v, mets, record){
						var codes = '';
						if(record.data.RXCUI != ''){
							codes += ' RxNorm: ' + record.data.RXCUI;
						}
						if(record.data.NDC != ''){
							codes += ' NDC: ' + record.data.NDC;
						}
						codes = codes != '' ? (' (' + codes + ' )') : '';
						return '<b>' + v + '</b>' + codes;
					}
				},
				{
					header: _('instructions') + ' (SIG)',
					flex: 1,
                    groupable: true,
                    hidden: false,
					minWidth: 200,
					dataIndex: 'directions',
					editor: {
						xtype: 'textfield'
					}
				},
				{
					xtype: 'datecolumn',
                    groupable: false,
					format: 'Y-m-d',
					header: _('begin_date'),
					width: 150,
					dataIndex: 'begin_date',
					sortable: false,
					hideable: false,
                    editor: {
                        xtype: 'datefield',
                        format: 'Y-m-d'
                    }
				},
				{
					xtype: 'datecolumn',
                    groupable: false,
					format: 'Y-m-d',
					header: _('end_date'),
					width: 150,
					dataIndex: 'end_date',
					sortable: false,
					hideable: false,
					editor: {
						xtype: 'datefield',
						format: 'Y-m-d',
						itemId: 'PatientMedicationEndDateField'
					}
				},
				{
					header: _('active?'),
                    groupable: false,
					width: 60,
					dataIndex: 'is_active',
					editor: {
						xtype: 'checkbox'
					},
					renderer: function(v){
						return app.boolRenderer(v);
					}
				}
			],
			plugins: Ext.create('Ext.grid.plugin.RowEditing', {
				autoCancel: false,
				errorSummary: false,
				clicksToEdit: 2
			}),
			bbar: [
				'-',
				{
					text: _('reconciled'),
					itemId: 'PatientMedicationReconciledBtn',
					enableToggle: true,
					pressed: true
				},
				'-',
                {
                    text: _('active'),
                    itemId: 'PatientMedicationActiveBtn',
                    enableToggle: true,
                    pressed: false
                },
				'->',
				{
					text: _('review'),
					itemId: 'ReviewMedicationsBtn'
				}
			]
		}
	],
	tbar: [
		'->',
		{
			text: _('add_new'),
			itemId: 'addPatientMedicationBtn',
			iconCls: 'icoAdd'
		}
	]


});
