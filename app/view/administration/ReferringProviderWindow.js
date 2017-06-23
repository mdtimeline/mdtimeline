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
	width: 800,
	height: 550,
	modal: true,
	bodyPadding: 5,
	requires: [

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
						xtype: 'container',
						margin: '0 10 0 0',
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
												allowBlank: false,
												fieldLabel: _('first_name')
											},
											{
												width: 100,
												xtype: 'textfield',
												name: 'mname',
												fieldLabel: _('middle_name')
											},
											{
												width: 150,
												xtype: 'textfield',
												name: 'lname',
												allowBlank: false,
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
										xtype: 'fieldcontainer',
										layout: {
											type: 'hbox'
										},
										items: [
											{
												xtype: 'textfield',
												fieldLabel: _('npi'),
												name: 'npi',
												width: 100
											},
											{
												xtype: 'textfield',
												fieldLabel: _('lic'),
												name: 'lic',
												width: 100
											},
											{
												xtype: 'textfield',
												fieldLabel: _('taxonomy'),
												name: 'taxonomy',
												width: 100
											},
											{
												xtype: 'textfield',
												fieldLabel: _('upin'),
												name: 'upin',
												width: 100
											},
											{
												xtype: 'textfield',
												fieldLabel: _('ssn'),
												name: 'ssn',
												width: 100
											}
										]
									},
									{
										height: 50,
										xtype: 'textareafield',
										fieldLabel: _('notes'),
										name: 'notes',
										emptyText: _('additional_info')
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
										fieldLabel: _('username'),
										minLength: 5,
										maxLength: 15,
										name: 'username'
									},
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
					},
					{
						xtype: 'fieldset',
						title: _('contact'),
						flex: 1,
						layout: {
							type: 'vbox',
							align: 'stretch'
						},
						items: [
							{
								xtype: 'textfield',
								fieldLabel: _('email'),
								name: 'email'
							},
							{
								xtype: 'textfield',
								fieldLabel: _('phone_number'),
								name: 'phone_number'
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
					{
						type: 'tbtext',
						text: _('facilities')
					},
					'->',
					{
						text: _('facility'),
						iconCls: 'icoAdd',
						itemId: 'ReferringProviderFacilityAddBtn'
					}
				],
				columns: [
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
						dataIndex: 'postal_code',
						width: 90,
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('zipcode'),
						dataIndex: 'state',
						width: 90,
						editor: {
							xtype: 'textfield'
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