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

Ext.define('App.view.patient.windows.CCDImportPreview', {
	extend: 'Ext.window.Window',
	xtype: 'ccdimportpreviewwindow',
	title: _('reconciled_preview'),
	bodyStyle: 'background-color:#fff',
	modal: true,
	layout: {
		type: 'vbox',
		align: 'stretch'
	},
	width: 900,
	height: 800,
	autoScroll: true,
	bodyPadding: 5,
	defaults: {
		xtype: 'grid',
		flex: 1,
		frame: true,
		hideHeaders: true,
		columnLines: true,
		multiSelect: true,
		disableSelection: true,
		margin: '0 0 5 0'
	},
	dockedItems: [
		{
			xtype: 'toolbar',
			dock: 'bottom',
			ui: 'footer',
			items: [
				'->',
				{
					text: _('import'),
					minWidth: 70,
					itemId: 'CcdImportPreviewWindowImportBtn'
				},
				'-',
				{
					text: _('cancel'),
					minWidth: 70,
					itemId: 'CcdImportPreviewWindowCancelBtn'
				}
			]
		}
	],
	initComponent: function(){

		var me = this;

		me.items = [
			{
				xtype: 'form',
				frame: true,
				title: _('patient'),
				itemId: 'CcdImportPreviewPatientForm',
				height: 145,
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
								name: 'fulladdress',
								value: 'fulladdress'
							},
							{
								fieldLabel: _('home_phone'),
								name: 'phones',
								value: '000-000-000 (H)'
							}
						]
					}
				]
			},
			{
				title: _('medications'),
				store: Ext.create('App.store.patient.Medications'),
				itemId: 'CcdImportPreviewMedicationsGrid',
				columns: [
					{
						dataIndex: 'STR',
						flex: 1,
						renderer: function (v, meta, record) {
							v = Ext.String.format(
								'MEDICATION: {0} - {1}<br>INSTRUCTIONS: {2}<br>NDC: {3}',
								record.get('RXCUI'),
								record.get('STR'),
								record.get('directions'),
								record.get('NDC')
							);
							return  me.importedRenderer(v, meta, record);
						}
					},
					{
						dataIndex: 'created_date',
						width: 100,
						renderer: me.importedRenderer
					},
					{
						dataIndex: 'end_date',
						width: 100,
						renderer: function (v, meta, record) {
							if(!v){
								v = _('active')
							}else {
								v = _('inactive');
							}
							return me.importedRenderer(v, meta, record);
						}
					}
				]
			},
			{
				title: _('active_problems'),
				store: Ext.create('App.store.patient.PatientActiveProblems'),
				itemId: 'CcdImportPreviewActiveProblemsGrid',
				columns: [
					{
						dataIndex: 'code_text',
						flex: 1,
						renderer: function (v, meta, record) {
							v = Ext.String.format(
								'PROBLEM: {0} - {1}',
								record.get('code'),
								record.get('code_text')
							);
							return me.importedRenderer(v, meta, record)
						}
					},
					{
						dataIndex: 'update_date',
						width: 100,
						renderer: me.importedRenderer
					},
					{
						dataIndex: 'status',
						width: 100,
						renderer: me.importedRenderer
					}
				]
			},
			{
				title: _('allergies'),
				store: Ext.create('App.store.patient.Allergies'),
				itemId: 'CcdImportPreviewAllergiesGrid',
				margin: 0,
				columns: [
					{
						dataIndex: 'allergy',
						flex: 1,
						renderer: function (v, meta, record) {
							v = Ext.String.format(
								'ALLERGY: {0} - {1}<br>REACTION: {2} - {3}',
								record.get('allergy_code'),
								record.get('allergy'),
								record.get('reaction_code'),
								record.get('reaction')
							);
							return me.importedRenderer(v, meta, record);
						}
					},
					{
						dataIndex: 'update_date',
						width: 100,
						renderer: me.importedRenderer
					},
					{
						dataIndex: 'status',
						width: 100,
						renderer: me.importedRenderer
					}
				]
			}
		];

		me.callParent();

	},

	importedRenderer:function(v, meta, record){
		if(!record.data.id || record.data.id === 0){
			meta.tdCls = 'btnBlueBackground'
		}

		return Ext.isDate(v) ? Ext.Date.format(v, g('date_display_format')) : v;
	}

});