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

Ext.define('App.view.patient.windows.EncounterProcedureWindow', {
	extend: 'App.ux.window.Window',
	requires: [
		'App.ux.LiveSnomedProcedureSearch',
		'App.ux.LiveSnomedBodySiteSearch'
	],
	title: _('procedure'),
	itemId: 'EncounterProcedureWindow',
	closeAction: 'hide',
	modal: true,
	layout: 'fit',
	height: 600,
	width: 1000,
	items: [
		{
			xtype: 'form',
			itemId: 'EncounterProcedureForm',
			bodyPadding: 10,
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			items: [
				{
					xtype: 'container',
					layout: 'hbox',
					items: [
						{
							xtype: 'datefield',
							name: 'procedure_date',
							fieldLabel: _('date'),
							labelAlign: 'top',
							width: 150,
						}
					]
				},

				{
					xtype: 'container',
					layout: 'hbox',
					items: [
						{
							xtype: 'snomedliveproceduresearch',
							name: 'code_text',
							displayField: 'Term',
							valueField: 'Term',
							fieldLabel: _('procedure'),
							hideLabel: false,
							labelAlign: 'top',
							flex: 1,
							margin: '0 10 0 0',
							itemId: 'EncounterProcedureSnomedProcedureSearch',
						},
						{
							xtype: 'snomedlivebodysitesearch',
							name: 'target_site_code_text',
							displayField: 'Term',
							valueField: 'Term',
							fieldLabel: _('body_site'),
							hideLabel: false,
							labelAlign: 'top',
							width: 400,
							itemId: 'EncounterProcedureSnomedSiteSearch',
						},
					]
				},
				{
					xtype: 'textareafield',
					name: 'observation',
					fieldLabel: _('observation'),
					labelAlign: 'top',
					flex: 1
				},
				{
					xtype: 'combobox',
					name: 'status_code',
					fieldLabel: _('status'),
					labelAlign: 'top',
					itemId: 'EncounterProcedureStatusField',
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'EncounterProcedureFormCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'EncounterProcedureFormSaveBtn'
		}
	]

});
