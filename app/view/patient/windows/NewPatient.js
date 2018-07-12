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

Ext.define('App.view.patient.windows.NewPatient', {
	extend: 'Ext.window.Window',
	itemId: 'NewPatientWindow',
	title: _('new_patient'),
	closeAction: 'hide',
	closable: false,
	modal: true,
	layout: {
		type: 'vbox',
		align: 'stretch'
	},
	items: [
		{
			xtype: 'form',
			itemId: 'NewPatientWindowForm',
			bodyPadding: '10 10 0 10',
			border: false,
			items: [
				// {
				// 	xtype: 'fieldset',
				// 	cls: 'highlight_fieldset',
				// 	margin: '0 0 5 0',
				// 	padding: 10,
				// 	title: _('numbers'),
				// 	layout: 'hbox',
				// 	defaults: {
				// 		margin: '0 10 5 0',
				// 		labelAlign: 'top'
				// 	},
				// 	items: [
				// 		{
				// 			xtype: 'textfield',
				// 			name: 'pubpid',
				// 			fieldLabel: _('medical'), //external_record
				// 			flex: 1,
				// 			enableKeyEvents: true
				// 		},
				// 		{
				// 			xtype: 'textfield',
				// 			name: 'pubaccount',
				// 			fieldLabel: _('account'), //external_account
				// 			flex: 1,
				// 			enableKeyEvents: true
				// 		},
				// 		{
				// 			xtype: 'textfield',
				// 			name: 'interface_mrn',
				// 			fieldLabel: _('interface_mrn'), //external_account
				// 			flex: 1,
				// 			margin: 0,
				// 			enableKeyEvents: true
				// 		}
				// 	]
				// },
				{
					xtype: 'fieldset',
					cls: 'highlight_fieldset',
					margin: '0 0 5 0',
					padding: 10,
					title: _('demographics'),
					layout: {
						type: 'vbox',
						align: 'stretch'
					},
					items: [
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
								}
							]
						},
						{
							xtype: 'fieldcontainer',
							layout: 'hbox',
							defaults: {
								margin: '0 5 0 0',
								labelAlign: 'top'
							},
							items: [
								{
									xtype: 'gaiaehr.combo',
									name: 'sex',
									fieldLabel: _('sex_at_birth'),
									width: 200,
									enableKeyEvents: true,
									allowBlank: false,
									listKey: 'sex',
									loadStore: true,
									editable: false
								},
								{
									xtype: 'datefield',
									name: 'DOB',
									format: 'm/d/Y',
									width: 200,
									fieldLabel: _('dob'),
									enableKeyEvents: true,
									allowBlank: false
								}
							]
						}
					]
				}
			]
		},
		{
			xtype: 'form',
			itemId: 'NewPatientWindowInsuranceForm',
			bodyPadding: '0 10 10 10',
			border: false,
			items: [
				{
					xtype: 'fieldset',
					cls: 'highlight_fieldset',
					margin: '0 0 5 0',
					padding: 10,
					title: _('insurance'),
					layout: 'vbox',
					defaults: {
						margin: '0 10 5 0',
						labelAlign: 'top'
					},
					items: [

						{
							xtype: 'fieldcontainer',
							layout: {
								type: 'hbox',
								align: 'bottom'
							},
							width: 410,
							defaults: {
								labelWidth: 50,
								labelAlign: 'top'
							},
							items: [
								{
									xtype: 'insurancescombo',
									name: 'insurance_id',
									fieldLabel: _('insurance'),
									queryMode: 'local',
									editable: false,
									margin: '0 10 0 0',
									itemId: 'NewPatientWindowInsuranceCmb',
									flex: 1
								},
								{
									xtype: 'button',
									text: _('eligibility'),
									itemId: 'NewPatientInsuranceEligibilityBtn',
									width: 90
								}
							]
						},
						{
							xtype: 'fieldcontainer',
							layout: 'hbox',
							width: 410,
							defaults: {
								labelWidth: 50,
								labelAlign: 'top'
							},
							items: [
								{
									xtype: 'textfield',
									name: 'policy_number',
									itemId: 'PatientInsuranceID',
									emptyText: _('policy_number'),
									fieldLabel: _('id'),
									labelWidth: 28,
									margin: '0 5 0 0',
									flex: 1
								},
								{
									xtype: 'textfield',
									name: 'group_number',
									emptyText: _('group_number'),
									fieldLabel: _('group'),
									labelWidth: 50,
									flex: 1
								}
							]
						},
						{
							xtype: 'fieldset',
							title: _('subscriber'),
							layout: 'hbox',
							padding: '0 10 10 10',
							width: 410,
							defaults: {
								margin: '0 5 0 0',
								labelWidth: 50,
								labelAlign: 'top'
							},
							items: [
								{
									xtype: 'textfield',
									name: 'subscriber_given_name',
									fieldLabel: _('first_name'),
									flex: 1,
									allowBlank: false,
									maxLength: 35
								},
								{
									xtype: 'textfield',
									name: 'subscriber_middle_name',
									fieldLabel: _('init'),
									width: 50,
									enableKeyEvents: true,
									maxLength: 35
								},
								{
									xtype: 'textfield',
									name: 'subscriber_surname_name',
									fieldLabel: _('last_name'),
									flex: 2,
									allowBlank: false,
									maxLength: 35
								}
							]
						}
					]
				}
			]
		}
	],
	buttons:[
		{
			text: _('cancel'),
			itemId: 'NewPatientWindowCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'NewPatientWindowSaveBtn'
		}
	]
});