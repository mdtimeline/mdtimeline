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

Ext.define('Modules.reportcenter.view.ReportCenter', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('report_center'),
    itemId: 'ReportCenterPanel',
    requires: [
        'App.ux.grid.Printer',
        'App.ux.grid.RowExpander'
    ],
    pageBody: [
        // Report List
        {
            xtype: 'gridpanel',
            itemId: 'reportCenterGrid',
            title: _('available_reports'),
            frame: false,
            store: Ext.create('Modules.reportcenter.store.ReportList'),
            features: [{
                ftype:'grouping'
            }],
            columns: [
                {
                    text: _('category'),
                    dataIndex: 'category',
                    hidden: true
                },
                {
                    text: _('report_name'),
                    dataIndex: 'title',
                    width: 300
                },
                {
                    text: _('version'),
                    dataIndex: 'version'
                },
                {
                    text: _('author'),
                    dataIndex: 'author',
                    width: 250
                },
                {
                    text: _('report_description'),
                    dataIndex: 'description',
                    flex: 1
                }
            ]
        },

        // Report Viewer
        {
            xtype: 'window',
            itemId: 'reportWindow',
            closeAction: 'hide',
            hidden: true,
            title: _('report_window'),
            layout: 'border',
            maximizable: true,
            maximized: false,
            minimizable: false,
            modal: false,
            autoScroll: false,
            items:[
                {
                    xtype: 'panel',
                    region: 'center',
                    itemId: 'reportPanel',
                    layout: 'border',
                    autoScroll: true,
                    items:[
                        {
                            xtype: 'panel',
                            frame: false,
                            border: false,
                            itemId: 'filterDisplayPanel',
                            region: 'north',
                            html: '',
                            autoScroll: true
                        }
                    ]
                }
            ],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    {
                        xtype: 'button',
                        text: _('render'),
                        itemId: 'render'
                    }

                    // '->',
                    // {
                    //     xtype: 'button',
                    //     text: _('export'),
                    //     itemId: 'export',
                    //     disabled: true
                    // }
                ]
            }]
        }
    ]

});
