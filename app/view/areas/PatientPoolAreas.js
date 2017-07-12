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

Ext.define('App.view.areas.PatientPoolAreas', {
	extend: 'App.ux.RenderPanel',

	requires:[
		'App.ux.grid.AreasDragDrop'
	],

	itemId: 'PatientPoolAreasPanel',

	pageTitle: _('patient_pool_areas'),

	initComponent: function(){
		var me = this;

		me.pageBody = Ext.create('Ext.container.Container', {
			defaults: {
				flex: 1,
				margin: 5,
				frame: false
			},
			layout: {
				type: 'hbox',
				align: 'stretch'
			}
		});

		me.listeners = {
			beforerender: me.setPoolAreas
		};

		me.callParent(arguments);
	},

	onPatientDrop: function(node, data, overModel, dropPosition, eOpts){

		var me = this,
			name = (data.records[0].data) ? data.records[0].data.name : data.records[0].name,
			pid = (data.records[0].data) ? data.records[0].data.pid : data.records[0].pid;

		app.msg(_('sweet'), name + ' ' + _('sent_to') + ' ' + me.panel.title);

		app.nav.activePanel.doSendPatientToPoolArea(pid, me.panel.action)
	},

	doSendPatientToPoolArea: function (pid, area_id) {
		PoolArea.sendPatientToPoolArea({ pid: pid, sendTo: area_id }, function(result){

			if(result.floor_plan_id == null){
				app.unsetPatient(null, true);
				app.nav['App_view_areas_PatientPoolAreas'].reloadStores();
				app.getPatientsInPoolArea();
				return;
			}

			app.getController('areas.FloorPlan').promptPatientZoneAssignment(result.record.pid, result.floor_plan_id);

		});
	},

	reRenderPoolAreas:function(){
		var me = this,
			panel = me.getPageBody().down('container');

		panel.removeAll();
		me.setPoolAreas();
	},

	getPoolAreas: function () {
		return this.getPageBody().down('container').items.items;
	},

	setPoolAreas: function(){
		var me = this,
			panel = me.getPageBody().down('container'),
			areas;

		me.stores = [];

		PoolArea.getFacilityActivePoolAreas(function(provider, response){

			areas = response.result;

			for(var i = 0; i < areas.length; i++){

				var store = Ext.create('App.store.areas.PoolDropAreas', {
					proxy: {
						type: 'direct',
						api: {
							read: 'PoolArea.getPoolAreaPatients'
						},
						extraParams: {
							area_id: areas[i].id
						}
					}
				});

				me.stores.push(store);

				panel.add({
					xtype: 'grid',
					title: areas[i].title,
					action: areas[i].id,
					store: store,
					floorPlanId: areas[i].floor_plan_id,
					columns: [
						Ext.create('Ext.grid.RowNumberer'),
						{
							header: _('record') + ' #',
							width: 100,
							dataIndex: 'pid'
						},
						{
							header: _('patient_name'),
							flex: 1,
							dataIndex: 'name'
						},
						{
							xtype: 'datecolumn',
							header: _('time_in'),
							width: 100,
							dataIndex: 'time_in',
							format: g('time_display_format')
						}
					],
					viewConfig: {
						loadMask: false,
						plugins: {
							ptype: 'areasgridviewdragdrop',
							dragGroup: 'patientPoolAreas',
							dropGroup: 'patientPoolAreas'
						},
						listeners: {
							//scope:me,
							drop: me.onPatientDrop
						}
					},
					listeners: {
						scope: me,
						itemdblclick: me.onPatientDblClick
					}
				})
			}

			me.reloadStores();
		});
	},

	onPatientDblClick: function(store, record){
		var data = record.data;
		// TODO: set priority
		app.setPatient(data.pid, data.name, null, function(){
			app.openPatientSummary();
		});
	},

	reloadStores: function(){
		if(this.stores){
			for(var i = 0; i < this.stores.length; i++){
				this.stores[i].load();
			}
		}
	},

	onActive: function(callback){
//		this.reloadStores();
		if(typeof callback == 'function') callback(true);
	}
});
