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

Ext.define('App.view.administration.EncounterTemplateWindow', {
    extend: 'Ext.window.Window',
    requires: [
        'App.ux.grid.DeleteColumn',
        'App.ux.combo.ActiveSpecialties'
    ],
    title: _('encounter_template'),
    itemId: 'AdministrationEncounterTemplateWindow',
    height: 500,
    width: 700,
    modal: true,
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    items: [
        {
            xtype: 'form',
            bodyPadding: 10,
            itemId: 'AdministrationEncounterTemplateForm',
            items: [
                {
                    xtype: 'activespecialtiescombo',
                    fieldLabel: _('specialty'),
                    name: 'specialty_id',
                    allowBank: false
                },
                {
                    xtype: 'textfield',
                    fieldLabel: _('description'),
                    name: 'description',
                    allowBank: false
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: _('active'),
                    name: 'active'
                }
            ]
        },
        {
            xtype: 'grid',
            itemId: 'AdministrationEncounterTemplateGrid',
            flex: 1,
            frame: true,
	        store: Ext.create('App.store.administration.EncounterTemplatePanelTemplates'),
            columns: [
                {
                    xtype: 'griddeletecolumn',
                    acl: true
                },
                {
                    text: _('type'),
                    dataIndex: 'template_type'
                },
                {
                    text: _('description'),
                    dataIndex: 'description',
                    flex: 1,
                },
                {
                    text: _('active'),
                    dataIndex: 'active'
                }
            ],
            tbar: [
                _('items'),
                '->',
                {
                    xtype: 'button',
                    text: _('rad_order'),
                    iconCls: 'icoAdd',
                    itemId: 'AdministrationEncounterTemplateRadOrderAddBtn'
                },
                '-',
                {
                    xtype: 'button',
                    text: _('lab_order'),
                    iconCls: 'icoAdd',
                    itemId: 'AdministrationEncounterTemplateLabOrderAddBtn'
                }
            ]
        }
    ],
    buttons: [
        {
            xtype: 'button',
            text: _('cancel'),
            itemId: 'AdministrationEncounterTemplateCancelBtn'
        },
        {
            xtype: 'button',
            text: _('save'),
            itemId: 'AdministrationEncounterTemplateSaveBtn'
        }
    ]

});
