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

Ext.define('App.controller.patient.ProgressNotesHistory', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref:'EncounterProgressNotesHistoryGrid',
			selector: '#EncounterProgressNotesHistoryGrid'
		},
		{
			ref:'ProgressNotesHistoryMineBtn',
			selector: '#ProgressNotesHistoryMineBtn'
		},
		{
			ref:'ProgressNotesHistorySpecialtyBtn',
			selector: '#ProgressNotesHistorySpecialtyBtn'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#EncounterProgressNotesHistoryGrid': {
				afterrender: me.onEncounterProgressNotesHistoryGridAfterRender
			},
			'#ProgressNotesHistoryMineBtn': {
				toggle: me.onProgressNotesHistoryMineBtnToggle
			},
			'#ProgressNotesHistorySpecialtyBtn': {
				toggle: me.onProgressNotesHistorySpecialtyBtnToggle
			}
		});

		me.encOunterCtl = this.getController('patient.encounter.Encounter');

	},

	onProgressNotesHistoryMineBtnToggle: function(btn, pressed){
		this.doEncounterProgressNotesHistoryGridFilter();
	},

	onProgressNotesHistorySpecialtyBtnToggle: function(btn, pressed){
		this.doEncounterProgressNotesHistoryGridFilter();
	},

	doEncounterProgressNotesHistoryGridFilter: function(){

		var me = this,
			store = me.getEncounterProgressNotesHistoryGrid().getStore(),
			mine_btn = me.getProgressNotesHistoryMineBtn(),
			specialty_btn = me.getProgressNotesHistorySpecialtyBtn(),
			encounter_record = me.encOunterCtl.getEncounterRecord(),
			filters = [], provider_uid, specialty_id;

		if(encounter_record === null) return;

		provider_uid = encounter_record.get('provider_uid');
		specialty_id = encounter_record.get('specialty_id');


		if(mine_btn.pressed && provider_uid != null){
			filters.push({
				property: 'provider_uid',
				value: provider_uid
			});
		}

		if(specialty_btn.pressed && specialty_id != null){
			filters.push({
				property: 'specialty_id',
				value: specialty_id
			});
		}

		if(filters.length === 0){
			store.clearFilter();
		}else{
			store.clearFilter(true);
			store.filter(filters);
		}
	},

	onEncounterProgressNotesHistoryGridAfterRender: function(grid){
		//grid.getStore().load();
	},

	loadPatientProgressHistory: function(pid, eid){
		var store = this.getEncounterProgressNotesHistoryGrid().getStore();

		store.getProxy().extraParams = { pid:pid, eid:eid };
		if(this.getEncounterProgressNotesHistoryGrid().rendered){
			store.load();
		}
	}

});