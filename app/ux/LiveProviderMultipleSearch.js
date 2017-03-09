/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

Ext.define('App.ux.LiveProviderMultipleSearch', {
    extend: 'App.ux.form.fields.BoxSelect',
    xtype: 'liveprovidermultiple',
    allowBlank: true,
    editable: true,
    typeAhead: false,
    autoSelect: false,
    emptyText: _('select_provider')+'...',
    queryMode: 'remote',
    labelTpl: '{option_name}',
    forceSelection: false,
    displayField: 'option_name',
    valueField: 'id',
    pageSize: 25,
    initComponent: function(){
        var me = this,
            model = 'ActiveProvidersMultipleModel' + me.id;

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'id',
                    type: 'int'
                },
                {
                    name: 'title',
                    type: 'string'
                },
                {
                    name: 'fname',
                    type: 'string'
                },
                {
                    name: 'mname',
                    type: 'string'
                },
                {
                    name: 'lname',
                    type: 'string'
                },
                {
                    name: 'fullname',
                    type: 'string',
                    convert: function(v, record){
                        return record.data.title + ' ' + record.data.lname + ', ' + record.data.fname + ' ' + record.data.mname;
                    }
                },
                {
                    name: 'option_name',
                    type: 'string',
                    convert: function(v, record){
                        return record.data.title + ' ' + record.data.lname + ', ' + record.data.fname + ' ' + record.data.mname;
                    }
                },
                {
                    name: 'option_value',
                    type: 'int',
                    convert: function(v, record){
                        return record.data.id;
                    }
                }
            ],
            proxy: {
                idProperty: 'ConceptId',
                type: 'direct',
                api: {
                    read: 'User.getActiveProviders'
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'ActiveProvidersMultipleModel' + me.id,
            pageSize: 25,
            autoLoad: false
        });

        Ext.apply(this, {
            store: me.store,
            listConfig: {
                loadingText: _('searching') + '...'
            },
            pageSize: 25
        });

        me.callParent();
    }
});
