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
        'App.ux.LivePhoneCommunicationMultipleSearch',
		'Modules.reportcenter.reports.PatientList.ux.LabResultValuesFilter'
	],
	xtype: 'reportFilter',
	region: 'west',
	title: _('filters'),
	itemId: 'PatientListFilters',
	collapsible: true,
	border: true,
	split: true,
    bodyPadding: 5,
	defaults: {
		enableReset: true,
		anchor: '100%',
	},
	items: [
		{
			xtype: 'datefield',
			name: 'begin_date',
			columnWidth: 1,
			fieldLabel: _('begin_date'),
			labelWidth: 100,
			format: g('date_display_format'),
			submitFormat: 'Y-m-d'
		},
		{
			xtype: 'datefield',
			name: 'end_date',
			columnWidth: 1,
			fieldLabel: _('end_date'),
			labelWidth: 100,
			format: g('date_display_format'),
			submitFormat: 'Y-m-d'
		},
		{
			xtype: 'liveprovidermultiple',
			name: 'provider',
			itemId: 'option_name',
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #provider_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var provider_list = '',
						field = Ext.ComponentQuery.query('reportFilter #provider_name')[0];
					for (var i = 0; i < records.length; i++) {
						provider_list = records[i].data.option_name + ', ' + provider_list;
					}
					field.setValue(provider_list.substr(0,provider_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'provider_name',
			name: 'provider_name',
			value: ''
		},
		{
			xtype: 'liverxnormallergymultiple',
			name: 'allergy_code',
			itemId: 'allergy_code',
			displayField: 'STR',
			valueField: 'RXCUI',
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #allergy_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var allergy_list = '',
						field = Ext.ComponentQuery.query('reportFilter #allergy_name')[0];
					for (var i = 0; i < records.length; i++) {
						allergy_list = records[i].data.STR + ', ' + allergy_list;
					}
					field.setValue(allergy_list.substr(0,allergy_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'allergy_name',
			name: 'allergy_name',
			value: ''
		},
		{
			xtype: 'livesnomedproblemmultiple',
			name: 'problem_code',
			itemId: 'problem_code',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #problem_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var problem_list = '',
						field = Ext.ComponentQuery.query('reportFilter #problem_name')[0];
					for (var i = 0; i < records.length; i++) {
						problem_list = records[i].data.FullySpecifiedName + ', ' + problem_list;
					}
					field.setValue(problem_list.substr(0,problem_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'problem_name',
			name: 'problem_name',
			value: ''
		},
		{
			xtype: 'liverxnormmultiple',
			name: 'medication_code',
			itemId: 'medication_code',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #medication_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var medication_list = '',
						field = Ext.ComponentQuery.query('reportFilter #medication_name')[0];
					for (var i = 0; i < records.length; i++) {
						allergy_list = records[i].data.STR + ', ' + medication_list;
					}
					field.setValue(medication_list.substr(0,medication_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'medication_name',
			name: 'medication_name',
			value: ''
		},
		{
			xtype: 'racemultiple',
			itemId: 'race',
			name: 'race',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #race_name')[0].setValue('');
					}
				},
				select: function (cmb, records, eOpts) {
					var race_list = '',
						field = Ext.ComponentQuery.query('reportFilter #race_name')[0];
					for (var i = 0; i < records.length; i++) {
                        race_list = records[i].data.option_name + ', ' + race_list;
                    }
					field.setValue(race_list.substr(0,race_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'race_name',
			name: 'race_name',
			value: ''
		},
		{
			xtype: 'ethnicitymultiple',
			name: 'ethnicity',
			itemId: 'ethnicity',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #ethnicity_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var ethnicity_list = '',
						field = Ext.ComponentQuery.query('reportFilter #ethnicity')[0];
					for (var i = 0; i < records.length; i++) {
						ethnicity_list = records[i].data.option_name + ', ' + ethnicity_list;
					}
					field.setValue(ethnicity_list.substr(0,ethnicity_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'ethnicity_name',
			name: 'ethnicity_name',
			value: ''
		},
		{
			xtype: 'sexmultiple',
			name: 'sex',
			itemId: 'sex',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #sex_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var sex_list = '',
						field = Ext.ComponentQuery.query('reportFilter #sex_name')[0];
					for (var i = 0; i < records.length; i++) {
						sex_list = records[i].data.option_name + ', ' + sex_list;
					}
					field.setValue(sex_list.substr(0,sex_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'sex_name',
			name: 'sex_name',
			value: ''
		},
		{

			xtype: 'communicationmultiple',
			name: 'phone_publicity',
			itemId: 'phone_publicity',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #phone_publicity_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var communication_list = '',
						field = Ext.ComponentQuery.query('reportFilter #phone_publicity_name')[0];
					for (var i = 0; i < records.length; i++) {
						communication_list = records[i].data.option_name + ', ' + communication_list;
					}
					field.setValue(communication_list.substr(0,communication_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'phone_publicity_name',
			name: 'phone_publicity_name',
			value: ''
		},
		{
			xtype: 'container',
			layout: 'hbox',
            margin: '0 0 5 0',
            items: [
				{
					xtype: 'numberfield',
					flex: 1,
					name: 'ageFrom',
					enableKeyEvents: true,
					value: null,
					emptyText: _('ageFrom')
				},
				{
					xtype: 'numberfield',
					flex: 1,
					name: 'ageTo',
					enableKeyEvents: true,
					value: null,
					emptyText: _('ageTo')
				}
			]
		},
		{

			xtype: 'maritalmultiple',
			id: 'marital',
			name: 'marital',
			itemId: 'marital',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #marital_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var marital_list = '',
						field = Ext.ComponentQuery.query('reportFilter #marital_name')[0];
					for (var i = 0; i < records.length; i++) {
						marital_list = records[i].data.option_name + ', ' + marital_list;
					}
					field.setValue(marital_list.substr(0,marital_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'marital_name',
			name: 'marital_name',
			value: ''
		},
		{

			xtype: 'languagemultiple',
			id: 'language',
			name: 'language',
			itemId: 'language',
			enableKeyEvents: true,
			value: null,
			listeners: {
				change: function (cmb, value) {
					if (value == '') {
						Ext.ComponentQuery.query('reportFilter #language_name')[0].setValue('');
					}
				},
				select: function (combo, records, eOpts) {
					var lang_list = '',
						field = Ext.ComponentQuery.query('reportFilter #language_name')[0];
					for (var i = 0; i < records.length; i++) {
						lang_list = records[i].data.option_name + ', ' + lang_list;
					}
					field.setValue(lang_list.substr(0,lang_list.length-2));
				}
			}
		},
		{
			xtype: 'hiddenfield',
			itemId: 'language_name',
			name: 'language_name',
			value: ''
		},
		{
			xtype: 'labresultvalues',
			itemId: 'labResult',
			submitValue: true,
			name: 'lab_results',
            hideFieldLabel: false,
            listeners: {
                addItem: function (combo, record, eOpts) {
                    var temp,parameters,
                        laboratoryResults = Ext.ComponentQuery.query('reportFilter #laboratory_results')[0],
                        filterLabOrder = Ext.ComponentQuery.query('reportFilter #WHERE_lab_order')[0],
                        filterSubLabOrder = Ext.ComponentQuery.query('reportFilter #WHERE_sub_lab_order')[0],
                        form = Ext.ComponentQuery.query('#PatientListFilters')[0];

                    // Clean up the filter and context
                    filterLabOrder.setValue('');
                    filterSubLabOrder.setValue('');
                    laboratoryResults.setValue('');

                    parameters = form.getValues();

                    if(parameters.lab_results.length === undefined){
                        record = parameters.lab_results;

                        temp='';
                        temp += record.lab_name+': '+record.operator+record.value;
                        laboratoryResults.setValue(record.lab_name+': '+record.operator+record.value);

                        temp='AND ';
                        temp += "(patient_order_results_observations.code ='"+record.lab_code+"' AND patient_order_results_observations.value "+record.operator+" "+record.value+')';
                        filterLabOrder.setValue(temp);

                        temp='AND ';
                        temp += "(PORO.code ='"+record.lab_code+"' AND PORO.value "+record.operator+" "+record.value+')';
                        filterSubLabOrder.setValue(temp);
                    } else {
                        for(i=0; i < parameters.lab_results.length; i++){
                            record = parameters.lab_results[i];

                            temp='';
                            temp += record.lab_name+': '+record.operator+record.value;
                            laboratoryResults.setValue(laboratoryResults.getValue()+', '+temp);

                            temp=' AND ';
                            temp += "(patient_order_results_observations.code ='"+record.lab_code+"' AND patient_order_results_observations.value "+record.operator+" "+record.value+')';
                            filterLabOrder.setValue(filterLabOrder.getValue()+temp);

                            temp=' AND ';
                            temp += "(PORO.code ='"+record.lab_code+"' AND PORO.value "+record.operator+" "+record.value+')';
                            filterSubLabOrder.setValue(filterSubLabOrder.getValue()+temp);
                        }
                    }
                },
                removeItem: function(combo, record, value){
                    var temp,parameters,
                        laboratoryResults = Ext.ComponentQuery.query('reportFilter #laboratory_results')[0],
                        filterLabOrder = Ext.ComponentQuery.query('reportFilter #WHERE_lab_order')[0],
                        filterSubLabOrder = Ext.ComponentQuery.query('reportFilter #WHERE_sub_lab_order')[0],
                        form = Ext.ComponentQuery.query('#PatientListFilters')[0];

                    // Clean up the filter and context
                    filterLabOrder.setValue('');
                    filterSubLabOrder.setValue('');
                    laboratoryResults.setValue('');

                    parameters = form.getValues();

                    if(parameters.lab_results.length === undefined){
                        record = parameters.lab_results;

                        temp='';
                        temp += record.lab_name+': '+record.operator+record.value;
                        laboratoryResults.setValue(record.lab_name+': '+record.operator+record.value);

                        temp='AND ';
                        temp += "(patient_order_results_observations.code ='"+record.lab_code+"' AND patient_order_results_observations.value "+record.operator+" "+record.value+')';
                        filterLabOrder.setValue(temp);

                        temp='AND ';
                        temp += "(PORO.code ='"+record.lab_code+"' AND PORO.value "+record.operator+" "+record.value+')';
                        filterSubLabOrder.setValue(temp);
                    } else {
                        for(i=0; i < parameters.lab_results.length; i++){
                            record = parameters.lab_results[i];

                            temp='';
                            temp += record.lab_name+': '+record.operator+record.value;
                            laboratoryResults.setValue(laboratoryResults.getValue()+', '+temp);

                            temp=' AND ';
                            temp += "(patient_order_results_observations.code ='"+record.lab_code+"' AND patient_order_results_observations.value "+record.operator+" "+record.value+')';
                            filterLabOrder.setValue(filterLabOrder.getValue()+temp);

                            temp=' AND ';
                            temp += "(PORO.code ='"+record.lab_code+"' AND PORO.value "+record.operator+" "+record.value+')';
                            filterSubLabOrder.setValue(filterSubLabOrder.getValue()+temp);
                        }
                    }
                }
            }
		},
        {
            xtype: 'hiddenfield',
            itemId: 'laboratory_results',
            name: 'laboratory_results',
            value: ''
        },
        {
            xtype: 'hiddenfield',
            itemId: 'WHERE_lab_order',
            name: 'WHERE_lab_order',
            value: null
        },
        {
            xtype: 'hiddenfield',
            itemId: 'WHERE_sub_lab_order',
            name: 'WHERE_sub_lab_order',
            value: null
        }
	]
});
