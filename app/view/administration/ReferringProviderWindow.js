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

Ext.define('App.view.administration.ReferringProviderWindow', {
	extend: 'Ext.window.Window',
	layout: {
		type: 'vbox',
		align: 'stretch'
	},
	title: _('referring_provider'),
	itemId: 'ReferringProviderWindow',
	width: 1000,
	height: 800,
	modal: true,
	bodyPadding: 5,
	requires: [
		'App.ux.NPIRegistrySearch',
		'App.ux.form.fields.InputTextMask',
		'App.ux.combo.Insurances',
		'App.ux.combo.Specialties'
	],

	initComponent: function(){
		var me = this;

		me.items = [
			{
				xtype: 'form',
				itemId: 'ReferringProviderWindowForm',
				bodyPadding: '5 10',
				fieldDefaults: {
					labelAlign: 'top',
					margin: '0 5 5 0'
				},
				layout: {
					type: 'hbox',
					align: 'stretch'
				},
				items: [

					{
						xtype: 'fieldset',
						title: _('provider'),
						layout: {
							type: 'vbox',
							align: 'stretch'
						},
						padding: '10 10 15 10',
						items: [
							{
								xtype: 'npiregistrysearch',
								fieldLabel: _('npi'),
								name: 'npi',
								width: 120,
								allowBlank: false,
								itemId: 'ReferringProviderWindowFormNpiSearchField',
							},
							{
								xtype: 'fieldcontainer',
								layout: {
									type: 'hbox'
								},
								items: [
									{
										width: 50,
										xtype: 'mitos.titlescombo',
										name: 'title',
										fieldLabel: _('title')
									},
									{
										width: 150,
										xtype: 'textfield',
										name: 'fname',
										fieldLabel: _('first_name')
									},
									{
										width: 100,
										xtype: 'textfield',
										name: 'mname',
										fieldLabel: _('middle_name')
									},
									{
										width: 250,
										xtype: 'textfield',
										name: 'lname',
										fieldLabel: _('last_name')
									},
									{
										xtype: 'checkbox',
										fieldLabel: _('active'),
										name: 'active'
									}
								]
							},
							{
								xtype: 'textfield',
								name: 'organization_name',
								fieldLabel: _('organization_name')
							},
							{
								xtype: 'fieldcontainer',
								layout: {
									type: 'hbox'
								},
								items: [
									{
										xtype: 'textfield',
										fieldLabel: _('lic'),
										name: 'lic',
										width: 120
									},
									{
										xtype: 'textfield',
										fieldLabel: _('taxonomy'),
										name: 'taxonomy',
										width: 120
									},
									{
										xtype: 'textfield',
										fieldLabel: _('upin'),
										name: 'upin',
										width: 120
									},
									{
										xtype: 'textfield',
										fieldLabel: _('ssn'),
										name: 'ssn',
										width: 120
									}
								]
							},
							{
								height: 75,
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								name: 'notes',
								emptyText: _('additional_info')
							}
						]
					},
					{
						xtype: 'container',
						margin: '0 0 0 10',
						flex: 1,
						items: [
							{
								xtype: 'fieldset',
								title: _('contact'),
								layout: {
									type: 'vbox',
									align: 'stretch'
								},
								items: [
									{
										xtype: 'textfield',
										fieldLabel: _('email'),
										vtype: 'email',
										name: 'email'
									},
									{
										xtype: 'textfield',
										fieldLabel: _('phone_number'),
										name: 'phone_number',
										plugins: [Ext.create('App.ux.form.fields.InputTextMask', '999-999-9999')],
									},
									{
										xtype: 'textfield',
										fieldLabel: _('fax_number'),
										name: 'fax_number'
									},
									{
										xtype: 'textfield',
										fieldLabel: _('cell_number'),
										name: 'cel_number'
									},
									{
										xtype: 'textfield',
										fieldLabel: _('direct_address'),
										vtype: 'email',
										name: 'direct_address'
									}
								]
							},
							{
								xtype: 'fieldset',
								title: _('provider_portal'),
								layout: {
									type: 'hbox'
								},
								items: [
									{
										xtype: 'textfield',
										fieldLabel: _('password'),
										minLength: 8,
										maxLength: 15,
										name: 'password',
										inputType: 'password',
										vtype: 'strength',
										strength: 24,
										plugins: {
											ptype: 'passwordstrength'
										}
									},
									{
										xtype: 'checkbox',
										fieldLabel: _('authorized'),
										name: 'authorized'
									}
								]
							}
						]
					}
				]
			},
			{
				xtype: 'grid',
				itemId: 'ReferringProviderWindowGrid',
				flex: 1,
				frame: true,
				plugins: [
					{
						ptype: 'cellediting'
					}
				],
				tbar: [
					_('facilities'),
					'->',
					{
						text: _('facility'),
						iconCls: 'icoAdd',
						itemId: 'ReferringProviderFacilityAddBtn'
					}
				],
				columns: [
					{
						xtype: 'griddeletecolumn',
						acl: a('allow_add_referring_physician'),
						width: 30,
					},
					{
						text: _('name'),
						dataIndex: 'name',
						flex: 1,
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('address_one'),
						dataIndex: 'address',
						flex: 1,
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('address_two'),
						dataIndex: 'address_cont',
						flex: 1,
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('city'),
						dataIndex: 'city',
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('state'),
						dataIndex: 'state',
						width: 100,
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('zipcode'),
						dataIndex: 'postal_code',
						width: 100,
						editor: {
							xtype: 'textfield'
						}
					}
				]
			},
			{
				xtype: 'grid',
				itemId: 'ReferringProviderInsuranceBlacklistGrid',
				flex: 1,
				frame: true,
				plugins: [
					{
						ptype: 'rowediting'
					}
				],
				tbar: [
					_('insurance_blacklist'),
					'->',
					{
						text: _('insurance_blacklist'),
						iconCls: 'icoAdd',
						acl: a('allow_edit_referring_physician_blacklist'),
						itemId: 'ReferringProviderInsuranceBlacklistAddBtn'
					}
				],
				columns: [
					{
						xtype: 'griddeletecolumn',
						acl: a('allow_edit_referring_physician_blacklist'),
						width: 30,
					},
					{
						text: _('npi'),
						dataIndex: 'npi'
					},
					{
						text: _('specialty'),
						dataIndex: 'specialty_id',
						width: 200,
						renderer: function (v,m,r){
							return r.get('specialty_name');
						},
						editor: {
							xtype: 'specialtiescombo',
							itemId: 'ReferringProviderInsuranceBlacklistSpecialtyCmb',
							allowBlank: false
						}
					},
					{
						text: _('insurance'),
						dataIndex: 'insurance_id',
						width: 200,
						renderer: function (v,m,r){
							return r.get('insurance_name');
						},
						editor: {
							xtype: 'insurancescombo',
							itemId: 'ReferringProviderInsuranceBlacklistInsuranceCmb',
							allowBlank: false
						}
					},
					{
						text: _('note'),
						dataIndex: 'note',
						flex: 1,
						editor: {
							xtype: 'textfield',
							maxLength: 255
						}
					}
				]
			}
		];

		me.buttons =  [
			{
				text: _('cancel'),
				itemId: 'ReferringProviderWindowCancelBtn'
			},
			{
				text: _('save'),
				itemId: 'ReferringProviderWindowSaveBtn'
			}
		];

		me.callParent(arguments);
	}
});
