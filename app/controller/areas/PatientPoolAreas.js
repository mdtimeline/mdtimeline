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

Ext.define('App.controller.areas.PatientPoolAreas', {
	extend: 'Ext.app.Controller',
	refs: [
		{
			ref: 'PatientPoolAreasPanel',
			selector: '#PatientPoolAreasPanel'
		},
		{
			ref: 'PatientPoolAreasRemovePatientMenu',
			selector: '#PatientPoolAreasRemovePatientMenu'
		},
		{
			ref: 'PatientToNextAreaWindow',
			selector: '#PatientToNextAreaWindow'
		},
		{
			ref: 'PatientPoolAreasPanel',
			selector: '#PatientPoolAreasPanel'
		},


		//navigation pool area
		{
			ref: 'NavigationPatientPoolAreaDatView',
			selector: '#NavigationPatientPoolAreaDatView'
		},
		{
			ref: 'NavigationPatientPoolAreaFloorPlanZonesCombo',
			selector: '#NavigationPatientPoolAreaFloorPlanZonesCombo'
		},
		{
			ref: 'PatientPoolAreasDisableAlertColorsBtn',
			selector: '#PatientPoolAreasDisableAlertColorsBtn'
		}


	],

	init: function(){
		var me = this;

		me.control({
			'#PatientPoolAreasPanel grid': {
				beforeitemcontextmenu: me.onPatientPoolAreasGridBeforeItemContextMenu,
				beforerender: me.onPatientPoolAreasGridBeforeRender
			},
			'#PatientPoolAreasRemovePatientMenu': {
				click: me.onPatientPoolAreasRemovePatientMenuClick
			},
			'#PatientToNextAreaWindow': {
				render: me.onPatientToNextAreaWindowBeforeRender
			},
			'#PatientToNextAreaWindow button[action=poolarea]': {
				click: me.onPatientToNextAreaWindowPoolAreaBtnClick
			},
			'#PatientToNextAreaWindowCancelBtn': {
				click: me.onPatientToNextAreaWindowCancelBtnClick
			},
			'#HeaderSendToPoolAreaBtn': {
				click: me.onHeaderSendToPoolAreaBtnClick
			},
			'#NavigationPatientPoolAreaFloorPlanZonesCombo': {
				beforerender: me.onNavigationPatientPoolAreaFloorPlanZonesComboBeforeRender
			},
			'#PatientPoolAreasDisableAlertColorsBtn': {
				toggle: me.onPatientPoolAreasDisableAlertColorsBtnToggle
			}
		});

		me.reloadAreaBuffer = Ext.Function.createBuffered(me.reloadArea, 50, me);
	},

	onPatientPoolAreasDisableAlertColorsBtnToggle: function (btn, pressed){
		var area_grids = this.getPatientPoolAreasPanel().query('grid');

		area_grids.forEach(function (grid){
			grid.showAlertColor = !pressed;
			grid.view.refresh();
		});
	},

	onPatientPoolAreasGridBeforeRender: function (grid){
		grid.showAlertColor = !this.getPatientPoolAreasDisableAlertColorsBtn().pressed;
	},

	getSelectedPoolAreaZones: function(){
		return this.getNavigationPatientPoolAreaFloorPlanZonesCombo().getValue();
	},

	onNavigationPatientPoolAreaFloorPlanZonesComboBeforeRender: function(cmb){
		// say('onNavigationPatientPoolAreaFloorPlanZonesComboBeforeRender');
		// say(cmb);
		// TODO: load only the zones allowed by user
		cmb.store.load();

	},

	getPoolAreaIdByConcept: function(concept){

		var container = this.getPatientPoolAreasPanel().getPageBody().down('container'),
			grid = container.child('grid[floorPlanConcept='+concept+']');

		return grid.floorPlanId || false;
	},

	onHeaderSendToPoolAreaBtnClick: function () {

		if(!app.patient.pid){
			app.msg(_('oops'),_('no_patient_selected'), true);
			return;
		}

		this.doSendPatientToNextArea(app.patient.pid, function () {
			app.openDashboard();
		});
	},

	reRenderPoolAreas: function () {
		this.getPatientPoolAreasPanel().reRenderPoolAreas();
	},

	doSendPatientToNextArea: function (pid, callback) {
		var win = this.showPatientToNextAreaWindow();
		win.pid = pid;
		win.callback = callback;
	},

	onPatientToNextAreaWindowPoolAreaBtnClick: function (btn) {
		var win = btn.up('window'),
			pid = win.pid,
			callback = win.callback,
			poolAreaId = btn.poolAreaId;

		//app.goToPoolAreas();
		win.close();
		this.getPatientPoolAreasPanel().doSendPatientToPoolArea(pid, poolAreaId, callback);
		app.patientPoolStore.reload();
	},

	onPatientToNextAreaWindowCancelBtnClick: function (btn) {
		btn.up('window').close();
	},

	showPatientToNextAreaWindow: function () {
		if(!this.getPatientToNextAreaWindow()){
			Ext.create('Ext.window.Window',{
				itemId: 'PatientToNextAreaWindow',
				title: _('send_patient_to_next_area'),
				layout: 'hbox',
				closeAction: 'hide',
				bodyPadding: '10 0 10 10',
				buttons: [
					{
						text: _('cancel'),
						itemId: 'PatientToNextAreaWindowCancelBtn'
					}
				]
			});
		}
		return this.getPatientToNextAreaWindow().show();
	},

	onPatientToNextAreaWindowBeforeRender: function (win) {
		var me = this,
			pools = me.getPatientPoolAreasPanel().getPoolAreas(),
			buttons = [];

		pools.forEach(function (pool) {
			buttons = Ext.Array.push(buttons, {
				xtype: 'button',
				text: '-> ' + pool.title,
				scale: 'medium',
				width: 100,
				action: 'poolarea',
				poolAreaId: pool.action,
				margin: '0 10 0 0'
			});
		});

		win.add(buttons);
	},

	onPatientPoolAreasRemovePatientMenuClick: function (item) {
		var me = this,
			pool_view = item.up('menu').pool_view,
			pool_view_store = pool_view.store;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('remove_patient_pool_area_msg'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn) {
				if(btn !== 'yes') return;

				var selection = pool_view.getSelectionModel().getSelection();

				selection.forEach(function (pool_record) {
					me.removePatientFromArea(pool_record, pool_view_store);
				});
			}
		});
	},

	reloadArea: function (store) {
		store.reload();
	},

	sendPatientToPoolArea: function(pid, area_id, appointment_id, callback){

		var me = this;

		PoolArea.sendPatientToPoolArea({
			pid: pid,
			sendTo: area_id,
			appointment_id: appointment_id
		}, function(response){

			app.fireEvent('sendpatienttoarea', me, pid, area_id, response);

			if(response.floor_plan_id == null){
				app.unsetPatient(null, true);
				app.nav['App_view_areas_PatientPoolAreas'].reloadStores();
				app.getPatientsInPoolArea();
				if(callback) callback(response);
				return;
			}

			app.getController('areas.FloorPlan').promptPatientZoneAssignment(response.record.pid, response.floor_plan_id, area_id, response.zone);

			if(callback) callback(response);


		});
	},


	removePatientFromArea: function (pool_record, pool_view_store) {
		var me = this,
			params = {
			area_id: pool_record.get('id')
		};

		PoolArea.removePatientArrivalLog(params, function (response) {
			if(response.success){
				me.reloadAreaBuffer(pool_view_store);
			}
		});
	},

	onPatientPoolAreasGridBeforeItemContextMenu: function (view, pool_record, item, index, e) {
		e.preventDefault();
		this.showPatientPoolAreasPanelGridMenu(view, e);
	},

	showPatientPoolAreasPanelGridMenu: function (pool_view, e) {
		var me = this;
		if(!me.grid_menu){
			me.grid_menu = Ext.widget('menu', {
				margin: '0 0 10 0',
				items: [
					{
						text: _('remove_patient'),
						itemId: 'PatientPoolAreasRemovePatientMenu',
						icon: 'resources/images/icons/delete.png'
					}
				]
			});
		}
		me.grid_menu.pool_view = pool_view;
		me.grid_menu.showAt(e.getXY());

		return me.grid_menu;
	}

});