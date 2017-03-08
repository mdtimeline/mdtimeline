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

Ext.define('Modules.reportcenter.reports.PatientList.filtersForm', {
    extend: 'Ext.form.Panel',
    requires: [
        'Ext.form.field.Date',
        'App.ux.combo.ActiveProviders',
        'App.ux.LiveSnomedProblemMultipleSearch',
        'App.ux.LiveRXNORMAllergyMultipleSearch',
        'App.ux.LiveRXNORMMultipleSearch',
        'App.ux.LiveProviderMultipleSearch',
        'App.ux.LiveSexMultipleSearch',
        'App.ux.LiveEthnicityMultipleSearch',
        'App.ux.LiveRaceMultipleSearch',
        'App.ux.LiveMaritalMultipleSearch',
        'App.ux.LiveLanguageMultipleSearch',
        'Modules.reportcenter.reports.PatientList.ux.LabResultValuesFilter'
    ],
    xtype: 'reportFilter',
    region: 'west',
    title: _('filters'),
    itemId: 'PatientListFilters',
    collapsible: true,
    border: true,
    split: true,
    defaults: {
        xtype: 'fieldset',
        layout: 'anchor',
        defaults: {anchor: '100%'},
        border: false,
        frame: false,
        margin: 2
    },
    items: [
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'begin_date',
                            columnWidth: 1,
                            fieldLabel: _('begin_date'),
                            labelWidth: 100,
                            format: g('date_display_format'),
                            submitFormat: 'Y-m-d'
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'end_date',
                            columnWidth: 1,
                            fieldLabel: _('end_date'),
                            labelWidth: 100,
                            format: g('date_display_format'),
                            submitFormat: 'Y-m-d'
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'liveprovidermultiple',
                            name: 'provider',
                            itemId: 'option_name',
                            columnWidth: 1,
                            hideLabel: true,
                            listeners: {
                                select: function (combo, records, eOpts) {
                                    var provider_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #provider_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        provider_list = records[i].data.STR + ', ' + provider_list;
                                    }
                                    field.setValue(provider_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var option_name = Ext.ComponentQuery.query('reportFilter #option_name')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #provider_name')[0];
                                    option_name.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'provider_name',
                            name: 'provider_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'liverxnormallergymultiple',
                            hideLabel: true,
                            name: 'allergy_code',
                            itemId: 'allergy_code',
                            columnWidth: 1,
                            displayField: 'STR',
                            valueField: 'RXCUI',
                            listeners: {
                                select: function (combo, records, eOpts) {
                                    var allergy_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #allergy_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        allergy_list = records[i].data.STR + ', ' + allergy_list;
                                    }
                                    field.setValue(allergy_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var allergy_code = Ext.ComponentQuery.query('reportFilter #allergy_code')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #allergy_name')[0];
                                    allergy_code.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'allergy_name',
                            name: 'allergy_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'livesnomedproblemmultiple',
                            hideLabel: true,
                            name: 'problem_code',
                            itemId: 'problem_code',
                            columnWidth: 1,
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function (combo, records, eOpts) {
                                    var problem_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #problem_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        problem_list = records[i].data.FullySpecifiedName + ', ' + problem_list;
                                    }
                                    field.setValue(problem_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var problem_code = Ext.ComponentQuery.query('reportFilter #problem_code')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #problem_name')[0];
                                    problem_code.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'problem_name',
                            name: 'problem_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'liverxnormmultiple',
                            hideLabel: true,
                            columnWidth: 1,
                            name: 'medication_code',
                            itemId: 'medication_code',
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function(combo, records, eOpts){
                                    var medication_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #medication_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        allergy_list = records[i].data.STR + ', ' + medication_list;
                                    }
                                    field.setValue(medication_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var medication_code = Ext.ComponentQuery.query('reportFilter #medication_code')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #medication_name')[0];
                                    medication_code.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'medication_name',
                            name: 'medication_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'racemultiple',
                            hideLabel: true,
                            columnWidth: 1,
                            itemId: 'race',
                            name: 'race',
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function(combo, records, eOpts){
                                    var race_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #race_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        race_list = records[i].data.STR + ', ' + race_list;
                                    }
                                    field.setValue(race_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var ethnicitycombo = Ext.ComponentQuery.query('reportFilter #race')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #race_name')[0];
                                    ethnicitycombo.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'race_name',
                            name: 'race_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'ethnicitymultiple',
                            hideLabel: true,
                            columnWidth: 1,
                            name: 'ethnicity',
                            itemId: 'ethnicity',
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function(combo, records, eOpts){
                                    var ethnicity_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #ethnicity')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        ethnicity_list = records[i].data.STR + ', ' + ethnicity_list;
                                    }
                                    field.setValue(ethnicity_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var racecombo = Ext.ComponentQuery.query('reportFilter #ethnicity')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #ethnicity_name')[0];
                                    racecombo.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'ethnicity_name',
                            name: 'ethnicity_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'sexmultiple',
                            hideLabel: true,
                            columnWidth: 1,
                            name: 'sex',
                            itemId: 'sex',
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function(combo, records, eOpts){
                                    var sex_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #sex_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        sex_list = records[i].data.STR + ', ' + sex_list;
                                    }
                                    field.setValue(sex_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var sexcombo = Ext.ComponentQuery.query('reportFilter #sex')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #sex_name')[0];
                                    sexcombo.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'sex_name',
                            name: 'sex_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'communicationmultiple',
                            hideLabel: true,
                            columnWidth: 1,
                            name: 'phone_publicity',
                            itemId: 'phone_publicity',
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function(combo, records, eOpts){
                                    var communication_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #phone_publicity_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        communication_list = records[i].data.STR + ', ' + communication_list;
                                    }
                                    field.setValue(communication_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var phone_publicity = Ext.ComponentQuery.query('reportFilter #phone_publicity')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #phone_publicity_name')[0];
                                    phone_publicity.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'phone_publicity_name',
                            name: 'phone_publicity_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'numberfield',
                            hideLabel: true,
                            columnWidth: 0.50,
                            name: 'ageFrom',
                            enableKeyEvents: true,
                            value: null,
                            emptyText: _('ageFrom')
                        },
                        {
                            xtype: 'numberfield',
                            hideLabel: true,
                            columnWidth: 0.50,
                            name: 'ageTo',
                            enableKeyEvents: true,
                            value: null,
                            emptyText: _('ageTo')
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'maritalmultiple',
                            id: 'marital',
                            hideLabel: true,
                            columnWidth: 1,
                            name: 'marital',
                            itemId: 'marital',
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function(combo, records, eOpts){
                                    var marital_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #marital_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        marital_list = records[i].data.STR + ', ' + marital_list;
                                    }
                                    field.setValue(marital_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var marital = Ext.ComponentQuery.query('reportFilter #marital')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #marital_name')[0];
                                    marital.reset();
                                    field.setValue('');

                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'marital_name',
                            name: 'marital_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'languagemultiple',
                            id: 'language',
                            hideLabel: true,
                            columnWidth: 1,
                            name: 'language',
                            itemId: 'language',
                            enableKeyEvents: true,
                            value: null,
                            listeners: {
                                select: function(combo, records, eOpts){
                                    var lang_list = '',
                                        field = Ext.ComponentQuery.query('reportFilter #language_name')[0];
                                    for (var i = 0; i < records.length; i++) {
                                        lang_list = records[i].data.STR + ', ' + lang_list;
                                    }
                                    field.setValue(lang_list);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'X',
                            listeners:{
                                click: function(btn){
                                    var language = Ext.ComponentQuery.query('reportFilter #language')[0],
                                        field = Ext.ComponentQuery.query('reportFilter #language_name')[0];
                                    language.reset();
                                    field.setValue('');
                                }
                            }
                        },
                        {
                            xtype: 'hiddenfield',
                            itemId: 'language_name',
                            name: 'language_name',
                            value: ''
                        }
                    ]
                }
            ]
        },
        {
            items: [
                {
                    xtype: 'panel',
                    layout: 'column',
                    border: false,
                    frame: false,
                    items: [
                        {
                            xtype: 'labresultvalues',
                            id: 'labResult',
                            itemId: 'labResult',
                            columnWidth: 1
                        }
                    ]
                }
            ]
        }
    ]
});
