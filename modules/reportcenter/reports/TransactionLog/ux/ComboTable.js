/**
 * mdTimeLine (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, inc.
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

Ext.define('Modules.reportcenter.reports.TransactionLog.ux.ComboTable', {
    extend: 'Ext.form.ComboBox',
    alias: 'widget.tablelist',
    displayField: 'table_name',
    valueField: 'table_name',
    forceSelection: false,
    editable: false,

    /**
     * List ID
     */
    list: null,
    /**
     * Auto Load Store
     */
    loadStore: false,
    /**
     * value data type
     */
    valueDataType: 'string',


    initComponent: function () {
        var me = this,
            model = me.id + 'ComboTableListModel';

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'table_name',
                    type: 'string'
                }
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'ReportcenterCombosData.getTableList'
                }
            },
            idProperty: 'table_name'
        });

        me.store = Ext.create('Ext.data.Store', {
            model: model,
            autoLoad: me.loadStore
        });

        me.callParent(arguments);

    }
});
