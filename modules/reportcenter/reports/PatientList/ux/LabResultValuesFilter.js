/**
 * mdTimeLine (Electronic Health Records)
 * PhotoIdWindow.js
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
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
Ext.define('Modules.reportcenter.reports.PatientList.LabResultValuesFilter',
{
    extend : 'Ext.container.Container',
    alias : 'widget.labresultvalues',
    layout: {
        type: 'vbox'
    },
    initComponent : function(config)
    {
        var me = this;

        /**
         * Model & Store for the Laboratory Results combo
         */
        me.model = Ext.define('LabResultValueFilterModel', {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'code',
                    type: 'string'
                },
                {
                    name: 'code_text',
                    type: 'string'
                }
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'LabResultsValuesFilter.getDistinctResults'
                }
            },
            idProperty: 'code'
        });
        me.store = Ext.create('Ext.data.Store', {
            model: me.model,
            autoLoad: true
        });

        /**
         * Data & Store for the comparison combo (operators)
         */
        me.operators = Ext.create('Ext.data.Store', {
            fields: ['name', 'value'],
            data : [
                {
                    "name":"More than",
                    "value":">="
                },
                {
                    "name":"Less than",
                    "value":"<="
                },
                {
                    "name":"Equal to",
                    "value":"="
                }
            ]
        });


        Ext.apply(me,
        {
            items: [
                {
                    xtype: 'label',
                    text: _('lab_results')+':',
                    margin: '0 0 0 0'
                },
                {
                    xtype: 'combo',
                    store: me.store,
                    hideLabel: true,
                    enableKeyEvents: true,
                    value: null,
                    width: '100%',
                    emptyText: _('select_lab_result'),
                    name: 'lab_result_code'
                },
                {
                    xtype: 'combo',
                    store: me.operators,
                    hideLabel: true,
                    enableKeyEvents: true,
                    value: null,
                    width: '100%',
                    emptyText: _('select_comparison'),
                    name: 'lab_comparison'
                },
                {
                    xtype: 'textfield',
                    hideLabel: true,
                    value: null,
                    width: '100%',
                    name: 'lab_value',
                    emptyText: _('lab_enter_value')
                }
            ]
        });

        me.callParent(arguments);
    }
});
