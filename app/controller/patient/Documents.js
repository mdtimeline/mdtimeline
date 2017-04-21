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

Ext.define('App.controller.patient.Documents', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.view.patient.windows.UploadDocument'
	],
	refs: [
		{
			ref: 'PatientDocumentPanel',
			selector: 'patientdocumentspanel'
		},
		{
			ref: 'PatientDocumentGrid',
			selector: 'patientdocumentspanel #patientDocumentGrid'
		},
		{
			ref: 'PatientDocumentViewerFrame',
			selector: 'patientdocumentspanel #patientDocumentViewerFrame'
		},
		{
			ref: 'PatientDocumentUploadWindow',
			selector: '#patientDocumentUploadWindow'
		},
		{
			ref: 'PatientDocumentUploadScanBtn',
			selector: '#patientDocumentUploadWindow #scanBtn'
		},
		{
			ref: 'PatientDocumentUploadFileUploadField',
			selector: '#patientDocumentUploadWindow #fileUploadField'
		},
		{
			ref: 'DocumentHashCheckBtn',
			selector: '#documentHashCheckBtn'
		},
		{
			ref: 'DocumentHashCheckBtn',
			selector: '#documentHashCheckBtn'
		},
		{
			ref: 'AddDocumentBtn',
			selector: 'patientdocumentspanel #addDocumentBtn'
		},
		{
			ref: 'DocumentUploadBtn',
			selector: 'patientdocumentspanel #documentUploadBtn'
		},
		{
			ref: 'DocumentUploadBtn',
			selector: 'patientdocumentspanel #documentUploadBtn'
		},
		{
			ref: 'DocumentScanBtn',
			selector: 'patientdocumentspanel #documentScanBtn'
		},
		{
			ref: 'PatientDocumentErrorNoteWindow',
			selector: 'patientdocumenterrornotewindow'
		}
	],

	scannedDocument: null,

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				browserhelperopen: me.onBrowserHelperOpen,
				browserhelperclose: me.onBrowserHelperClose,
				documentedit: me.onDocumentEdit
			},
			'patientdocumentspanel': {
				activate: me.onPatientDocumentPanelActive,
				beforerender: me.onPatientDocumentBeforeRender
			},
			'patientdocumentspanel #patientDocumentGrid': {
				selectionchange: me.onPatientDocumentGridSelectionChange,
				afterrender: me.onPatientDocumentGridAfterRender,
				beforeitemcontextmenu: me.onPatientDocumentGridBeforeItemContextMenu
			},
			'patientdocumentspanel [toggleGroup=documentgridgroup]': {
				toggle: me.onDocumentGroupBtnToggle
			},
			'patientdocumentspanel #documentGroupBtn': {
				toggle: me.onDocumentGroupBtnToggle
			},
			'patientdocumentspanel #documentUploadBtn': {
				click: me.onDocumentUploadBtnClick
			},
			'patientdocumentspanel #documentScanBtn': {
				click: me.onDocumentScanBtnClick
			},
			'#patientDocumentUploadWindow': {
				show: me.onPatientDocumentUploadWindowShow
			},
			'#patientDocumentUploadWindow #uploadBtn': {
				click: me.onDocumentUploadSaveBtnClick
			},
			'#DocumentErrorNoteSaveBtn': {
				click: me.onDocumentErrorNoteSaveBtnClick
			}
		});

		me.nav = this.getController('Navigation');
	},

	onPatientDocumentGridBeforeItemContextMenu: function (grid, record, item, index, e, eOpts) {
		var me = this;

		e.preventDefault();
		Ext.Msg.show({
			title:'C-CDA',
			msg: 'Would you like to view the raw xml data?',
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if (btn === 'yes'){
					var frame = grid.up('panel').up('panel').query('#patientDocumentViewerFrame')[0];

					if(record){
						frame.setSrc('dataProvider/DocumentViewer.php?site=' + me.site + '&token=' + app.user.token + '&id=' + record.data.id + '&rawXml');
					}
				}
			}
		});
	},

	setDocumentInError: function(document_record){
		var me = this;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('document_entered_in_error_message'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if(btn == 'yes'){
					var win = me.showPatientDocumentErrorNoteWindow();
					win.down('form').getForm().loadRecord(document_record);
				}
			}
		});
	},

	onDocumentErrorNoteSaveBtnClick: function(){
		var me = this,
			win = me.getPatientDocumentErrorNoteWindow(),
			form = win.down('form').getForm(),
			values = form.getValues(),
			document_record = form.getRecord(),
			site = document_record.site ? document_record.site : null;

		if(form.isValid()){
			values.entered_in_error = true;
			values.site = site;
			document_record.set(values);
			document_record.save({
				success: function(){
					win.close();
					document_record.set({groupDate:''});
					document_record.commit();
				}
			});
		}
	},

	showPatientDocumentErrorNoteWindow: function(){
		if(!this.getPatientDocumentErrorNoteWindow()){
			Ext.create('App.view.patient.windows.DocumentErrorNote');
		}
		return this.getPatientDocumentErrorNoteWindow().show();
	},

	onPatientDocumentBeforeRender: function(panel){
		this.setViewerSite(app.user.site);
		panel.defereOnActiveReload = false;
	},

	onDocumentEdit: function(data){

		var store = this.getPatientDocumentGrid().getStore(),
			record = store.getById(data.save.id);

		if(record){
			var src = data.save.document.split(',');

			record.set({document: (src[1] || src[0])});
			record.save({
				success: function(){
					if(window.dual){
						dual.msg('sweet', _('record_saved'));
					}else{
						app.msg('sweet', _('record_saved'));
					}
				},
				failure: function(){
					if(window.dual){
						dual.msg('oops', _('record_error'), true);
					}else{
						app.msg('oops', _('record_error'), true);
					}
				}
			})
		}
	},

	onBrowserHelperOpen: function(){
		if(this.getDocumentScanBtn()){
			this.getDocumentScanBtn().show();
		}
	},

	onBrowserHelperClose: function(){
		if(this.getDocumentScanBtn()){
			this.getDocumentScanBtn().hide();
		}
	},

	onPatientDocumentUploadWindowShow: function(){
		this.scannedDocument = null;
		this.getPatientDocumentUploadFileUploadField().enable();
		this.getPatientDocumentUploadScanBtn().setVisible(this.getController('Scanner').conencted);
	},

	onPatientDocumentGridSelectionChange: function(sm, records){
		var frame = sm.view.panel.up('panel').query('#patientDocumentViewerFrame')[0];

		if(records.length > 0){
			frame.setSrc('dataProvider/DocumentViewer.php?site=' + this.site + '&token=' + app.user.token + '&id=' + records[0].data.id);
		}else{
			frame.setSrc('dataProvider/DocumentViewer.php?site=' + this.site + '&token=' + app.user.token);
		}
	},

	onPatientDocumentGridAfterRender: function (container) {
		if(eval(a('allow_document_drag_drop_upload'))) {
			this.initDocumentDnD(container);
		}
	},

	onPatientDocumentPanelActive: function(panel){
		var me = this,
			grid = panel.down('grid'),
			store = grid.getStore(),
			params = me.nav.getExtraParams();

		me.activePAnel = panel;

		if(params && params.document){
			store.on('load', me.doSelectDocument, me);
		}

		if(panel.defereOnActiveReload) return;

		me.doPatientDocumentReload(panel);
	},

	doPatientDocumentReload: function(panel, pid){
		var me = this,
			grid = panel.down('grid'),
			store = grid.getStore();

		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: pid || app.patient.pid
			}
		]);
	},

	doSelectDocument: function(store){
		var me = this,
			grid = me.activePAnel.down('grid'),
			params = me.nav.getExtraParams();

		var doc = store.getById(params.document);
		if(doc){
			grid.getSelectionModel().select(doc);

		}else{
			app.msg(_('oops'), _('unable_to_find_document'), true);
		}
		store.un('load', me.doSelectDocument, me);
	},

	onDocumentGroupBtnToggle: function(btn, pressed){
		var grid = btn.up('grid'),
			selector = '[dataIndex=' + btn.action + ']',
			header = grid.headerCt.down(selector);

		if(pressed){
			grid.getStore().group(btn.action);
			header.hide();
			btn.disable();
		}else{
			header.show();
			btn.enable();
		}
	},

	onDocumentScanBtnClick: function () {
		this.getController('Scanner').showScanWindow();
	},

	onDocumentUploadBtnClick: function(){
		this.setDocumentUploadWindow('click');
	},

	setDocumentUploadWindow: function(action){
		var record = this.getNewPatientDocumentRecord(),
			win = this.getUploadWindow(action);
		win.down('form').getForm().loadRecord(record);
		return win;
	},

	getNewPatientDocumentRecord: function(){
		return Ext.create('App.model.patient.PatientDocuments', {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			facility_id: app.user.facility,
			date: new Date()
		})
	},

	getGroupName: function(store, record){
		var group = store.groupers.items[0].property;

		if(group == 'docTypeCode'){
			return Ext.String.capitalize(record.get('docTypeCode') + ' - ' + record.get('docType'));
		}else if(group == 'groupDate'){
			return Ext.Date.format(record.get(group), g('date_display_format'));
		}else{
			return Ext.String.capitalize(record.get(group));
		}
	},

	onDocumentHashCheckBtnClick: function(grid, rowIndex){
		var rec = grid.getStore().getAt(rowIndex);

		DocumentHandler.checkDocHash(rec.data, function(provider, response){

			var message = Ext.String.htmlDecode(response.result.msg);

			Ext.Msg.show({
				title: _('document_hash'),
				msg: message,
				buttons: Ext.Msg.OK,
				icon: Ext.Msg.INFO
			});
		});
	},

	getUploadWindow: function(action){
		return Ext.create('App.view.patient.windows.UploadDocument', {
			action: action,
			itemId: 'patientDocumentUploadWindow'
		})
	},

	onDocumentUploadSaveBtnClick: function(){
		var me = this,
			win = me.getPatientDocumentUploadWindow(),
			form = win.down('form').getForm(),
			record = form.getRecord(),
			values = form.getValues(),
			reader = new FileReader(),
			uploadField = form.findField('document');

		if(!form.isValid()) return;

		record.set(values);

		if(win.action == 'click'){
			var uploadValue = uploadField.getValue();
			record.set({name: uploadValue});

			if(me.scannedDocument){
				record.set({document: me.scannedDocument});
				me.doNewDocumentRecordSave(record);
			}else{
				reader.onload = function(e){
					record.set({document: e.target.result});
					me.doNewDocumentRecordSave(record);
				};
				reader.readAsDataURL(uploadField.extractFileInput().files[0]);
			}
		}else{
			me.doNewDocumentRecordSave(record);
		}
	},

	onDocumentUploadScanBtnClick: function(){
		var me = this,
			scanCtrl = this.getController('Scanner');

		scanCtrl.initScan();
		app.on('scancompleted', this.onScanCompleted, me);
	},

	onScanCompleted: function(controller, document){
		var me = this,
			win = me.getPatientDocumentUploadWindow(),
			form = win.down('form').getForm(),
			uploadField = form.findField('document');

		uploadField.disable();

		me.scannedDocument = document;
		app.un('scancompleted', this.onScanCompleted, me);
	},

	doNewDocumentRecordSave: function(record){
		var me = this,
			store = me.getPatientDocumentGrid().getStore(),
			index = store.indexOf(record);

		record.set({facility_id: app.user.facility});

		if(index == -1){
			store.add(record);
		}

		store.sync({
			success: function(){
				app.msg(_('sweet'), _('document_added'));
				me.getPatientDocumentUploadWindow().close();
				me.getPatientDocumentGrid().getSelectionModel().select(record);

			},
			failure: function(){
				store.rejectChanges();
				if(window.dual){
					dual.msg(_('oops'), _('document_error'), true);
				}else{
					app.msg(_('oops'), _('document_error'), true);
				}
			}
		})
	},

	initDocumentDnD: function(grid){
		var me = this;

		me.dnding = false;

		grid.on({
			drop: {
				element: 'el',
				fn: me.documentDrop,
				scope: me
			},
			dragstart: {
				element: 'el',
				fn: me.documentAddDropZone
			},
			dragenter: {
				element: 'el',
				fn: me.documentAddDropZone
			},
			dragover: {
				element: 'el',
				fn: me.documentAddDropZone
			},
			dragleave: {
				element: 'el',
				fn: me.documentRemoveDropZone
			},
			dragexit: {
				element: 'el',
				fn: me.documentRemoveDropZone
			}
		});
	},

	documentDrop: function (e) {
		var me = this;
		e.stopEvent();

		var files = Ext.Array.from(e.browserEvent.dataTransfer.files);
		me.fileHandler(files[0]);

		Ext.get(e.target).removeCls('drag-over');

	},

	documentAddDropZone: function (e) {
		if (!e.browserEvent.dataTransfer || Ext.Array.from(e.browserEvent.dataTransfer.types).indexOf('Files') === -1) {
			return;
		}
		e.stopEvent();

		this.addCls('drag-over');
	},

	documentRemoveDropZone: function (e) {

		var el = e.getTarget(),
			thisEl = this.el;

		e.stopEvent();

		if (el === thisEl.dom) {
			this.removeCls('drag-over');
			return;
		}

		while (el !== thisEl.dom && el && el.parentNode) {
			el = el.parentNode;
		}

		if (el !== thisEl.dom) {
			this.removeCls('drag-over');
		}
	},

	fileHandler: function(file){
		var me = this,
			win = me.setDocumentUploadWindow('drop'),
			form = win.down('form').getForm(),
			record = form.getRecord(),
			reader = new FileReader(),
			uploadField = form.findField('document');

		uploadField.hide();
		uploadField.disable();

		reader.onload = function(e){
			record.set({
				document: e.target.result,
				name: file.name
			});
		};

		reader.readAsDataURL(file);
	},

	setViewerSite: function(site){
		this.site = site;
	}
});