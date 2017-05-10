/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, LLC.
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

Ext.define('App.view.administration.CronJob', {
    extend: 'App.ux.RenderPanel',
    requires: [
        'Ext.grid.plugin.RowEditing'
    ],
    xtype: 'cronjobpanel',
    itemId: 'CronJobPanel',
    border: false,
    pageLayout: {
        type: 'vbox',
        align: 'stretch'
    },
    pageTitle: _('cronjobs'),
    initComponent: function() {
        var me = this;

        me.CronJobStore = Ext.create('App.store.administration.CronJob', {
            remoteFilter: false,
            autoLoad: false,
            autoSync: false
        });

        me.pageBody = [
            {
                xtype: 'grid',
                flex: 1,
                frame: true,
                title: _('cronjobs_available'),
                itemId: 'CronJobGrid',
                columnLines: true,
                store: me.CronJobStore,
                selType: 'rowmodel',
                plugins: {
                    ptype: 'rowediting',
                    clicksToEdit: 2
                },
                columns: [
                    {
                        text: _('name'),
                        dataIndex: 'name',
                        flex: 1
                    },
                    {
                        text: _('filename'),
                        dataIndex: 'filename',
                        width: 200
                    },
                    {
                        text: _('minute'),
                        dataIndex: 'minute',
                        align: 'center',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: _('hour'),
                        dataIndex: 'hour',
                        align: 'center',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: _('month_day'),
                        dataIndex: 'month_day',
                        align: 'center',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: _('month'),
                        dataIndex: 'month',
                        align: 'center',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: _('week_day'),
                        dataIndex: 'week_day',
                        align: 'center',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: _('timeout'),
                        dataIndex: 'timeout',
                        align: 'center',
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: _('elapsed'),
                        dataIndex: 'elapsed',
                        align: 'center'
                    },
                    {
                        text: _('pid'),
                        dataIndex: 'pid',
                        align: 'center'
                    },
                    {
                        text: _('running'),
                        dataIndex: 'running',
                        renderer: me.boolRenderer,
                        align: 'center'
                    },
                    {
                        text: _('active'),
                        dataIndex: 'active',
                        renderer: me.boolRenderer,
                        align: 'center',
                        editor: {
                            xtype: 'checkboxfield'
                        }
                    }
                ],
                tbar: [
                    '->',
                    {
                        xtype: 'button',
                        text: _('refresh'),
                        itemId: 'refresh'
                    }
                ]
            }
        ];

        me.callParent(arguments);

    }

});
