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

Ext.define('App.controller.administration.Users', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'AdminUsersPanel',
			selector: '#AdminUsersPanel'
		},
		{
			ref: 'AdminUserGridPanel',
			selector: '#AdminUserGridPanel'
		},
		{
			ref: 'PasswordExpiredWindow',
			selector: '#PasswordExpiredWindow'
		},
		{
			ref: 'PasswordExpiredWindowForm',
			selector: '#PasswordExpiredWindowForm'
		},
		{
			ref: 'PasswordExpiredWindowPasswordField',
			selector: '#PasswordExpiredWindowPasswordField'
		},
		{
			ref: 'PasswordExpiredWindowConfirmPasswordField',
			selector: '#PasswordExpiredWindowConfirmPasswordField'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				afterrender: me.onApplicationAfterRender
			},
			'#AdminUserGridPanel': {
				beforeedit: me.onAdminUserGridPanelBeforeEdit
			},
			'#UserGridEditFormProviderCredentializationActiveBtn': {
				click: me.onUserGridEditFormProviderCredentializationActiveBtnClick
			},
			'#UserGridEditFormProviderCredentializationInactiveBtn': {
				click: me.onUserGridEditFormProviderCredentializationInactiveBtnClick
			},

			'#AdminUserGridPanelPrintBtn': {
				click: me.onAdminUserGridPanelPrintBtnClick
			},
			'#PasswordExpiredWindowUpdateBtn': {
				click: me.onPasswordExpiredWindowUpdateBtnClick
			},
			'#SwitchUserBtn': {
				click: me.onSwitchUserBtnClick
			},
			'#AdminUserGridPanelAuthyRegisterBtn': {
				click: me.onAdminUserGridPanelAuthyRegisterBtnClick
			},
			'#AdminUserGridPanelLDAPSyncBtn': {
				click: me.onAdminUserGridPanelLDAPSyncBtnClick
			}
		});
	},

	onAdminUserGridPanelLDAPSyncBtnClick: function(btn){

		LDAP.Sync(function (response) {

			if(response.success){
				app.msg(_('oops'), response.message);
				btn.up('grid').getStore().reload();
			}else {
				app.msg(_('oops'), response.error, true);
			}

		});
	},

	onAdminUserGridPanelAuthyRegisterBtnClick: function(btn){
		var user_grid = this.getAdminUserGridPanel(),
			user_store = user_grid.getStore(),
			user_record = user_grid.getSelectionModel().getLastSelected();

		say(user_record);

		TwoFactorAuthentication.registerUserByIdAndType(
			user_record.get('id'),
			'application',
			user_record.get('email'),
			user_record.get('mobile').replace(/[-() ]/g,''),
			function (response) {
				say(response);
				if(!response.success){
					app.msg(_('oops'), response.errors.replace(/,/g, '<br>'), true);
				}else {
					user_record.set({authy_id: response.authy_id});
					user_record.commit();
					user_grid.view.refresh();
				}
			}
		);

	},

	onAdminUserGridPanelPrintBtnClick: function(){

		var grid = this.getAdminUserGridPanel(),
			store = grid.getStore();

		store.load({
			start: 0,
			limit: 5000,
			callback: function () {
				App.ux.grid.Printer.mainTitle = 'User Report';
				App.ux.grid.Printer.filtersHtml = '------------'; //optional
				App.ux.grid.Printer.print(grid, null);
			}
		});

	},

	/***********************************************
	 ** passwrod expiration
	 ***********************************************/

	onApplicationAfterRender: function (comp) {
		if(comp.user.password_expired){
			this.doPasswordExpiredUpdate();
		}

		this.validateProviderUser();
	},

	doPasswordExpiredUpdate: function () {
		this.showPasswordExpiredWindow();
	},

	showPasswordExpiredWindow: function () {
		if(!this.getPasswordExpiredWindow()){
			Ext.create('App.view.administration.PasswordExpiredWindow');
		}
		return this.getPasswordExpiredWindow().show();
	},

	passwordConfirmationValidation: function (value) {
		if(this.getPasswordExpiredWindowPasswordField().getValue() === value){
			return true
		}

		return _('password_confirmation_error');
	},

	onPasswordExpiredWindowUpdateBtnClick: function () {
		say('onPasswordExpiredWindowUpdateBtnClick');

		var win = this.getPasswordExpiredWindow(),
			form = this.getPasswordExpiredWindowForm().getForm(),
			values = form.getValues();

		if (!form.isValid()) return;

		if(values['old_password'] == ''){
			return;
		}

		if(values['new_password'] != values['confirmation_password']){
			return;
		}

		values.id = app.user.id;

		User.updatePassword(values, function (response) {

			if(response.success){
				app.msg(_('sweet'), _('password_changed'));
				form.reset();
				win.close();
				return;
			}

			app.msg(_('oops'), _(response.message), true);
		});
	},

	onAdminUserGridPanelBeforeEdit: function(plugin, context){
		var grid = plugin.editor.down('grid'),
			store = grid.getStore(),
			filters = [
				{
					property: 'provider_id',
					value: context.record.data.id
				}
			],
			params = {};

		store.clearFilter(true);
		if(context.record.data.id > 0 && context.record.data.npi != ''){
			params = {
				providerId: context.record.data.id,
				fullList: true
			};
			Ext.Array.push(filters, {
				property: 'provider_id',
				value: null
			});
		}

		store.load({
			filters: filters,
			params: params
		});
	},

	onUserGridEditFormProviderCredentializationActiveBtnClick: function(btn){
		var store = btn.up('grid').getStore(),
			records = store.data.items,
			now = Ext.Date.format(new Date(), 'Y-m-d');

		for(var i = 0; i < records.length; i++){
			records[i].set({
				start_date: now,
				end_date: '9999-12-31',
				active: true
			});
		}
	},

	onUserGridEditFormProviderCredentializationInactiveBtnClick: function(btn){
		var store = btn.up('grid').getStore(),
			records = store.data.items,
			date = new Date(),
			yesterday = Ext.Date.format(Ext.Date.subtract(date, Ext.Date.DAY, 1), 'Y-m-d');

		for(var i = 0; i < records.length; i++){
			records[i].set({
				start_date: yesterday,
				end_date: yesterday,
				active: false
			});
		}
	},

	onSwitchUserBtnClick: function () {
		this.getController('LogOut').doApplicationLock();
	},

	validateProviderUser: function (){
		// npi
		// lic
		// signature

		if(
			(Boolean(app.user['is_attending']) || Boolean(app.user['is_resident'])) &&
			(!Boolean(app.user.npi) || !Boolean(app.user.lic) || !Boolean(app.user.signature))
		){
			var win = Ext.create('Ext.Window', {
				title: 'Provider Required Info Missing',
				modal: true,
				bodyPadding: 5,
				items: [
					{
						xtype: 'form',
						bodyPadding: 10,
						width: 500,
						defaults:{
							allowBlank: false,
							labelAlign: 'top',
						},
						items: [
							{
								xtype: 'textfield',
								fieldLabel: 'Provider NPI',
								name: 'npi',
								maxLength: 15,
								vtype: 'npi'
							},
							{
								xtype: 'textfield',
								fieldLabel: 'Provider Lic.',
								name: 'lic',
								maxLength: 20,
							},
							{
								xtype: 'textfield',
								fieldLabel: 'Provider Signature',
								emptyText: 'ei: JOHN SMITH DOE, MD, DBAR LIC. 0000',
								name: 'signature',
								maxLength: 250,
								anchor: '100%'
							}
						]
					}
				],
				buttons: [
					{
						text: 'Save',
						handler: function (btn){

							var win = btn.up('window'),
								form  = win.down('form').getForm(),
							  	values = form.getValues();

							if(!form.isValid()) return;

							values.id = app.user.id;

							User.updateUser(values, function (){
								app.user.npi = values.npi;
								app.user.lic = values.lic;
								app.user.signature = values.signature;
								win.close();
							});

						}
					}
				]
			}).show();

			var form = win.down('form').getForm();
			form.setValues(app.user);
			form.isValid();

		}

	}
});