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

Ext.define('App.view.administration.practice.ReferringProviders', {
    extend: 'Ext.grid.Panel',
    xtype: 'referringproviderspanel',
    requires: [
        'Ext.ux.SlidingPager',
	    'App.ux..grid.ColumnSearchField'
    ],
    title: _('referring_providers'),

    initComponent: function(){
        var me = this;

        me.store = Ext.create('App.store.administration.ReferringProviders', {
            autoSync: false,
            remoteSort: true,
            remoteFilter: true,
            sorters: [
                {
                    property: 'lname',
                    direction: 'ASC'
                }
            ]
        });

        Ext.apply(me, {
            columns: [
                {
                    width: 200,
                    text: _('name'),
                    sortable: true,
	                dataIndex: 'lname',
                    renderer:function(v, meta, record){
                        return record.data.title + ' ' + record.data.lname + ', ' + record.data.fname + ' ' + record.data.mname;
                    },
	                items: [
		                {
			                xtype: 'columnsearchfield',
			                autoSearch: true,
			                operator: 'LIKE',
			                suffix: '%'
		                }
	                ]
                },
                {
                    flex: 1,
                    text: _('email'),
                    sortable: true,
                    dataIndex: 'email'
                },
                {
                    flex: 1,
                    text: _('phone_number'),
                    sortable: true,
                    dataIndex: 'phone_number'
                },
                {
                    flex: 1,
                    text: _('cell_number'),
                    sortable: true,
                    dataIndex: 'cel_number'
                },
                {
                    flex: 1,
                    text: _('aditional_info'),
                    sortable: true,
                    dataIndex: 'notes'
                },
                {
                    text: _('active'),
                    sortable: true,
                    dataIndex: 'active',
                    renderer: me.boolRenderer
                }
            ],
            dockedItems: [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
	                    {
		                    xtype: 'button',
		                    text: _('referring_provider'),
		                    iconCls: 'icoAdd',
		                    itemId: 'referringProviderAddBtn',
	                    },
                        '->',
                        {
                            xtype: 'button',
                            text: _('referring_provider'),
                            iconCls: 'icoAdd',
                            itemId: 'referringProviderAddBtn',
                        }
                    ]
                },
                {
                    xtype: 'pagingtoolbar',
                    dock: 'bottom',
                    pageSize: 25,
                    store: me.store,
                    displayInfo: true,
                    plugins: Ext.create('Ext.ux.SlidingPager')
                }
            ]
        });

        me.callParent(arguments);

    }

});
