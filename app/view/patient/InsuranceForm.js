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
							title: _('insurance'),
							width: 660,
							margin: 0,
							items: [
								{
									xtype: 'fieldcontainer',
									fieldLabel: 'Insurance',
									hideLabel: true,
									enableKeyEvents: true,
									layout: 'hbox',
									margin: '5 0 0 0',
									items: [
										{
											xtype: 'insurancescombo',
											fieldLabel: _('provider'),
											width: 440,
											margin: '0 5 0 0',
											name: 'insurance_id',
											queryMode: 'local',
											editable: false,
											allowBlank: false
										},
										{
											xtype: 'gaiaehr.combo',
											emptyText: _('type'),
											width: 140,
											name: 'insurance_type',
											list: 96,
											queryMode: 'local',
											allowBlank: false,
											editable: false,
											loadStore: true
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: 'Insurance Dates',
									hideLabel: false,
									layout: 'hbox',
									width: 590,
									margin: '5 0 0 0',
									items: [
										{
											xtype: 'datefield',
											emptyText: _('effective_date'),
											width: 140,
											margin: '0 5 0 0',
											name: 'effective_date'
										},
										{
											xtype: 'datefield',
											emptyText: _('expiration_date'),
											width: 140,
											name: 'expiration_date'
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: 'Insurance POL/GRP',
									hideLabel: false,
									layout: 'hbox',
									width: 590,
									margin: '5 0 0 0',
									items: [
										{
											xtype: 'textfield',
											emptyText: _('policy_number'),
											width: 140,
											margin: '0 5 0 0',
											name: 'policy_number'
										},
										{
											xtype: 'textfield',
											emptyText: _('group_number'),
											width: 140,
											margin: '0 5 0 0',
											name: 'group_number'
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: 'Insurance Covers',
									hideLabel: false,
									layout: 'hbox',
									width: 590,
									margin: '5 0 10 0',
									items: [
										{
											xtype: 'textfield',
											emptyText: _('medical'),
											width: 140,
											margin: '0 5 0 0',
											name: 'cover_medical'
										},
										{
											xtype: 'textfield',
											emptyText: _('dental'),
											width: 140,
											name: 'cover_dental'
										}
									]
								}
							]
						},
						{
							xtype: 'fieldset',
							title: _('subscriber'),
							width: 660,
							style: 'background-color: beige',
							items: [
								{
									xtype: 'gaiaehr.combo',
									fieldLabel: _('relationship'),
									width: 320,
									margin: '0 5 0 0',
									name: 'subscriber_relationship',
									list: 25,
									editable: false,
									allowBlank: false,
									queryMode: 'local',
									itemId: 'PatientInsuranceFormSubscribeRelationshipCmb'
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: _('name'),
									layout: 'hbox',
									margin: '5 0 0 0',
									items: [
										{
											xtype: 'gaiaehr.combo',
											emptyText: _('title'),
											width: 60,
											margin: '0 5 0 0',
											name: 'subscriber_title',
											list: 22,
											queryMode: 'local',
											loadStore: true,
											editable: false
										},
										{
											xtype: 'textfield',
											emptyText: _('first_name'),
											width: 100,
											margin: '0 5 0 0',
											name: 'subscriber_given_name'
										},
										{
											xtype: 'textfield',
											emptyText: _('middle_name'),
											width: 100,
											margin: '0 5 0 0',
											name: 'subscriber_middle_name'
										},
										{
											xtype: 'textfield',
											emptyText: _('last_name'),
											width: 215,
											margin: '0 5 0 0',
											name: 'subscriber_surname'
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: _('dob_sex_ss'),
									layout: 'hbox',
									margin: '5 0 0 0',
									items: [
										{
											xtype: 'datefield',
											emptyText: _('dob'),
											width: 165,
											margin: '0 5 0 0',
											name: 'subscriber_dob'
										},
										{
											xtype: 'gaiaehr.combo',
											emptyText: _('sex'),
											width: 100,
											margin: '0 5 0 0',
											name: 'subscriber_sex',
											list: 95,
											queryMode: 'local',
											loadStore: true,
											editable: false
										},
										{
											xtype: 'textfield',
											emptyText: _('ss'),
											width: 215,
											name: 'subscriber_ss'
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: _('phone'),
									layout: 'hbox',
									margin: '5 0 0 0',
									items: [
										{
											xtype: 'textfield',
											emptyText: '000-000-0000',
											width: 165,
											margin: '0 5 0 0',
											name: 'subscriber_phone'
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: _('address'),
									layout: 'hbox',
									margin: '5 0 0 0',
									items: [
										{
											xtype: 'textfield',
											emptyText: _('street'),
											width: 490,
											name: 'subscriber_street'
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									layout: 'hbox',
									margin: '5 0 5 145',
									items: [
										{
											xtype: 'textfield',
											emptyText: _('city'),
											width: 165,
											margin: '0 5 0 0',
											name: 'subscriber_city'
										},
										{
											xtype: 'gaiaehr.combo',
											emptyText: _('state'),
											width: 110,
											margin: '0 5 0 0',
											name: 'subscriber_state',
											list: 20,
											queryMode: 'local',
											loadStore: true,
											editable: false

										},
										{
											xtype: 'gaiaehr.combo',
											emptyText: _('country'),
											width: 110,
											margin: '0 5 0 0',
											name: 'subscriber_country',
											list: 3,
											queryMode: 'local',
											loadStore: true,
											editable: false

										}, {
											xtype: 'textfield',
											emptyText: _('postal_code'),
											width: 90,
											name: 'subscriber_postal_code'
										}
									]
								},
								{
									xtype: 'fieldcontainer',
									fieldLabel: _('employer'),
									layout: 'hbox',
									margin: '5 0 10 0',
									items: [
										{
											xtype: 'textfield',
											width: 490,
											name: 'subscriber_employer'
										}
									]
								}
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

