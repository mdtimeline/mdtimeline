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
Ext.define('App.view.administration.PrintJobsWindow', {
    extend: 'Ext.window.Window',
    pageTitle: _('print_jobs'),
    itemId: 'PrintJobsWindow',
    width: 1000,
    height: 800,
    title: _('print_jobs'),
    layout: 'fit',
    initComponent: function () {
        var me = this,
            ctl = app.getController('PrintJob');


        me.items = [
            {
                xtype: 'grid',
                store: ctl.print_job_store,
                itemId: 'PrintJobsWindowGird',
                selType: 'checkboxmodel',

                columns: [
                    {
                        text: 'Job Id',
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        text: 'Document Id',
                        dataIndex: 'document_id',
                        hidden: true,
                    },
                    {
                        text: 'Printer Id',
                        dataIndex: 'printer_id',
                        hidden: true
                    },
                    {
                        text: _('document_title'),
                        dataIndex: 'document_title',
                        flex: 2
                    },
                    {
                        text: _('document_doc_type'),
                        dataIndex: 'document_doc_type',
                        flex: 1.5
                    },
                    {
                        text: _('document_note'),
                        dataIndex: 'document_note',
                        hidden: true
                    },
                    {
                        text: 'Printer Type',
                        dataIndex: 'printer_type',
                        flex: 1
                    },
                    {
                        text: 'Status',
                        dataIndex: 'print_status',
                        flex: 1
                    },
                    {
                        text: _('priority'),
                        flex: 1,
                        dataIndex: 'priority',
                        renderer: function(value){
                            if (value === 1) {
                                return 'High';
                            }
                            if(value === 2){
                                return 'Med'
                            }
                            if(value === 3){
                                return 'Low'
                            }

                            return '';
                        }
                    },
                    {
                        text: _('username'),
                        dataIndex: 'user_username',
                        flex: 1
                    },
                    {
                        text: _('user') +' ' + _('first_name'),
                        dataIndex: 'user_fname',
                        hidden: true
                    },
                    {
                        text: _('user') +' ' + _('last_name'),
                        dataIndex: 'user_lname',
                        hidden: true
                    },
                    {
                        text: _('user') +' ' + _('middle_name'),
                        dataIndex: 'user_mname',
                        hidden: true
                    },
                    {
                        xtype: 'datecolumn',
                        text: 'Created At',
                        dataIndex: 'created_at',
                        format: 'M j Y \\a\\t g:i a',
                        flex: 1
                    },
                    {
                        xtype: 'datecolumn',
                        text: 'Updated At',
                        dataIndex: 'updated_at',
                        format: 'M j Y \\a\\t g:i a',
                        hidden: true
                    },
                ]
            }
        ];

        me.bbar = {
            xtype: 'pagingtoolbar',
            pageSize: 10,
            store: ctl.print_job_store,
            displayInfo: true,
            plugins: new Ext.ux.SlidingPager(),
            items: [
                // '->',
                {
                    xtype: 'printerscombo',
                    itemId: 'PrintJobsWindowPrintersCombo',
                    emptyText: 'Select Printer',
                    fieldLabel: 'Printer',
                    labelWidth: 40,
                    name: 'printer',
                    editable: false,
                    width: 300,
                },
                {
                    xtype: 'button',
                    text: _('print'),
                    itemId: 'PrintJobsWindowPrintBtn'
                }
            ]
        };

        me.callParent();
    },
    tbar: [
        {
            xtype: 'container',
            layout: 'vbox',
            items: [
                {
                    xtype: 'datefield',
                    fieldLabel: _('from'),
                    labelAlign: 'top',
                    margin: '0 10 0 0',
                    itemId: 'PrintJobsWindowFromField',
                    value: new Date()
                },
                {
                    xtype: 'datefield',
                    fieldLabel: _('to'),
                    labelAlign: 'top',
                    itemId: 'PrintJobsWindowToField',
                    value: new Date()
                }
            ]
        },
        {
            xtype: 'fieldset',
            title: 'Status',
            margin: '0 0 0 10',
            width: 220,
            items: [
                {
                    xtype: 'checkboxgroup',
                    itemId: 'PrintJobsWindowStatusChkGroup',
                    columns: 3,
                    vertical: true,
                    items: [
                        {
                            boxLabel: _('send'),
                            name: 'status_sended',
                            checked: true
                        },
                        {
                            boxLabel: _('printing'),
                            name: 'status_printing',
                            checked: true
                        },
                        {
                            boxLabel: _('waiting'),
                            name: 'status_waiting',
                            checked: true
                        },
                        {
                            boxLabel: _('done'),
                            name: 'status_done',
                            // width: 50
                        },
                        {
                            boxLabel: 'Failed',
                            name: 'status_failed',
                        }
                    ]
                },

            ]
        },
        {
            xtype: 'fieldset',
            title: _('priority'),
            margin: '0 0 0 10',
            width: 150,
            items: [
                {
                    xtype: 'checkboxgroup',
                    itemId: 'PrintJobsWindowPriorityChkGroup',
                    columns: 2,
                    vertical: true,
                    items: [
                        {
                            boxLabel: _('high'),
                            name: 'priority_high',
                        },
                        {
                            boxLabel: _('low'),
                            name: 'priority_low',
                        },
                        {
                            boxLabel: _('med'),
                            name: 'priority_med',
                        },
                    ]
                },
            ]
        },
        {
            xtype: 'container',
            layout: 'vbox',
            margin: '0 0 0 10',
            items: [
                {
                    xtype: 'userlivetsearch',
                    itemId: 'PrintJobsWindowUserLiveSearch',
                    fieldLabel: _('user'),
                    labelWidth: 25,
                    hideLabel: false,
                }
            ]
        },
    ],


});