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
    xtype: 'TransactionLogPanel',
    pageLayout: 'border',
    pageTitle: _('transaction_log'),
	itemId: 'TransactionLogPanel',
	requires: [
		'App.ux.LivePatientSearch',
		'App.ux.LiveUserSearch'
	],
    initComponent: function() {
        var me = this;

        me.transactionLogStore = Ext.create('App.store.administration.TransactionLog', {
            remoteFilter: true,
            remoteSort: true
        });

        me.auditLogStore = Ext.create('App.store.administration.AuditLogs', {
            remoteFilter: true,
            remoteSort: true
        });

        me.pageBody = [
            {
                xtype: 'form',
                itemId: 'TransactionLogFilterFormPanel',
                region: 'west',
                width: 250,
                autoScroll: true,
	            collapsible: true,
	            split: true,
                border: true,
                bodyPadding: 10,
                title: _('filters'),
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
		                xtype: 'tablelist',
		                fieldLabel: _('table'),
		                name: 'table_name',
		                labelAlign: 'top',
		                columnWidth: 1,
		                anchor: '100%',
		                enableReset: true
	                },
	                {
		                xtype: 'eventlist',
		                fieldLabel: _('event_type'),
		                labelAlign: 'top',
		                name: 'event_type',
		                columnWidth: 1,
		                anchor: '100%',
		                enableReset: true
	                },
	                {
		                xtype: 'userlivetsearch',
		                fieldLabel: _('user'),
		                labelAlign: 'top',
		                hideLabel: false,
		                name: 'uid',
		                anchor: '100%'
	                },
	                {
		                xtype: 'patienlivetsearch',
		                fieldLabel: _('patient'),
		                labelAlign: 'top',
		                hideLabel: false,
		                name: 'pid',
		                anchor: '100%',
		                resetEnabled: true
	                }
                ],
                bbar: [
                	{
	                    xtype: 'button',
	                    text: _('search'),
		                flex: 1,
	                    itemId: 'TransactionLogFilterSearchBtn'
	                }
                ]
            },
            {
                xtype: 'tabpanel',
                //layout: 'fit',
                itemId: 'TransactionLogTabPanel',
                region: 'center',
                items: [
                    {
                        // Transaction Log Grid
                        xtype: 'grid',
                        itemId: 'TransactionLogDataGrid',
                        store: me.transactionLogStore,
                        region: 'center',
                        border: true,
                        title: _('transaction_log'),
                        dockedItems: [
                            {
                                xtype: 'pagingtoolbar',
                                store: me.transactionLogStore,
                                dock: 'bottom',
                                displayInfo: true
                            }
                        ],
                        columns: [
                            {
                                xtype:'datecolumn',
                                text: 'Date',
                                format:'Y-m-d H:i:s',
                                dataIndex: 'date',
                                width: 250
                            },
                            {
                                text: 'Patient',
                                dataIndex: 'patient_lname',
                                flex: 1,
                                renderer: function (v, meta,rec) {
                                    return rec.get('patient_name');
                                }
                            },
                            {
                                text: 'User',
                                dataIndex: 'user_lname',
                                flex: 1,
                                renderer: function (v, meta,rec) {
                                    return rec.get('user_name');
                                }
                            },
                            {
                                text: 'Category',
                                dataIndex: 'category',
                                flex: 1
                            },
                            {
                                text: 'Event',
                                dataIndex: 'event',
                                flex: 1
                            },
                            {
                                text: 'Pk',
                                dataIndex: 'pk'
                            },
                            {
                                text: 'Table',
                                dataIndex: 'table_name',
                                flex: 1
                            },
                            {
                                text: 'IP',
                                dataIndex: 'ip',
                                flex: 1
                            }
                        ]
                    },
                    // Audit Log Grid
                    {
                        xtype: 'grid',
                        itemId: 'AuditLogDataGrid',
                        store: me.auditLogStore,
                        region: 'center',
                        border: true,
                        title: _('audit_log'),
                        dockedItems: [
                            {
                                xtype: 'pagingtoolbar',
                                store: me.auditLogStore,
                                dock: 'bottom',
                                displayInfo: true
                            }
                        ],
                        columns: [
                            {
                                xtype:'datecolumn',
                                text: 'Date',
                                format:'Y-m-d H:i:s',
                                dataIndex: 'event_date',
                                width: 250
                            },
                            {
                                text: 'Patient',
                                dataIndex: 'patient_lname',
                                flex: 1,
                                renderer: function (v, meta,rec) {
                                    return rec.get('patient_name');
                                }
                            },
                            {
                                text: 'User',
                                dataIndex: 'user_lname',
                                flex: 1,
                                renderer: function (v, meta,rec) {
                                    return rec.get('user_name');
                                }
                            },
                            {
                                text: 'Event',
                                dataIndex: 'event',
                                flex: 1
                            },
                            {
                                text: 'Event Description',
                                dataIndex: 'event_description',
                                flex: 1
                            },
                            {
                                text: 'Foreign Pk',
                                dataIndex: 'foreign_id'
                            },
                            {
                                text: 'Foreign Table',
                                dataIndex: 'foreign_table',
                                flex: 1
                            },
                            {
                                text: 'IP',
                                dataIndex: 'ip',
                                flex: 1
                            }
                        ]
                    }
                ]
            }

            // {
            //     xtype: 'grid',
            //     itemId: 'TransactionLogDataGrid',
            //     store: me.transactionLogStore,
            //     region: 'center',
	        //     border: true,
	        //     title: _('results'),
            //     dockedItems: [
            //         {
            //             xtype: 'pagingtoolbar',
            //             store: me.transactionLogStore,
            //             dock: 'bottom',
            //             displayInfo: true
            //         }
            //     ],
            //     columns: [
	        //         {
		    //             text: 'Date',
		    //             dataIndex: 'date',
		    //             width: 250
	        //         },
            //         {
            //             text: 'Patient',
            //             dataIndex: 'patient_lname',
	        //             flex: 1,
	        //             renderer: function (v, meta,rec) {
		    //                 return rec.get('patient_name');
	        //             }
            //         },
            //         {
            //             text: 'User',
            //             dataIndex: 'user_lname',
	        //             flex: 1,
	        //             renderer: function (v, meta,rec) {
		    //                 return rec.get('user_name');
	        //             }
            //         },
            //         {
            //             text: 'Category',
            //             dataIndex: 'category',
	        //             flex: 1
            //         },
            //         {
            //             text: 'Event',
	        //             dataIndex: 'event',
	        //             flex: 1
            //         },
            //         {
            //             text: 'Pk',
            //             dataIndex: 'pk'
            //         },
            //         {
            //             text: 'Table',
            //             dataIndex: 'table_name',
	        //             flex: 1
            //         },
            //         {
            //             text: 'IP',
            //             dataIndex: 'ip',
	        //             flex: 1
            //         }
            //     ]
            // }
        ];

        me.callParent(arguments);

    }

});
