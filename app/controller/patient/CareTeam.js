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

Ext.define('App.controller.patient.CareTeam', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'PatientSummaryCareTeamGrid',
			selector: '#PatientSummaryCareTeamGrid'
		},
		{
			ref: 'CareTeamGridReferringPhysicianSearch',
			selector: '#CareTeamGridReferringPhysicianSearch'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'viewport': {
				patientsummaryload: me.onPatientSummaryLoad
			},
			'#CareTeamGridReferringPhysicianSearch': {
				select: me.onCareTeamGridReferringPhysicianSearchSelect
			},
			'#CareTeamGridIdPrimaryCheckColumn': {
				beforecheckchange: me.onCareTeamGridIdPrimaryCheckColumnBeforeCheckChange,
				checkchange: me.onCareTeamGridIdPrimaryCheckColumnCheckChange
			}
		});
	},

	onCareTeamGridIdPrimaryCheckColumnCheckChange: function(col){
		col.up('grid').getStore().sort('is_primary', 'DESC');
	},

	onCareTeamGridIdPrimaryCheckColumnBeforeCheckChange: function(col, row_index, checked){

		if(checked){
			var store = col.up('grid').getStore(),
				records = store.data.items;

			for (var i = 0; i < records.length; i++) {
				if(i === row_index) continue;
				records[i].set({
					is_primary: false
				});
			}
		}
	},

	onCareTeamGridReferringPhysicianSearchSelect: function(field, selection){

		var store = field.up('grid').getStore();

		store.add({
			pid: app.patient.pid,
			npi: selection[0].get('npi'),
			fname: selection[0].get('fname'),
			mname: selection[0].get('mname'),
			lname: selection[0].get('lname'),
			create_uid: app.user.id,
			create_date: app.getDate(),
		});

		field.reset();
	},

	onPatientSummaryLoad: function(ctl, patient){

		say('onPatientSummaryLoad');

		var me = this;

		if(!me.getPatientSummaryCareTeamGrid()){
			return;
		}

		Ext.Function.defer(function () {
			me.getPatientSummaryCareTeamGrid().getStore().load({
				filter: [
					{
						property: 'pid',
						value: patient.pid
					}
				]
			});
		}, 250);

	},



});
