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

Ext.define('App.view.administration.EmailTemplateWindow', {
    extend: 'Ext.window.Window',
    requires: [
        'App.ux.combo.ActiveFacilities'
    ],
    title: _('email_template'),
    itemId: 'AdministrationEmailTemplateWindow',
    height: 700,
    width: 900,
    modal: true,
    layout: 'fit',
    items: [
        {
            xtype: 'form',
            bodyPadding: 10,
            itemId: 'AdministrationEmailTemplateForm',
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    items: [
                        {
                            xtype: 'container',
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            margin: '0 10 0 0',
                            items: [
                                {
                                    xtype: 'activefacilitiescombo',
                                    fieldLabel: _('facility'),
                                    name: 'facility_id',
                                    allowBank: false
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: _('template_type'),
                                    name: 'template_type',
                                    allowBank: false
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: _('language'),
                                    name: 'language',
                                    store: ['en', 'es'],
                                    allowBank: false,
                                    editable: false
                                },
                            ]
                        },
                        {
                            xtype: 'container',
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: _('subject'),
                                    name: 'subject',
                                    allowBank: false,
                                    width: 400
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: _('from_email'),
                                    name: 'from_email',
                                    width: 400
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: _('from_name'),
                                    name: 'from_name',
                                    width: 400
                                },
                            ]
                        },
                    ]
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: _('active'),
                    name: 'active'
                },
                {
                    xtype: 'htmleditor',
                    fieldLabel: _('body'),
                    name: 'body',
                    itemId: 'AdministrationEmailTemplateBodyField',
                    flex: 1
                },
            ]
        }
    ],
    buttons: [
        {
            xtype: 'button',
            text: _('cancel'),
            itemId: 'AdministrationEmailTemplateCancelBtn'
        },
        {
            xtype: 'button',
            text: _('save'),
            itemId: 'AdministrationEmailTemplateSaveBtn'
        }
    ]

});
