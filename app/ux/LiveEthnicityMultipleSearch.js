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

Ext.define('App.ux.LiveEthnicityMultipleSearch', {
    extend: 'App.ux.form.fields.BoxSelect',
    xtype: 'ethnicitymultiple',
    allowBlank: true,
    editable: true,
    typeAhead: false,
    autoSelect: false,
    emptyText: _('ethnicity_search') + '...',
    queryMode: 'remote',
    minChars: 3,
    forceSelection: false,
    displayField: 'option_name',
    valueField: 'option_value',
    pageSize: 25,
    enableReset: false,
    initComponent: function(){
        var me = this,
            model = 'EthnicityMultipleModel' + me.id;

	    if(me.enableReset){
		    me.trigger2Cls = 'x-form-clear-trigger';
		    me.onTrigger2Click = function() {
			    me.reset();
		    }
	    }

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'option_name', type: 'string' },
                {name: 'option_value', type: 'string' }
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'CombosData.getOptionsByListId'
                },
                extraParams: {
                    list_id: 59
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'EthnicityMultipleModel' + me.id,
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
