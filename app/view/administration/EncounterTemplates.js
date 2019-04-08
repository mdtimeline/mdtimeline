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

Ext.define('App.view.administration.EncounterTemplates', {
    extend: 'App.ux.RenderPanel',
    pageTitle: _('encounter_templates'),
    itemId: 'AdministrationEncounterTemplatesPanel',
    initComponent: function(){
        var me = this;

        me.store = Ext.create('App.store.administration.EncounterTemplatePanels');

        me.grid = Ext.create('Ext.grid.Panel', {
            itemId: 'AdministrationEncounterTemplatesGrid',
            store: me.store,
            columns: [
                {
                    text: _('specialty'),
                    width: 200,
                    sortable: true,
                    dataIndex: 'specialty'
                },
                {
                    text: _('description'),
                    flex: 1,
                    sortable: true,
                    dataIndex: 'description'
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
            tbar: [
                '->',
                {
                    xtype: 'button',
                    text: _('template'),
                    itemId: 'EncounterTemplatesAddBtn',
                    iconCls: 'icoAdd'
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
