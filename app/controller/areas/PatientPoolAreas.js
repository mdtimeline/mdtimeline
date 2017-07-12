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
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#PatientPoolAreasPanel grid': {
				beforeitemcontextmenu: me.onPatientPoolAreasGridBeforeItemContextMenu
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
			}
		});
	},

	doSendPatientToNextArea: function (pid) {
		var win = this.showPatientToNextAreaWindow();
		win.pid = pid;
	},

	onPatientToNextAreaWindowPoolAreaBtnClick: function (btn) {
		var win = btn.up('window'),
			pid = win.pid,
			poolAreaId = btn.poolAreaId;

		app.goToPoolAreas();
		this.getPatientPoolAreasPanel().doSendPatientToPoolArea(pid, poolAreaId);
		win.close();
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
			pool_record = item.up('menu').pool_record;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('remove_patient_pool_area_msg'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn) {
				if(btn !== 'yes') return;
				me.removePatientFromArea(pool_record);
			}
		});
	},

	removePatientFromArea: function (pool_record) {
		var params = {
			area_id: pool_record.get('id')
		};

		PoolArea.removePatientArrivalLog(params, function (response) {
			if(response.success){
				pool_record.store.reload();
			}
		});
	},

	onPatientPoolAreasGridBeforeItemContextMenu: function (view, pool_record, item, index, e) {
		e.preventDefault();
		this.showPatientPoolAreasPanelGridMenu(pool_record, e);
	},

	showPatientPoolAreasPanelGridMenu: function (pool_record, e) {
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
		me.grid_menu.pool_record = pool_record;
		me.grid_menu.showAt(e.getXY());

		return me.grid_menu;
	}

});