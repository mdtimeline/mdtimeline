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

Ext.define('App.controller.patient.HealthConcerns', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'HealthConcernGrid',
			selector: 'healthconcerngird'
		},
	],

	init: function(){
		var me = this;
		me.control({
			'viewport': {
				'beforeencounterload': me.onBeforeOpenEncounter
			},
			'#InterventionsGrid': {
				'itemdblclick': me.onInterventionsGridItemDblClick
			},
			'#HealthConcernGridAddBtn': {
				'click': me.onHealthConcernGridAddBtnClick
			}
		});
	},

	onBeforeOpenEncounter: function(encounter){
		if(this.getHealthConcernGrid()){
			this.getHealthConcernGrid().getStore().load({
				filters:[
					{
						property: 'eid',
						value: encounter.data.eid
					}
				]
			});
		}
	},

	onHealthConcernGridAddBtnClick: function(btn){
		var grid = btn.up('grid'),
			store = grid.getStore(),
			records;

		records = store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			create_uid: app.user.id,
			create_date: app.getDate(),
			update_uid: app.user.id,
			update_date: app.getDate()
		});

		grid.editingPlugin.cancelEdit();
		grid.editingPlugin.startEdit(records[0], 0);

	}

});
