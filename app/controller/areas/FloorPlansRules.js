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

Ext.define('App.controller.areas.FloorPlansRules', {
	extend: 'Ext.app.Controller',
	refs: [
		{
			ref: 'FloorPlansRulesWindow',
			selector: '#FloorPlansRulesWindow'
		},
		{
			ref: 'FloorPlansRulesGrid',
			selector: '#FloorPlansRulesGrid'
		},
		{
			ref: 'FloorPlansRulesZoneCombo',
			selector: '#FloorPlansRulesZoneCombo'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				appfacilitychanged: me.onAppFacilityChanged
			},
			'#PatientPoolAreasPanel': {
				activate: me.onFPatientPoolAreasPanelActivate
			},
			'#FloorPlansRulesWindow': {
				show: me.onFloorPlansRulesWindowShow
			},
			'#FloorPlansRulesCancelBtn': {
				click: me.onFloorPlansRulesCancelBtnClick
			},
			'#FloorPlansRulesSaveBtn': {
				click: me.onFloorPlansRulesSaveBtnClick
			},
			'#FloorPlansRulesGrid': {
				beforeedit: me.onFloorPlansRulesGridBeforeEdit,
				validateedit: me.onFloorPlansRulesGridEdit
			},
			'#PatientPoolAreasRulesBtn': {
				click: me.onPatientPoolAreasRulesBtnClick
			},
		});

		// Ext.Function.defer(function () {
		// 	me.showFloorPlansRulesWindow();
		// },1000, me);

	},

	onFPatientPoolAreasPanelActivate: function(panel){
		panel.reloadStores();
	},

	onPatientPoolAreasRulesBtnClick: function(){
		this.showFloorPlansRulesWindow();
	},

	onAppFacilityChanged: function(ctl, facility_id){
		this.doLoadRules();
	},

	onFloorPlansRulesGridBeforeEdit: function(editor, e){
		var field = this.getFloorPlansRulesZoneCombo();

		field.store.load({
			params: {
				floor_plan_id: e.record.get('floor_plan_id')
			}
		});
	},

	onFloorPlansRulesGridEdit: function(editor, e){
		var field = this.getFloorPlansRulesZoneCombo(),
			zone_record = field.findRecordByValue(field.getValue());

		e.record.set({zone: zone_record.get('title')});
	},

	onFloorPlansRulesWindowShow: function(btn){
		this.doLoadRules();
	},

	doLoadRules: function(){

		if(!this.getFloorPlansRulesGrid()) return;

		var store = this.getFloorPlansRulesGrid().getStore();

		store.load({
			params: {
				facility_id: app.user.facility
			}
		});
	},

	onFloorPlansRulesCancelBtnClick: function(btn){
		this.getFloorPlansRulesGrid().getStore().rejectChanges();
		btn.up('window').close();
	},

	onFloorPlansRulesSaveBtnClick: function(btn){
		this.getFloorPlansRulesGrid().getStore().sync({
			callback: function () {
				btn.up('window').close();
			}
		});
	},

	showFloorPlansRulesWindow: function () {
		if(!this.getFloorPlansRulesWindow()){
			Ext.create('App.view.areas.FloorPlansRulesWindow');
		}
		return this.getFloorPlansRulesWindow().show();
	}

});