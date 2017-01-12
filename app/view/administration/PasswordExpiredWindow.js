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

Ext.define('App.view.administration.PasswordExpiredWindow', {
	extend: 'Ext.window.Window',
	layout: 'fit',
	title: _('password_expired'),
	itemId: 'PasswordExpiredWindow',
	// width: 400,
	// height: 200,

	modal: true,
	closable: false,
	resizable: false,

	initComponent: function () {
		var me = this;

		me.items = [
			{
				xtype: 'form',
				bodyPadding: 20,
				itemId: 'PasswordExpiredWindowForm',
				items: [
					{
						xtype: 'textfield',
						fieldLabel: _('password'),
						labelAlign: 'top',
						name: 'old_password',
						inputType: 'password',
						width: 200,
						itemId: 'PasswordExpiredWindowPassword',
						allowBlank: false
					},
					{
						xtype: 'textfield',
						fieldLabel: _('new_password'),
						labelAlign: 'top',
						name: 'new_password',
						inputType: 'password',
						vtype: 'strength',
						strength: 24,
						plugins: {
							ptype: 'passwordstrength'
						},
						width: 200,
						itemId: 'PasswordExpiredWindowPasswordField'
					},
					{
						xtype: 'textfield',
						fieldLabel: _('confirmation'),
						labelAlign: 'top',
						name: 'confirmation_password',
						inputType: 'password',
						width: 200,
						enableKeyEvents: true,
						itemId: 'PasswordExpiredWindowConfirmPasswordField',
						validator: function (value) {

							if(app){
								return app.getController('administration.Users').passwordConfirmationValidation(value);
							}

							return true;
						}
					}
				]
			}
		];

		me.callParent();
	},
	buttons: [
		{
			xtype: 'button',
			text: _('save'),
			itemId: 'PasswordExpiredWindowUpdateBtn'
		}
	]
});