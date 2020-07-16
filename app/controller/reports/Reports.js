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

Ext.define('App.controller.reports.Reports', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.ux.grid.Printer'
	],
	refs: [
		{
			ref: 'ReportsPanel',
			selector: '#ReportsPanel'
		},
		{
			ref: 'ReportsGrid',
			selector: '#ReportsGrid'
		},
		{
			ref: 'ReportWindow',
			selector: '#ReportWindow'
		},
		{
			ref: 'ReportWindowForm',
			selector: '#ReportWindowForm'
		},
		{
			ref: 'ReportWindowGrid',
			selector: '#ReportWindowGrid'
		}
	],

	init: function(){

		if(!a('access_reports')) return;

		var me = this;

		me.nav = me.getController('Navigation');
		me.nav.addNavigationNodes('reports', {
			'text': _('reports'),
			'leaf': true,
			'cls': 'file',
			'id': 'App.view.reports.ReportsPanel',
		}, 0);

		me.control({
			'#ReportsGrid': {
				beforerender: me.onReportsGridBeforeRender,
				itemdblclick: me.onReportsGridItemDblClick
			},
			'#ReportWindowReloadBtn': {
				click: me.onReportWindowReloadBtnClick
			},
			'#ReportWindowGridPrintBtn': {
				click: me.onReportWindowGridPrintBtnClick
			}
		});

	},

	onReportWindowGridPrintBtnClick: function(btn){

		var report_grid = btn.up('grid'),
			report_win = report_grid.up('window'),
			report_form = report_win.down('form').getForm(),
			filters = [];

		if(!report_grid.filters){
			app.msg(_('oops'), 'No Filters Found', true);
			return;
		}

		Ext.Object.each(report_grid.filters, function (property, filter) {
			var val = filter ? filter : 'None';
			filters.push(Ext.String.format('<b>{0}</b>: {1}', _(property), val));
		});

		App.ux.grid.Printer.mainTitle = Ext.String.format('{0} Report', report_win.title);
		App.ux.grid.Printer.filtersHtml = 'Filters: ' + filters.join(', ');
		App.ux.grid.Printer.print(report_grid);

	},

	onReportsGridBeforeRender: function (grid) {
		grid.store.load();
	},

	onReportsGridItemDblClick: function (grid, record) {
		var me = this,
			win = me.showReportsWindow(),
			form = win.down('form'),
			report_grid = win.down('grid'),
			group_fields = record.get('group_fields'),
			model_fields = [], search_fields = [], columns, group_by;

		report_grid.filters = undefined;
		win.setTitle(record.get('title'));
		win.el.mask('Getting Things Ready');

		Reports.getReport(record.get('id'), function (response) {

			win.el.unmask();
			if(response === false) {
				win.close();
				return;
			}

			response.parameters.forEach(function (parameter) {

				// fire event... any module can listen for this event and handle the search field
				// just make sure the listener function return false to continue to the next field
				if(app.fireEvent('beforereportfilteradd', me, parameter, search_fields) === false){
					return;
				}

				if(parameter.DATA_TYPE === 'date' || parameter.DATA_TYPE === 'datetime'){
					search_fields.push({
						xtype: 'datefield',
						fieldLabel: _(parameter.PARAMETER_NAME),
						labelAlign: 'top',
						margin: '0 5 0 0',
						name: parameter.PARAMETER_NAME
					});
					return;
				}
				if(parameter.PARAMETER_NAME === 'facility_id'){
					search_fields.push({
						xtype: 'mitos.facilitiescombo',
						fieldLabel: _(parameter.PARAMETER_NAME),
						labelAlign: 'top',
						margin: '0 5 0 0',
						multiSelect: true,
						name: parameter.PARAMETER_NAME
					});
					return;
				}
				if(parameter.PARAMETER_NAME === 'specialty_id'){
					search_fields.push({
						xtype: 'specialtiescombo',
						fieldLabel: _(parameter.PARAMETER_NAME),
						labelAlign: 'top',
						margin: '0 5 0 0',
						multiSelect: true,
						name: parameter.PARAMETER_NAME
					});
					return;
				}
				if(parameter.PARAMETER_NAME === 'department_id'){
					search_fields.push({
						xtype: 'depatmentscombo',
						fieldLabel: _(parameter.PARAMETER_NAME),
						labelAlign: 'top',
						margin: '0 5 0 0',
						multiSelect: true,
						name: parameter.PARAMETER_NAME
					});
					return;
				}
				if(parameter.PARAMETER_NAME === 'insurance_id'){
					search_fields.push({
						xtype: 'insurancescombo',
						fieldLabel: _(parameter.PARAMETER_NAME),
						labelAlign: 'top',
						margin: '0 5 0 0',
						multiSelect: true,
						name: parameter.PARAMETER_NAME
					});
					return;
				}
				// any parameter ending in _uid will add a user search field
				if(parameter.PARAMETER_NAME.search(/_uid$/) !== -1){
					search_fields.push({
						xtype: 'userlivetsearch',
						fieldLabel: _(parameter.PARAMETER_NAME),
						labelAlign: 'top',
						margin: '0 5 0 0',
						hideLabel: false,
						name: parameter.PARAMETER_NAME
					});
					return;
				}

				// any pid field or ending in _pid will add a patient live search
				if(parameter.PARAMETER_NAME.search(/^pid$|_pid$/) !== -1){
					search_fields.push({
						xtype: 'patienlivetsearch',
						fieldLabel: _(parameter.PARAMETER_NAME),
						labelAlign: 'top',
						margin: '0 5 0 0',
						hideLabel: false,
						name: parameter.PARAMETER_NAME
					});
					return;
				}

				// by default add the text field
				search_fields.push({
					xtype: 'textfield',
					fieldLabel: _(parameter.PARAMETER_NAME),
					labelAlign: 'top',
					margin: '0 5 0 0',
					name: parameter.PARAMETER_NAME
				});


			});

			if(group_fields !== ''){
				group_fields = group_fields.split(',');
				var data = [];

				group_fields.forEach(function (group_field) {
					if(group_by == null) {
						group_by = group_field;
					}
					data.push([group_field, _(group_field)]);
				});

				search_fields.push({
					xtype: 'combo',
					fieldLabel: _('group_by'),
					labelAlign: 'top',
					margin: '0 5 0 0',
					itemId: 'ReportWindowGroupByCmb',
					submitValue: false,
					value: group_by,
					store: data
				});
			}

			// add fields to the form
			form.add(search_fields);

			columns = eval('(' + response.columns + ')');
			columns.forEach(function (column) {

				model_fields.push({
					type: column.dataType,
					name: column.dataIndex
				});

			});

			if(group_by){
				report_grid.reconfigure(Ext.create('Ext.data.Store',{ fields: model_fields, groupField: group_by  }), columns);
			}else{
				report_grid.reconfigure(Ext.create('Ext.data.Store',{ fields: model_fields }), columns);
			}
		});

	},

	onReportWindowReloadBtnClick: function(btn){

		var me = this,
			win = btn.up('window'),
			filter_form = win.down('form').getForm(),
			report_grid = win.down('grid'),
			filters = filter_form.getValues(),
			report_record = me.getReportsGrid().getSelectionModel().getSelection()[0];

		Ext.Object.each(filters, function (property, value) {
			if(Ext.isArray(value)){
				filters[property] = value.join(',');
			}
		});

		report_grid.view.el.mask('Loading!!!');
		report_grid.filters = filters;




		Reports.runReportByIdAndFilters(report_record.get('id'), filters, function (response) {

			report_grid.getStore().loadData(response);
			report_grid.view.el.unmask();

		});

	},

	showReportsWindow: function () {
		if(!this.getReportWindow()){
			Ext.create('App.view.reports.ReportWindow');
		}
		return this.getReportWindow().show();
	}

});