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

Ext.define('App.controller.patient.EncounterGrid', {
	extend: 'Ext.app.Controller',
	refs: [
		{
			ref: 'EncountersGrid',
			selector: '#EncountersGrid'
		},
		{
			ref: 'EncountersGridNewEncounterBtn',
			selector: '#EncountersGridNewEncounterBtn'
		},
		{
			ref: 'EncountersGridNewProgressReportsBtn',
			selector: '#EncountersGridNewProgressReportsBtn'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#EncountersGrid': {
				activate: me.onEncountersGridActivate,
				itemdblclick: me.onEncountersGridItemDblClick,
				selectionchange: me.onEncountersGridSelectionChange
			},
			'#EncountersGridNewEncounterBtn': {
				click: me.onEncountersGridNewEncounterBtnClick
			},
			'#EncountersGridNewProgressReportsBtn': {
				click: me.onEncountersGridNewProgressReportsBtnClick
			}
		});
	},

	onEncountersGridActivate: function (grid){
		grid.store.clearFilter(true);
		grid.store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	onEncountersGridNewProgressReportsBtnClick: function (btn) {
		var record = btn.up('grid').getSelectionModel().getLastSelected();
		this.showProgressByEncounterRecord(record);
	},

	showProgressByEncounterRecord: function (record){
		this.showProgressByPidAndEidAndProviderId(record.get('pid'), record.get('eid'), record.get('provider_uid'));
	},

	showProgressByPidAndEidAndProviderId(pid, eid, provider_id){
		var params = {
			pid: pid,
			eid: eid,
			provider_uid: provider_id,
			docType: 'EncProgress',
			templateId: '11'
		};

		DocumentHandler.createTempDocument(params, function(provider, response){
			say(response);
			app.onDocumentView(response.result.id, 'EncProgress');
		});
	},

	onEncountersGridSelectionChange: function (sm, selection) {
		sm.view.panel.down('#EncountersGridNewProgressReportsBtn').setDisabled(selection.length === 0);
	},

	onEncountersGridItemDblClick: function(view, record){
		app.openEncounter(record.data.eid);
	},

	onEncountersGridNewEncounterBtnClick: function () {
		app.createNewEncounter();
	}
});
