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

Ext.define('App.controller.patient.DoctorsNotes', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'DoctorsNotesGrid',
			selector: 'patientdoctorsnotepanel'
		},
		{
			ref: 'DoctorsNoteWindow',
			selector: '#DoctorsNoteWindow'
		},
		{
			ref: 'PatientNoteForm',
			selector: '#PatientNoteForm'
		},
		{
			ref: 'PrintDoctorsNoteBtn',
			selector: '#printDoctorsNoteBtn'
		},
		{
			ref: 'NewDoctorsNoteBtn',
			selector: '#newDoctorsNoteBtn'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientdoctorsnotepanel': {
				activate: me.onDoctorsNotesGridActive,
				selectionchange: me.onDoctorsNotesGridSelectionChange,
				beforerender: me.onDoctorsNotesGridBeforeRender,
				itemdblclick: me.onDoctorsNotesGridItemDblClick
			},
			'#printDoctorsNoteBtn': {
				click: me.onPrintDoctorsNoteBtn
			},
			'#newDoctorsNoteBtn': {
				click: me.onNewDoctorsNoteBtn
			},
			'#DoctorsNoteWindowCancelBtn': {
				click: me.onDoctorsNoteWindowCancelBtnClick
			},
			'#DoctorsNoteWindowSaveBtn': {
				click: me.onDoctorsNoteWindowSaveBtnClick
			}
		});

		me.docTemplates = {};

		/** get the document templates data of grid renderer **/
		CombosData.getTemplatesTypes(function(provider, response){
			for(var i = 0; i < response.result.length; i++){
				if(response.result[i].id) me.docTemplates[response.result[i].id] = response.result[i].title;
			}
		});

	},

	onDoctorsNotesGridBeforeRender: function(grid){
		app.on('patientunset', function(){
			grid.getStore().removeAll();
		});
	},

	onDoctorsNotesGridSelectionChange: function(sm, selected){
		this.getPrintDoctorsNoteBtn().setDisabled(selected.length === 0);
	},

	onNewDoctorsNoteBtn: function(btn){

		this.showDoctorsNoteWindow();

		var grid = btn.up('grid'),
			form = this.getPatientNoteForm().getForm();

		var records = grid.getStore().insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			refill: 0,
			order_date: new Date(),
			from_date: new Date()
		});

		form.loadRecord(records[0]);

	},

	onPrintDoctorsNoteBtn: function(note){
		var me = this,
			grid = me.getDoctorsNotesGrid(),
			record = (note.isModel ? note : grid.getSelectionModel().getSelection()[0]),
			params = {};

		params.pid = record.data.pid;
		params.eid = record.data.eid;
		params.docType = 'Doctors Note';
		params.templateId = record.data.template_id;
		params.docNoteid = record.data.id;

		DocumentHandler.createTempDocument(params, function(provider, response){
			if(window.dual){
				dual.onDocumentView(response.result.id, 'Doctors Note');
			}else{
				app.onDocumentView(response.result.id, 'Doctors Note');
			}
		});
	},

	onDoctorsNotesGridActive: function(grid){
		var store = grid.getStore();

		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	templatesRenderer: function(v){
		return this.docTemplates[v];
	},

	onDoctorsNotesGridItemDblClick: function (grid, record) {
		this.showDoctorsNoteWindow();

		var me = this,
			panel = me.getPatientNoteForm(),
			form = panel.getForm(),
			restrictionsFIeld = panel.query('multitextfield')[0],
			restrictions = record.get('restrictions');

		form.loadRecord(record);
		restrictionsFIeld.setValue(restrictions);

	},

	onDoctorsNoteWindowCancelBtnClick: function () {
		this.getDoctorsNotesGrid().getStore().rejectChanges();
		this.getPatientNoteForm().getForm().reset(true);
		this.getDoctorsNoteWindow().close();
	},

	onDoctorsNoteWindowSaveBtnClick: function () {

		var me = this,
			panel = me.getPatientNoteForm(),
			form = panel.getForm(),
			values = form.getValues(),
			record = form.getRecord(),
			store = record.store,
			restrictionsFIeld = panel.query('multitextfield')[0],
			restrictions = restrictionsFIeld.getValue();

		if(!form.isValid()) return;

		values.restrictions = restrictions;
		record.set(values);

		if(Ext.isEmpty(store.getModifiedRecords())){
			me.getPatientNoteForm().getForm().reset(true);
			me.getDoctorsNoteWindow().close();
		}else{
			store.sync({
				callback: function () {
					me.getPatientNoteForm().getForm().reset(true);
					me.getDoctorsNoteWindow().close();
				}
			});
		}
	},

	showDoctorsNoteWindow: function () {
		if(!this.getDoctorsNoteWindow()){
			Ext.create('App.view.patient.windows.DoctorsNoteWindow');
		}
		return this.getDoctorsNoteWindow().show();
	}

});