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

Ext.define('App.view.patient.encounter.EncounterAddendumWindow', {
	extend: 'Ext.window.Window',
	requires: [

	],
	itemId: 'EncounterAddendumWindow',
	title: _('addendum'),
	closable: false,
	closeAction: 'hide',
	layout: 'fit',
	height: 400,
	width: 500,
	items: [
		{
			xtype: 'form',
			itemId: 'EncounterAddendumForm',
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			bodyPadding: 10,
			items: [
				{
					xtype: 'combobox',
					fieldLabel: _('source'),
					labelAlign: 'top',
					name: 'source',
					editable: false,
					allowBlank: false,
					store: [
						['provider',_('provider')],
						['patient',_('patient')],
						['referral',_('referral')],
						['other',_('other')]
					]
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('notes'),
					labelAlign: 'top',
					name: 'notes',
					allowBlank: false,
					flex: 1
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'EncounterAddendumFormCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'EncounterAddendumFormSaveBtn'
		}
	]
});