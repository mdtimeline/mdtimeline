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

Ext.define('App.view.patient.encounter.Snippets', {
	extend: 'Ext.window.Window',
	xtype: 'snippetswindow',
	requires: [

	],
	itemId: 'SnippetWindow',
	title: _('snippet'),
	closable: false,
	width: 800,
	modal: true,
	items: [
		{
			xtype: 'form',
			itemId: 'SnippetForm',
			border: false,
			bodyPadding: 10,
			fieldDefaults: {
				labelAlign: 'top',
				anchor: '100%',
				margin: '0 0 10 0'
			},
			items: [
				{
					xtype: 'textfield',
					fieldLabel: _('description'),
					name: 'description',
					allowBlank: false
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('subjective'),
					name: 'subjective'
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('objective'),
					name: 'objective'
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('assessment'),
					name: 'assessment'
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('instructions'),
					name: 'instructions'
				},
				{
					xtype: 'textfield',
					fieldLabel: _('diagnoses'),
					name: 'diagnoses'
				}
			]
		}
	],
	buttons:[
		{
			text: _('delete'),
			itemId: 'SnippetDeleteBtn'
		},
		'->',
		{
			text: _('cancel'),
			itemId: 'SnippetCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'SnippetSaveBtn'
		}
	]
});