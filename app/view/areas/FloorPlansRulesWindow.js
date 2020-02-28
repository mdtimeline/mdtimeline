/**
 GaiaEHR (Electronic Health Records)
 Copyright (C) 2013 Certun, LLC.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.view.areas.FloorPlansRulesWindow', {
    extend: 'Ext.window.Window',
    itemId: 'FloorPlansRulesWindow',
    title: _('floor_plan_rules'),
    layout: 'fit',
    width: 800,
    height: 600,
    initComponent: function(){
        var me = this;

        me.items = [
            {
                xtype: 'grid',
                itemId: 'FloorPlansRulesGrid',
                store: Ext.create('App.store.areas.FloorPlansRules',{
                    groupField: 'provider',
                }),
                features: [
                    {
                        ftype:'grouping',
                        collapsible: false
                    }
                ],
                plugins: [
                   {
                       ptype: 'cellediting'
                   }
                ],
                columns: [
                    {
                        text: _('provider'),
                        dataIndex: 'provider',
                        flex: 1
                    },
                    {
                        text: _('facility'),
                        dataIndex: 'facility',
                        flex: 1
                    },
                    {
                        text: _('pool_area'),
                        dataIndex: 'pool_area',
                        flex: 1
                    },
                    {
                        text: _('zone_rule'),
                        dataIndex: 'zone_id',
                        flex: 1,
                        renderer: function (v,m,r) {
                            return r.get('zone');
                        },
                        editor: {
                            xtype: 'floorplanazonescombo',
                            itemId: 'FloorPlansRulesZoneCombo'
                        }
                    }
                ]
            }
        ];

        me.buttons = [
            {
                xtype: 'button',
                text: _('cancel'),
                itemId: 'FloorPlansRulesCancelBtn'
            },
            {
                xtype: 'button',
                text: _('save'),
                itemId: 'FloorPlansRulesSaveBtn'
            }
        ];

        me.callParent(arguments);
    }
});
