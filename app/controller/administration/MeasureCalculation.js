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
    requires: [],

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
            }
        });
    },

    onMeasureCalculationGridCellDblClick: function(view, td, cellIndex, record, tr, rowIndex, e, eOpts ){
        var column = view.panel.columnManager.getHeaderAtIndex(cellIndex);
        if(column.dataIndex !== 'denominator' && column.dataIndex !== 'numerator') return;

        say('show patient records');
        say(column.dataIndex + '_pids');
        say(record.get(column.dataIndex + '_pids'));

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

        this.doReportMeasureByDates('CPOEMedications', provider, from, to);
        this.doReportMeasureByDates('CPOELaboratory', provider, from, to);
        this.doReportMeasureByDates('CPOERadiology', provider, from, to);

    },

    doReportMeasureByDates: function (measure, provider_id, start_date, end_date) {
        var me = this;

        MeasureCalculation.getReportMeasureByDates(measure, provider_id, start_date, end_date, function (response) {
            me.getMeasureCalculationGrid().getStore().loadData(response, true);
        });



    }

});
