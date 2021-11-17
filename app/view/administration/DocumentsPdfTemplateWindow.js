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

Ext.define('App.view.administration.DocumentsPdfTemplateWindow', {
	extend: 'Ext.window.Window',
	itemId: 'AdministrationDocumentsPdfTemplateWindow',
	title: _('pdf_template'),
	layout: {
		type: 'fit'
	},
	width: 400,
	items: [
		{
			xtype: 'form',
			itemId: 'AdministrationDocumentsPdfTemplateWindowForm',
			bodyPadding: 10,
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			defaults: {
				labelWidth: 120
			},
			items: [
				{
					xtype: 'activefacilitiescombo',
					anchor: '100%',
					name: 'facility_id',
					fieldLabel: _('facility')
				},
				{
					xtype: 'textfield',
					anchor: '100%',
					name: 'template',
					allowBlank: false,
					fieldLabel: _('template_path')
				},
				{
					xtype: 'textfield',
					anchor: '100%',
					name: 'body_font_family',
					fieldLabel: _('body_font_family')
				},
				{
					xtype: 'textfield',
					anchor: '100%',
					name: 'body_font_style',
					fieldLabel: _('body_font_style')
				},
				{
					xtype: 'numberfield',
					anchor: '100%',
					name: 'body_font_size',
					fieldLabel: _('body_font_size'),
					minValue: 1,
					maxValue: 100
				},
				{
					xtype: 'numberfield',
					anchor: '100%',
					name: 'body_margin_top',
					fieldLabel: _('body_margin_top')
				},
				{
					xtype: 'numberfield',
					anchor: '100%',
					name: 'body_margin_right',
					fieldLabel: _('body_margin_right')
				},
				{
					xtype: 'numberfield',
					anchor: '100%',
					name: 'body_margin_bottom',
					fieldLabel: _('body_margin_bottom')
				},
				{
					xtype: 'numberfield',
					anchor: '100%',
					name: 'body_margin_left',
					fieldLabel: _('body_margin_left')
				},
				{
					xtype: 'checkboxfield',
					anchor: '100%',
					name: 'header_line',
					fieldLabel: _('header_line')
				},
				{
					xtype: 'checkboxfield',
					anchor: '100%',
					name: 'footer_margin',
					fieldLabel: _('footer_margin')
				},
				{
					xtype: 'checkboxfield',
					anchor: '100%',
					name: 'is_interface_tpl',
					fieldLabel: _('is_interface_tpl')
				}


			]
		}
	],
	buttons: [
		{
			text: _('delete'),
			cls: 'btnRedBackground',
			itemId: 'AdministrationDocumentsPdfTemplateWindowDeleteBtn'
		},
		'->',
		{
			text: _('save'),
			itemId: 'AdministrationDocumentsPdfTemplateWindowSaveBtn'
		},
		{
			text: _('save_close'),
			itemId: 'AdministrationDocumentsPdfTemplateWindowSaveCloseBtn'
		},
		{
			text: _('cancel'),
			itemId: 'AdministrationDocumentsPdfTemplateWindowCancelBtn'
		}

	]
});