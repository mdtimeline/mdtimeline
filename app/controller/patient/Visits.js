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

Ext.define('App.controller.patient.Visits', {
	extend: 'Ext.app.Controller',
	refs: [
		{
			ref: 'PatientVisitsPanel',
			selector: '#PatientVisitsPanel'
		},
		{
			ref: 'PatientVisitsGrid',
			selector: '#PatientVisitsGrid'
		},
		{
			ref: 'PatientVisitsNewEncounterBtn',
			selector: '#PatientVisitsNewEncounterBtn'
		},
		{
			ref: 'PatientVisitsNewProgressReportsBtn',
			selector: '#PatientVisitsNewProgressReportsBtn'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#PatientVisitsPanel': {
				activate: me.onPatientVisitsPaneActivate
			},
			'#PatientVisitsGrid': {
				itemdblclick: me.onPatientVisitsGridItemDblClick,
				selectionchange: me.onPatientVisitsGridSelectionChange
			},
			'#PatientVisitsNewEncounterBtn': {
				click: me.onPatientVisitsNewEncounterBtnClick
			},
			'#PatientVisitsNewProgressReportsBtn': {
				click: me.onPatientVisitsNewProgressReportsBtnClick
			}
		});
	},

	onPatientVisitsNewProgressReportsBtnClick: function () {
		var record = this.getPatientVisitsGrid().getSelectionModel().getLastSelected(),
			params = {
				pid: record.get('pid'),
				eid: record.get('eid'),
				provider_uid: record.get('provider_uid'),
				docType: 'EncProgress',
				templateId: '11'
			};

		say(record);

		DocumentHandler.createTempDocument(params, function(provider, response){

			say(response);

			app.onDocumentView(response.result.id, 'EncProgress');
		});

	},

	onPatientVisitsGridSelectionChange: function (sm, selection) {



		this.getPatientVisitsNewProgressReportsBtn().setDisabled(selection.length === 0);
	},

	onPatientVisitsPaneActivate: function (panel) {
		panel.updateTitle(app.patient.name + ' (' + _('encounters') + ')');
		panel.store.clearFilter(true);
		panel.store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	onPatientVisitsGridItemDblClick: function(view, record){
		app.openEncounter(record.data.eid);
	},

	onPatientVisitsNewEncounterBtnClick: function () {
		app.createNewEncounter();
	}
});
