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

Ext.define('App.ux.combo.Burners', {
    extend: 'Ext.form.ComboBox',
    xtype: 'burnerscombo',
    editable: false,
    queryMode: 'local',
    valueField: 'id',
    displayField: 'description',
    emptyText: _('select_burner'),
    initComponent: function () {
        var me = this;

        Ext.define('BurnersComboModel', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'int'},
                {name: 'facility_id', type: 'int'},
                {name: 'description', type: 'string'},
                {name: 'aet', type: 'string'},
                {name: 'ip', type: 'string'},
                {name: 'port', type: 'string'},
                {name: 'path', type: 'string'},
                {name: 'url', type: 'string'},
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'WorkListPacs.getFacilityBurners'
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'BurnersComboModel',
            autoLoad: true
        });

        app.on('appfacilitychanged', function () {
            me.store.load(function (records, operation, success) {
                if (me.store.getCount() > 0) {
                	me.setValue(me.store.first());
                } else {
					me.setValue(null);
                }
            });
        });

        me.callParent(arguments);
    }
});