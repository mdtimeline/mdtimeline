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

Ext.define('App.view.patient.InsuranceForm', {
	extend: 'Ext.form.Panel',
	requires: [
		'App.ux.combo.Insurances',
		'App.ux.combo.Combo'
	],
	xtype: 'patientinsuranceform',
	border: false,
	bodyBorder: false,
	closable: true,
	fieldDefaults: {
		labelAlign: 'right',
		labelWidth: 140
	},
	items: [
		{
			xtype:'container',
			layout: {
				type: 'hbox'
			},
			items: [
				{
					xtype: 'container',
					items: [
						{
							xtype: 'fieldset',
							title: _('insurance_card'),
                            cls: 'highlight_fieldset_Green',
                            margin: '10 0 5 5',
							items: [
								{
									xtype: 'fieldcontainer',
									layout: 'hbox',
                                    enableKeyEvents: true,
                                    width: 700,
                                    defaults: {
                                        margin: '10 0 0 2',
                                        labelAlign: 'right'
                                    },
									items: [
										{
											xtype: 'insurancescombo',
                                            name: 'insurance_id',
                                            fieldLabel: _('insurance'),
                                            labelWidth: 120,
											width: 432,
											queryMode: 'local',
											editable: false,
											allowBlank: false
										},
										{
											xtype: 'gaiaehr.combo',
											emptyText: _('type'),
                                            fieldLabel: _('type'),
                                            labelWidth: 100,
											width: 225,
											name: 'insurance_type',
											list: 96,
											queryMode: 'local',
											allowBlank: false,
											editable: false,
											loadStore: true
										}
									]
								}, //Insurance Name
                                {
                                    xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    hideLabel: false,
                                    width: 700,
                                    defaults: {
                                        margin: '3 0 0 2',
                                        labelAlign: 'right'
                                    },
                                    items: [
                                        {
                                            xtype: 'button',
                                            text: 'Eligibility',
                                            itemId: 'InsuranceEligibilityBtn',
                                            //labelWidth: 70,
                                            width: 90
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'policy_number',
                                            itemId: 'PatientInsuranceID',
                                            emptyText: _('policy_number'),
                                            fieldLabel: _('id'),
                                            labelWidth: 28,
                                            width: 150
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'group_number',
                                            emptyText: _('group_number'),
                                            fieldLabel: _('group'),
                                            labelWidth: 50,
                                            width: 180
                                        },
                                        {
                                            xtype: 'gaiaehr.combo',
                                            name: 'subscriber_relationship',
                                            itemId: 'PatientInsuranceFormSubscribeRelationshipCmb',
                                            fieldLabel: _('relationship'),
                                            emptyText: _('relationship'),
                                            queryMode: 'local',
                                            list: 134,
                                            loadStore: true,
                                            editable: false,
                                            labelWidth: 100,
                                            width: 225
                                        } //Relationship Insured vwith Patient
                                    ]
                                }, //ID, Group, Dates Exp y Eff.
								{
									xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    hideLabel: false,
                                    enableKeyEvents: true,
                                    width: 700,
                                    defaults: {
                                        margin: '3 0 0 2',
                                        labelAlign: 'right'
                                    },
									items: [
										{
											xtype: 'textfield',
                                            name: 'card_first_name',
                                            emptyText: _('first_name'),
                                            fieldLabel: _('card_name'),
                                            labelWidth: 120,
											width: 225,
											allowBlank: false
										},
										{
											xtype: 'textfield',
                                            name: 'card_middle_name',
                                            emptyText: _('middle_name'),
											width: 20,
                                            allowBlank: true
										},
										{
											xtype: 'textfield',
                                            name: 'card_last_name',
                                            emptyText: _('last_name'),
											width: 183,
											allowBlank: false
										},
                                        {
                                            xtype: 'datefield',
                                            name: 'effective_date',
                                            emptyText: _('effective_date'),
                                            fieldLabel: _('effective_date'),
                                            labelWidth: 100,
                                            width: 225
                                        }
									]
								}, //Card Name
								{
                                    xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    hideLabel: false,
                                    width: 700,
                                    defaults: {
                                        margin: '3 0 0 2',
                                        labelAlign: 'right'
                                    },
									items: [
										{
											xtype: 'textfield',
                                            name: 'cover_medical',
											emptyText: _('medical'),
                                            fieldLabel: _('covers'),
                                            labelWidth: 120,
                                            width: 275
										},
										{
											xtype: 'textfield',
											emptyText: _('dental'),
											width: 155,
											name: 'cover_dental'
										},
                                        {
                                            xtype: 'datefield',
                                            name: 'expiration_date',
                                            emptyText: _('expiration_date'),
                                            fieldLabel: _('expiration_date'),
                                            labelWidth: 100,
                                            width: 225
                                        }
									]
								}  //Cubiertas Medical y Dental
							]
						}, //Patient Information
						{
							xtype: 'fieldset',
							title: _('subscriber'),
                            cls: 'highlight_fieldset',
                            margin: '10 0 5 5',
							items: [
								{
                                    xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    width: 700,
                                    defaults: {
                                        margin: '5 5 0 2',
                                        labelWidth: 40,
                                        labelAlign: 'right'
                                    },
                                    items: [
                                        {
                                            xtype: 'gaiaehr.combo',
                                            name: 'subscriber_title',
                                            fieldLabel: _('name'),
                                            emptyText: _('title'),
                                            queryMode: 'local',
                                            list: 22,
                                            width: 100,
                                            loadStore: true,
                                            editable: false
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_given_name',
                                            emptyText: _('first_name'),
                                            width: 100
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_middle_name',
                                            emptyText: _('middle_name'),
                                            width: 20
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_surname',
                                            emptyText: _('last_name'),
                                            width: 180
                                        },
                                        {
                                            xtype: 'datefield',
                                            name: 'subscriber_dob',
                                            emptyText: _('dob'),
                                            fieldLabel: _('dob'),
                                            width: 150
                                        },
                                        {
                                            xtype: 'gaiaehr.combo',
                                            name: 'subscriber_sex',
                                            emptyText: _('sex'),
                                            fieldLabel: _('sex'),
                                            labelWidth: 30,
                                            list: 95,
                                            queryMode: 'local',
                                            width: 110,
                                            loadStore: true,
                                            editable: false
                                        }
                                    ]
								} //Full Name
							]
						},  //Principal Insured Info
                        {
                            xtype: 'fieldset',
                            //title: _('subscriber'),
                            cls: 'highlight_fieldset',
                            margin: '5 0 5 5',
                            items: [
                                {
                                    xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    width: 700,
                                    defaults: {
                                        margin: '5 5 0 0',
                                        labelAlign: 'right'
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_phone',
                                            emptyText: '000-000-0000',
                                            fieldLabel: _('phone'),
                                            labelWidth: 40,
                                            width: 140
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_ss',
                                            fieldLabel: _('social_security'),
                                            emptyText: _('social_security'),
                                            labelWidth: 90,
                                            width: 190
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_employer',
                                            emptyText: _('employer'),
                                            fieldLabel: _('employer'),
                                            labelWidth: 60,
                                            width: 330
                                        }
                                    ]
                                } //DOB Sex SocialSecurity Phone
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            //title: _('subscriber'),
                            cls: 'highlight_fieldset',
                            margin: '5 0 5 5',
                            items: [
                                {
                                    xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    width: 700,
                                    defaults: {
                                        margin: '5 5 0 0',
                                        labelAlign: 'right'
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            emptyText: _('street'),
                                            fieldLabel: _('street'),
                                            labelWidth: 40,
                                            width: 340,
                                            name: 'subscriber_street'
                                        }
                                    ]
                                }, //Street
                                {
                                    xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    width: 700,
                                    defaults: {
                                        margin: '2 5 0 0',
                                        labelWidth: 40,
                                        labelAlign: 'right'
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_city',
                                            emptyText: _('city'),
                                            fieldLabel: _('city'),
                                            width: 140
                                        },
                                        {
                                            xtype: 'gaiaehr.combo',
                                            name: 'subscriber_state',
                                            emptyText: _('state'),
                                            queryMode: 'local',
                                            list: 20,
                                            width: 100,
                                            loadStore: true,
                                            editable: false

                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'subscriber_postal_code',
                                            emptyText: _('postal_code'),
                                            width: 90
                                        },
                                        {
                                            xtype: 'gaiaehr.combo',
                                            name: 'subscriber_country',
                                            emptyText: _('country'),
                                            queryMode: 'local',
                                            list: 3,
                                            width: 100,
                                            loadStore: true,
                                            editable: false
                                        }
                                    ]
                                }, //City State Zip Country
                                {
                                    xtype: 'fieldcontainer',
                                    layout: 'hbox',
                                    width: 700,
                                    defaults: {
                                        margin: '2 5 0 0',
                                        labelWidth: 40,
                                        labelAlign: 'right'
                                    },
                                    items: [
                                    ]
                                }  //Employer Suscriber
                            ]
                        }



					]
				},
				{
					xtype: 'container',
					flex: 1,
					layout: {
						type: 'anchor'
					},
					items: [
						{
							xtype:'container',
							anchor: '98%',
							layout: {
								type: 'vbox',
								align: 'right'
							},
							items:[
								{
									xtype: 'panel',
									height: 182,
									width: 255,

									itemId: 'insContainer',
									items: [
										{
											xtype: 'image',
											action: 'insImage',
											width: 253,
											height: 153,
											src: 'resources/images/icons/insurance_placeholder.jpg'
										},
										{
											xtype: 'textareafield',
											action: 'insImage',
											name: 'image',
											hidden: true
										}
									],
									bbar: [
										'->',
										'-',
										{
											text: _('upload'),
											action: 'onWebCam'
										},
										'-'
									]
								}
							]
						},
						{
							xtype: 'textareafield',
							labelAlign: 'top',
							fieldLabel: _('notes'),
							margin: '5 0 0 10',
							grow: false,
							anchor: '98%',
							name: 'notes'
						}
					]
				}
			]
		}
	]

});

