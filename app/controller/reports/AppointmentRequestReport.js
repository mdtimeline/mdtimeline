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

Ext.define('App.controller.reports.AppointmentRequestReport', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.ux.grid.Printer'
	],
	refs: [
		{
			ref: 'AppointmentRequestReport',
			selector: '#AppointmentRequestReport'
		},
		{
			ref: 'AppointmentRequestReportFrom',
			selector: '#AppointmentRequestReportFrom'
		},
		{
			ref: 'AppointmentRequestReportGrid',
			selector: '#AppointmentRequestReportGrid'
		}
	],

	init: function(){

		// if(!a('access_appointments_request_report')) return;

		var me = this;

		me.nav = me.getController('Navigation');
		me.nav.addNavigationNodes('reports', {
			'text': _('appointment_requests'),
			'leaf': true,
			'cls': 'file',
			'id': 'App.view.reports.AppointmentRequestReport',
		}, 0);

		me.control({
			'#AppointmentRequestReportSearchBtn': {
				click: me.onAppointmentRequestReportSearchBtnClick
			},
			'#AppointmentRequestReportGridPrintBtn': {
				click: me.onAppointmentRequestReportGridPrintBtnClick
			}
		});

	},

	onAppointmentRequestReportGridPrintBtnClick: function(btn){

		var grid = btn.up('grid');

		App.ux.grid.Printer.mainTitle = 'Appointment Request Report';
		App.ux.grid.Printer.filtersHtml = grid.filtersHtml;
		App.ux.grid.Printer.print(grid);
	},

	onAppointmentRequestReportSearchBtnClick: function (btn) {

		say('onAppointmentRequestReportSearchBtnClick');

		var me = this,
			form = me.getAppointmentRequestReportFrom().getForm(),
			values = form.getValues(),
			grid = me.getAppointmentRequestReportGrid(),
			store = grid.getStore(),
			filters = [];

		grid.filtersHtml = '';

		if(!form.isValid()) return;

		Ext.Object.each(values, function (key, value) {

			if(value === '') return;

			if(key === 'date_from'){
				filters.push({
					property: 'create_date',
					operator: '>=',
					value: value + ' 00:00:00'
				});

				grid.filtersHtml += ' From: ' + value;

			}else if(key === 'date_to'){
				filters.push({
					property: 'create_date',
					operator: '<=',
					value: value + ' 23:59:59'
				});

				grid.filtersHtml += ' To: ' + value;

			}else{
				filters.push({
					property: key,
					value: value
				});
			}

		});

		store.clearFilter(true);
		store.filter(filters);

	}
});