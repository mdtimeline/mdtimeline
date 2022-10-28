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
Ext.define('App.view.administration.ContentManagement', {
    extend: 'App.ux.RenderPanel',
    pageTitle: _('content_management'),
    itemId: 'AdministrationContentManagement',
    initComponent: function(){
        var me = this;

        me.store = Ext.create('App.store.administration.ContentManagement');

        me.grid = Ext.create('Ext.grid.Panel', {
            itemId: 'ContentManagementGrid',
            store: me.store,
            title: _('content_management'),
            columnsLines: true,
            tbar: [
                {
                    xtype: 'button',
                    iconCls: 'icoAdd',
                    itemId: 'ContentManagementAddBtn',
                    text: _('add_content')
                },
            ],
            columns: [
                {
                    xtype: 'griddeletecolumn',
                    acl: true,
                    width: 20
                },
                {
                    text: _('content_type'),
                    dataIndex: 'content_type',
                    flex: 1
                },
                {
                    text: _('content_lang'),
                    dataIndex: 'content_lang',
                    flex: 1
                },
                {
                    text: _('content_body'),
                    dataIndex: 'content_body',
                    flex: 1
                },
                {
                    text: _('content_version'),
                    dataIndex: 'content_version',
                    flex: 1
                }
            ],
            bbar: {
                xtype: 'pagingtoolbar',
                pageSize: 50,
                store: me.store,
                displayInfo: true,
                plugins: new Ext.ux.SlidingPager()
            }
        });

        me.pageBody = [ me.grid ];
        me.callParent(arguments);
    }
});