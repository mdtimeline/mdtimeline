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
Ext.define('App.view.administration.LegalLetters', {
    extend: 'App.ux.RenderPanel',
    pageTitle: _('content_management'),
    itemId: 'AdministrationLegalLetters',
    initComponent: function(){
        var me = this;

        me.store = Ext.create('App.store.administration.LegalLetters');

        me.grid = Ext.create('Ext.grid.Panel', {
            itemId: 'LegalLettersGrid',
            store: me.store,
            columns: [
                {
                    text: _('title'),
                    dataIndex: 'title',
                    flex: 1
                },
                {
                    text: _('facility'),
                    dataIndex: 'facility_id',
                    flex: 1
                },
                {
                    text: _('workflow'),
                    dataIndex: 'workflow',
                    flex: 1
                },
                {
                    text: _('version'),
                    dataIndex: 'version'
                },
                {
                    text: _('days_valid_for'),
                    dataIndex: 'days_valid_for',
                    flex: 1
                },
                {
                    text: _('active'),
                    dataIndex: 'active',
                    renderer: app.boolRenderer
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