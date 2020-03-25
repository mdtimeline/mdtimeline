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

Ext.define('App.view.patient.DisclosureEditWindow', {
    extend: 'Ext.window.Window',
    requires: [
        'App.ux.TopazSignature'
    ],
    xtype: 'disclosureeditwindow',
    itemId: 'DisclosureEditWindow',
    title: 'Disclosure',
    modal: true,
    width: 400,
    layout: 'fit',
    bodyPadding: 5,
    closable: false,
    items:
        {
            xtype: 'form',
            itemId: 'DisclosureEditWindowForm',
            bodyPadding: 20,
            fieldDefaults: {
                labelAlign: 'top',
                msgTarget: 'side',
                anchor: '100%'
            },
            items: [
                {
                    xtype: 'textareafield',
                    fieldLabel: 'Description',
                    name: 'description'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Recipient',
                    name: 'recipient'
                },
                {
                    xtype: 'datefield',
                    fieldLabel: 'Requested Date',
                    format: 'F j, Y',
                    submitFormat: 'Y-m-d H:i:s',
                    name: 'request_date',
                    allowBlank: false
                    // itemId: 'DisclosureEditWindowRequestedDate'
                },
                {
                    xtype: 'datefield',
                    fieldLabel: 'Fulfil Date',
                    format: 'F j, Y',
                    submitFormat: 'Y-m-d H:i:s',
                    name: 'fulfil_date',
                    // itemId: 'DisclosureEditWindowFulfilDate'
                },
                {
                    xtype: 'fieldset',
                    title: _('pickup'),
                    items: [
                        {
                            xtype: 'datefield',
                            fieldLabel: _('date'),
                            format: 'F j, Y',
                            submitFormat: 'Y-m-d H:i:s',
                            name: 'pickup_date',
                            // itemId: 'DisclosureEditWindowPickupDate'
                        },
                        {
                            xtype: 'fieldcontainer',
                            fieldLabel: _('signature'),
                            labelAlign: 'top',
                            anchor: '100%',
                            layout: 'fit',
                            items: [
                                {
                                    xtype: 'topazsignature',
                                    itemId: 'ResultsPickUpSignatureField',
                                    height: 100
                                }
                            ]
                        }
                    ]
                }

            ],
            buttons: [
                {
                    text: 'Cancel',
                    itemId: 'DisclosureEditWindowFormCancelBtn',
                },
                {
                    text: 'Save',
                    itemId: 'DisclosureEditWindowFormSaveBtn',
                }
            ]
        }

});