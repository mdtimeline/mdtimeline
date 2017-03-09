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

Ext.define('App.ux.LiveSnomedProblemMultipleSearch', {
    extend: 'App.ux.form.fields.BoxSelect',
    xtype: 'livesnomedproblemmultiple',
    allowBlank: true,
    editable: true,
    typeAhead: false,
    autoSelect: false,
    emptyText: _('problem_search') + '...',
    queryMode: 'remote',
    labelTpl: '{FullySpecifiedName} &#60;{ConceptId}&#62;',
    forceSelection: false,
    displayField: 'FullySpecifiedName',
    valueField: 'ConceptId',
    pageSize: 25,
    minChars: 3,
    listConfig: {
        tpl: [
            '<tpl for=".">',
            '<div role="option" class="x-boundlist-item">({CodeType}) {FullySpecifiedName}  &#60;{ConceptId}&#62;</div>',
            '</tpl>'
        ]
    },
    initComponent: function(){
        var me = this,
            model = 'SnomedProblemsMultipleSearchModel' + me.id;

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'ConceptId',
                    type: 'string'
                },
                {
                    name: 'FullySpecifiedName',
                    type: 'string'
                },
                {
                    name: 'CodeType',
                    type: 'string'
                },
                {
                    name: 'OCCURRENCE',
                    type: 'int'
                }
            ],
            proxy: {
                idProperty: 'ConceptId',
                type: 'direct',
                api: {
                    read: 'SnomedCodes.liveProblemCodeSearch'
                },
                reader: {
                    totalProperty: 'totals',
                    root: 'data'
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'SnomedProblemsMultipleSearchModel' + me.id,
            pageSize: 25,
            autoLoad: false
        });

        Ext.apply(this, {
            store: me.store,
            listConfig: {
                loadingText: _('searching') + '...',
                getInnerTpl: function(){
                    return '<div class="search-item"><h3>{FullySpecifiedName}<span style="font-weight: normal"> ({ConceptId}) </span></h3></div>';
                }
            },
            pageSize: 25
        });

        me.callParent();
    }
});
