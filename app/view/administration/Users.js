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

Ext.define('App.view.administration.Users', {
	extend: 'App.ux.RenderPanel',
	requires: [
		'App.ux.form.fields.plugin.PasswordStrength',
		'App.ux.combo.ActiveSpecialties',
		'App.ux.combo.Departments',
		'App.ux.grid.exporter.Exporter'
	],
	pageTitle: _('users'),
	itemId: 'AdminUsersPanel',
	initComponent: function(){
		var me = this;

		me.userStore = Ext.create('App.store.administration.User', {
			remoteFilter: true,
			remoteSort: true,
			autoSync: false,
			pageSize: 100
		});

		me.userGrid = Ext.create('Ext.grid.Panel', {
			itemId: 'AdminUserGridPanel',
			store: me.userStore,
			columns: [
				{
					text: 'id',
					sortable: false,
					dataIndex: 'id',
					width: 50
				},
				{
					width: 150,
					text: _('username'),
					sortable: true,
					dataIndex: 'username',
					items: [
						{
							xtype: 'columnsearchfield',
							autoSearch: true,
							operator: 'LIKE',
							suffix: '%'
						}
					]
				},
				{
					width: 200,
					text: _('name'),
					sortable: true,
					dataIndex: 'lname',
					renderer: function(v, meta, rec){
						return rec.get('fullname');
					},
					items: [
						{
							xtype: 'columnsearchfield',
							autoSearch: true,
							operator: 'LIKE',
							suffix: '%'
						}
					]
				},
				{
					flex: 1,
					text: _('role'),
					sortable: false,
					dataIndex: 'role'
				},
				{
					flex: 1,
					text: _('department'),
					sortable: false,
					dataIndex: 'department'
				},
				{
					flex: 1,
					text: _('aditional_info'),
					sortable: true,
					dataIndex: 'notes'
				},
				{
					text: _('authy_id'),
					sortable: true,
					dataIndex: 'authy_id'
				},
				{
					text: _('active'),
					sortable: true,
					dataIndex: 'active',
					renderer: me.boolRenderer
				},
				{
					text: _('authorized'),
					sortable: true,
					dataIndex: 'authorized',
					renderer: me.boolRenderer
				},
				{
					text: _('is_attending'),
					sortable: true,
					dataIndex: 'is_attending',
					renderer: me.boolRenderer
				}
			],
			plugins: [
				me.formEditing = Ext.create('App.ux.grid.RowFormEditing', {
					clicksToEdit: 2,
					items: [
						{
							xtype: 'tabpanel',
							items: [
								{
									title: _('general'),
									itemId: 'UserGridEditFormContainer',
									layout: 'hbox',
									items: [
										{
											xtype: 'container',
											itemId: 'UserGridEditFormContainerLeft',
											items: [
												{
													xtype: 'fieldcontainer',
													layout: {
														type: 'hbox'
													},
													fieldDefaults: {
														labelAlign: 'right'
													},
													items: [
														{
															width: 250,
															xtype: 'textfield',
															fieldLabel: _('username'),
															name: 'username',
															allowBlank: false,
															validateOnBlur: true,
															vtype: 'usernameField'
														},
														{
															width: 200,
															labelWidth: 60,
															xtype: 'textfield',
															fieldLabel: _('password'),
															name: 'password',
															inputType: 'password',
															vtype: 'strength',
															strength: 24,
															plugins: {
																ptype: 'passwordstrength'
															}
														},
														{
															width: 125,
															labelWidth: 40,
															xtype: 'textfield',
															fieldLabel: _('pin'),
															name: 'pin',
															inputType: 'password'
														}
													]
												},
												{
													xtype: 'fieldcontainer',
													layout: {
														type: 'hbox'
													},
													fieldDefaults: {
														labelAlign: 'right'
													},
													fieldLabel: _('name'),
													items: [
														{
															width: 50,
															xtype: 'mitos.titlescombo',
															name: 'title'
														},
														{
															width: 145,
															xtype: 'textfield',
															name: 'fname',
															allowBlank: false
														},
														{
															width: 100,
															xtype: 'textfield',
															name: 'mname'
														},
														{
															width: 175,
															xtype: 'textfield',
															name: 'lname'
														}
													]
												},
												{
													xtype: 'fieldcontainer',
													layout: {
														type: 'hbox'
													},
													fieldDefaults: {
														labelAlign: 'right'
													},
													items: [
														{
															width: 125,
															xtype: 'checkbox',
															fieldLabel: _('active'),
															name: 'active'
														},
														{
															width: 100,
															labelWidth: 85,
															xtype: 'checkbox',
															fieldLabel: _('authorized'),
															name: 'authorized'
														},
														{
															width: 100,
															labelWidth: 85,
															xtype: 'checkbox',
															fieldLabel: _('calendar_q'),
															name: 'calendar'
														},
														{
															width: 250,
															labelWidth: 50,
															xtype: 'gaiaehr.combo',
															fieldLabel: _('type'),
															name: 'doctor_type',
															list: 121,
															loadStore: true
														}
													]
												},
												{
													xtype: 'fieldcontainer',
													layout: {
														type: 'hbox'
													},
													fieldDefaults: {
														labelAlign: 'right'
													},
													items: [
														{
															width: 280,
															xtype: 'mitos.facilitiescombo',
															fieldLabel: _('default_facility'),
															name: 'facility_id'
														},
														// {
														// 	width: 300,
														// 	xtype: 'combobox',
														// 	fieldLabel: _('ldap_domain'),
														// 	editable: false,
														// 	name: 'ldap_domain',
														// 	queryMode: 'local',
														// 	store: g('ldap_user_domains') ? g('ldap_user_domains').split(',') : []
														// }
													]
												},
												{
													xtype: 'fieldcontainer',
													layout: {
														type: 'hbox'
													},
													fieldDefaults: {
														labelAlign: 'right'
													},
													items: [
														{
															width: 280,
															xtype: 'depatmentscombo',
															fieldLabel: _('department'),
															name: 'department_id',
															allowBlank: false
														},
														{
															width: 300,
															xtype: 'mitos.rolescombo',
															fieldLabel: _('access_control'),
															name: 'role_id',
															allowBlank: false
														}
													]
												}
											]
										},
										{
											xtype: 'container',
											itemId: 'UserGridEditFormContainerRight',
											items: []
										}
									]
								},
								{
									xtype: 'panel',
									title: _('contact_info'),
									itemId: 'UserGridEditFormContactInfoPanel',
									layout: 'column',
									bodyPadding: 10,
									items: [
										{
											xtype: 'fieldset',
											title: _('address'),
											margin: '0 10 0 0',
											padding: 5,
											defaults: {
												margin: '0 0 5 0',
												width: 300
											},
											items: [
												{
													xtype: 'textfield',
													fieldLabel: _('address'),
													name: 'street'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('address_cont'),
													name: 'street_cont'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('city'),
													name: 'city'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('state'),
													name: 'state'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('postal_code'),
													name: 'postal_code'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('country_code'),
													name: 'country_code'
												}
											]
										},
										{
											xtype: 'fieldset',
											title: _('phone') + ' / ' + _('email'),
											padding: 5,
											defaults: {
												margin: '0 0 5 0',
												width: 300
											},
											items: [
												{
													xtype: 'textfield',
													fieldLabel: _('home'),
													name: 'phone'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('mobile'),
													name: 'mobile'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('email'),
													name: 'email',
													vtype: 'email'
												}
											]
										}
									]
								},
								{
									xtype: 'panel',
									title: _('provider'),
									itemId: 'UserGridEditFormProviderPanel',
									layout: 'hbox',
									items: [
										{
											xtype: 'fieldcontainer',
											itemId: 'UserGridEditFormProviderPanelLeft',
											fieldDefaults: {
												labelAlign: 'right',
												width: 500,
												margin: '0 0 5 0'
											},
											margin: '20 10 0 0',
											items: [
												{
													xtype: 'checkbox',
													fieldLabel: _('is_attending'),
													name: 'is_attending'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('federal_tax_id'),
													name: 'fedtaxid'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('fed_drug_id'),
													name: 'feddrugid'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('state_drug_id'),
													name: 'statedrugid'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('lic'),
													name: 'lic'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('npi'),
													name: 'npi',
													maxLength: 10,
													vtype: 'npi'
												},
												{
													xtype: 'activespecialtiescombo',
													fieldLabel: _('specialties'),
													name: 'specialty',
													margin: '5 0',
													labelAlign: 'right',
													multiSelect: true
												},
												{
													xtype: 'textfield',
													fieldLabel: _('additional_info'),
													name: 'notes',
													labelAlign: 'right'
												},
												{
													xtype: 'textfield',
													fieldLabel: _('signature'),
													name: 'signature',
													labelAlign: 'right'
												}
											]
										},
										{
											xtype: 'grid',
											title: _('provider_credentialization'),
											itemId: 'UserGridEditFormProviderCredentializationGrid',
											flex: 1,
											maxHeight: 210,
											frame: true,
											store: Ext.create('App.store.administration.ProviderCredentializations', {
												pageSize: 1000
											}),
											plugins:[
												{
													ptype:'cellediting'
												}
											],
											tools: [
												{
													xtype: 'button',
													text: _('active_all'),
													icon: 'resources/images/icons/yes.png',
													margin: '0 5 0 0',
													itemId: 'UserGridEditFormProviderCredentializationActiveBtn'
												},
												{
													xtype:'button',
													text: _('inactive_all'),
													icon: 'resources/images/icons/no.png',
													itemId: 'UserGridEditFormProviderCredentializationInactiveBtn'
												}
											],
											columns: [
												{
													text: _('insurance'),
													width: 150,
													dataIndex: 'insurance_company_id',
													renderer: function(v, meta, record){
														return record.data.insurance_company_id +
															': ' +
															record.data.insurance_company_name;
													}
												},
												{
													xtype:'datecolumn',
													format: g('date_display_format'),
													text: _('start'),
													dataIndex: 'start_date',
													editor: {
														xtype: 'datefield'
													}
												},
												{
													xtype:'datecolumn',
													format: g('date_display_format'),
													text: _('end'),
													dataIndex: 'end_date',
													editor: {
														xtype: 'datefield'
													}
												},
												{
													text: _('note'),
													dataIndex: 'credentialization_notes',
													flex: 1,
													editor: {
														xtype: 'textfield'
													}
												},
												{
													text: _('active'),
													dataIndex: 'active',
													renderer: app.boolRenderer
												}
											]
										}
									]
								}
							]
						}

					]
				})
			],
			tbar: [
				{
					xtype: 'button',
					text: _('user'),
					iconCls: 'icoAdd',
					scope: me,
					handler: me.onNewUser
				},
				'->',
				{
					xtype: 'button',
					text: _('authy_register'),
					itemId: 'AdminUserGridPanelAuthyRegisterBtn'
				},
				'-',
				{
					xtype: 'button',
					text: _('print'),
					iconCls: 'icoPrint',
					itemId: 'AdminUserGridPanelPrintBtn'
				},
				'-',
				{
					xtype: 'exporterbutton',
					text: 'Save As CSV',
				},
				'-',
				{
					xtype: 'exporterbutton',
					text: 'Save As XLS',
					format: 'excel'
				}
			],
			bbar: {
				xtype: 'pagingtoolbar',
				pageSize: 1000,
				store: me.userStore,
				displayInfo: true,
				plugins: new Ext.ux.SlidingPager()
			}

		});

		me.pageBody = [me.userGrid];
		me.callParent(arguments);

	},

	onNewUser: function(){
		var me = this;

		me.formEditing.cancelEdit();
		me.userStore.insert(0, {
			create_date: new Date(),
			update_date: new Date(),
			create_uid: app.user.id,
			update_uid: app.user.id,
			active: 1,
			authorized: 0,
			calendar: 0
		});
		me.formEditing.startEdit(0, 0);
	},

	/**
	 * This function is called from Viewport.js when
	 * this panel is selected in the navigation panel.
	 * place inside this function all the functions you want
	 * to call every this panel becomes active
	 */
	onActive: function(callback){
		this.userStore.load();
		callback(true);
	}

});
