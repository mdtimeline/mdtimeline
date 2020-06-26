/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2012 Ernesto Rodriguez
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

Ext.define('App.view.administration.SnippetsManager', {
    extend: 'App.ux.RenderPanel',
    pageTitle: 'Snippets Manager',
    requires: [


    ],

    initComponent: function () {
        var me = this;


        me.nurse_store = Ext.create('App.store.administration.NursesNoteSnippets',{
            groupField: 'category'
        });
        me.provider_store = Ext.create('App.store.administration.EncounterSnippets', {
            autoLoad: false,
            pageSize: 1000,
            groupField: 'category'
        });

        me.pageBody = [
            {
                xtype: 'tabpanel',
                items: [
                    {
                        xtype: 'grid',
                        title: 'Nurse Snippets',
                        itemId: 'NursesNoteSnippetsGrid',
                        region: 'west',
                        store: me.nurse_store,
                        features: [{ ftype:'grouping' }],
                        tools: [
                            {
                                xtype: 'button',
                                text: _('snippet'),
                                iconCls: 'icoAdd',
                                itemId: 'NursesNoteSnippetAddBtn'
                            }
                        ],
                        columns: [
                            {
                                text: _('edit'),
                                width: 25,
                                menuDisabled: true,
                                xtype: 'actioncolumn',
                                tooltip: 'Edit Snippet',
                                align: 'center',
                                icon: 'resources/images/icons/edit.png',
                                handler: function(grid, rowIndex, colIndex, actionItem, event, record){
                                    var snippetCtrl = app.getController('patient.NursesNotes');
                                    snippetCtrl.onSnippetBtnEdit(grid, rowIndex, colIndex, actionItem, event, record);
                                }
                            },
                            {
                                text: _('description'),
                                dataIndex: 'description',
                                flex: 1
                            }
                        ],
                        tbar: [
                            '->',
                            {
                                xtype: 'button',
                                text: _('snippet'),
                                iconCls: 'icoAdd',
                                itemId: 'NursesNoteSnippetAddBtn'
                            }
                        ],
                        bbar: {
                            xtype: 'pagingtoolbar',
                            pageSize: 10,
                            store: me.nurse_store,
                            displayInfo: true,
                            plugins: new Ext.ux.SlidingPager()
                        }
                    },
                    {
                        xtype: 'grid',
                        title: 'Provider Snippets',
                        itemId: 'SnippetsTreePanel',
                        store: me.provider_store,
                        features: [{ ftype:'grouping' }],
                        tools: [
                            {
                                xtype: 'button',
                                text: _('snippet'),
                                iconCls: 'icoAdd',
                                itemId: 'SnippetAddBtn'
                            }
                        ],
                        columns: [
                            {
                                text: _('edit'),
                                width: 25,
                                menuDisabled: true,
                                xtype: 'actioncolumn',
                                tooltip: 'Edit Snippet',
                                align: 'center',
                                icon: 'resources/images/icons/edit.png',
                                handler: function(grid, rowIndex, colIndex, actionItem, event, record){
                                    var snippetCtrl = app.getController('administration.EncounterSnippets');
                                    snippetCtrl.onSnippetBtnEdit(grid, rowIndex, colIndex, actionItem, event, record);
                                }
                            },
                            {
                                text: _('description'),
                                dataIndex: 'description',
                                flex: 1
                            }
                        ],
                        tbar: [
                            {
                                xtype: 'specialtiescombo',
                                itemId: 'SoapTemplateSpecialtiesCombo',
                                flex: 1
                            },
                            {
                                xtype: 'button',
                                text: _('snippet'),
                                iconCls: 'icoAdd',
                                itemId: 'SnippetAddBtn'
                            }
                        ],
                        bbar: {
                            xtype: 'pagingtoolbar',
                            pageSize: 10,
                            store: me.provider_store,
                            displayInfo: true,
                            plugins: new Ext.ux.SlidingPager()
                        }
                    }
                ]
            }

        ];

        me.callParent();
    }
});
//ens servicesPage class
