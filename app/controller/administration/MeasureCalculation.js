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
        {
            selector: '#MeasureCalculationGrid',
            ref: 'MeasureCalculationGrid'
        },
        {
            selector: '#MeasureCalculationFromField',
            ref: 'MeasureCalculationFromField'
        },
        {
            selector: '#MeasureCalculationToField',
            ref: 'MeasureCalculationToField'
        },
        {
            selector: '#MeasureCalculationProviderField',
            ref: 'MeasureCalculationProviderField'
        },
    ],

    /**
     *
     */
    init: function(){
        var me = this;

        me.control({
            '#MeasureCalculationRefreshBtn': {
                click: me.onMeasureCalculationRefreshBtnClick
            },
            '#MeasureCalculationGrid': {
                celldblclick: me.onMeasureCalculationGridCellDblClick
            },
            '#MeasureCalculationGridPrintBtn': {
                click: me.onMeasureCalculationGridPrintBtnClick
            }
        });

        me.doSortGridStoreBuffer = Ext.Function.createBuffered(me.doSortGridStore, 250, me);
    },

    onMeasureCalculationGridPrintBtnClick: function(btn){

        var fromField = this.getMeasureCalculationFromField(),
            toField = this.getMeasureCalculationToField(),
            providerField = this.getMeasureCalculationProviderField(),
            provider = providerField.findRecordByValue(providerField.getValue()),
            grid = this.getMeasureCalculationGrid();

        if(!fromField.isValid() || !toField.isValid() || !providerField.isValid() || grid.store.count() === 0) return;

        App.ux.grid.Printer.mainTitle = 'Measure Calculation'; //optional
        App.ux.grid.Printer.filtersHtml = Ext.String.format(
            '<b>From:</b> {0}<br><b>To:</b> {1}<br><b>Provider:</b> {2}',
            Ext.Date.format(fromField.getValue(), 'F j, Y'),
            Ext.Date.format(toField.getValue(), 'F j, Y'),
            provider.get('fullname'),
        ); //optional
        App.ux.grid.Printer.print(grid);
    },

    onMeasureCalculationGridCellDblClick: function(view, td, cellIndex, record){
        var me = this,
            column = view.panel.columnManager.getHeaderAtIndex(cellIndex),
            pids;

        if(column.dataIndex !== 'denominator' && column.dataIndex !== 'numerator') return;

        pids = record.get(column.dataIndex + '_pids');

        if(pids == '') return;

        MeasureCalculation.getPatientList(pids, function (response) {
            me.showMeasureCalculationPatientListWindow(Ext.String.capitalize(column.dataIndex) + ' Patient List', response);
        });

    },

    onMeasureCalculationRefreshBtnClick: function (btn) {
        var fromField = this.getMeasureCalculationFromField(),
            toField = this.getMeasureCalculationToField(),
            providerField = this.getMeasureCalculationProviderField(),
            grid_store = this.getMeasureCalculationGrid().getStore();

        grid_store.removeAll();
        grid_store.commitChanges();

        if(!fromField.isValid() || !toField.isValid() || !providerField.isValid()){
            return;
        }

        var provider = providerField.getValue(),
	        from = Ext.Date.format(fromField.getValue(), 'Y-m-d'),
	        to = Ext.Date.format(toField.getValue(), 'Y-m-d');

        this.doReportMeasureByDates('ePrescribing', provider, from, to);
        this.doReportMeasureByDates('PatientEducation', provider, from, to);
        this.doReportMeasureByDates('ProvidePatientsElectronicAccess', provider, from, to);
        this.doReportMeasureByDates('ViewDownloadTransmit', provider, from, to);
        this.doReportMeasureByDates('SecureMessaging', provider, from, to);
        this.doReportMeasureByDates('PatientGeneratedHealthData', provider, from, to);
        this.doReportMeasureByDates('SupportElectronicReferralLoopsSending', provider, from, to);
        this.doReportMeasureByDates('ReceiveAndIncorporate', provider, from, to);
        this.doReportMeasureByDates('MedicationClinicalInformationReconciliation', provider, from, to);
        this.doReportMeasureByDates('CPOEMedications', provider, from, to);
        this.doReportMeasureByDates('CPOELaboratory', provider, from, to);
        this.doReportMeasureByDates('CPOERadiology', provider, from, to);

    },

    doReportMeasureByDates: function (measure, provider_id, start_date, end_date) {
        var me = this;

        MeasureCalculation.getReportMeasureByDates(measure, provider_id, start_date, end_date, function (response) {
            var store =  me.getMeasureCalculationGrid().getStore();
            store.loadData(response, true);
            me.doSortGridStoreBuffer(store);
        });

    },

    doSortGridStore: function(store){
        store.sort();
    },

    showMeasureCalculationPatientListWindow: function (title, data) {

        say(title);
        say(data);

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
                    }
                ]
            }
        }).show();

        store.loadData(data);
    }

});
