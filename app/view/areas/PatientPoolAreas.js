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
				margin: 2,
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

		me.pageBBar = [
			'->',
			{
				xtype: 'button',
				icon: 'resources/images/icons/gear.png',
				itemId: 'PatientPoolAreasRulesBtn'
			}
		];


		me.patientPoolAreaCtl = app.getController('areas.PatientPoolAreas');

		me.callParent(arguments);

	},

	onPatientDrop: function(node, data, overModel, dropPosition, eOpts){

		var me = this,
			name = (data.records[0].data) ? data.records[0].data.name : data.records[0].name,
			pid = (data.records[0].data) ? data.records[0].data.pid : data.records[0].pid;

		app.msg(_('sweet'), name + ' ' + _('sent_to') + ' ' + me.panel.title);

		app.nav.activePanel.doSendPatientToPoolArea(pid, me.panel.action, undefined, function (response) {

			// say('onPatientDrop');
			// say(data);
			// say(response);
			// say(response.zone.data.zone);

			if(response.zone){
				data.records[0].set({zone: response.zone.data.zone, time_in: app.getDate()});
			}else{
				data.records[0].set({time_in: app.getDate()});
			}
			data.records[0].commit();

		});
	},

	doSendPatientToPoolArea: function (pid, area_id, appointment_id, callback) {

		var me = this;

		app.fireEvent('beforesendpatienttoarea', me, pid, area_id, appointment_id);

		PoolArea.sendPatientToPoolArea({
			pid: pid,
			sendTo: area_id,
			appointment_id: appointment_id
		}, function(response){

			app.fireEvent('sendpatienttoarea', me, pid, area_id, appointment_id);

			if(response.floor_plan_id == null || response.zone){
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

	doSendPatientToPoolAreaByConcept: function (pid, area_concept, appointment_id, callback) {

		var me = this,
			area_id = me.patientPoolAreaCtl.getPoolAreaIdByConcept(area_concept);

		me.doSendPatientToPoolArea(pid, area_id, appointment_id, callback)
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
					groupField: (areas[i].floor_plan_id !== null ? 'zone' : undefined),
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
					multiSelect: true,
					title: areas[i].title,
					action: areas[i].id,
					store: store,
					floorPlanId: areas[i].floor_plan_id,
					floorPlanConcept: areas[i].concept,
					features: (areas[i].floor_plan_id !== null ? [{ftype:'grouping'}] : []),
					collapsible: true,
					collapseDirection: 'left',
					animCollapse: false,
					columns: [
						Ext.create('Ext.grid.RowNumberer'),
						{
							header: _('record') + ' #',
							width: 100,
							dataIndex: 'pubpid',
							hidden: true,
							renderer: function (v,m,r) {
								m.style = 'background-color:' + r.get('alert_color');
								return v;
							}
						},
						{
							header: _('patient_name'),
							flex: 1,
							dataIndex: 'lname',
							renderer: function (v,m,r) {
								m.style = 'background-color:' + r.get('alert_color');
								return Ext.String.format(
									'{0}{1}, {2} {3}',
									(r.get('eid') ? '*':''),
									r.get('lname'),
									r.get('fname'),
									r.get('mname')
								);
							}
						},
						{
							header: _('time_in'),
							width: 65,
							dataIndex: 'time_in',
							renderer: function (v,m,r) {
								m.style = 'background-color:' + r.get('alert_color');
								return Ext.Date.format(v,g('time_display_format'));
							}
						},
						{
							header: _('schedule'),
							width: 65,
							dataIndex: 'appointment_time',
							renderer: function (v,m,r) {
								m.style = 'background-color:' + r.get('alert_color');
								return Ext.Date.format(v,g('time_display_format'));
							}
						},
						{
							header: _('timer'),
							width: 65,
							dataIndex: 'timer',
							renderer: function (v,m,r) {
								m.style = 'background-color:' + r.get('alert_color');
								return v;
							}
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
