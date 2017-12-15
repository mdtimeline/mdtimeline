/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2012 Ernesto Rodriguez
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

Ext.define('App.controller.dashboard.panel.NotSignedVitals', {
	extend: 'App.controller.dashboard.Dashboard',

	init: function(){
		if(!a('view_dashboard_vitals_not_signed')) return;

		var me = this;

		me.control({
			'portalpanel':{
				render: me.onDashboardPanelBeforeRender
			},
			'#DashboardPanel':{
				activate: me.onDashboardPanelActivate
			},
			'#DashboardNotSignedVitalsGrid':{
				selectionchange: me.onDashboardNotSignedVitalsGridSelectionChange,
				itemdblclick: me.onDashboardNotSignedVitalsGridClick
			},
			'#DashboardNotSignedVitalsSignBtn':{
				click: me.onDashboardNotSignedVitalsSignBtnClick
			}
		});

		me.addRef([
			{
				ref: 'PatientSummaryPanel',
				selector:'#PatientSummaryPanel'
			},
			{
				ref: 'PatientSummaryPanelVitalsPanel',
				selector:'#PatientSummaryPanel vitalspanel'
			},
			{
				ref: 'DashboardRenderPanel',
				selector:'#DashboardPanel'
			},
			{
				ref: 'DashboardNotSignedVitalsGrid',
				selector:'#DashboardNotSignedVitalsGrid'
			},
			{
				ref: 'DashboardNotSignedVitalsSignBtn',
				selector:'#DashboardNotSignedVitalsSignBtn'
			}
		]);
	},

	onDashboardNotSignedVitalsGridSelectionChange: function (grid, selection) {
		this.getDashboardNotSignedVitalsSignBtn().setDisabled(selection.length === 0);
	},

	onDashboardNotSignedVitalsGridClick: function(grid, record){

		var me = this;

		grid.el.mask(_('please_wait'));
		app.setPatient(record.data.pid, null, null, function(){
			app.openPatientSummary();

			grid.el.unmask();

			Ext.Function.defer(function () {
				me.getPatientSummaryPanel().tabPanel.setActiveTab(me.getPatientSummaryPanelVitalsPanel());
			},500);

		});
	},


	onDashboardPanelActivate: function(){
		this.getDashboardNotSignedVitalsGrid().getStore().load();
	},

	onDashboardPanelBeforeRender: function(){
		this.addLeftPanel(_('vitals_not_signed'), Ext.create('App.view.dashboard.panel.NotSignedVitals'));
	},

	onDashboardNotSignedVitalsSignBtnClick: function () {

		var me = this,
			vitals_store = this.getDashboardNotSignedVitalsGrid().getStore(),
			vitals_records = this.getDashboardNotSignedVitalsGrid().getSelectionModel().getSelection();

		if(vitals_records.length === 0) return;

		app.passwordVerificationWin(function(btn, password){

			if(btn === 'ok'){

				User.verifyUserPass(password, function(provider, response){
					if(response.result){
						vitals_records.forEach(function (vitals_record) {
							vitals_record.set({
								auth_uid: app.user.id
							});
						});

						vitals_store.sync({
							callback: function () {
								vitals_store.reload();
							}
						});
					}else{
						Ext.Msg.show({
							title: 'Oops!',
							msg: _('incorrect_password'),
							buttons: Ext.Msg.OKCANCEL,
							icon: Ext.Msg.ERROR,
							fn: function(btn){
								if(btn === 'ok'){
									me.onDashboardNotSignedVitalsSignBtnClick();
								}
							}
						});
					}
				});
			}
		});

	}

});