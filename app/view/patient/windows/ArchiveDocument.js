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

Ext.define('App.view.patient.windows.ArchiveDocument', {
    extend: 'Ext.window.Window',
    xtype: 'patientarchivedocumentwindow',
    draggable: false,
    modal: true,
    autoShow: true,
    closeAction: 'hide',
    title: _('archive_document'),
    items: [
        {
            xtype: 'form',
            bodyPadding: 10,
            width: 400,
            defaults: {
                xtype: 'textfield',
                anchor: '100%',
                labelWidth: 70
            },
            items: [
                {
                    name: 'id',
                    hidden: true
                },
                {
                    xtype: 'gaiaehr.combo',
                    fieldLabel: _('category'),
                    list: 102,
                    name: 'docTypeCode',
                    editable: false,
                    allowBlank: false
                },
                {
                    fieldLabel: _('title'),
                    name: 'title'
                },
                {
                    xtype: 'textareafield',
                    name: 'note',
                    fieldLabel: _('notes')
                },
                {
                    xtype: 'fieldset',
                    itemId: 'ArchiveDocumentOptionalFieldSet',
                    title: 'Print (' + _('optional') + ')',
                    margin: '0 0 0 10',
                    // width: 220,
                    items: [
                        // {
                        //     xtype: 'printerscombo',
                        //     itemId: 'archiveDocumentPrintersCombo',
                        //     emptyText: _('select_print_optional'),
                        //     fieldLabel: 'Printer',
                        //     name: 'printer',
                        //     editable: false,
                        //     width: 300
                        // },
                        {
                            xtype: 'numberfield',
                            itemId: 'archiveDocumentNumberOfJobCopies',
                            name: 'number_of_copies',
                            fieldLabel:_('number_of_copies'),
                            value: 1,
                            maxValue: 10,
                            minValue: 1,
                            hideLabel: false,
                            allowBlank: false,
                            width: 300
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: _('priority'),
                            name: 'priority',
                            store: Ext.create('Ext.data.Store', {
                                fields: ['value', 'display'],
                                data : [
                                    {"value": 1, "display":"High"},
                                    {"value": 2, "display":"Med"},
                                    {"value": 3, "display":"Low"},
                                ]
                            }),
                            value: 2,
                            displayField: 'display',
                            valueField: 'value',
                            editable: false,
                            width: 300
                        },
                        // {
                        //     xtype: 'checkbox',
                        //     name: 'printNow',
                        //     fieldLabel: 'Print Now',
                        // },
                    ]
                },
            ]
        }
    ],
    buttons: [
        {
            text: _('cancel'),
            handler: function (btn) {
                btn.up('window').close();
            }
        },
        {
            text: _('archive'),
            itemId: 'archiveBtn'
        }
    ]
});