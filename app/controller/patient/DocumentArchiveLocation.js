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

Ext.define('App.controller.patient.DocumentArchiveLocation', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'PatientDocumentArchiveLocation',
			selector: '#PatientDocumentArchiveLocation'
		},
		{
			ref: 'PatientDocumentArchiveLocationDocumentsGrid',
			selector: '#PatientDocumentArchiveLocationDocumentsGrid'
		},
		{
			ref: 'PatientDocumentArchiveLocationDocumentsLocationGrid',
			selector: '#PatientDocumentArchiveLocationDocumentsLocationGrid'
		},
		{
			ref: 'DocumentLocationWindow',
			selector: '#DocumentLocationWindow'
		},
		{
			ref: 'DocumentLocationWindowForm',
			selector: '#DocumentLocationWindowForm'
		},
		{
			ref: 'PatientDocumentArchiveLocationDocumentsLocationSearch',
			selector: '#PatientDocumentArchiveLocationDocumentsLocationSearch'
		},
		{
			ref: 'PatientDocumentArchiveLocationDocumentArchivedBtn',
			selector: '#PatientDocumentArchiveLocationDocumentArchivedBtn'
		},

		{
			ref: 'PatientDocumentArchiveLocationFromDate',
			selector: '#PatientDocumentArchiveLocationFromDate'
		},
		{
			ref: 'PatientDocumentArchiveLocationToDate',
			selector: '#PatientDocumentArchiveLocationToDate'
		},
		{
			ref: 'PatientDocumentArchiveLocationPatientSearch',
			selector: '#PatientDocumentArchiveLocationPatientSearch'
		},
		{
			ref: 'PatientDocumentArchiveLocationDocumentsLocationNewEditBtn',
			selector: '#PatientDocumentArchiveLocationDocumentsLocationNewEditBtn'
		},
		{
			ref: 'PatientDocumentArchiveLocationScannedBySearch',
			selector: '#PatientDocumentArchiveLocationScannedBySearch'
		},
		{
			ref: 'PatientDocumentLocationDepartmentSearch',
			selector: '#PatientDocumentLocationDepartmentSearch'
		},
		{
			ref: 'PatientDocumentArchiveLocationFacilitySearch',
			selector: '#PatientDocumentArchiveLocationFacilitySearch'
		},
		{
			ref: 'PatientDocumentArchiveLocationDocumentPreview',
			selector: '#PatientDocumentArchiveLocationDocumentPreview'
		},
		{
			ref: 'PatientDocumentArchiveLocationDocumentPreviewFrame',
			selector: '#PatientDocumentArchiveLocationDocumentPreviewFrame'
		}
	],

	init: function(){
		var me = this;
		me.control({

			'viewport': {
				beforerender: me.onAppBeforeRender
			},
			'#PatientDocumentArchiveLocationDocumentsGrid': {
				beforerender: me.onPatientDocumentLocationDocumentsGridBeforeRender,
				itemdblclick: me.onPatientDocumentLocationDocumentsGridItemDblClick
			},
			'#PatientDocumentArchiveLocationDocumentsGrid gridview': {
				drop: me.onPatientDocumentLocationDocumentsGridDrop
			},
			'#PatientDocumentArchiveLocationDocumentsLocationGrid': {
				beforerender: me.onPatientDocumentLocationDocumentsLocationGridBeforeRender,
				itemdblclick: me.onPatientDocumentLocationDocumentsLocationGridItemDblClick
			},
			'#PatientDocumentArchiveLocationDocumentsLocationGrid gridview': {
				drop: me.onPatientDocumentLocationDocumentsLocationGridDrop,
				beforedrop: me.onPatientDocumentLocationDocumentsLocationGridBeforeDrop
			},
			'#PatientDocumentArchiveLocationDocumentArchivedBtn': {
				toggle: me.onPatientDocumentLocationDocumentArchivedBtnToggle
			},
			'#PatientDocumentArchiveLocationFromDate': {
				change: me.onPatientDocumentLocationFromDateChange
			},
			'#PatientDocumentArchiveLocationToDate': {
				change: me.onPatientDocumentLocationToDateChange
			},
			'#PatientDocumentArchiveLocationPatientSearch': {
				change: me.onPatientDocumentLocationPatientSearchChange
			},
			'#PatientDocumentArchiveLocationScannedBySearch': {
				change: me.onPatientDocumentLocationScannedBySearchChange
			},
			'#PatientDocumentLocationDepartmentSearch': {
				change: me.onPatientDocumentLocationDepartmentSearchChange
			},
			'#PatientDocumentArchiveLocationFacilitySearch': {
				change: me.onPatientDocumentLocationFacilitySearchChange
			},
			'#PatientDocumentArchiveLocationDocumentsLocationSearch': {
				select: me.onPatientDocumentLocationDocumentsLocationSearchSelect
			},
			'#PatientDocumentArchiveLocationDocumentsLocationNewEditBtn': {
				click: me.onPatientDocumentLocationDocumentsLocationNewEditBtnClick
			},
			'#DocumentLocationWindowCancelBtn': {
				click: me.onDocumentLocationWindowCancelBtnClick
			},
			'#DocumentLocationWindowSaveBtn': {
				click: me.onDocumentLocationWindowSaveBtnClick
			},
			'#PatientDocumentArchiveLocationDocumentPreview': {
				collapse: me.onPatientDocumentLocationDocumentPreviewCollapse
			}
		});


		me.doDocumentsSearchBuffer = Ext.Function.createBuffered(me.doDocumentsSearch, 500, me);

		me.docExtraParams = {};
		me.locExtraParams = {};
	},

	onPatientDocumentLocationDocumentsGridDrop: function(node, data, overModel, dropPosition, eOpts){

		data.records.forEach(function (record) {

			DocumentLocation.destroyPatientDocumentLocation(record.data, function () {

				record.set({
					id: 0,
					location_id: 0,
					archive_reference_number: '',
					archive_description: '',
					archived_by_fname: '',
					archived_by_mname: '',
					archived_by_lname: '',
					create_uid: 0,
					create_date: null,
					update_uid: 0,
					update_date: null
				});

				record.commit();

			});

		});


	},

	onPatientDocumentLocationDocumentsLocationGridDrop: function(node, data, overModel, dropPosition, eOpts){

		var records = data.records,
			archive_field = this.getPatientDocumentArchiveLocationDocumentsLocationSearch(),
			archive_record = archive_field.findRecordByValue(archive_field.getValue());

		for (var i = 0; i < records.length; i++) {

			records[i].set({
				location_id: archive_record.get('id'),
				archive_reference_number: archive_record.get('reference_number'),
				archive_description: archive_record.get('description'),
				archived_by_fname: app.user.fname,
				archived_by_mname: app.user.mname,
				archived_by_lname: app.user.lname,
				create_uid: app.user.id,
				create_date: app.getDate(),
				update_uid: app.user.id,
				update_date: app.getDate()
			});

			records[i].save({
				callback: function (r) {
					r.commit();
				}
			});
		}
	},

	onPatientDocumentLocationDocumentsLocationGridBeforeDrop: function(node, data, overModel, dropPosition, eOpts){

		var cmb = this.getPatientDocumentArchiveLocationDocumentsLocationSearch(),
			value = cmb.getValue(),
			record = cmb.findRecordByValue(cmb.getValue());


		if(!value || !record){
			return false;
		}

	},

	onAppBeforeRender: function(){

		if(!a('access_documents_archive_panel'))  return;

		app.HeaderLeft.add({
			xtype: 'button',
			scale: 'large',
			margin: '0 3 0 0',
			cls: 'headerLargeBtn',
			padding: 0,
			//acl: !a('worklist_allow_birads_panel'),
			icon: 'resources/images/icons/archive.png',
			handler: function () {
				app.nav.navigateTo('App.view.patient.DocumentArchiveLocation');
			},
			tooltip: _('document_archive')
		});

	},

	onPatientDocumentLocationDocumentsGridItemDblClick: function(grid, record){
		this.doDocumentPreview(record);
	},


	onPatientDocumentLocationDocumentsLocationGridItemDblClick: function(grid, record){
		this.doDocumentPreview(record);
	},

	doDocumentPreview: function(record){
		var me =this,
			panel = me.getPatientDocumentArchiveLocationDocumentPreview(),
			frame = me.getPatientDocumentArchiveLocationDocumentPreviewFrame(),
			src = Ext.String.format('dataProvider/DocumentViewer.php?site={0}&id={1}&token={2}',
				app.user.site,
				record.get('document_id'),
				app.user.token
			);

		panel.expand();
		frame.setSrc(src);
	},

	onPatientDocumentLocationDocumentPreviewCollapse: function(){
		this.getPatientDocumentArchiveLocationDocumentPreviewFrame().setSrc('about:blank');
	},

	onPatientDocumentLocationDocumentsGridBeforeRender: function(grid){
		var me = this;

		grid.store.on('beforeload', function (store) {
			store.getProxy().extraParams = me.docExtraParams;
		});
	},

	onPatientDocumentLocationDocumentsLocationGridBeforeRender: function(grid){
		var me = this;

		grid.store.on('beforeload', function (store) {
			store.getProxy().extraParams = me.locExtraParams;
		});
	},

	doDocumentsSearch: function(){

		var me = this,
			fromField = me.getPatientDocumentArchiveLocationFromDate(),
			toField = me.getPatientDocumentArchiveLocationToDate(),
			patientField = me.getPatientDocumentArchiveLocationPatientSearch(),
			userField = me.getPatientDocumentArchiveLocationScannedBySearch(),
			facilityField = me.getPatientDocumentArchiveLocationFacilitySearch(),
			departmentField = me.getPatientDocumentLocationDepartmentSearch(),
			archiveBtn = me.getPatientDocumentArchiveLocationDocumentArchivedBtn(),
			store = me.getPatientDocumentArchiveLocationDocumentsGrid().getStore(),
			params = {};


		if(!fromField.isValid() || !toField.isValid()) return;

		params.date_from =  Ext.Date.format(fromField.getValue(), 'Y-m-d 00:00:00');
		params.date_to =  Ext.Date.format(toField.getValue(), 'Y-m-d 23:59:59');
		params.archived = archiveBtn.pressed;
		params.is_worklist = true;

		if(patientField.getValue()){
			params.pid =  patientField.getValue();
		}

		if(userField.getValue()){
			params.uid =  userField.getValue();
		}

		if(facilityField.getValue()){
			params.facility_id =  facilityField.getValue();
		}

		me.docExtraParams = params;

		store.loadPage(1);

	},

	showDocumentLocationWindow: function(){
		if(!this.getDocumentLocationWindow()){
			Ext.create('App.view.administration.DocumentLocationWindow');
		}

		return this.getDocumentLocationWindow().show();
	},

	onPatientDocumentLocationDocumentArchivedBtnToggle: function(){
		this.doDocumentsSearchBuffer();
	},

	onPatientDocumentLocationFromDateChange: function(field, value){
		this.getPatientDocumentArchiveLocationToDate().setMinValue(value);
		this.doDocumentsSearchBuffer();
	},

	onPatientDocumentLocationToDateChange: function(field, value){
		this.getPatientDocumentArchiveLocationFromDate().setMaxValue(value);
		this.doDocumentsSearchBuffer();
	},

	onPatientDocumentLocationPatientSearchChange: function(){
		this.doDocumentsSearchBuffer();
	},

	onPatientDocumentLocationScannedBySearchChange: function(){
		this.doDocumentsSearchBuffer();
	},

	onPatientDocumentLocationDepartmentSearchChange: function(){
		this.doDocumentsSearchBuffer();
	},

	onPatientDocumentLocationFacilitySearchChange: function(){
		this.doDocumentsSearchBuffer();
	},

	onPatientDocumentLocationDocumentsLocationSearchSelect: function(cmb, selection){

		var grid = this.getPatientDocumentArchiveLocationDocumentsLocationGrid(),
			store = grid.getStore();

		if(selection.length === 0){
			store.removeAll();
			store.commitChanges();
		}else{
			this.locExtraParams = {
				is_worklist: false,
				location_id: selection[0].get('id')
			};
			store.loadPage(1);
		}
	},

	onDocumentLocationWindowCancelBtnClick: function(){
		this.getDocumentLocationWindow().close();
	},

	onDocumentLocationWindowSaveBtnClick: function(){

		var me = this,
			cmb = me.getPatientDocumentArchiveLocationDocumentsLocationSearch(),
			form = me.getDocumentLocationWindowForm().getForm(),
			values = form.getValues(),
			record = form.getRecord();

		if(!form.isValid()) return;

		record.set(values);

		record.save({
			callback: function () {
				me.getDocumentLocationWindow().close();
				cmb.store.add(record);
				cmb.setValue(record.get('id'));
				me.onPatientDocumentLocationDocumentsLocationSearchSelect(cmb, [record])
			}
		});
	},

	onPatientDocumentLocationDocumentsLocationNewEditBtnClick: function(){

		var cmb = this.getPatientDocumentArchiveLocationDocumentsLocationSearch(),
			value = cmb.getValue(),
			record = cmb.findRecordByValue(value);


		if(!record){
			record = Ext.create('App.model.patient.DocumentArchiveLocation',{
				reference_number: value
			});
		}

		this.showDocumentLocationWindow();

		this.getDocumentLocationWindowForm().getForm().loadRecord(record);

	},


});