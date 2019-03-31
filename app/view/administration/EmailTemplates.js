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

Ext.define('App.view.administration.EmailTemplates', {
    extend: 'App.ux.RenderPanel',
    pageTitle: _('email_templates'),
    itemId: 'AdministrationEmailTemplates',
    initComponent: function(){
        var me = this;

        me.store = Ext.create('App.store.administration.EmailTemplates');

        me.grid = Ext.create('Ext.grid.Panel', {
            itemId: 'AdministrationEmailTemplatesGrid',
            store: me.store,
            columns: [
                {
                    text: _('facility'),
                    width: 200,
                    dataIndex: 'facility_id'
                },
                {
                    text: _('template_type'),
                    sortable: true,
                    dataIndex: 'template_type'
                },
                {
                    text: _('language'),
                    sortable: true,
                    dataIndex: 'language'
                },
                {
                    text: _('subject'),
                    sortable: true,
                    dataIndex: 'subject',
                    flex: 1
                },
                {
                    text: _('from_email'),
                    sortable: true,
                    dataIndex: 'from_email',
                    flex: 1
                },
                {
                    text: _('from_name'),
                    sortable: true,
                    dataIndex: 'from_name',
                    flex: 1
                },
                {
                    text: _('active?'),
                    width: 60,
                    sortable: true,
                    renderer: me.boolRenderer,
                    dataIndex: 'active',
                    editor:{
                        xtype:'checkbox'
                    }
                }
            ],
            bbar: {
                xtype: 'pagingtoolbar',
                pageSize: 25,
                store: me.store,
                displayInfo: true,
                plugins: new Ext.ux.SlidingPager()
            }
        });
        me.pageBody = [ me.grid ];
        me.callParent(arguments);
    }
});
