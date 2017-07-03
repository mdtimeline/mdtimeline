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

Ext.define('App.view.patient.Patient', {
	extend: 'Ext.panel.Panel',
	requires: [
		'App.ux.AddTabButton',
		'App.view.patient.InsuranceForm'
	],

	layout: 'fit',
	xtype: 'patientdeomgraphics',
	itemId: 'PatientDemographicsPanel',

	newPatient: true,
	pid: null,

	defaultPatientImage: 'resources/images/patientPhotoPlaceholder.jpg',
	defaultQRCodeImage: 'resources/images/QRCodeImage.png',

	initComponent: function(){
		var me = this,
			configs;

		me.store = Ext.create('App.store.patient.Patient');
		me.patientAlertsStore = Ext.create('App.store.patient.MeaningfulUseAlert');
		me.patientContacsStore = Ext.create('App.store.patient.PatientContacts', {
			autoLoad: false
		});

		me.compactDemographics = eval(g('compact_demographics'));
		me.containersWidth = 720;

		configs = {
			items: [
				me.demoForm = Ext.widget('form', {
					action: 'demoFormPanel',
					itemId: 'PatientDemographicForm',
					border: false,
					padding: (me.compactDemographics ? 0 : 10),
					fieldDefaults: {
						labelAlign: 'right',
						msgTarget: 'side'
					},
					layout: 'fit',
					items: [
						{
							xtype: 'tabpanel',
							itemId: 'Demographics',
							border: false,
							defaults: {
								autoScroll: true
							},
							items: [
								{
									xtype: 'panel',
									title: _('patient_info'),
									layout: {
										type: 'column'
									},
									action: 'DemographicWhoFieldSet',
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									items: [
										{
											xtype: 'fieldcontainer',
											layout: 'vbox',
											items: [
												{
													xtype: 'fieldset',
													cls: 'highlight_fieldset',
													margin: '5 0 5 0',
													padding: '15 10 10 10',
													width: me.containersWidth,
													layout: 'hbox',
													defaults: {
														margin: '0 5 0 0',
														labelAlign: 'top'
													},
													items:[
														{
															xtype: 'textfield',
															name: 'pubpid',
															fieldLabel: _('medical'), //external_record
															flex: 1,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'pubaccount',
															fieldLabel: _('account'), //external_account
															flex: 1,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'interface_mrn',
															fieldLabel: _('interface_mrn'), //external_account
															flex: 1,
															enableKeyEvents: true
														}
													]
												},      //MRN ACCNT INTERFACE
												{
													xtype: 'fieldset',
													cls: 'highlight_fieldset',
													margin: '5 0 5 0',
													padding: '15 10 10 10',
													width: me.containersWidth,
													layout: {
														type: 'vbox',
														align: 'stretch'
													},
													items:[
														{
															xtype: 'fieldcontainer',
															layout: 'hbox',
															defaults: {
																margin: '0 5 0 0',
																labelWidth: 50,
																labelAlign: 'top'
															},
															items: [
																{
																	xtype: 'gaiaehr.combo',
																	name: 'title',
																	fieldLabel: _('title'),
																	width: 75,
																	listKey: 'titles',
																	loadStore: true,
																	editable: false
																},
																{
																	xtype: 'textfield',
																	name: 'fname',
																	fieldLabel: _('first_name'),
																	flex: 1,
																	allowBlank: false,
																	maxLength: 35
																},
																{
																	xtype: 'textfield',
																	name: 'mname',
																	fieldLabel: _('init'),
																	width: 50,
																	enableKeyEvents: true,
																	maxLength: 35
																},
																{
																	xtype: 'textfield',
																	name: 'lname',
																	fieldLabel: _('last_name'),
																	flex: 2,
																	allowBlank: false,
																	maxLength: 35
																},
																{
																	xtype: 'gaiaehr.combo',
																	name: 'marital_status',
																	fieldLabel: _('marital_status'),
																	flex: 1,
																	listKey: 'marital_stat',
																	loadStore: true,
																	editable: false
																}
															]
														},
														{
															xtype: 'fieldcontainer',
															layout: 'hbox',
															labelWidth: 50,
															defaults: {
																margin: '0 5 0 0',
																labelWidth: 50,
																labelAlign: 'top'
															},
															items: [
																{
																	xtype: 'gaiaehr.combo',
																	name: 'sex',
																	fieldLabel: _('sex_at_birth'),
																	flex: 1,
																	enableKeyEvents: true,
																	allowBlank: false,
																	listKey: 'sex',
																	loadStore: true,
																	editable: false
																},
																{
																	xtype: 'gaiaehr.combo',
																	name: 'identity',
																	fieldLabel: _('identity'),
																	flex: 2,
																	listKey: 'identity',
																	loadStore: true,
																	editable: false
																},
																{
																	xtype: 'gaiaehr.combo',
																	name: 'orientation',
																	fieldLabel: _('orientation'),
																	flex: 2,
																	listKey: 'orientation',
																	loadStore: true,
																	editable: false
																}
															]
														}
													]
												},      //Name, Sex, Marital Status
												{
													xtype: 'fieldset',
													cls: 'highlight_fieldset',
													margin: '5 0 5 0',
													padding: '15 10 10 10',
													width: me.containersWidth,
													layout: 'hbox',
													defaults: {
														margin: '0 5 0 0',
														labelAlign: 'top'
													},
													items:[
														{
															xtype: 'mitos.datetime',
															name: 'DOB',
															format: 'm/d/Y',
															width: 230,
															fieldLabel: _('dob'),
															enableKeyEvents: true,
															allowBlank: false
														},
														{
															xtype:'fieldcontainer',
															layout: 'hbox',
															fieldLabel:_('multiple_birth'),
															items: [
																{
																	xtype: 'checkbox',
																	name: 'birth_multiple'
																},
																{
																	xtype: 'box',
																	margin: '5 5 5 5',
																	html: 'Yes, birth order'
																},
																{
																	xtype: 'numberfield',
																	name: 'birth_order',
																	width: 50,
																	value: 1,
																	maxValue: 15,
																	minValue: 1
																}
															]
														},
														{
															xtype: 'textfield',
															name: 'birth_place',
															fieldLabel: _('birth_place'),
															flex: 1
														}
													]
												},      //DOB, Multiple, Order, Place
												{
													xtype: 'fieldset',
													cls: 'highlight_fieldset',
													margin: '5 0 5 0',
													padding: '15 10 10 10',
													width: me.containersWidth,
													height: 125,
													layout: {
														type: 'hbox',
														align: 'stretch'
													},
													defaults: {
														labelAlign: 'top',
														flex: 1
													},
													items: [
														{
															xtype: 'fieldcontainer',
															fieldLabel: _('postal_address'),
															margin: '0 5 0 0',
															layout: 'anchor',
															items: [
																{
																	xtype: 'textfield',
																	anchor: '100%',
																	emptyText: _('street'),
																	name: 'postal_address'
																},
																{
																	xtype: 'textfield',
																	anchor: '100%',
																	emptyText: '(' + _('optional') + ')',
																	name: 'postal_address_cont'
																},
																{
																	xtype: 'fieldcontainer',
																	anchor: '100%',
																	layout: 'hbox',
																	defaults: {
																		margin: '0 5 0 0'
																	},
																	items: [
																		{
																			xtype: 'textfield',
																			emptyText: _('city'),
																			width: 119,
																			name: 'postal_city'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: _('state'),
																			width: 30,
																			name: 'postal_state'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: _('zip'),
																			width: 80,
																			name: 'postal_zip'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: _('country'),
																			flex: 1,
																			name: 'postal_country',
																			margin: 0
																		}
																	]
																}
															]
														},
														{
															xtype: 'fieldcontainer',
															fieldLabel: _('physical_address'),
															margin: '0 0 0 5',
															layout: 'anchor',
															items: [
																{
																	xtype: 'textfield',
																	emptyText: _('street'),
																	anchor: '100%',
																	name: 'physical_address'
																},
																{
																	xtype: 'textfield',
																	emptyText: '(' + _('optional') + ')',
																	anchor: '100%',
																	name: 'physical_address_cont'
																},
																{
																	xtype: 'container',
																	layout: 'hbox',
																	anchor: '100%',
																	defaults: {
																		margin: '0 5 0 0'
																	},
																	items: [
																		{
																			xtype: 'textfield',
																			emptyText: _('city'),
																			width: 119,
																			name: 'physical_city'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: _('state'),
																			width: 30,
																			name: 'physical_state'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: _('zip'),
																			width: 80,
																			name: 'physical_zip'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: _('country'),
																			flex: 1,
																			name: 'physical_country',
																			margin: 0
																		}
																	]
																}
															]
														}
													]
												},      //Postal and Physical Address
												{
													xtype: 'fieldset',
													cls: 'highlight_fieldset',
													margin: '5 0 5 0',
													padding: '15 10 10 10',
													width: me.containersWidth,
													layout: 'hbox',
													defaults: {
														margin: '0 5 0 0',
														labelAlign: 'top'
													},
													items: [
														{
															xtype: 'textfield',
															name: 'phone_home',
															emptyText: '000-000-0000',
															fieldLabel: _('home'),
															flex: 1
														},
														{
															xtype: 'textfield',
															name: 'phone_mobile',
															emptyText: '000-000-0000',
															fieldLabel:_('mobile'),
															flex: 1
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'phone_mobile_supplier',
															emptyText: _('supplier'),
															fieldLabel: _('supplier'),
															flex: 1,
															listKey: 'cellular_prov',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'textfield',
															name: 'email',
															emptyText: 'example@email.com',
															fieldLabel:_('email'),
															flex: 2
														}

													]
												},      //Phones, Emails ....
												{
													xtype: 'fieldset',
													cls: 'highlight_fieldset',
													margin: '5 0 5 0',
													padding: '15 10 10 10',
													layout: 'hbox',
													width: me.containersWidth,
													defaults: {
														margin: '0 5 0 0',
														labelAlign: 'top'
													},
													items: [
														{
															xtype: 'gaiaehr.combo',
															name: 'race',
															fieldLabel: _('race'),
															flex: 1,
															listKey: 'race',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'gaiaehr.combo',
															name: _('secondary_race'),
															fieldLabel: _('secondary_race'),
															flex: 1,
															listKey: 'race',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'ethnicity',
															fieldLabel: _('ethnicity'),
															flex: 1,
															listKey: 'ethnicity',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'language',
															fieldLabel: _('language'),
															listKey: 'lang',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'religion',
															fieldLabel: _('religion'),
															flex: 1,
															listKey: 'religion',
															loadStore: true,
															editable: false
														}
													]
												},      //Race, Ethnicity, Language, Religion...
												{
													xtype: 'fieldset',
													cls: 'highlight_fieldset',
													margin: '5 0 5 0',
													padding: '15 10 10 10',
													width: me.containersWidth,
													layout: 'hbox',
													defaults: {
														margin: '0 5 0 0',
														labelAlign: 'top'
													},
													items: [
														{
															xtype: 'activefacilitiescombo',
															name: 'primary_facility',
															flex: 1,
															fieldLabel: _('facility'),
															queryMode: 'local',
															forceSelection: true
														},
														{
															xtype: 'activeproviderscombo',
															name: 'primary_provider',
															fieldLabel: _('provider'),
															flex: 1,
															forceSelection: true
														}
													]
												}       //Facility y Provider
											]
										}
									]
								}, //Demographics
								{
									xtype: 'panel',
									title: _('contacts'),
									itemId: 'DemographicsContactFieldSet',
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									layout: 'vbox',
									items: [
										{
											xtype: 'fieldset',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items:[
												{
													xtype: 'textfield',
													name: 'father_fname',
													fieldLabel: _('first_name') + ' (' + _('father') + ')',
													width: 150,
													maxLength: 35
												},
												{
													xtype: 'textfield',
													name: 'father_mname',
													fieldLabel: _('init'),
													width: 40,
													maxLength: 35
												},
												{
													xtype: 'textfield',
													name: 'father_lname',
													fieldLabel: _('last_name'),
													flex: 1,
													maxLength: 35
												},
												{
													xtype: 'textfield',
													name: 'mother_fname',
													fieldLabel: _('first_name') + ' (' + _('mother') + ')',
													width: 150,
													margin: '0 5 0 10',
													maxLength: 35
												},
												{
													xtype: 'textfield',
													name: 'mother_mname',
													fieldLabel: _('init'),
													width: 40,
													maxLength: 35
												},
												{
													xtype: 'textfield',
													name: 'mother_lname',
													fieldLabel: _('last_name'),
													flex: 1,
													maxLength: 35
												}
											]
										}, //Father and Mother
										{
											xtype: 'fieldset',
											title: _('employer'),
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'textfield',
													name: 'employer_name',
													fieldLabel: _('employer_name'),
													width: 150
												},
												{
													xtype: 'textfield',
													name: 'occupation',
													fieldLabel: _('occupation'),
													width: 125
												},
												{
													xtype: 'textfield',
													name: 'phone_work',
													fieldLabel: _('phone'),
													emptyText: '000-000-0000',
													width: 100
												},
												{
													xtype: 'textfield',
													name: 'phone_work_ext',
													width: 105,
													labelWidth: 30,
													fieldLabel: _('ext') + '.',
												},
												{
													xtype: 'textfield',
													name: 'phone_fax',
													fieldLabel: _('fax'),
													emptyText: '000-000-0000',
													width: 160,
													labelWidth: 30,
													fieldLabel: _('fax')
												}
											]
										}, //Employer
										{
											xtype: 'fieldset',
											title: _('persons_authorized'),
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'vbox',
											items:[
												{
													xtype: 'fieldcontainer',
													layout: 'hbox',
													defaults: {
														labelWidth: 50,
														margin: '5 0 5 5',
														labelAlign: 'top'
													},
													items: [
														{
															xtype: 'gaiaehr.combo',
															name: 'authorized_01_relation',
															fieldLabel: _('relationship'),
															width: 125,
															listKey: 'rela_hl70063',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'textfield',
															name: 'authorized_01_fname',
															fieldLabel: _('first_name'),
															width: 100,
															//fieldLabel: _('name'),
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'authorized_01_mname',
															fieldLabel: _('init'),
															width: 40,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'authorized_01_lname',
															fieldLabel: _('last_name'),
															width: 180,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'authorized_01_phone',
															fieldLabel: _('phone'),
															emptyText: '000-000-0000',
															width: 90
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'authorized_01_phone_type',
															fieldLabel: _('phone_type'),
															width: 113,
															listKey: 'phone_type',
															loadStore: true,
															editable: false
														}
													]
												},
												{
													xtype: 'fieldcontainer',
													layout: 'hbox',
													defaults: {
														labelWidth: 50,
														margin: '5 0 5 5',
														labelAlign: 'top'
													},
													items: [
														{
															xtype: 'gaiaehr.combo',
															name: 'authorized_02_relation',
															fieldLabel: _('relationship'),
															width: 125,
															listKey: 'rela_hl70063',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'textfield',
															name: 'authorized_02_fname',
															fieldLabel: _('first_name'),
															width: 100,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'authorized_02_mname',
															fieldLabel: _('init'),
															width: 40,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'authorized_02_lname',
															fieldLabel: _('last_name'),
															width: 180,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'authorized_02_phone',
															fieldLabel: _('phone'),
															emptyText: '000-000-0000',
															width: 90
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'authorized_02_phone_type',
															fieldLabel: _('phone_type'),
															width: 113,
															listKey: 'phone_type',
															loadStore: true,
															editable: false
														}
													]
												}, //Persons Authorized to Pickup Results
											]
										},
										{
											xtype: 'fieldset',
											title: _('emer_contact'),
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: {
												type: 'vbox',
												align: 'stretch'
											},
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'fieldcontainer',
													layout: 'hbox',
													defaults: {
														margin: '5 0 5 5',
														labelWidth: 50,
														labelAlign: 'left'
													},
													items: [
														{
															xtype: 'gaiaehr.combo',
															name: 'emergency_contact_relation',
															emptyText: _('relationship'),
															width: 125,
															listKey: 'rela_hl70063',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_fname',
															emptyText: _('first_name'),
															width: 100,
															//fieldLabel: _('name'),
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_mname',
															emptyText: _('middle_name'),
															width: 40,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_lname',
															emptyText: _('last_name'),
															width: 180,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_phone',
															emptyText: '000-000-0000',
															width: 90
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'emergency_contact_phone_type',
															emptyText: _('phone_type'),
															width: 113,
															listKey: 'phone_type',
															loadStore: true,
															editable: false
														}
													]
												},
												{
													xtype: 'fieldcontainer',
													layout: 'hbox',
													defaults: {
														margin: '5 0 5 5',
														labelWidth: 50,
														labelAlign: 'left'
													},
													items: [
														{
															xtype: 'textfield',
															name: 'emergency_contact_address',
															emptyText: _('street'),
															width: 170
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_address_cont',
															emptyText: '(' + _('optional') + ')',
															width: 170
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_city',
															emptyText: _('city'),
															width: 90
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_state',
															emptyText: _('state'),
															width: 30
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_zip',
															emptyText: _('zip'),
															width: 80
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_country',
															emptyText: _('country'),
															width: 90
														}
													]
												}
											]
										}, //Emergency
										{
											xtype: 'fieldset',
											title: _('guardians_contact'),
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'vbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'fieldcontainer',
													layout: 'hbox',
													defaults: {
														margin: '5 0 5 5',
														labelWidth: 50,
														labelAlign: 'left'
													},
													items: [
														{
															xtype: 'gaiaehr.combo',
															name: 'guardians_relation',
															emptyText: _('relationship'),
															width: 125,
															listKey: 'rela_hl70063',
															loadStore: true,
															editable: false
														},
														{
															xtype: 'textfield',
															name: 'guardians_fname',
															emptyText: _('first_name'),
															width: 100,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'guardians_mname',
															emptyText: _('middle_name'),
															width: 40,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'guardians_lname',
															emptyText: _('last_name'),
															width: 180,
															enableKeyEvents: true
														},
														{
															xtype: 'textfield',
															name: 'guardians_phone',
															emptyText: '000-000-0000',
															width: 90
														},
														{
															xtype: 'gaiaehr.combo',
															name: 'guardians_phone_type',
															emptyText: _('phone_type'),
															width: 113,
															listKey: 'phone_type',
															loadStore: true,
															editable: false
														}
													]
												},
												{
													xtype: 'fieldcontainer',
													layout: 'hbox',
													defaults: {
														margin: '5 0 5 5',
														labelWidth: 50,
														labelAlign: 'top'
													},
													items: [
														{
															xtype: 'textfield',
															name: 'guardians_address',
															emptyText: _('street'),
															width: 170
														},
														{
															xtype: 'textfield',
															name: 'guardians_address_cont',
															emptyText: '(' + _('optional') + ')',
															width: 170
														},
														{
															xtype: 'textfield',
															name: 'guardians_city',
															emptyText: _('city'),
															width: 90
														},
														{
															xtype: 'textfield',
															name: 'guardians_state',
															emptyText: _('state'),
															width: 30
														},
														{
															xtype: 'textfield',
															name: 'guardians_zip',
															emptyText: _('zip'),
															width: 80
														},
														{
															xtype: 'textfield',
															name: 'guardians_country',
															emptyText: _('country'),
															width: 90
														}
													]
												}
											]
										}  //Guardian
									]
								}, //Contacts
								{
									xtype: 'panel',
									title: _('communication'),
									action: 'DemographicWhoFieldSet',
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									layout: 'vbox',
									items: [
										{
											xtype: 'fieldset',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'gaiaehr.combo',
													name: 'phone_publicity',
													fieldLabel: _('publicity'),
													emptyText: _('publicity'),
													width: 450,
													listKey: 'publicity_code',
													loadStore: true,
													editable: false,
													margin: '10 0 5 0'
												}
											]
										}, //Publicity
										{
											xtype: 'fieldset',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'mitos.pharmaciescombo',
													name: 'pharmacy',
													fieldLabel: _('pharmacy'),
													emptyText: _('pharmacy'),
													width: 450,
													margin: '10 0 5 0',
													forceSelection: true
												}
											]
										}, //Pharmacy
										{
											xtype: 'fieldset',
											title: _('allow'),
											fieldLabel:'allow',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'checkbox',
													name: 'allow_sms',
													flex: 1,
													boxLabel: _('text_mobile_msg'),
													margin: '5 0 0 15',
													labelWidth: 100
												},
												{
													xtype: 'checkbox',
													name: 'allow_voice_msg',
													boxLabel: _('voice_msg'),
													flex: 1,
													labelWidth: 95
												},
												{
													xtype: 'checkbox',
													name: 'allow_email',
													boxLabel: _('email'),
													flex: 1,
													labelWidth: 70
												},
												{
													xtype: 'checkbox',
													name: 'allow_mail_msg',
													boxLabel: _('mail_msg'),
													flex: 1,
													labelWidth: 85
												}
											]
										}, //Allow Phones, Allow Emails
										{
											xtype: 'fieldset',
											title: _('allow'),
											fieldLabel:'allow',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'fieldset',
													checkboxName: 'allow_patient_web_portal',
													title: _('patient_access_web_portal'),
													checkboxToggle: true,
													width: 225,
													margin: '5 0 5 0',
													items: [
														{
															xtype: 'textfield',
															name: 'portal_username',
															fieldLabel: _('username'),
															width: 200,
															labelWidth: 60
														},
														{
															xtype: 'textfield',
															name: 'portal_password',
															fieldLabel: _('password'),
															inputType: 'password',
															width: 200,
															labelWidth: 60
														}
													]
												}, //Access Patient Web Portal
												{
													xtype: 'fieldset',
													title: _('emergency_access_web_portal'),
													checkboxName: 'allow_emergency_contact_web_portal',
													checkboxToggle: true,
													width: 225,
													margin: '5 0 5 10',
													items: [
														{
															xtype: 'textfield',
															name: 'emergency_contact_portal_username',
															fieldLabel: _('username'),
															width: 200,
															labelWidth: 70
														},
														{
															xtype: 'textfield',
															name: 'emergency_contact_portal_password',
															fieldLabel: _('password'),
															inputType: 'password',
															width: 200,
															labelWidth: 70
														},
														{
															xtype: 'checkbox',
															fieldLabel: _('view_record'),
															name: 'allow_emergency_contact_web_portal_cda',
															labelWidth: 70
														}
													]
												}, //Access Emergency Web Portal
												{
													xtype: 'fieldset',
													title: _('guardian_access_web_portal'),
													checkboxName: 'allow_guardian_web_portal',
													checkboxToggle: true,
													collapsible: false,
													width: 225,
													margin: '5 0 5 10',
													items: [
														{
															xtype: 'textfield',
															name: 'guardian_portal_username',
															fieldLabel: _('username'),
															width: 200,
															labelWidth: 70
														},
														{
															xtype: 'textfield',
															name: 'guardian_portal_password',
															fieldLabel: _('password'),
															inputType: 'password',
															width: 200,
															labelWidth: 70
														},
														{
															xtype: 'checkbox',
															fieldLabel: _('view_record'),
															name: 'allow_guardian_web_portal_cda',
															labelWidth: 70
														}
													]
												}  //Guardian Web Portal
											]
										}, //Allow Web Access - Portal
										{
											xtype: 'fieldset',
											title: _('allow'),
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'checkbox',
													name: 'allow_immunization_info_sharing',
													boxLabel: _('immunization_info_sharing'),
													width: 225,
													margin: '0 5 0 15'
												},
												{
													xtype: 'checkbox',
													name: 'allow_immunization_registry',
													boxLabel: _('immunization_registry_use'),
													width: 225,
													margin: '0 5 0 5'
												},
												{
													xtype: 'checkbox',
													name: 'allow_health_info_exchange',
													boxLabel: _('health_information_exchange'),
													width: 225,
													margin: '0 5 0 5'
												}
											]
										} //Allow Immunization Sharing, Registry, HIE
									]
								}, //Communication
								{
									xtype: 'panel',
									title: _('aditional_info')+'.',
									action: 'DemographicWhoFieldSet',
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									layout: 'vbox',
									items: [
										{
											xtype: 'fieldset',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'textfield',
													name: 'alias',
													fieldLabel: _('alias_name'),
													flex: 1,
													labelWidth: 100,
													hideLabel: false
												},
												{
													xtype: 'gaiaehr.combo',
													name: 'citizenship',
													fieldLabel: _('citizenship'),
													hideLabel: false,
													flex: 1,
													labelWidth: 60,
													listKey: 'citiz',
													loadStore: true,
													editable: false
												},
												{
													xtype: 'gaiaehr.combo',
													fieldLabel: _('veteran'),
													boxLabel: 'Yes',
													name: 'is_veteran',
													flex: 1,
													loadStore: true,
													editable: false
												}
											]
										}, //Alias Name, Citizen, Veteran
										{
											xtype: 'fieldset',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'textfield',
													fieldLabel: _('social_security'),
													emptyText: _('social_security'),
													name: 'SS',
													flex: 1
												},
												{
													xtype: 'textfield',
													emptyText: _('license_no'),
													fieldLabel: _('drivers_info'),
													enableKeyEvents: true,
													flex: 1,
													name: 'drivers_license'
												},
												{
													xtype: 'gaiaehr.combo',
													name: 'drivers_license_state',
													emptyText: _('license'),
													fieldLabel: _('state'),
													flex: 1,
													listKey: 'state',
													loadStore: true,
													editable: false
												},
												{
													xtype: 'datefield',
													name: 'drivers_license_exp',
													fieldLabel: _('expiration'),
													emptyText: _('license'),
													flex: 1,
													format: 'Y-m-d'
												}
											]
										}, //SocSec, Drivers Info
										{
											xtype: 'fieldset',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'checkbox',
													name: 'deceased',
													fieldLabel: _('deceased'),
													boxLabel: _('yes')
												},
												{
													xtype: 'mitos.datetime',
													name: 'death_date',
													fieldLabel: _('death_date'),
													hideLabel: false,
													width: 200
												},
												{
													xtype: 'textfield',
													name: 'death_cause',
													fieldLabel: _('cause'),
													flex: 1
												}
											]
										}, //Deceased and Date
										{
											xtype: 'fieldset',
											cls: 'highlight_fieldset',
											margin: '5 0 5 0',
											padding: '15 10 10 10',
											width: me.containersWidth,
											layout: 'hbox',
											defaults: {
												margin: '0 5 0 0',
												labelAlign: 'top'
											},
											items: [
												{
													xtype: 'gaiaehr.combo',
													fieldLabel: _('hipaa_notice'),
													name: 'hipaa_notice',
													flex: 1,
													listKey: 'boolean',
													loadStore: true,
													editable: false
												},
												{
													xtype: 'gaiaehr.combo',
													name: 'organ_donor_code',
													fieldLabel: _('organ_donor'),
													listKey: 'proc_lateral',
													flex: 1,
													loadStore: true,
													editable: false
												}
											]
										} //Hipaa Notice, Organ Donor
									]
								}  //Additional Info
							]
						}
					]
				}),
			]
		};

		configs.bbar = [
			{
				xtype: 'button',
				action: 'readOnly',
				text: _('possible_duplicates'),
				minWidth: 75,
				itemId: 'PatientPossibleDuplicatesBtn'
			},
			'-',
			{
				xtype: 'button',
				action: 'readOnly',
				text: _('merge_record'),
				minWidth: 75,
				acl: a('allow_merge_patients'),
				itemId: 'PatientMergeBtn'
			},
			'-',
			'->',
			'-',
			{
				xtype: 'button',
				action: 'readOnly',
				text: _('save'),
				itemId: 'PatientDemographicSaveBtn',
				minWidth: 75,
				scope: me,
				handler: me.formSave
			},
			'-',
			{
				xtype: 'button',
				text: _('cancel'),
				action: 'readOnly',
				itemId: 'PatientDemographicCancelBtn',
				minWidth: 75,
				scope: me,
				handler: me.formCancel
			}
		];

		configs.listeners = {
			scope: me,
			beforerender: me.beforePanelRender
		};

		Ext.apply(me, configs);

		me.callParent(arguments);

		Ext.Function.defer(function(){

			me.insTabPanel = Ext.widget('tabpanel', {
				itemId: 'PatientInsurancesPanel',
				flex: 1,
				defaults: {
					autoScroll: true,
					padding: 10
				},
				plugins: [
					{
						ptype: 'AddTabButton',
						iconCls: 'icoAdd',
						toolTip: _('new_insurance'),
						btnText: _('add_insurance'),
						forceText: true,
						panelConfig: {
							xtype: 'patientinsuranceform'
						}
					}
				],
				listeners: {
					scope: me,
					beforeadd: me.insurancePanelAdd
				}
			});

			me.insTabPanel.title = _('insurance');
			me.insTabPanel.addDocked({
				xtype: 'toolbar',
				dock: 'bottom',
				items: [
					'->',
					'-',
					{
						xtype: 'button',
						action: 'readOnly',
						text: _('save'),
						minWidth: 75,
						scope: me,
						handler: me.formSave
					},
					'-',
					{
						xtype: 'button',
						text: _('cancel'),
						action: 'readOnly',
						minWidth: 75,
						scope: me,
						handler: me.formCancel
					}
				]
			});

			me.up('tabpanel').insert(1, me.insTabPanel);
		}, 300);
	},

	beforePanelRender: function(){
		var me = this,
			whoPanel,
			form = me.demoForm.getForm(),
			fname = form.findField('fname'),
			mname = form.findField('mname'),
			lname = form.findField('lname'),
			sex = form.findField('sex'),
			dob = form.findField('DOB'),
			crtl;

		if(fname) fname.vtype = 'nonspecialcharacters';
		if(mname) mname.vtype = 'nonspecialcharacters';
		if(lname) lname.vtype = 'nonspecialcharacters';

		if(dob) dob.setMaxValue(new Date());

		if(me.newPatient){
			crtl = App.app.getController('patient.Patient');
			fname.on('blur', crtl.checkForPossibleDuplicates, crtl);
			lname.on('blur', crtl.checkForPossibleDuplicates, crtl);
			sex.on('blur', crtl.checkForPossibleDuplicates, crtl);
			dob.dateField.on('blur', crtl.checkForPossibleDuplicates, crtl);
		}else{
			whoPanel = me.demoForm.query('[action=DemographicWhoFieldSet]')[0];
			whoPanel.insert(0,
				me.patientImages = Ext.create('Ext.panel.Panel', {
					action: 'patientImage',
					layout: 'vbox',
					style: 'float:right;',
					bodyPadding: 10,
					height: 300,
					width:180,
					items: [
						{
							xtype: 'image',
							itemId: 'image',
							imageAlign: 'center',
							width: 150,
							height: 120,
							margin: '0 5 10 5',
							src: me.defaultPatientImage
						},
						{
							xtype: 'textareafield',
							name: 'image',
							hidden: true
						},
						{
							xtype: 'image',
							itemId: 'qrcode',
							imageAlign: 'center',
							width: 150,
							height: 120,
							margin: '0 5 10 5',
							src: me.defaultQRCodeImage
						}
					],
					bbar: [
						'-',
						{
							text: _('take_picture'),
							action: 'onWebCam'
							//handler: me.getPhotoIdWindow
						},
						'-',
						'->',
						'-',
						{
							text: _('print_qrcode'),
							scope: me,
							handler: function () {
								window.printQRCode(app.patient.pid);
							}
						},
						'-'
					]
				})
			);
		}
	},

	onAddNewContact: function(btn){
		var grid = btn.up('grid'),
			store = grid.store,
			record;

		record = {
			created_date: new Date(),
			pid: app.patient.pid,
			uid: app.user.id
		};

		grid.plugins[0].cancelEdit();
		store.insert(0, record);
		grid.plugins[0].startEdit(0, 0);
	},

	insurancePanelAdd: function(tapPanel, panel){
		var me = this,
			record = panel.insurance || Ext.create('App.model.patient.Insurance', {pid: me.pid});

		panel.title = _('insurance') + ' (' + (record.data.insurance_type ? record.data.insurance_type : _('new')) + ')';

		me.insuranceFormLoadRecord(panel, record);
		if(record.data.image !== '') panel.down('image').setSrc(record.data.image);
	},

	insuranceFormLoadRecord: function(form, record){
		form.getForm().loadRecord(record);
		app.fireEvent('insurancerecordload', form, record);
	},

	getValidInsurances: function(){
		var me = this,
			forms = me.insTabPanel.items.items,
			records = [],
			form,
			rec;

		for(var i = 0; i < forms.length; i++){
			form = forms[i].getForm();
			if(!form.isValid()){
				me.insTabPanel.setActiveTab(forms[i]);
				return false;
			}
			rec = form.getRecord();
			app.fireEvent('beforepatientinsuranceset', form, rec);
			rec.set(form.getValues());
			app.fireEvent('afterpatientinsuranceset', form, rec);
			records.push(rec);
		}
		return records;
	},

	getPatientImages: function(record){
		var me = this;
		if(me.patientImages){
			me.patientImages.getComponent('image').setSrc(
				(record.data.image !== '' ? record.data.image : me.defaultPatientImage)
			);
		}
		if(me.patientImages){
			me.patientImages.getComponent('qrcode').setSrc(
				(record.data.qrcode !== '' ? record.data.qrcode : me.defaultQRCodeImage)
			);
		}
	},

	getPatientContacts: function(pid){
		var me = this;

		me.patientContacsStore.clearFilter(true);
		me.patientContacsStore.load({
			params: {
				pid: pid
			},
			filters: [
				{
					property: 'pid',
					value: pid
				}
			]
		});
	},

	/**
	 * verify the patient required info and add a yellow background if empty
	 */
	verifyPatientRequiredInfo: function(){
		var me = this,
			field;
		me.patientAlertsStore.load({
			scope: me,
			params: {pid: me.pid},
			callback: function(records, operation, success){
				for(var i = 0; i < records.length; i++){
					field = me.demoForm.getForm().findField(records[i].data.name);
					if(records[i].data.val){
						if(field) field.removeCls('x-field-yellow');
					}else{
						if(field) field.addCls('x-field-yellow');
					}
				}
			}
		});
	},

	/**
	 * allow to edit the field if the filed has no data
	 * @param fields
	 */
	readOnlyFields: function(fields){
		// for(var i = 0; i < fields.items.length; i++){
		//    var f = fields.items[i], v = f.getValue(), n = f.name;
		//    if(n == 'SS' || n == 'DOB' || n == 'sex'){
		//        if(v == null || v == ''){
		//            f.setReadOnly(false);
		//        }else{
		//            f.setReadOnly(true);
		//        }
		//    }
		// }
	},

	getValidInsurances: function(){
		var me = this,
			forms = Ext.ComponentQuery.query('#PatientInsurancesPanel')[0].items.items,
			records = [],
			form,
			rec;

		for(var i = 0; i < forms.length; i++){
			form = forms[i].getForm();
			if(!form.isValid()){
				me.insTabPanel.setActiveTab(forms[i]);
				return false;
			}
			rec = form.getRecord();
			app.fireEvent('beforepatientinsuranceset', form, rec);
			rec.set(form.getValues());
			app.fireEvent('afterpatientinsuranceset', form, rec);
			records.push(rec);
		}
		return records;
	},

	formSave: function(){
		var me = this,
			form = me.demoForm.getForm(),
			record = form.getRecord(),
			values = form.getValues();
		insRecs = me.getValidInsurances();

		if(form.isValid() && insRecs !== false){
			record.set(values);

			// fire global event
			app.fireEvent('beforedemographicssave', record, me);

			record.save({
				scope: me,
				callback: function(record){

					app.setPatient(record.data.pid, null, null, function(){

						var insStore = record.insurance();

						for(var i = 0; i < insRecs.length; i++){
							if(insRecs[i].data.id === 0){
								insStore.add(insRecs[i]);
							}
						}

						insStore.sync();

						if(me.newPatient){
							app.openPatientSummary();
						}else{
							me.getPatientImages(record);
							me.verifyPatientRequiredInfo();
							me.readOnlyFields(form.getFields());
						}
					});

					// fire global event
					app.fireEvent('afterdemographicssave', record, me);
					me.msg(_('sweet'), _('record_saved'));
				}
			});
		}else{
			me.msg(_('oops'), _('missing_required_data'), true);
		}
	},

	formCancel: function(btn){
		var me = this,
			form = me.demoForm.getForm(),
			record = form.getRecord();
		form.loadRecord(record);
	},

	loadNew: function(){
		var patient = Ext.create('App.model.patient.Patient', {
			'create_uid': app.user.id,
			'update_uid': app.user.id,
			'create_date': new Date(),
			'update_date': new Date(),
			'DOB': '0000-00-00 00:00:00'
		});
		this.demoForm.getForm().loadRecord(patient);
	},

	loadPatient: function(pid){
		var me = this,
			form = me.demoForm.getForm();

		me.pid = pid;

		form.reset();

		me.getPatientContacts(pid);

		me.getPatientImages(app.patient.record);

		app.patient.record.insurance().load({
			filters: [
				{
					property: 'pid',
					value: app.patient.record.data.pid
				}
			],
			callback: function(records){

				form.loadRecord(app.patient.record);
				me.setReadOnly(app.patient.readOnly);
				me.setButtonsDisabled(me.query('button[action="readOnly"]'));
				me.verifyPatientRequiredInfo();
				me.insTabPanel = Ext.ComponentQuery.query('#PatientInsurancesPanel')[0];

				// set the insurance panel
				me.insTabPanel.removeAll(true);
				for(var i = 0; i < records.length; i++){
					me.insTabPanel.add(
						Ext.widget('patientinsuranceform', {
							closable: false,
							insurance: records[i]
						})
					);
				}
				if(me.insTabPanel.items.length !== 0) me.insTabPanel.setActiveTab(0);
			}
		});
	}
});