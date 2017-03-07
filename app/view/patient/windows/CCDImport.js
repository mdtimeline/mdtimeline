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

Ext.define('App.view.patient.windows.CCDImport', {
	extend: 'Ext.window.Window',
	requires: [
		'App.view.patient.windows.CCDImportPreview'
	],
	xtype: 'ccdimportwindow',
	title: _('ccd_viewer_and_import'),
	bodyStyle: 'background-color:#fff',
	modal: true,
	layout: 'fit',
	width: 1500,
	height: 700,
	autoScroll: true,
	ccdData: null,
	items: [
		{
			xtype: 'container',
			layout: {
				type: 'hbox',
				align: 'stretch'
			},
			padding: 5,
			items: [
				{
					xtype: 'panel',
					title: _('system_data_ro'),
					flex: 1,
					frame: true,
					margin: '0 5 0 0',
					layout: {
						type: 'vbox',
						align: 'stretch'
					},
					tools:[
						{
							xtype: 'patienlivetsearch',
							emptyText: _('import_and_merge_with') + '...',
							itemId: 'CcdImportWindowPatientSearchField',
							width: 300,
							height: 18
						}
					],
					defaults: {
						xtype: 'grid',
						height: 123,
						frame: true,
						hideHeaders: true,
						columnLines: true,
						multiSelect: true,
						disableSelection: true,
						margin: '0 0 5 0'
					},
					items: [
						{
							xtype: 'form',
							frame: true,
							title: _('patient'),
							itemId: 'CcdPatientPatientForm',
							height: 146,
							autoScroll: true,
							layout: 'column',
							items: [
								{
									xtype: 'container',
									defaults: {
										xtype: 'displayfield',
										labelWidth: 45,
										labelAlign: 'right',
										margin: 0
									},
									columnWidth: 0.5,
									items: [
										{
											fieldLabel: _('rec_num'),
											name: 'record_number'
										},
										{
											fieldLabel: _('name'),
											name: 'name'
										},
										{
											fieldLabel: _('sex'),
											name: 'sex'
										},
										{
											fieldLabel: _('dob'),
											name: 'DOBFormatted'
										},
										{
											fieldLabel: _('race'),
											name: 'race_text'
										}
									]
								},
								{
									xtype: 'container',
									defaults: {
										xtype: 'displayfield',
										labelWidth: 60,
										labelAlign: 'right',
										margin: 0
									},
									columnWidth: 0.5,
									items: [
										{
											fieldLabel: _('ethnicity'),
											name: 'ethnicity_text'
										},
										{
											fieldLabel: _('language'),
											name: 'language'
										},
										{
											fieldLabel: _('address'),
											name: 'fulladdress'
										},
										{
											fieldLabel: _('home_phone'),
											name: 'phones'
										}
									]
								}
							]
						},
						{
							title: _('medications'),
							store: Ext.create('App.store.patient.Medications'),
							itemId: 'CcdPatientMedicationsGrid',
							// selType: 'checkboxmodel',
							flex: 1,
							columns: [
								{
									dataIndex: 'STR',
									flex: 1,
									renderer: function (v, meta, record) {
										return Ext.String.format(
											'MEDICATION: {0} - {1}<br>INSTRUCTIONS: {2}<br>NDC: {3}',
											record.get('RXCUI'),
											record.get('STR'),
											record.get('directions'),
											record.get('NDC')
										);
									}
								},
								{
									xtype: 'datecolumn',
									dataIndex: 'created_date',
									width: 100,
									format: g('date_display_format')
								},
								{
									dataIndex: 'end_date',
									width: 100,
									renderer: function (v, meta, record) {
										if(!v){
											return _('active')
										}
										return _('inactive');
									}
								}
							]
						},
						{
							title: _('active_problems'),
							store: Ext.create('App.store.patient.PatientActiveProblems'),
							itemId: 'CcdPatientActiveProblemsGrid',
							// selType: 'checkboxmodel',
							flex: 1,
							columns: [
								{
									dataIndex: 'code_text',
									flex: 1,
									renderer: function (v, meta, record) {
										return Ext.String.format(
											'PROBLEM: {0} - {1}',
											record.get('code'),
											record.get('code_text')
										);
									}
								},
								{
									xtype: 'datecolumn',
									dataIndex: 'update_date',
									width: 100,
									format: g('date_display_format')
								},
								{
									dataIndex: 'status',
									width: 100
								}
							]
						},
						{
							title: _('allergies'),
							store: Ext.create('App.store.patient.Allergies'),
							itemId: 'CcdPatientAllergiesGrid',
							// selType: 'checkboxmodel',
							flex: 1,
							margin: 0,
							columns: [
								{
									dataIndex: 'allergy',
									flex: 1,
									renderer: function (v, meta, record) {
										return Ext.String.format(
											'ALLERGY: {0} - {1}<br>REACTION: {2} - {3}',
											record.get('allergy_code'),
											record.get('allergy'),
											record.get('reaction_code'),
											record.get('reaction')
										);
									}
								},
								{
									xtype: 'datecolumn',
									dataIndex: 'update_date',
									width: 100,
									format: g('date_display_format')
								},
								{
									dataIndex: 'status',
									width: 100
								}
							]
						}
					]
				},
				{
					xtype: 'panel',
					title: _('import_data'),
					flex: 1,
					frame: true,
					layout: {
						type: 'vbox',
						align: 'stretch'
					},
					defaults: {
						xtype: 'grid',
						height: 123,
						frame: true,
						hideHeaders: true,
						columnLines: true,
						multiSelect: true,
						margin: '0 0 5 0'
					},
					items: [
						{
							xtype: 'form',
							frame: true,
							title: _('patient'),
							itemId: 'CcdImportPatientForm',
							height: 146,
							autoScroll: true,
							layout: 'column',
							items: [
								{
									xtype: 'container',
									defaults: {
										xtype: 'displayfield',
										labelWidth: 45,
										labelAlign: 'right',
										margin: 0
									},
									columnWidth: 0.5,
									items: [
										{
											fieldLabel: _('rec_num'),
											name: 'record_number'
										},
										{
											fieldLabel: _('name'),
											name: 'name'
										},
										{
											fieldLabel: _('sex'),
											name: 'sex'
										},
										{
											fieldLabel: _('dob'),
											name: 'DOBFormatted'
										},
										{
											fieldLabel: _('race'),
											name: 'race_text'
										}
									]
								},
								{
									xtype: 'container',
									defaults: {
										xtype: 'displayfield',
										labelWidth: 60,
										labelAlign: 'right',
										margin: 0
									},
									columnWidth: 0.5,
									items: [
										{
											fieldLabel: _('ethnicity'),
											name: 'ethnicity_text'
										},
										{
											fieldLabel: _('language'),
											name: 'language'
										},
										{
											fieldLabel: _('address'),
											name: 'fulladdress'
										},
										{
											fieldLabel: _('home_phone'),
											name: 'phones'
										}
									]
								}
							]
						},
						{
							title: _('medications'),
							store: Ext.create('App.store.patient.Medications'),
							itemId: 'CcdImportMedicationsGrid',
							selType: 'checkboxmodel',
							flex: 1,
							columns: [
								{
									dataIndex: 'STR',
									flex: 1,
									renderer: function (v, meta, record) {
										return Ext.String.format(
											'MEDICATION: {0} - {1}<br>INSTRUCTIONS: {2}<br>NDC: {3}',
											record.get('RXCUI'),
											record.get('STR'),
											record.get('directions'),
											record.get('NDC')
										);
									}
								},
								{
									xtype: 'datecolumn',
									dataIndex: 'created_date',
									width: 100,
									format: g('date_display_format')
								},
								{
									dataIndex: 'end_date',
									width: 100,
									renderer: function (v, meta, record) {
										if(!v){
											return _('active')
										}
										return _('inactive');
									}
								}
							]
						},
						{
							title: _('active_problems'),
							store: Ext.create('App.store.patient.PatientActiveProblems'),
							itemId: 'CcdImportActiveProblemsGrid',
							selType: 'checkboxmodel',
							flex: 1,
							columns: [
								{
									dataIndex: 'code_text',
									flex: 1,
									renderer: function (v, meta, record) {
										return Ext.String.format(
											'PROBLEM: {0} - {1}',
											record.get('code'),
											record.get('code_text')
										);
									}
								},
								{
									xtype: 'datecolumn',
									dataIndex: 'update_date',
									width: 100,
									format: g('date_display_format')
								},
								{
									dataIndex: 'status',
									width: 100
								}
							]
						},
						{
							title: _('allergies'),
							store: Ext.create('App.store.patient.Allergies'),
							itemId: 'CcdImportAllergiesGrid',
							selType: 'checkboxmodel',
							flex: 1,
							margin: 0,
							columns: [
								{
									dataIndex: 'allergy',
									flex: 1,
									renderer: function (v, meta, record) {
										return Ext.String.format(
											'ALLERGY: {0} - {1}<br>REACTION: {2} - {3}',
											record.get('allergy_code'),
											record.get('allergy'),
											record.get('reaction_code'),
											record.get('reaction')
										);
									}
								},
								{
									xtype: 'datecolumn',
									dataIndex: 'update_date',
									width: 100,
									format: g('date_display_format')
								},
								{
									dataIndex: 'status',
									width: 100
								}
							]
						}
					]
				}
			]
		}
	],
	dockedItems: [
		{
			xtype: 'toolbar',
			dock: 'bottom',
			ui: 'footer',
			items: [
				{
					text: _('view_raw_ccd'),
					itemId: 'CcdImportWindowViewRawCcdBtn'
				},
				'->',
				{
					xtype: 'checkboxfield',
					fieldLabel: _('select_all'),
					labelWidth: 55,
					labelAlign: 'right',
					itemId: 'CcdImportWindowSelectAllField'
				},
				'-',
				{
					text: _('preview'),
					minWidth: 70,
					itemId: 'CcdImportWindowPreviewBtn'
				},
				'-',
				{
					text: _('close'),
					minWidth: 70,
					itemId: 'CcdImportWindowCloseBtn'
				}
			]
		}
	]
});