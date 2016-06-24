/**
 * mdTimeLine (Electronic Health Records)
 * Copyright (C) 2016 TRA, inc.
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

Ext.define('Modules.reportcenter.reports.PatientList.ux.ComboOptionListReport', {
    extend: 'Ext.form.ComboBox',
    alias: 'widget.listcomboreport',
    displayField: 'option_name',
    valueField: 'option_value',
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
            model = me.id + 'ComboOptionModel';

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'option_name',
                    type: 'string'
                },
                {
                    name: 'option_value',
                    type: me.valueDataType
                }
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'CombosData.getOptionsByListId'
                },
                extraParams: {
                    list_id: me.list
                }
            },
            idProperty: 'option_value'
        });

        me.store = Ext.create('Ext.data.Store', {
            model: model,
            autoLoad: me.loadStore
        });

        me.listConfig = {
            itemTpl: new Ext.XTemplate(
                '<tpl>' +
                '   <div style="white-space: nowrap;">{option_value} (<span style="font-weight: bold;">{option_name}</span>)</div>',
                '</tpl>'
            )
        };

        me.callParent(arguments);

    }
});
