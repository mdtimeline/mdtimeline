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

Ext.define('App.view.patient.DisclosuresGrid', {
    extend: 'Ext.grid.Panel',
    requires: [
        'App.ux.TopazSignature'
    ],
    xtype: 'patientdisclosuresgrid',
    selType: 'checkboxmodel',
    title: _('disclosures'),
    itemId: 'PatientDisclosuresGrid',
    bodyPadding: 0,
    tbar: [
        '->',
        {
            xtype: 'container',
            items: [
                {
                    xtype: 'combobox',
                    itemId: 'PatientDisclosuresPrinterCmb',
                    store: Ext.create('App.store.administration.Printer'),
                    queryMode: 'local',
                    editable: false,
                    forceSelection: true,
                    autoSelect: true,
                    valueField: 'id',
                    displayField: 'printer_description',
                    emptyText: 'Select Printer',
                    labelPad: 0,
                    width: 200
                }
            ]
        },
        {
            xtype: 'button',
            text: _('print'),
            itemId: 'PatientDisclosuresPrintBtn',
        },
        {
            xtype: 'button',
            text: _('download'),
            itemId: 'PatientDisclosuresDownloadBtn',
        },
        {
            xtype: 'button',
            text: _('burn'),
            itemId: 'PatientDisclosuresBurnBtn',
        },
        '-',
        {
            text: _('disclosure'),
            iconCls: 'icoAdd',
            itemId: 'PatientDisclosuresGridAddBtn',
        }
    ],
    columns: [
        // {
        //     header: _('type'),
        //     dataIndex: 'type',
        //     flex: 1,
        // },
        // {
        //     header: _('status'),
        //     dataIndex: 'status',
        //     flex: 1,
        // },
        {
            text: _('description'),
            dataIndex: 'description',
            flex: 1,
        },
        {
            header: _('recipient'),
            dataIndex: 'recipient',
            flex: 1,
        },
        {
            xtype: 'datecolumn',
            format: 'F j, Y',
            text: _('request_date'),
            dataIndex: 'request_date',
            flex: 1,
        },
        {
            xtype: 'datecolumn',
            format: 'F j, Y',
            text: _('fulfil_date'),
            dataIndex: 'fulfil_date',
            flex: 1,
        },
        {
            xtype: 'datecolumn',
            format: 'F j, Y',
            text: _('pickup_date'),
            dataIndex: 'pickup_date',
            flex: 1,
        },
        {
            text: _('signature'),
            dataIndex: 'signature',
            flex: 1,
            renderer: function (v, meta, record) {
                meta.tdCls = record.data.rowClasses;

                var signature =  record.get('signature');

                if(signature){
                    signature = '<img src="' + signature + '" height="30" >';
                    return Ext.String.format('<div><b>Signature By:</b></div> {0}', signature);
                }

                return '';

            }
        },
        {
            xtype: 'numbercolumn',
            text: _('document_attached'),
            dataIndex: 'document_inventory_count',
            flex: 1,
            format: '0'
        }
    ],

    initComponent: function () {
        var me = this;

        me.store = Ext.create('App.store.patient.Disclosures', {
            autoSync: false,
            autoLoad: false
        });

        me.callParent();


    }
});