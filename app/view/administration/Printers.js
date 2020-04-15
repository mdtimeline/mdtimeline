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
Ext.define('App.view.administration.Printers', {
    extend: 'App.ux.RenderPanel',
    pageTitle: _('printers'),
    itemId: 'AdministrationPrinters',
    initComponent: function(){
        var me = this;

        me.store = Ext.create('App.store.administration.Printer', {
            autoSync: false,
            autoLoad: true
        });

        me.grid = Ext.create('Ext.grid.Panel', {
            requires: [
                'Ext.grid.plugin.CellEditing'
            ],
            itemId: 'AdministrationPrintersGrid',
            store: me.store,
            title: _('printers'),
            columnsLines: true,
            plugins: [
                {
                    ptype: 'rowediting'
                }
            ],

            tbar: [
                '->',
                {
                    xtype: 'button',
                    text: _('remove_printer'),
                    iconCls: 'icoDelete',
                    itemId: 'AdministrationPrintersRemoveBtn'
                },
                {
                    xtype: 'button',
                    text: _('add_printer'),
                    iconCls: 'icoAdd',
                    itemId: 'AdministrationPrintersAddBtn'
                }
            ],
            columns: [
                {
                    text: _('facility_id'),
                    dataIndex: 'facility_id',
                    flex: 1,
                    editor: 'mitos.facilitiescombo'
                },
                {
                    text: _('department_id'),
                    dataIndex: 'department_id',
                    flex: 1,
                    editor: 'depatmentscombo'
                },
                {
                    text: _('printer_description'),
                    dataIndex: 'printer_description',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 120
                    }
                },
                {
                    text: _('printer_name'),
                    dataIndex: 'printer_name',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 80
                    }
                },
                {
                    text: _('printer_protocol'),
                    dataIndex: 'printer_protocol',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 10
                    }
                },
                {
                    text: _('printer_options'),
                    dataIndex: 'printer_options',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 250
                    }
                },
                {
                    text: _('printer_host'),
                    dataIndex: 'printer_host',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 180
                    }
                },
                {
                    text: _('printer_port'),
                    dataIndex: 'printer_port',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 10
                    }
                },
                {
                    text: _('printer_uri'),
                    dataIndex: 'printer_uri',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 180
                    }
                },
                {
                    text: _('printer_user'),
                    dataIndex: 'printer_user',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 40
                    }
                },
                {
                    text: _('printer_pass'),
                    dataIndex: 'printer_pass',
                    flex: 1,
                    editor: {
                        xtype: 'textfield',
                        maxLength: 20
                    }
                },
                {
                    text: _('active'),
                    width: 60,
                    sortable: true,
                    renderer: me.boolRenderer,
                    dataIndex: 'active',
                    editor: 'checkbox'
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