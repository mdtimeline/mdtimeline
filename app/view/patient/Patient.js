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
	layout: {
		type: 'vbox',
		align: 'stretch'
	},
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

		configs = {
			items: [
				me.demoForm = Ext.widget('form', {
					action: 'demoFormPanel',
					itemId: 'PatientDemographicForm',
					type: 'anchor',
					border: false,
					autoScroll: true,
					padding: (me.compactDemographics ? 0 : 10),
					fieldDefaults: {
						labelAlign: 'right',
						msgTarget: 'side'
					},
					items: [
						{
							xtype: (me.compactDemographics ? 'tabpanel' : 'panel'),
							itemId: 'Demographics',
							border: false,
							height: 300,
							defaults: {
								autoScroll: true
							},
							items: [
								{
									xtype: 'panel',
									title: _('demographics'),
									hideLabel: false,
									collapsible: true,
									enableKeyEvents: true,
									checkboxToggle: false,
									collapsed: false,
									action: 'DemographicWhoFieldSet',
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									items: [
										{
											xtype: 'fieldcontainer',
											fieldLabel: 'Extermal IDs Rec# Acc#',
											labelWidth: 149,
											hideLabel: false,
											layout: 'hbox',
											width: 660,
											items: [
												{
													xtype: 'textfield',
													fieldLabel: 'External Rec#',
													emptyText: 'External Rec#',
													labelWidth: 149,
													hideLabel: true,
													enableKeyEvents: true,
													width: 175,
													margin: '0 5 0 0',
													name: 'pubpid'
												},
												{
													xtype: 'textfield',
													fieldLabel: 'External Acc#',
													emptyText: 'External Acc#',
													hideLabel: true,
													enableKeyEvents: true,
													width: 175,
													name: 'pubaccount'
												}
											]
										},
										{
											xtype: 'fieldcontainer',
											fieldLabel: _('fullname'),
											labelWidth: 149,
											layout: 'hbox',
											width: 660,
											items: [
												{
													xtype: 'gaiaehr.combo',
													emptyText: _('title'),
													width: 70,
													margin: '0 5 0 0',
													name: 'title',
													list: 22,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'textfield',
													emptyText: _('first_name'),
													width: 100,
													margin: '0 5 0 0',
													allowBlank: false,
													maxLength: 35,
													name: 'fname'
												},
												{
													xtype: 'textfield',
													emptyText: _('middle_name'),
													enableKeyEvents: true,
													width: 100,
													margin: '0 5 0 0',
													maxLength: 35,
													name: 'mname'
												},
												{
													xtype: 'textfield',
													emptyText: _('last_name'),
													width: 215,
													margin: '0 5 0 0',
													allowBlank: false,
													maxLength: 35,
													name: 'lname'
												}
											]

										},
										{
											xtype: 'fieldcontainer',
											fieldLabel: 'Sex DOB Status S.S.',
											labelWidth: 149,
											hideLabel: false,
											layout: 'hbox',
											width: 660,
											collapsible: false,
											checkboxToggle: false,
											collapsed: false,
											items: [
												{
													xtype: 'gaiaehr.combo',
													fieldLabel: _('sex'),
													hideLabel: true,
													enableKeyEvents: true,
													emptyText: _('sex'),
													name: 'sex',
													width: 70,
													margin: '0 5 0 0',
													allowBlank: false,
													list: 95,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'mitos.datetime',
													emptyText: _('dob'),
													labelWidth: 30,
													enableKeyEvents: true,
													width: 205,
													margin: '0 5 0 0',
													allowBlank: false,
													name: 'DOB',
													collapsible: false,
													checkboxToggle: false,
													collapsed: false
												},
												{
													xtype: 'gaiaehr.combo',
													emptyText: _('marital_status'),
													width: 110,
													margin: '0 5 0 0',
													name: 'marital_status',
													list: 12,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'textfield',
													emptyText: _('social_security'),
													name: 'SS',
													width: 100,
													margin: '0 5 0 0'
												}
											]
										},
										{
											xtype: 'fieldcontainer',
											fieldLabel: 'Driver Lic. Sate Exp. Date',
											labelWidth: 149,
											hideLabel: false,
											layout: 'hbox',
											width: 660,
											items: [
												{
													xtype: 'textfield',
													emptyText: _('driver_license'),
													labelWidth: 149,
													enableKeyEvents: true,
													width: 175,
													margin: '0 5 0 0',
													name: 'drivers_license'
												},
												{
													xtype: 'gaiaehr.combo',
													width: 175,
													margin: '0 5 0 0',
													name: 'drivers_license_state',
													list: 20,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'datefield',
													width: 140,
													margin: '0 5 0 0',
													name: 'drivers_license_exp',
													format: 'Y-m-d'
												}
											]
										},
										{
											xtype: 'gaiaehr.combo',
											fieldLabel: 'Ethnicity',
											labelWidth: 149,
											width: 400,
											margin: '0 5 5 0',
											name: 'ethnicity',
											list: 59,
											loadStore: true,
											editable: false
										},
										{
											xtype: 'fieldcontainer',
											fieldLabel: 'Race',
											labelWidth: 149,
											hideLabel: false,
											layout: 'hbox',
											width: 660,
											items: [
												{
													xtype: 'gaiaehr.combo',
													width: 245,
													margin: '0 5 0 0',
													name: 'race',
													emptyText: 'Race',
													list: 14,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'gaiaehr.combo',
													flex: 1,
													margin: '0 5 0 0',
													name: 'secondary_race',
													emptyText: 'Secondary Race',
													list: 14,
													loadStore: true,
													editable: false
												}
											]
										},
										{
											xtype: 'gaiaehr.combo',
											fieldLabel: 'Language',
											labelWidth: 149,
											hideLabel: false,
											width: 400,
											margin: '0 5 5 0',
											name: 'language',
											list: 10,
											loadStore: true,
											editable: false
										}
									]
								},
								{
									xtype: 'panel',
									title: 'Additional Info.',
									layout: 'column',
									collapsible: true,
									enableKeyEvents: true,
									checkboxToggle: false,
									collapsed: false,
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									items: [
										{
											xtype: 'container',
											width: 370,
											items: [
												{
													xtype: 'textfield',
													fieldLabel: 'Alias Name',
													labelWidth: 149,
													hideLabel: false,
													width: 350,
													name: 'alias'
												},
												{
													xtype: 'textfield',
													fieldLabel: 'Birth Place',
													labelWidth: 149,
													hideLabel: false,
													width: 350,
													name: 'birth_place'
												},
												{
													xtype: 'gaiaehr.combo',
													fieldLabel: 'Citizenship',
													labelWidth: 149,
													hideLabel: false,
													width: 350,
													name: 'citizenship',
													list: 104,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'fieldcontainer',
													fieldLabel: 'Multiple Birth',
													labelWidth: 149,
													hideLabel: false,
													layout: 'hbox',
													width: 350,
													items: [
														{
															xtype: 'checkbox',
															margin: '0 10 5 0',
															boxLabel: ' ',
															name: 'birth_multiple'
														},
														{
															xtype: 'numberfield',
															fieldLabel: 'Order',
															labelWidth: 50,
															hideLabel: false,
															width: 165,
															value: 1,
															maxValue: 15,
															minValue: 1,
															name: 'birth_order'
														}
													]
												},
												{
													xtype: 'gaiaehr.combo',
													fieldLabel: 'Deceased',
													labelWidth: 149,
													hideLabel: false,
													width: 350,
													boxLabel: 'Yes',
													name: 'deceased',
													list: 103,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'mitos.datetime',
													fieldLabel: 'Death Date',
													labelWidth: 149,
													hideLabel: false,
													width: 350,
													margin: '0 5 5 0',
													name: 'death_date'
												}
											]
										},
										{
											xtype: 'container',
											items: [
												{
													xtype: 'activeproviderscombo',
													fieldLabel: 'Primary Provider',
													width: 300,
													name: 'primary_provider',
													forceSelection: true
												},
												{
													xtype: 'activefacilitiescombo',
													fieldLabel: 'Primary Facility',
													width: 300,
													name: 'primary_facility',
													displayField: 'option_name',
													valueField: 'option_value',
													queryMode: 'local',
													forceSelection: true
												},
												{
													xtype: 'gaiaehr.combo',
													fieldLabel: 'Veteran',
													width: 300,
													boxLabel: 'Yes',
													name: 'is_veteran',
													list: 103,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'fieldcontainer',
													fieldLabel: 'Mother\'s Name',
													layout: 'hbox',
													width: 660,
													items: [
														{
															xtype: 'textfield',
															emptyText: 'First Name',
															width: 100,
															margin: '0 5 0 0',
															maxLength: 35,
															name: 'mother_fname'
														},
														{
															xtype: 'textfield',
															emptyText: 'Middle Name',
															width: 100,
															margin: '0 5 0 0',
															maxLength: 35,
															name: 'mother_mname'
														},
														{
															xtype: 'textfield',
															emptyText: 'Last Name',
															width: 215,
															margin: '0 5 0 0',
															maxLength: 35,
															name: 'mother_lname'
														}
													]
												},
												{
													xtype: 'fieldcontainer',
													fieldLabel: 'Father\'s Name',
													layout: 'hbox',
													width: 660,
													items: [
														{
															xtype: 'textfield',
															emptyText: 'First Name',
															width: 100,
															margin: '0 5 0 0',
															maxLength: 35,
															name: 'father_fname'
														},
														{
															xtype: 'textfield',
															emptyText: 'Middle Name',
															width: 100,
															margin: '0 5 0 0',
															maxLength: 35,
															name: 'father_mname'
														},
														{
															xtype: 'textfield',
															emptyText: 'Last Name',
															width: 215,
															margin: '0 5 0 0',
															maxLength: 35,
															name: 'father_lname'
														}
													]
												}
											]
										}
									]
								},
								{
									xtype: 'panel',
									title: 'Choices',
									hideLabel: false,
									collapsible: true,
									enableKeyEvents: true,
									checkboxToggle: false,
									collapsed: false,
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									items: [
										{
											xtype: 'container',
											layout: 'hbox',
											items:[
												{
													xtype: 'container',
													layout: 'vbox',
													items:[
														{
															xtype: 'activeproviderscombo',
															fieldLabel: 'Provider',
															labelWidth: 100,
															margin: '0 5 5 0',
															name: 'provider',
															forceSelection: true
														},
														{
															xtype: 'mitos.pharmaciescombo',
															fieldLabel: 'Pharmacy',
															labelWidth: 100,
															margin: '0 5 5 0',
															name: 'pharmacy',
															forceSelection: true,
															emptyText: 'Select'
														},
														{
															xtype: 'gaiaehr.combo',
															fieldLabel: 'HIPAA Notice',
															labelWidth: 100,
															margin: '0 5 5 0',
															name: 'hipaa_notice',
															list: 1,
															loadStore: true,
															editable: false
														}
													]
												},
												{
													xtype: 'container',
													layout: 'vbox',
													items:[
														{
															xtype: 'gaiaehr.combo',
															name: 'organ_donor_code',
															fieldLabel: 'Organ Donor',
															list: 137,
															width: 500,
															loadStore: true,
															editable: false
														},
														{
															xtype: 'container',
															layout: 'hbox',
															margin: '0 0 0 10',
															items: [
																{
																	xtype: 'checkbox',
																	width: 150,
																	margin: '0 5 0 0',
																	boxLabel: 'Allow Voice Msg',
																	name: 'allow_voice_msg'
																},
																{
																	xtype: 'checkbox',
																	width: 150,
																	margin: '0 5 0 0',
																	boxLabel: 'Allow Mail Msg',
																	name: 'allow_mail_msg'
																},
																{
																	xtype: 'checkbox',
																	width: 240,
																	margin: '0 5 0 0',
																	boxLabel: 'Allow Immunization Registry Use',
																	name: 'allow_immunization_registry'
																},
																{
																	xtype: 'checkbox',
																	margin: '0 5 0 0',
																	boxLabel: 'Allow Health Information Exchange',
																	name: 'allow_health_info_exchange'
																}
															]
														},
														{
															xtype: 'container',
															layout: 'hbox',
															margin: '5 0 0 10',
															items: [
																{
																	xtype: 'checkbox',
																	width: 150,
																	margin: '0 5 0 0',
																	boxLabel: ' Allow SMS',
																	name: 'allow_sms'
																},
																{
																	xtype: 'checkbox',
																	width: 150,
																	margin: '0 5 0 0',
																	boxLabel: 'Allow Email',
																	name: 'allow_email'
																},
																{
																	xtype: 'checkbox',
																	width: 240,
																	margin: '0 5 0 0',
																	boxLabel: 'Allow Immunization Info Sharing',
																	name: 'allow_immunization_info_sharing'
																}
															]
														}
													]
												}
											]
										},
										{
											xtype: 'container',
											layout: 'hbox',
											margin: '0 0 10 10',
											items: [
												{
													xtype: 'fieldset',
													title: 'Allow Patient Web Portal',
													checkboxName: 'allow_patient_web_portal',
													checkboxToggle: true,
													width: 320,
													margin: '0 5 0 0',
													items: [
														{
															xtype: 'textfield',
															fieldLabel: 'Web Portal Username',
															labelWidth: 149,
															name: 'portal_username'
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Web Portal Password',
															labelWidth: 149,
															name: 'portal_password',
															inputType: 'password'
														}
													]
												},
												{
													xtype: 'fieldset',
													title: 'Allow Patient Guardian Access Web Portal',
													checkboxName: 'allow_guardian_web_portal',
													checkboxToggle: true,
													collapsible: false,
													width: 320,
													margin: '0 5 0 0',
													items: [
														{
															xtype: 'textfield',
															fieldLabel: 'Web Portal Username',
															labelWidth: 149,
															name: 'guardian_portal_username'
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Web Portal Password',
															labelWidth: 149,
															name: 'guardian_portal_password',
															inputType: 'password'
														}
													]
												},
												{
													xtype: 'fieldset',
													title: 'Allow Patient Emergency Contact Access Web Portal',
													checkboxName: 'allow_emergency_contact_web_portal',
													checkboxToggle: true,
													width: 320,
													margin: '0 5 0 0',
													items: [
														{
															xtype: 'textfield',
															fieldLabel: 'Web Portal Username',
															labelWidth: 149,
															name: 'emergency_contact_portal_username'
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Web Portal Password',
															labelWidth: 149,
															name: 'emergency_contact_portal_password',
															inputType: 'password'
														}
													]
												}
											]
										}
									]
								},
								{
									xtype: 'panel',
									title: 'Employer',
									hideLabel: false,
									collapsible: true,
									enableKeyEvents: true,
									checkboxToggle: false,
									collapsed: false,
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									items: [
										{
											xtype: 'textfield',
											fieldLabel: 'Occupation',
											labelWidth: 149,
											hideLabel: false,
											emptyText: 'Occupation',
											name: 'occupation',
											width: 350,
											margin: '0 5 5 0'
										},
										{
											xtype: 'textfield',
											fieldLabel: 'Employer Name',
											labelWidth: 149,
											hideLabel: false,
											emptyText: 'Employer Name',
											name: 'employer_name',
											width: 350,
											margin: '0 5 5 0'
										},
										{
											xtype: 'textfield',
											fieldLabel: 'Employer Address',
											labelWidth: 149,
											hideLabel: false,
											emptyText: 'Street',
											name: 'employer_address',
											width: 609,
											margin: '0 5 5 0'
										},
										{
											xtype: 'fieldcontainer',
											fieldLabel: 'Employer Address Cont.',
											labelWidth: 149,
											hideLabel: false,
											layout: 'hbox',
											width: 609,
											items: [
												{
													xtype: 'textfield',
													emptyText: 'City',
													name: 'employer_city',
													width: 130,
													margin: '0 5 5 0'
												},
												{
													xtype: 'gaiaehr.combo',
													margin: '0 5 5 0',
													width: 130,
													name: 'employer_state',
													emptyText: 'State',
													list: 20,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'gaiaehr.combo',
													emptyText: 'Country',
													name: 'employer_country',
													width: 100,
													margin: '0 5 5 0',
													list: 3,
													loadStore: true,
													editable: false
												},
												{
													xtype: 'textfield',
													emptyText: 'Zip Code',
													name: 'employer_postal_code',
													width: 80,
													margin: '0 5 5 0'
												}
											]
										}
									]
								},
								{
									xtype: 'panel',
									title: 'Contact',
									layout: 'column',
									collapsible: true,
									enableKeyEvents: true,
									checkboxToggle: false,
									collapsed: false,
									itemId: 'DemographicsContactFieldSet',
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
									items: [
										{
											xtype: 'container',
											margin: '0 10 0 0',
											items: [
												{
													xtype: 'gaiaehr.combo',
													fieldLabel: 'Publicity',
													labelWidth: 60,
													name: 'phone_publicity',
													list: 132,
													loadStore: true,
													editable: false,
													width: 300
												},
												{
													xtype: 'fieldset',
													title: 'Phones',
													items: [
														{
															xtype: 'textfield',
															fieldLabel: 'Home',
															labelWidth: 50,
															emptyText: '000-000-0000',
															name: 'phone_home',
															width: 250
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Mobile',
															labelWidth: 50,
															emptyText: '000-000-0000',
															name: 'phone_mobile',
															width: 250
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Work',
															labelWidth: 50,
															margin: '0 5 0 0',
															emptyText: '000-000-0000',
															name: 'phone_work',
															width: 250
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Ext.',
															labelWidth: 50,
															name: 'phone_work_ext',
															width: 250
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Fax',
															emptyText: '000-000-0000',
															labelWidth: 50,
															name: 'phone_fax',
															width: 250
														},
														{
															xtype: 'textfield',
															fieldLabel: 'Email',
															emptyText: 'example@email.com',
															labelWidth: 50,
															name: 'email',
															width: 250
														}
													]
												}
											]
										},
										{
											xtype: 'container',
											layout: 'vbox',
											margin: '0 10 0 0',
											items: [
												{
													xtype: 'fieldset',
													title: 'Postal Address',
													collapsible: false,
													checkboxToggle: false,
													collapsed: false,
													items: [
														{
															xtype: 'textfield',
															emptyText: 'Street',
															labelWidth: 50,
															width: 370,
															name: 'postal_address'
														},
														{
															xtype: 'textfield',
															emptyText: '(optional)',
															labelWidth: 50,
															width: 370,
															name: 'postal_address_cont'
														},
														{
															xtype: 'container',
															layout: 'hbox',
															width: 370,
															items: [
																{
																	xtype: 'textfield',
																	emptyText: 'City',
																	labelWidth: 50,
																	margin: '0 5 5 0',
																	name: 'postal_city'
																},
																{
																	xtype: 'textfield',
																	emptyText: 'State',
																	labelWidth: 50,
																	margin: '0 5 0 0',
																	name: 'postal_state'
																},
																{
																	xtype: 'textfield',
																	emptyText: 'Zip',
																	labelWidth: 50,
																	width: 92,
																	name: 'postal_zip'
																}
															]
														},
														{
															xtype: 'textfield',
															emptyText: 'Country',
															labelWidth: 50,
															width: 100,
															name: 'postal_country'
														}
													]
												},
												{
													xtype: 'fieldset',
													title: 'Physical Address',
													items: [
														{
															xtype: 'textfield',
															emptyText: 'Street',
															labelWidth: 50,
															width: 370,
															name: 'physical_address'
														},
														{
															xtype: 'textfield',
															emptyText: '(optional)',
															labelWidth: 50,
															width: 370,
															name: 'physical_address_cont'
														},
														{
															xtype: 'container',
															layout: 'hbox',
															width: 370,
															items: [
																{
																	xtype: 'textfield',
																	emptyText: 'City',
																	labelWidth: 50,
																	margin: '0 5 5 0',
																	name: 'physical_city'
																},
																{
																	xtype: 'textfield',
																	emptyText: 'State',
																	labelWidth: 50,
																	margin: '0 5 0 0',
																	name: 'physical_state'
																},
																{
																	xtype: 'textfield',
																	emptyText: 'Zip',
																	labelWidth: 50,
																	width: 92,
																	margin: '0 5 0 0',
																	name: 'physical_zip'
																}
															]
														},
														{
															xtype: 'textfield',
															emptyText: 'Country',
															labelWidth: 50,
															width: 100,
															name: 'physical_country'
														}
													]
												}
											]
										}, {
											xtype: 'container',
											layout: 'vbox',
											items: [
												{
													xtype: 'fieldset',
													title: 'Emergency Contact',
													collapsible: false,
													checkboxToggle: false,
													collapsed: false,
													items: [
														{
															xtype: 'gaiaehr.combo',
															fieldLabel: 'Relation',
															labelWidth: 50,
															name: 'emergency_contact_relation',
															list: 134,
															loadStore: true,
															editable: false
														},
														{
															xtype: 'fieldcontainer',
															fieldLabel: 'Name',
															labelWidth: 50,
															layout: 'hbox',
															items: [
																{
																	xtype: 'textfield',
																	enableKeyEvents: true,
																	margin: '0 5 0 0',
																	name: 'emergency_contact_fname'
																},
																{
																	xtype: 'textfield',
																	enableKeyEvents: true,
																	width: 75,
																	margin: '0 5 0 0',
																	name: 'emergency_contact_mname'
																},
																{
																	xtype: 'textfield',
																	enableKeyEvents: true,
																	width: 150,
																	name: 'emergency_contact_lname'
																}
															]
														},
														{
															xtype: 'fieldcontainer',
															fieldLabel: 'Phone',
															labelWidth: 50,
															hideLabel: false,
															layout: 'hbox',
															items: [
																{
																	xtype: 'textfield',
																	emptyText: '000-000-0000',
																	margin: '0 5 5 0',
																	name: 'emergency_contact_phone'
																},
																{
																	xtype: 'gaiaehr.combo',
																	emptyText: 'Phone Type',
																	name: 'emergency_contact_phone_type',
																	list: 136,
																	loadStore: true,
																	editable: false
																}
															]
														},
														{
															xtype: 'fieldcontainer',
															fieldLabel: _('address'),
															labelWidth: 50,
															items: [
																{
																	xtype: 'textfield',
																	emptyText: 'Street',
																	width: 370,
																	name: 'emergency_contact_address'
																},
																{
																	xtype: 'textfield',
																	emptyText: '(optional)',
																	width: 370,
																	name: 'emergency_contact_address_cont'
																},
																{
																	xtype: 'container',
																	layout: 'hbox',
																	width: 370,
																	items: [
																		{
																			xtype: 'textfield',
																			emptyText: 'City',
																			margin: '0 5 5 0',
																			name: 'emergency_contact_city'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: 'State',
																			margin: '0 5 0 0',
																			name: 'emergency_contact_state'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: 'Zip',
																			width: 92,
																			margin: '0 5 0 0',
																			name: 'emergency_contact_zip'
																		}
																	]
																},
																{
																	xtype: 'textfield',
																	emptyText: 'Country',
																	labelWidth: 50,
																	width: 100,
																	name: 'emergency_contact_country'
																}
															]
														}
													]
												},
												{
													xtype: 'fieldset',
													title: 'Guardian\'s Contact',
													collapsible: false,
													checkboxToggle: false,
													collapsed: false,
													items: [
														{
															xtype: 'gaiaehr.combo',
															fieldLabel: 'Relation',
															labelWidth: 50,
															name: 'guardians_relation',
															list: 134,
															loadStore: true,
															editable: false
														},
														{
															xtype: 'fieldcontainer',
															fieldLabel: 'Name',
															labelWidth: 50,
															hideLabel: false,
															layout: 'hbox',
															items: [
																{
																	xtype: 'textfield',
																	margin: '0 5 0 0',
																	name: 'guardians_fname'
																}, {
																	xtype: 'textfield',
																	width: 75,
																	margin: '0 5 0 0',
																	name: 'guardians_mname'
																}, {
																	xtype: 'textfield',
																	width: 150,
																	name: 'guardians_lname'
																}
															]
														},
														{
															xtype: 'fieldcontainer',
															fieldLabel: 'Phone',
															labelWidth: 50,
															layout: 'hbox',
															items: [
																{
																	xtype: 'textfield',
																	emptyText: '000-000-0000',
																	labelWidth: 50,
																	margin: '0 5 5 0',
																	name: 'guardians_phone'
																}, {
																	xtype: 'gaiaehr.combo',
																	name: 'guardians_phone_type',
																	list: 136,
																	loadStore: true,
																	editable: false
																}
															]
														},
														{
															xtype: 'fieldcontainer',
															fieldLabel: _('address'),
															labelWidth: 50,
															items: [
																{
																	xtype: 'textfield',
																	emptyText: 'Street',
																	width: 370,
																	name: 'guardians_address'
																},
																{
																	xtype: 'textfield',
																	emptyText: '(optional)',
																	width: 370,
																	name: 'guardians_address_cont'
																},
																{
																	xtype: 'container',
																	layout: 'hbox',
																	width: 370,
																	items: [
																		{
																			xtype: 'textfield',
																			emptyText: 'City',
																			margin: '0 5 5 0',
																			name: 'guardians_city'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: 'State',
																			margin: '0 5 0 0',
																			name: 'guardians_state'
																		},
																		{
																			xtype: 'textfield',
																			emptyText: 'Zip',
																			width: 92,
																			margin: '0 5 0 0',
																			name: 'guardians_zip'
																		}
																	]
																},
																{
																	xtype: 'textfield',
																	emptyText: 'Country',
																	labelWidth: 50,
																	width: 100,
																	name: 'guardians_country'
																}
															]
														}
													]
												}
											]
										}
									]
								}
							]
						}
					]
				})
			]
		};

		if(me.compactDemographics){
			configs.items.push(me.insTabPanel);
		}

		configs.bbar = [
			{
				xtype: 'button',
				action: 'readOnly',
				text: _('possible_duplicates'),
				minWidth: 75,
				itemId: 'PatientPossibleDuplicatesBtn'
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

		if(!me.compactDemographics){

			Ext.Function.defer(function(){
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
		}
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
					layout: 'hbox',
					style: 'float:right',
					bodyPadding: 5,
					height: 160,
					width: 255,
					items: [
						{
							xtype: 'image',
							width: 119,
							height: 119,
							itemId: 'image',
							margin: '0 5 0 0',
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
							width: 119,
							height: 119,
							margin: 0,
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
							handler: function(){
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
		//        for(var i = 0; i < fields.items.length; i++){
		//            var f = fields.items[i], v = f.getValue(), n = f.name;
		//            if(n == 'SS' || n == 'DOB' || n == 'sex'){
		//                if(v == null || v == ''){
		//                    f.setReadOnly(false);
		//                }else{
		//                    f.setReadOnly(true);
		//                }
		//            }
		//        }
	},

	formSave: function(){
		var me = this,
			form = me.demoForm.getForm(),
			record = form.getRecord(),
			values = form.getValues(),
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
		var form = btn.up('form').getForm(), record = form.getRecord();
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
