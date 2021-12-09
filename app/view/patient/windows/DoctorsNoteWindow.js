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

Ext.define('App.view.patient.windows.DoctorsNoteWindow', {
	extend: 'Ext.window.Window',
	requires: [

	],
	title: _('patient_note'),
	itemId: 'DoctorsNoteWindow',
	layout: 'fit',
	height: 700,
	width: 900,
	closeAction: 'hide',
	modal: true,
	closable: false,
	items: [
		{
			xtype: 'form',
			itemId: 'PatientNoteForm',
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			items: [
				{
					xtype: 'fieldset',
					layout: 'hbox',
					title: _('general'),
					// height: 145,
					margin: 5,
					defaults: {
						margin: '0 0 5 0'
					},
					items: [
						{
							xtype: 'fieldcontainer',
							margin: '0 10 0 0',
							items: [
								{
									xtype: 'datefield',
									fieldLabel: _('order_date'),
									format: g('date_display_format'),
									name: 'order_date'
								},
								{
									xtype: 'documentstemplatescombo',
									fieldLabel: _('document'),
									name: 'template_id'
								}
							]
						},
						{
							xtype: 'fieldcontainer',
							items: [
								{
									xtype: 'datefield',
									fieldLabel: _('from'),
									format: g('date_display_format'),
									name: 'from_date',
									labelAlign: 'right'
								},
								{
									xtype: 'datefield',
									fieldLabel: _('to'),
									format: g('date_display_format'),
									name: 'to_date',
									labelAlign: 'right'
								}
							]
						}
					]
				},
				{
					xtype: 'fieldset',
					layout: 'fit',
					title: _('comments_body'),
					flex: 1,
					height: 145,
					margin: '0 5',
					items: [
						{
							xtype: 'textareafield',
							anchor: '100%',
							margin: '5 5 10 5',
							name: 'comments'
						}
					]
				},
				{
					xtype: 'fieldset',
					title: _('restrictions'),
					height: 145,
					margin: 5,
					autoScroll: true,
					items: [
						{
							xtype: 'multitextfield',
							name: 'restrictions'
						}
					]
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'DoctorsNoteWindowCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'DoctorsNoteWindowSaveBtn'
		}
	]
});