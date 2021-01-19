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

Ext.define('App.view.scanner.BasicScanWindow', {
	extend: 'Ext.window.Window',
	xtype: 'basiccanwindow',
	itemId: 'BasicScanWindow',
	bodyPadding: 10,
	closeAction: 'hide',
	title: _('scanner'),
	layout: {
		type: 'vbox',
		align: 'stretch'
	},
	modal: true,
	items: [
		{
			xtype: 'combobox',
			itemId: 'BasicScanSourceCombo',
			editable: false,
			queryMode: 'local',
			displayField: 'name',
			valueField: 'id',
			fieldLabel: _('scanner'),
			allowBlank: false,
			name: 'scannerId',
			width: 300,
			labelAlign: 'top',
			store: Ext.create('Ext.data.Store', {
				fields: [
					{
						name: 'id',
						type: 'string'
					},
					{
						name: 'name',
						type: 'string'
					}
				]
			})
		},
		{
			xtype: 'progressbar',
			margin: '5 0 5 0'
		}
	],
	buttons: [
		{
			xtype: 'checkbox',
			boxLabel: _('landscape'),
			itemId: 'BasicScanLandscapeCheckbox',
			stateful: true,
			stateId: 'BasicScanLandscapeCheckboxState'
		},
		'->',
		{
			text: _('cancel'),
			itemId: 'BasicScanCancelBtn'
		},
		{
			text: _('scan'),
			itemId: 'BasicScanScanBtn'
		}
	]
});