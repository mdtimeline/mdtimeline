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

Ext.define('App.ux.LiveRXNORMAllergyMultipleSearch', {
    extend: 'App.ux.form.fields.BoxSelect',
    xtype: 'liverxnormallergymultiple',
    validateOnChange: false,
    validateOnBlur: false,
    allowBlank: true,
    editable: true,
    typeAhead: false,
    autoSelect: false,
    //triggerOnClick: false,
    createNewOnEnter: true,
    createNewOnBlur: true,
    emptyText: _('allergy_search') + '...',
    queryMode: 'remote',
    labelTpl: '{STR} &#60;{RXCUI}&#62;',
    forceSelection: false,
    displayField: 'STR',
    valueField: 'STR',
    pageSize: 25,
    initComponent: function(){
        var me = this,
            model = 'RXNORMAllergyMultipleSearchModel' + me.id;

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'RXCUI', type: 'auto'},
                {name: 'CODE', type: 'auto'},
                {name: 'STR', type: 'auto'},
                {name: 'DST', type: 'auto'},
                {name: 'DRT', type: 'auto'},
                {name: 'DDF', type: 'auto'},
                {name: 'DDFA', type: 'auto'},
                {name: 'RXN_QUANTITY', type: 'auto'},
                {name: 'SAB', type: 'auto'},
                {name: 'RXAUI', type: 'auto'},
                {name: 'CodeType', defaultValue: 'RXNORM'}
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'Rxnorm.getRXNORMAllergyLiveSearch'
                },
                reader: {
                    totalProperty: 'totals',
                    root: 'rows'
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'RXNORMAllergyMultipleSearchModel' + me.id,
            pageSize: 25,
            autoLoad: false
        });

        Ext.apply(this, {
            store: me.store,
            listConfig: {
                loadingText: _('searching') + '...',
                getInnerTpl: function(){
                    return '<div class="search-item"><h3>{STR}<span style="font-weight: normal"> ({RXCUI}) </span></h3></div>';
                }
            },
            pageSize: 25
        });

        me.callParent();
    }
});
