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

Ext.define('App.view.patient.Search', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('patient_search'),
	pageLayout: 'border',
	itemId: 'PatientSearchPanel',
	requires: [
		'Ext.form.field.Date',
		'Ext.ux.SlidingPager',
		'App.ux.combo.ActiveProviders',
		'App.ux.LiveSnomedProblemMultipleSearch',
		'App.ux.LiveRXNORMAllergyMultipleSearch',
		'App.ux.LiveRXNORMMultipleSearch',
		'App.ux.LiveProviderMultipleSearch',
		'App.ux.LiveSexMultipleSearch',
		'App.ux.LiveEthnicityMultipleSearch',
		'App.ux.LiveRaceMultipleSearch',
		'App.ux.LiveMaritalMultipleSearch',
		'App.ux.LiveLanguageMultipleSearch',
		'App.ux.LivePhoneCommunicationMultipleSearch',
		'App.ux.LabResultValuesFilter'
	],
	initComponent: function () {
		var me = this;

		me.search_store = Ext.create('App.store.patient.PatientSearch', {
			remoteSort: true
		});

		me.pageBody = [
			{
				xtype: 'form',
				region: 'west',
				collapsible: true,
				border: true,
				split: true,
				bodyPadding: 5,
				width: 300,
				itemId: 'PatientSearchFrom',
				defaults: {
					enableReset: true,
					anchor: '100%',
					labelAlign: 'top'
				},
				items: [
					{
						xtype: 'datefield',
						name: 'date_from',
						fieldLabel: _('date_from'),
						labelWidth: 100,
						format: g('date_display_format'),
						allowBlank : false,
						submitFormat: 'Y-m-d',
						value: new Date()
					},
					{
						xtype: 'datefield',
						name: 'date_to',
						fieldLabel: _('date_to'),
						labelWidth: 100,
						allowBlank : false,
						format: g('date_display_format'),
						submitFormat: 'Y-m-d',
						value: new Date()
					},
					{
						xtype:'fieldset',
						title: _('patient'),
						defaults: {
							enableReset: true,
							anchor: '100%',
							labelAlign: 'top'
						},
						items: [
							{
								xtype: 'textfield',
								name: 'lname',
								emptyText: _('last_name')
							},
							{
								xtype: 'textfield',
								name: 'fname',
								emptyText: _('first_name')
							},
							{
								xtype: 'container',
								layout: 'hbox',
								margin: '0 0 5 0',
								items: [
									{
										xtype: 'numberfield',
										flex: 1,
										name: 'ageFrom',
										emptyText: _('ageFrom')
									},
									{
										xtype: 'numberfield',
										flex: 1,
										name: 'ageTo',
										emptyText: _('ageTo')
									}
								]
							},
							{
								xtype: 'racemultiple',
								name: 'race'
							},
							{
								xtype: 'ethnicitymultiple',
								name: 'ethnicity'
							},
							{
								xtype: 'sexmultiple',
								name: 'sex'
							},
							{

								xtype: 'communicationmultiple',
								name: 'phone_publicity'
							},
							{

								xtype: 'maritalmultiple',
								name: 'marital_status'
							},
							{

								xtype: 'languagemultiple',
								name: 'language'
							}
						]
					},

					{
						xtype: 'liveprovidermultiple',
						name: 'providers'
					},
					{
						xtype: 'liverxnormallergymultiple',
						name: 'allergy_codes',
						displayField: 'STR',
						valueField: 'RXCUI'
					},
					{
						xtype: 'livesnomedproblemmultiple',
						name: 'problem_codes'
					},
					{
						xtype: 'liverxnormmultiple',
						name: 'medication_codes',
						displayField: 'STR',
						valueField: 'RXCUI'
					},
					{
						xtype: 'labresultvalues',
						submitValue: true,
						name: 'lab_results'
					}
				],
				bbar: [
					{
						text: _('search'),
						itemId: 'PatientSearchFromSearchBtn',
						flex: 1
					}
				]
			},
			{
				xtype: 'grid',
				region: 'center',
				frame: true,
				title: _('results'),
				itemId: 'PatientSearchGrid',
				store: me.search_store,
				columns: [
					{
						text: _('record_number'),
						dataIndex: 'pubpid'
					},
					{
						text: _('date'),
						dataIndex: 'service_date'
					},
					{
						text: _('patient'),
						dataIndex: 'lname',
						renderer: function (v,meta,rec) {
							return Ext.String.format(
								'{0}, {1} {2}',
								rec.get('lname'),
								rec.get('fname'),
								rec.get('mname')
							);
						}
					},
					{
						text: _('sex'),
						dataIndex: 'sex',
						renderer: function (v) {
							if(v == 'M'){
								return 'Male';
							}else  if(v == 'F'){
								return 'Female';
							}
							return v;
						}
					},
					{
						text: _('language'),
						dataIndex: 'language'
					},
					{
						text: _('dob') + ' (age)',
						dataIndex: 'DOB',
						renderer: function (v,meta,rec) {
							return Ext.String.format(
								'{0} ({1})',
								rec.get('DOBFormatted'),
								rec.getAge(rec.get('DOB'))
							);
						}
					},
					{
						text: _('communication'),
						dataIndex: 'phone_publicity',
						renderer: function (v) {
							if(v == '01'){
								return 'No reminder/recall';
							}else  if(v == '02'){
								return 'Reminder/recall - any method';
							}else  if(v == '03'){
								return 'Reminder/recall - no calls';
							}else  if(v == '04'){
								return 'Reminder only - any method';
							}else  if(v == '05'){
								return 'Reminder only - no calls';
							}else  if(v == '06'){
								return 'Recall only - any method';
							}else  if(v == '07'){
								return 'Recall only - no calls';
							}else  if(v == '08'){
								return 'Reminder/recall - to provider';
							}else  if(v == '09'){
								return 'Reminder to provider';
							}else  if(v == '10'){
								return 'Only reminder to provider no recall';
							}else  if(v == '11'){
								return 'Recall to provider';
							}else  if(v == '12'){
								return 'Only recall to provider no reminder';
							}
							return v;
						}
					},
					{
						text: _('marital_status'),
						dataIndex: 'marital_status',
						renderer: function (v) {
							if(v == 'M'){
								return 'Married';
							}else  if(v == 'S'){
								return 'Single';
							}else  if(v == 'D'){
								return 'Divorced';
							}else  if(v == 'W'){
								return 'Widowed';
							}else  if(v == 'A'){
								return 'Separated';
							}else  if(v == 'P'){
								return 'Domestic Partner';
							}else  if(v == 'O'){
								return 'Other';
							}
							return v;
						}
					},
					{
						text: _('race'),
						dataIndex: 'race',
						renderer: function (v) {
							if(v == '1002-5'){
								return 'American Indian or Alaska Native';
							}else  if(v == '2028-9'){
								return 'Asian';
							}else  if(v == '2054-5'){
								return 'Black or African American';
							}else  if(v == '2076-8'){
								return 'Native Hawaiian or Other Pacific Islander';
							}else  if(v == '2106-3'){
								return 'White';
							}else  if(v == 'ASKU'){
								return 'Declined to specify';
							}else  if(v == '2131-1'){
								return 'Other Race';
							}
							return v;
						}
					},
					{
						text: _('ethnicity'),
						dataIndex: 'ethnicity',
						renderer: function (v) {
							if(v == 'H'){
								return 'Hispanic or Latino';
							}else  if(v == 'N'){
								return 'Not Hispanic or Latino';
							}else  if(v == 'ASKU'){
								return 'Declined to specify';
							}else  if(v == 'U') {
								return 'Unknown';
							}
							return v;
						}
					},
					{
						text: _('providers'),
						dataIndex: 'providers',
						width: 200
					},
					{
						text: _('allergies'),
						dataIndex: 'allergies',
						width: 200
					},
					{
						text: _('problems'),
						dataIndex: 'problems',
						width: 200
					},
					{
						text: _('medications'),
						dataIndex: 'medications',
						width: 200
					},
					{
						text: _('lab_results'),
						dataIndex: 'lab_results',
						width: 200
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 10,
					store: me.search_store,
					displayInfo: true,
					plugins: new Ext.ux.SlidingPager()
				}
			}
		];

		me.callParent(arguments);

	}
});
