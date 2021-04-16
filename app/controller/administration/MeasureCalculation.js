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

Ext.define('App.controller.administration.MeasureCalculation', {
    extend: 'Ext.app.Controller',
    uses: [
        'App.ux.grid.Printer'
    ],
    refs: [
        {
            selector: '#MeasureCalculation',
            ref: 'MeasureCalculation'
        },

        // Meaningful Use
        {
            selector: '#MeaningfulUseGrid',
            ref: 'MeaningfulUseGrid'
        },
        {
            selector: '#MeaningfulUseFromField',
            ref: 'MeaningfulUseFromField'
        },
        {
            selector: '#MeaningfulUseToField',
            ref: 'MeaningfulUseToField'
        },
        {
            selector: '#MeaningfulUseProviderField',
            ref: 'MeaningfulUseProviderField'
        },

        // MIPS
        {
            selector: '#MIPSGrid',
            ref: 'MIPSGrid'
        },
        {
            selector: '#MIPSGridFromField',
            ref: 'MIPSGridFromField'
        },
        {
            selector: '#MIPSGridToField',
            ref: 'MIPSGridToField'
        },
        {
            selector: '#MIPSGridProviderField',
            ref: 'MIPSGridProviderField'
        },
        {
            selector: '#MIPSGridInsuranceField',
            ref: 'MIPSGridInsuranceField'
        }
    ],

    /**
     *
     */
    init: function(){
        var me = this;

        me.control({
            '#MeaningfulUseRefreshBtn': {
                click: me.onMeaningfulUseRefreshBtnClick
            },
            '#MeaningfulUseGrid': {
                celldblclick: me.onMeaningfulUseGridCellDblClick
            },
            '#MeaningfulUseGridPrintBtn': {
                click: me.onMeaningfulUseGridPrintBtnClick
            },


            '#MIPSGridRefreshBtn': {
                click: me.onMIPSGridRefreshBtnClick
            },
            '#MIPSGrid': {
                celldblclick: me.onMIPSGridCellDblClick
            },
            '#MIPSGridPrintBtn': {
                click: me.onMIPSGridPrintBtnClick
            }
        });

        me.doSortGridStoreBuffer = Ext.Function.createBuffered(me.doSortGridStore, 250, me);
    },

    onMIPSGridRefreshBtnClick: function (btn){
        var fromField = this.getMIPSGridFromField(),
            toField = this.getMIPSGridToField(),
            providerField = this.getMIPSGridProviderField(),
            insuranceField = this.getMIPSGridInsuranceField(),
            grid_store = this.getMIPSGrid().getStore();

        grid_store.removeAll();
        grid_store.commitChanges();

        if(!fromField.isValid() || !toField.isValid() || !providerField.isValid()){
            return;
        }

        var provider_id = providerField.getValue(),
            insurance_id = insuranceField.getValue(),
            from = Ext.Date.format(fromField.getValue(), 'Y-m-d'),
            to = Ext.Date.format(toField.getValue(), 'Y-m-d');

        this.doReportMeasureByDates(grid_store, 'AdvanceCarePlan', provider_id, from, to, insurance_id);
        this.doReportMeasureByDates(grid_store, 'ControllingHighBloodPressure', provider_id, from, to, insurance_id);
        this.doReportMeasureByDates(grid_store, 'CoronaryArteryDisease', provider_id, from, to, insurance_id);
        // this.doReportMeasureByDates(grid_store, 'CoronaryArteryDiseaseWIthMIorLVSD', provider_id, from, to, insurance_id);
        this.doReportMeasureByDates(grid_store, 'CoronaryArteryDiseaseAntiplatelet', provider_id, from, to, insurance_id);
        // this.doReportMeasureByDates(grid_store, 'CoronaryArteryDiseaseBetaBlocker', provider_id, from, to, insurance_id);


    },

    onMIPSGridCellDblClick: function (view, td, cellIndex, report_record){
        this.doShowPatientList(view, td, cellIndex, report_record);
    },

    onMIPSGridPrintBtnClick: function (btn){
        var fromField = this.getMIPSGridFromField(),
            toField = this.getMIPSGridToField(),
            providerField = this.getMIPSGridProviderField(),
            provider = providerField.findRecordByValue(providerField.getValue()),
            grid = this.getMIPSGrid();

        if(!fromField.isValid() || !toField.isValid() || !providerField.isValid() || grid.store.count() === 0) return;

        App.ux.grid.Printer.mainTitle = 'MIPS Calculation'; //optional
        App.ux.grid.Printer.filtersHtml = Ext.String.format(
            '<b>From:</b> {0}<br><b>To:</b> {1}<br><b>Provider:</b> {2}',
            Ext.Date.format(fromField.getValue(), 'F j, Y'),
            Ext.Date.format(toField.getValue(), 'F j, Y'),
            provider.get('fullname')
        ); //optional
        App.ux.grid.Printer.print(grid);
    },

    onMeaningfulUseGridPrintBtnClick: function(btn){

        var fromField = this.getMeaningfulUseFromField(),
            toField = this.getMeaningfulUseToField(),
            providerField = this.getMeaningfulUseProviderField(),
            provider = providerField.findRecordByValue(providerField.getValue()),
            grid = this.getMeaningfulUseGrid();

        if(!fromField.isValid() || !toField.isValid() || !providerField.isValid() || grid.store.count() === 0) return;

        App.ux.grid.Printer.mainTitle = 'Meaningful Use Calculation'; //optional
        App.ux.grid.Printer.filtersHtml = Ext.String.format(
            '<b>From:</b> {0}<br><b>To:</b> {1}<br><b>Provider:</b> {2}',
            Ext.Date.format(fromField.getValue(), 'F j, Y'),
            Ext.Date.format(toField.getValue(), 'F j, Y'),
            provider.get('fullname')
        ); //optional
        App.ux.grid.Printer.print(grid);
    },

    onMeaningfulUseGridCellDblClick: function(view, td, cellIndex, report_record){
        this.doShowPatientList(view, td, cellIndex, report_record);
    },

    onMeaningfulUseRefreshBtnClick: function (btn) {
        var fromField = this.getMeaningfulUseFromField(),
            toField = this.getMeaningfulUseToField(),
            providerField = this.getMeaningfulUseProviderField(),
            grid_store = this.getMeaningfulUseGrid().getStore();

        grid_store.removeAll();
        grid_store.commitChanges();

        if(!fromField.isValid() || !toField.isValid() || !providerField.isValid()){
            return;
        }

        var provider_id = providerField.getValue(),
	        from = Ext.Date.format(fromField.getValue(), 'Y-m-d'),
	        to = Ext.Date.format(toField.getValue(), 'Y-m-d');

        this.doReportMeasureByDates(grid_store,'ePrescribing', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'PatientEducation', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'ProvidePatientsElectronicAccess', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'ViewDownloadTransmit', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'SecureMessaging', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'PatientGeneratedHealthData', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'SupportElectronicReferralLoopsSending', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'ReceiveAndIncorporate', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'MedicationClinicalInformationReconciliation', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'CPOEMedications', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'CPOELaboratory', provider_id, from, to, null);
        this.doReportMeasureByDates(grid_store,'CPOERadiology', provider_id, from, to, null);

    },

    doReportMeasureByDates: function (grid_store, measure, provider_id, start_date, end_date, insurance_id) {
        var me = this;

        MeasureCalculation.getReportMeasureByDates(measure, provider_id, start_date, end_date, insurance_id, function (response) {
            grid_store.loadData(response, true);
            me.doSortGridStoreBuffer(grid_store);
        });

    },

    doSortGridStore: function(store){
        store.sort();
    },

    showMeasureCalculationPatientListWindow: function (title, data, numerator_types_obj) {


        var store = Ext.create('Ext.data.ArrayStore', {
            fields: [
                {  name: 'pid', type: 'string' },
                {  name: 'pubpid', type: 'string'  },
                {  name: 'fname', type: 'string'  },
                {  name: 'mname', type: 'string'  },
                {  name: 'lname', type: 'string'  },
                {  name: 'sex', type: 'string'  },
                {  name: 'DOB', type: 'date', dateFormat: 'Y-m-d H:i:s' }
            ]
        });

        Ext.create('Ext.window.Window', {
            title: title + ' [' + data.length + ']',
            width: 800,
            height: 500,
            layout: 'fit',
            items: {  // Let's put an empty grid in just to illustrate fit layout
                xtype: 'grid',
                store: store,
                columns: [
                    {
                        text: 'ID',
                        dataIndex: 'pid',
                        width: 50
                    },
                    {
                        text: 'MRN',
                        dataIndex: 'pubpid',
                        width: 120
                    },
                    {
                        text: 'Name',
                        dataIndex: 'pubpid',
                        flex: 1,
                        renderer: function (v,m,r) {
                            return Ext.String.format('{0}, {1} {2}', r.get('lname'), r.get('fname'), r.get('mname'))
                        }
                    },
                    {
                        text: 'Sex',
                        dataIndex: 'sex',
                        width: 50
                    },
                    {
                        xtype: 'datecolumn',
                        text: 'DOB',
                        format:'Y-m-d',
                        dataIndex: 'DOB'
                    },
                    {
                        text: 'Action',
                        dataIndex: 'pid',
                        renderer: function (v) {
                            switch (numerator_types_obj[v]) {
                                case 'CCDA_VDT':
                                    return 'VDT';
                                case 'CCDA_VDT_API':
                                    return 'API';
                                default:
                                    return 'N/A';
                            }
                        }
                    }
                ]
            }
        }).show();

        store.loadData(data);
    },

    doShowPatientList: function (view, td, cellIndex, report_record){
        var me = this,
            column = view.panel.columnManager.getHeaderAtIndex(cellIndex),
            pids;

        if(column.dataIndex !== 'denominator' && column.dataIndex !== 'numerator') return;

        pids = report_record.get(column.dataIndex + '_pids');

        if(pids == '') return;

        var numerator_types = report_record.get('numerator_types'), i, len,
            numerator_types_obj = {}, v;

        if(numerator_types){

            numerator_types = numerator_types.split(',');

            len = numerator_types.length;
            for (i = 0; i < len; i++) {
                v = numerator_types[i].split('~');
                numerator_types_obj[v[0]] = v[1]
            }
        }

        MeasureCalculation.getPatientList(pids, function (response) {
            me.showMeasureCalculationPatientListWindow(Ext.String.capitalize(column.dataIndex) + ' Patient List', response, numerator_types_obj);
        });
    },

});
