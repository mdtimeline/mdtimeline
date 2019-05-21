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

Ext.define('App.view.patient.encounter.InterventionWindow', {
	extend: 'Ext.window.Window',
	requires: [
		'App.ux.LiveSnomedProcedureSearch'
	],
	itemId: 'InterventionWindow',
	title: _('intervention'),
	closeAction: 'hide',
	layout: 'fit',
	width: 400,
	height: 400,
	items: [
		{
			xtype: 'form',
			itemId: 'InterventionForm',
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			bodyPadding: 10,
			items: [
				{
					xtype: 'combo',
					name: 'intervention_type',
					fieldLabel: _('type'),
					labelAlign: 'top',
					queryMode: 'local',
					editable: false,
					store: [
						'RecommendedNutrition',
						'EntryReference'
					]
				},
				{
					xtype: 'snomedliveproceduresearch',
					itemId: 'InterventionSearchField',
					fieldLabel: _('intervention'),
					displayField: 'Term',
					valueField: 'Term',
					labelAlign: 'top',
					allowBlank: false,
					hideLabel: false,
					name: 'code_text'
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('instructions'),
					labelAlign: 'top',
					name: 'notes',
					flex: 1
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'InterventionFormCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'InterventionFormSaveBtn'
		}
	]
});
