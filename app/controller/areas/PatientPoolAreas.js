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
			}
		});
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