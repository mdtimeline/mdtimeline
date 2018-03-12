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

Ext.define('App.view.patient.windows.EncounterTransferWindow', {
	extend: 'Ext.window.Window',
	requires: [

	],
	title: _('transfer_to'),
	itemId: 'EncounterTransferWindow',
	layout: 'fit',
	closeAction: 'hide',
	modal: true,
	closable: false,
	items: [
		{
			xtype: 'form',
			items: [
				{
					xtype: 'patienlivetsearch',
					itemId: 'EncounterTransferPatientSearchField',
					allowBlank: false,
					hideLabel: false,
					labelAlign: 'top',
					fieldLabel: _('patient'),
					width: 400,
					margin: '5 15 15 15'
				}
			]
		}

	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'EncounterTransferWindowCancelBtn'
		},
		{
			text: _('transfer'),
			itemId: 'EncounterTransferWindowTransferBtn'
		}
	]
});