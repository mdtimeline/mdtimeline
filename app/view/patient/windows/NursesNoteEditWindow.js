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

Ext.define('App.view.patient.windows.NursesNoteEditWindow', {
	extend: 'Ext.window.Window',
	requires: [

	],
	title: _('nurse_note'),
	itemId: 'NursesNoteEditWindow',
	layout: 'border',
	height: 700,
	width: 900,
	closeAction: 'hide',
	modal: true,
	closable: false,
	bodyPadding: 5,
	items: [
		{
			xtype: 'grid',
			title: _('snippets'),
			itemId: 'NursesNoteSnippetsGrid',
			region: 'west',
			width: 300,
			split: true,
			animate: false,
			hideHeaders: true,
			useArrows: true,
			rootVisible: false,
			singleExpand: true,
			collapsible: true,
			collapseMode: 'mini',
			animCollapse: false,
			hideCollapseTool: true,
			store: Ext.create('App.store.administration.NursesNoteSnippets',{
				groupField: 'category'
			}),
			features: [{ ftype:'grouping' }],
			tools: [
				{
					xtype: 'button',
					text: _('snippet'),
					iconCls: 'icoAdd',
					itemId: 'NursesNoteSnippetAddBtn'
				}
			],
			columns: [
				{
					text: _('edit'),
					width: 25,
					menuDisabled: true,
					xtype: 'actioncolumn',
					tooltip: 'Edit Snippet',
					align: 'center',
					icon: 'resources/images/icons/edit.png',
					handler: function(grid, rowIndex, colIndex, actionItem, event, record){
						var snippetCtrl = app.getController('patient.NursesNotes');
						snippetCtrl.onSnippetBtnEdit(grid, rowIndex, colIndex, actionItem, event, record);
					}
				},
				{
					text: _('description'),
					dataIndex: 'description',
					flex: 1
				}
			]
		},
		{
			xtype: 'form',
			itemId: 'NursesNoteEditWindowForm',
			region: 'center',
			bodyPadding: 15,
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			items: [
				{
					xtype: 'textarea',
					itemId: 'NursesNoteEditWindowFormNoteField',
					name: 'note',
					flex: 1
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'NursesNoteEditWindowCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'NursesNoteEditWindowSaveBtn'
		}
	]
});