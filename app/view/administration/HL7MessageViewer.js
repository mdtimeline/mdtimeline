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


Ext.define('App.view.administration.HL7MessageViewer', {
	xtype: 'hl7messageviewer',
	extend: 'Ext.window.Window',
	title: _('hl7_viewer'),
	itemId: 'HL7MessageViewerWindow',
	width: 1200,
	maximizable: true,
	modal: true,
	layout: 'fit',
	items: [
		{
			xtype: 'form',
			itemId: 'HL7MessageViewerForm',
			bodyPadding: 10,
			border: false,
			fieldDefaults: {
				labelAlign: 'top',
				readOnly: true,
				anchor: '100%'
			},
			items: [
				{
					xtype: 'hiddenfield',
					name: 'id'
				},
				{
					xtype: 'hiddenfield',
					name: 'isOutbound'
				},
				{
					xtype: 'hiddenfield',
					name: 'reference'
				},
				{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					items:[
						{
							fieldLabel: _('type'),
							xtype: 'textfield',
							name: 'msg_type',
							margin: '0 5 0 0',
							flex: 1
						},
						{
							fieldLabel: _('date'),
							xtype: 'textfield',
							name: 'date_processed',
							flex: 1
						}
					]
				},
				{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					items:[
						{
							fieldLabel: _('facility'),
							xtype: 'textfield',
							name: 'foreign_facility',
							margin: '0 5 0 0',
							flex: 1
						},
						{
							fieldLabel: _('application'),
							xtype: 'textfield',
							name: 'foreign_application',
							flex: 1
						}
					]
				},
				{
					xtype: 'button',
					text: 'Edit Message',
					itemId: 'HL7MessageViewerMessageEditBtn',
					enableToggle: true
				},
				{
					xtype: 'textareafield',
					fieldLabel: 'Editable Message',
					name: 'raw_message',
					readOnly: false,
					height: 250,
					hidden: true,
					itemId: 'HL7MessageViewerRawMessageField',
				},
				{
					xtype: 'htmleditor',
					fieldLabel: _('message'),
					name: 'message',
					height: 250,
					enableAlignments: false,
					enableColors: false,
					enableFont: false,
					enableFontSize: false,
					enableFormat: false,
					enableLinks: false,
					enableLists: false,
					enableSourceEdit: false,
					itemId: 'HL7MessageViewerMessageField',
				},
				{
					xtype: 'htmleditor',
					fieldLabel: _('acknowledge'),
					name: 'response',
					readOnly: true,
					height: 100,
					enableAlignments: false,
					enableColors: false,
					enableFont: false,
					enableFontSize: false,
					enableFormat: false,
					enableLinks: false,
					enableLists: false,
					enableSourceEdit: false
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('error'),
					name: 'error',
					readOnly: true,
					height: 60
				}
			]
		}
	],
	buttons: [
		{
			text: _('resend'),
			itemId: 'HL7MessageViewerWindowResendBtn'
		},
		{
			text: '<',
			itemId: 'HL7MessageViewerWindowPreviousBtn'
		},
		{
			text: '>',
			itemId: 'HL7MessageViewerWindowNextBtn'
		},
		{
			text: _('close'),
			itemId: 'HL7MessageViewerWindowCloseBtn'
		}
	]
}); 