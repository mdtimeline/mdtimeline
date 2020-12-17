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

Ext.define('App.view.administration.ContentManagementWindow', {
	extend: 'Ext.window.Window',
	itemId: 'ContentManagementWindow',
	title: _('content_management'),
	layout: {
		type: 'fit'
	},
	width: 1100,
	height: 700,
	items: [
		{
			xtype: 'form',
			itemId: 'ContentManagementWindowForm',
			bodyPadding: 10,
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			items: [
				{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					items: [
						{
							xtype: 'textfield',
							name: 'content_type',
							fieldLabel: _('content_type'),
							readOnly: true,
							labelAlign: 'top',
							flex: 1,
							margin: '0 10 0 0',
							allowBlank: false
						},
						{
							xtype: 'textfield',
							name: 'content_lang',
							fieldLabel: _('language'),
							readOnly: true,
							labelAlign: 'top',
							flex: 1,
							margin: '0 10 0 0',
							allowBlank: false
						}
					]
				},
				{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					items: [
						{
							xtype: 'textfield',
							name: 'content_version',
							fieldLabel: _('version'),
							labelAlign: 'top',
							flex: 1,
							margin: '0 10 0 0',
							readOnly: true,
							allowBlank: false
						},
						{
							xtype: 'checkboxfield',
							name: 'is_html',
							fieldLabel: _('is_html'),
							labelAlign: 'top',
							flex: 1,
							margin: '0 10 0 0'
						},
					]
				},
				{
					xtype: 'container',
					margin: 0,
					padding: 0,
					layout: {
						type: 'hbox',
						align: 'stretch'
					},
					flex: 1,
					items: [
						{
							xtype: 'textareafield',
							name: 'content_body',
							fieldLabel: _('body'),
							labelAlign: 'top',
							flex: 1,
							allowBlank: false,
							margin: '0 10 0 0',
							action: 'content_body'
						},
						{
							xtype: 'htmleditor',
							name: 'content_body',
							fieldLabel: _('body'),
							labelAlign: 'top',
							flex: 1,
							allowBlank: false,
							margin: '0 10 0 0',
							hidden: true,
							action: 'content_body'
						},
						{
							xtype: 'textareafield',
							itemId: 'ContentManagementWindowTokensTextArea',
							fieldLabel: _('tokens'),
							labelAlign: 'top',
							readOnly: true,
							submitValue: false,
							margin: 0,
							width: 300,
						}
					]

				}

			]
		}
	],
	buttons: [
		{
			text: _('save'),
			itemId: 'ContentManagementWindowSaveBtn'
		},
		{
			text: _('cancel'),
			itemId: 'ContentManagementWindowCancelBtn'
		}

	]
});