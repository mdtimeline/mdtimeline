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

Ext.define('App.view.patient.windows.NursesNoteSnippetEditWindow', {
	extend: 'Ext.window.Window',
	requires: [

	],
	title: _('snippet'),
	itemId: 'NursesNoteSnippetEditWindow',
	layout: 'fit',
	width: 400,
	closeAction: 'hide',
	modal: true,
	closable: false,
	bodyPadding: 5,
	items: [
		{
			xtype: 'form',
			itemId: 'NursesNoteSnippetEditWindowForm',
			bodyPadding: 15,
			items: [
				{
					xtype: 'textfield',
					name: 'description',
					fieldLabel: _('description'),
					labelAlign: 'top',
					anchor: '100%'
				},
				{
					xtype: 'combo',
					name: 'category',
					fieldLabel: _('category'),
					labelAlign: 'top',
					anchor: '100%',
					store: [
						'Meds Intravenous',
						'Meds Intramuscular',
						'Serum/Infiltrations'
					]
				},
				{
					xtype: 'textareafield',
					name: 'snippet',
					fieldLabel: _('snippet'),
					labelAlign: 'top',
					height: 200,
					anchor: '100%'
				},
				{
					xtype: 'userscombo',
					fieldLabel: _('owner'),
					labelAlign: 'top',
					includeAllOption: true,
					name: 'uid',
					anchor: '100%'
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'NursesNoteSnippetEditWindowCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'NursesNoteSnippetEditWindowSaveBtn'
		}
	]
});