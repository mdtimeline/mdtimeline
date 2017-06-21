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

Ext.define('App.view.administration.ApplicationLockWindow', {
	extend: 'Ext.window.Window',
	itemId: 'ApplicationLockWindow',
	title: 'LOCKED',
	layout: 'fit',
	width: 250,
	height: 150,

	plain: true,
	modal: false,
	resizable: false,
	draggable: false,
	closable: false,
	frame: false,
	border: false,
	cls: 'login-window',
	shadow: false,
	items: [
		{
			xtype: 'textfield',
			name: 'pin',
			inputType: 'password',
			itemId: 'ApplicationLockWindowPingField',
			allowBlank: false,
			inputAttrTpl: 'style="text-align:center;font-size:40px"',
			enableKeyEvents: true
		}
	],
	buttons: [
		{
			xtype: 'button',
			scale: 'medium',
			text: _('logout'),
			itemId: 'ApplicationLockWindowLogoutBtn'
		},
		'->',
		{
			xtype: 'button',
			scale: 'medium',
			text: _('ok'),
			itemId: 'ApplicationLockWindowOkBtn'
		}
	]
});