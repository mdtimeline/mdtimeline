/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

Ext.define('App.view.administration.TransactionLog', {
    extend: 'App.ux.RenderPanel',
    xtype: 'transactionLogPanel',
    border: false,
    pageLayout: {
        type: 'vbox',
        align: 'stretch'
    },
    pageTitle: _('transaction_log'),
    initComponent: function() {
        var me = this;

        me.transactionLogStore = Ext.create('App.store.administration.TransactionLog', {
            remoteFilter: true,
            autoLoad: true,
            autoSync: false
        });

        me.pageBody = [
            {
                xtype: 'panel',
                frame: false,
                border: false,
                itemId: 'filterPanel',
                region: 'west',
                width: 400,
                autoScroll: true,
                items:[
                    {
                        xtype: 'fieldcontainer',
                        layout: 'column',
                        fieldLabel: _('from_date'),
                        labelAlign: 'top',
                        items: [
                            {
                                xtype: 'datefield',
                                name: 'begin_date',
                                columnWidth:.5,
                                hideLabel: true,
                                emptyText:  _('begin_date'),
                                allowBlank: true,
                                format: g('date_display_format'),
                                submitFormat: 'Y-m-d',
                                value: new Date()
                            },
                            {
                                xtype: 'timefield',
                                name: 'begin_time',
                                hideLabel: true,
                                emptyText:  _('begin_time'),
                                increment: 30,
                                columnWidth:.5,
                                format: 'H:i:s',
                                submitFormat: 'H:i:s',
                                value: '00:00:00'
                            }
                        ]
                    },
                    {
                        xtype: 'fieldcontainer',
                        layout: 'column',
                        fieldLabel: _('to_date'),
                        labelAlign: 'top',
                        items: [
                            {
                                xtype: 'datefield',
                                name: 'end_date',
                                columnWidth:.5,
                                hideLabel: true,
                                emptyText:  _('end_date'),
                                allowBlank: true,
                                format: g('date_display_format'),
                                submitFormat: 'Y-m-d',
                                value: new Date()
                            },
                            {
                                xtype: 'timefield',
                                name: 'end_time',
                                hideLabel: true,
                                emptyText:  _('end_time'),
                                increment: 30,
                                columnWidth:.5,
                                format: 'H:i:s',
                                submitFormat: 'H:i:s',
                                value: '23:00:00'
                            }
                        ]
                    },
                    {
                        xtype: 'fieldcontainer',
                        layout: 'column',
                        labelAlign: 'top',
                        items: [
                            {
                                xtype: 'tablelist',
                                fieldLabel: _('table'),
                                name: 'table_name',
                                labelAlign: 'top',
                                columnWidth: 1
                            },
                            {
                                xtype: 'eventlist',
                                fieldLabel: _('event_type'),
                                labelAlign: 'top',
                                name: 'event_type',
                                columnWidth: 1
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'grid',
                itemId: 'reportDataGrid',
                store: me.transactionLogStore,
                region: 'center',
                rowLines: false,
                columnLines: true,
                dockedItems: [
                    {
                        xtype: 'pagingtoolbar',
                        store: me.transactionLogStore,
                        dock: 'bottom',
                        displayInfo: true
                    }
                ],
                features: [{ftype: 'grouping'}],
                columns: [
                    {
                        text: 'Record #',
                        dataIndex: 'RecordNumber',
                        align: 'center'
                    },
                    {
                        text: 'Patient',
                        dataIndex: 'PatientName',
                        align: 'left'
                    },
                    {
                        text: 'Date',
                        dataIndex: 'date',
                        align: 'center',
                        width: 120
                    },
                    {
                        text: 'User',
                        dataIndex: 'UserName',
                        align: 'left'
                    },
                    {
                        text: 'Facility',
                        dataIndex: 'legal_name',
                        align: 'left'
                    },
                    {
                        text: 'Category',
                        dataIndex: 'category',
                        align: 'center'
                    },
                    {
                        text: 'Event',
                        dataIndex: 'event',
                        align: 'left'
                    },
                    {
                        text: 'Table',
                        dataIndex: 'table_name',
                        align: 'left'
                    },
                    {
                        text: 'IP',
                        dataIndex: 'ip',
                        align: 'center'
                    },
                    {
                        text: 'Valid',
                        dataIndex: 'valid',
                        align: 'center'
                    }
                ]
            }
        ];

        me.callParent(arguments);

    }

});
