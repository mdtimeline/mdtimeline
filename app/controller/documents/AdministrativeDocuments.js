/**
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

Ext.define('App.controller.documents.AdministrativeDocuments', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.view.documents.windows.UploadAdministrativeDocument'
	],
	refs: [
		{
			ref: 'AdministrativeDocumentPanel',
			selector: 'administrativedocumentspanel'
		},
		{
			ref: 'AdministrativeDocumentGrid',
			selector: 'administrativedocumentspanel #AdministrativeDocumentGrid'
		},
		{
			ref: 'AdministrativeDocumentViewerFrame',
			selector: 'administrativedocumentspanel #AdministrativeDocumentViewerFrame'
		},
		{
			ref: 'AdministrativeDocumentUploadWindow',
			selector: '#AdministrativeDocumentUploadWindow'
		},
		{
			ref: 'AdministrativeDocumentUploadScanBtn',
			selector: '#AdministrativeDocumentUploadWindow #scanBtn'
		},
		{
			ref: 'AdministrativeDocumentUploadFileUploadField',
			selector: '#AdministrativeDocumentUploadWindow #fileUploadField'
		},
		{
			ref: 'AdministrativeDocumentHashCheckBtn',
			selector: '#AdministrativeDocumentHashCheckBtn'
		},
		{
			ref: 'AdministrativeDocumentHashCheckBtn',
			selector: '#AdministrativeDocumentHashCheckBtn'
		},
		{
			ref: 'AddAdministrativeDocumentBtn',
			selector: 'administrativedocumentspanel #AddAdministrativeDocumentBtn'
		},
		{
			ref: 'AdministrativeDocumentUploadBtn',
			selector: 'administrativedocumentspanel #AdministrativeDocumentUploadBtn'
		},
		{
			ref: 'AdministrativeDocumentUploadBtn',
			selector: 'administrativedocumentspanel #AdministrativeDocumentUploadBtn'
		},
		{
			ref: 'AdministrativeDocumentScanBtn',
			selector: 'administrativedocumentspanel #AdministrativeDocumentScanBtn'
		},
		{
			ref: 'AdministrativeDocumentErrorNoteWindow',
			selector: 'administrativedocumenterrornotewindow'
		},
		{
			ref: 'AdministrativeDocumentViewerOpacityField',
			selector: 'AdministrativeDocumentViewerOpacityField'
		},
		{
			ref: 'AdministrativeDocumentWindow',
			selector: '#AdministrativeDocumentWindow'
		}
	],

	scannedDocument: null,

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				browserhelperopen: me.onBrowserHelperOpen,
				browserhelperclose: me.onBrowserHelperClose,
				documentedit: me.onAdministrativeDocumentEdit
			},
			'administrativedocumentspanel': {
				activate: me.onAdministrativeDocumentPanelActive,
				beforerender: me.onAdministrativeDocumentBeforeRender
			},
			'administrativedocumentspanel #AdministrativeDocumentGrid': {
				selectionchange: me.onAdministrativeDocumentGridSelectionChange,
				afterrender: me.onAdministrativeDocumentGridAfterRender,
				beforerender: me.onAdministrativeDocumentGridBeforeRender,
				beforeitemcontextmenu: me.onAdministrativeDocumentGridBeforeItemContextMenu
			},
			'administrativedocumentspanel #administrativeDocumentGrid gridview': {
				beforedrop: me.onAdministrativeDocumentGridViewBeforeDrop,
				drop: me.onAdministrativeDocumentGridViewDrop,
			},
			'administrativedocumentspanel [toggleGroup=administrativedocumentgridgroup]': {
				toggle: me.onAdministrativeDocumentGroupBtnToggle
			},
			'administrativedocumentspanel #AdministrativeDocumentGroupBtn': {
				toggle: me.onAdministrativeDocumentGroupBtnToggle
			},
			'administrativedocumentspanel #AdministrativeDocumentUploadBtn': {
				click: me.onAdministrativeDocumentUploadBtnClick
			},
			'administrativedocumentspanel #AdministrativeDocumentScanBtn': {
				click: me.onAdministrativeDocumentScanBtnClick
			},
			'#AdministrativeDocumentUploadWindow': {
				show: me.onAdministrativeDocumentUploadWindowShow
			},
			'#AdministrativeDocumentUploadWindow #uploadBtn': {
				click: me.onAdministrativeDocumentUploadSaveBtnClick
			},
			'#AdministrativeDocumentGridGroupBtn': {
				beforerender: me.onAdministrativeDocumentGridGroupBtnBeforeRender
			},
			'#AdministrativeDocumentGridGroupBtn menucheckitem': {
				checkchange: me.onAdministrativeDocumentGridGroupBtnCheckChange
			},
			'#AdministrativeDocumentErrorNoteSaveBtn': {
				click: me.onAdministrativeDocumentErrorNoteSaveBtnClick
			},
			'#AdministrativeDocumentViewerOpacityField': {
				afterrender: me.onAdministrativeDocumentViewerOpacityFieldAfterRender,
				change: me.onAdministrativeDocumentViewerOpacityFieldChange
			},
			'#AdministrativeDocumentViewerFrame': {
				render: me.onAdministrativeDocumentViewerFrameRender
			},
			'#AdministrativeDocumentEnteredInErrorBtn': {
				click: me.onAdministrativeDocumentEnteredInErrorBtnClick
			},
			// '#EncounterPatientDocumentsBtn': {
			// 	click: me.onEncounterPatientDocumentsBtnClick
			// },
			'#AdministrativeDocumentWindow': {
				show: me.onAdministrativeDocumentWindowShow
			}
		});

		me.nav = this.getController('Navigation');

		me.document_group_menu = [];
		me.document_groups = {};

		// CombosData.getAdministrativeDocumentsOptionsByListId({list_key : 'doc_type_admin_cat', code: 'CD'}, function (groups){
		// 	groups.forEach(function (group){
		//
		// 		var stateId = ((group.option_value+ '-' + group.option_name + '_').replace(' ', '_') + app.user.id),
		// 			state = Ext.state.Manager.get(stateId, {});
		//
		// 		me.document_groups[group.option_value] = state.checked || false;
		//
		// 		me.document_group_menu.push({
		// 			xtype: 'menucheckitem',
		// 			text: group.option_value + ' - ' + group.option_name,
		// 			stateful: true,
		// 			stateId: stateId,
		// 			_document_group: group.option_value
		// 		});
		// 	});
		// });
	},

	// getAdministrativeDocumentsOptionsByListId: function(docTypeCode) {
	// 	var me = this;
	// 	//
	// 	// me.document_group_menu = [];
	// 	// me.document_groups = {};
	//
	// 	CombosData.getAdministrativeDocumentsOptionsByListId({list_key : 'doc_type_admin_cat', code: docTypeCode}, function (groups){
	// 		groups.forEach(function (group){
	//
	// 			var stateId = ((group.option_value+ '-' + group.option_name + '_').replace(' ', '_') + app.user.id),
	// 				state = Ext.state.Manager.get(stateId, {});
	//
	// 			me.document_groups[group.option_value] = state.checked || false;
	//
	// 			me.document_group_menu.push({
	// 				xtype: 'menucheckitem',
	// 				text: group.option_value + ' - ' + group.option_name,
	// 				stateful: true,
	// 				stateId: stateId,
	// 				_document_group: group.option_value
	// 			});
	// 		});
	// 	});
	// },

	onAdministrativeDocumentHashCheckBtnClick: function(grid, rowIndex){
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

	onAdministrativeDocumentEdit: function(data){

		var store = this.getAdministrativeDocumentGrid().getStore(),
			record = store.getById(data.save.id);

		if(record){
			var src = data.save.document.split(','),
				document = src[1] || src[0],
				record_data;

			record.set({ document: document });

			record_data = record.data;
			record_data.edit_document = true;

			DocumentHandler.updateAdministrativeDocument(record_data, function (response) {

				record.commit();

				if(window.dual){
					dual.msg('sweet', _('record_saved'));
				}else{
					app.msg('sweet', _('record_saved'));
				}

			});

		}
	},

	onBrowserHelperOpen: function(){
		if(this.getAdministrativeDocumentScanBtn()){
			this.getAdministrativeDocumentScanBtn().show();
		}
	},

	onBrowserHelperClose: function(){
		if(this.getAdministrativeDocumentScanBtn()){
			this.getAdministrativeDocumentScanBtn().hide();
		}
	},

	onAdministrativeDocumentPanelActive: function(panel, win){
		var me = this,
			grid = panel.down('grid'),
			store = grid.getStore(),
			params = me.nav.getExtraParams();

		me.activePanel = panel;

		if(params && params.document){
			store.on('load', me.doSelectDocument, me);
		}

		if(panel.defereOnActiveReload) return;

		me.doAdministrativeDocumentReload(panel, win);
	},

	doSelectDocument: function(store){
		var me = this,
			grid = me.activePanel.down('grid'),
			params = me.nav.getExtraParams();

		var doc = store.getById(params.document);
		if(doc){
			grid.getSelectionModel().select(doc);

		}else{
			app.msg(_('oops'), _('unable_to_find_document'), true);
		}
		store.un('load', me.doSelectDocument, me);
	},

	onAdministrativeDocumentBeforeRender: function(panel){
		this.setViewerSite(app.user.site);
		panel.defereOnActiveReload = false;
	},

	doAdministrativeDocumentReload: function(panel, win){
		var me = this,
			grid = panel.down('grid'),
			store = grid.getStore();

		store.clearFilter(true);

		store.filter([
			{
				property: 'belongs_to_id',
				value: win.belongs_to_id
			},
			{
				property: 'belongs_to',
				value: win.belongs_to
			}
		]);
	},

	onAdministrativeDocumentGridSelectionChange: function(sm, selection){
		var frame = sm.view.panel.up('panel').query('#AdministrativeDocumentViewerFrame')[0];

		if(selection.length === 0 || selection[selection.length - 1].get('disabled_selection')){
			frame.setSrc('about:blank');
		}else{
			frame.setSrc('dataProvider/DocumentViewer.php?site=' + this.site + '&token=' + app.user.token + '&id=' + selection[selection.length - 1].data.id + '&_dc=' + Ext.Date.now()
				+ '&doctype=' + 'claimdoc');
		}
	},

	onAdministrativeDocumentGridAfterRender: function (container) {
		if(eval(a('allow_document_drag_drop_upload'))) {
			this.initDocumentDnD(container);
		}
	},

	onAdministrativeDocumentGridBeforeRender: function (grid) {
		grid.store.on('load', function (store){
			this.onAdministrativeDocumentGridBeforeRefresh(grid, store);
		}, this);
	},

	onAdministrativeDocumentGridBeforeItemContextMenu: function (grid, record, item, index, e, eOpts) {
		var me = this,
			activePanelCls = this.getController('Navigation').getUrlCls();

		//if(activePanelCls !== 'App.view.patient.Summary') return;

		e.preventDefault();
		Ext.Msg.show({
			title:'C-CDA',
			msg: 'Would you like to view the raw xml data?',
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if (btn === 'yes'){
					var frame = grid.up('panel').up('panel').query('#AdministrativeDocumentViewerFrame')[0];

					if(record){
						frame.setSrc('dataProvider/DocumentViewer.php?site=' + me.site + '&token=' + app.user.token + '&id=' + record.data.id + '&rawXml');
					}
				}
			}
		});
	},

	onAdministrativeDocumentGridBeforeRefresh: function (grid){
		// var me = this,
		// 	groups = grid.store.getGroups(),
		// 	v = grid.view;
		//
		// if(groups.length === 0){
		// 	return;
		// }
		//
		// groups.forEach(function (group){
		// 	try{
		// 		if(me.document_groups[group.name]){
		// 			v.summaryFeature.expand(group.name);
		// 		}else{
		// 			v.summaryFeature.collapse(group.name);
		// 		}
		// 	}catch (e){
		// 		say('Document Group Collapse/Expand Error');
		// 		say(e);
		// 	}
		// });
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

	onAdministrativeDocumentGridViewBeforeDrop: function (node, data, over_record, drop_position) {
		// say('onPatientDocumentGridViewBeforeDrop');
		// say(node);
		// say(data);
		// say(over_record);
		// say(drop_position);

		if(data.records.length === 0) {
			return false;
		}

		// id validation
		if(!a('allow_document_drag_drop')){
			app.msg(_('oops'), 'Unable to Edit, Not Authorized to Drag & Drop Document', true);
			return false;
		}

		// id validation
		if(data.records[0].get('id') <= 0){
			app.msg(_('oops'), 'Unable to Edit, Document is not a stored document', true);
			return false;
		}
		// ZZZ "ENTERED IN ERROR" validation
		if(data.records[0].get('docTypeCode') === 'ZZZ'){
			app.msg(_('oops'), 'Unable to Edit, Document entered in error', true);
			return false;
		}

		// Drop outside group
		if(data.records[0].get('docType') === ''){
			app.msg(_('oops'), 'Unable to Edit, Please drop the document inside group', true);
			return false;
		}

		// Drop  different group
		if(data.records[0].get('docTypeCode') === over_record.get('docTypeCode')){
			app.msg(_('oops'), 'Please drop the document inside a different group', true);
			return false;
		}

	},

	onAdministrativeDocumentGridViewDrop: function (node, data, over_record, drop_position) {
		// say('onPatientDocumentGridViewDrop');
		// say(node);
		// say(data);
		// say(over_record);
		// say(drop_position);

		data.records[0].set({
			docTypeCode: over_record.get('docTypeCode'),
			docType: over_record.get('docType'),
			date: data.records[0].get('date')
		});

		data.records[0].store.sync();

	},

	getGroupName: function(store, record){
		var group = store.groupers.items[0].property;

		if(group === 'docTypeCode'){
			return Ext.String.capitalize(record.get('docTypeCode') + ' - ' + record.get('docType'));
		}else{
			return record.get('groupDate');
		}
	},

	onAdministrativeDocumentGroupBtnToggle: function(btn, pressed){
		var grid = btn.up('grid'),
			selector = '#' + btn.action,
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

	onAdministrativeDocumentUploadBtnClick: function(btn){
		this.setAdministrativeDocumentUploadWindow(btn, 'click');
	},

	setAdministrativeDocumentUploadWindow: function(btn, action){
		var document_win = btn.up('window'),
			record = this.getNewAdministrativeDocumentRecord(document_win),
			upload_win = this.getUploadWindow(action),
			admin_doc_type_cat_combo_store = upload_win.down('form').down('#adminDocTypeCatCombo').getStore();

		admin_doc_type_cat_combo_store.clearFilter(true);

		admin_doc_type_cat_combo_store.filter([
			{
				property: 'code',
				value: document_win.doc_type_code
			}
		]);

		admin_doc_type_cat_combo_store.load();

		upload_win.down('form').getForm().loadRecord(record);

		return upload_win;
	},

	getUploadWindow: function(action){
		return Ext.create('App.view.documents.windows.UploadAdministrativeDocument', {
			action: action,
			itemId: 'AdministrativeDocumentUploadWindow'
		});
	},

	getNewAdministrativeDocumentRecord: function(document_win){
		return Ext.create('App.model.documents.AdministrativeDocuments', {
			belongs_to: document_win.belongs_to, // acc_billing_claim_payment_headers
			belongs_to_id: document_win.belongs_to_id, // TODO
			uid: app.user.id,
			date: new Date()
		})
	},

	onAdministrativeDocumentScanBtnClick: function (btn) {
		var administrative_documents_grid = btn.up('grid');

		this.getController('Scanner').showDocumentScanWindow(undefined, function (documents){
			if(documents){
				administrative_documents_grid.add(documents);
			}
		});
	},

	onAdministrativeDocumentUploadWindowShow: function(){
		this.scannedDocument = null;
		this.getAdministrativeDocumentUploadFileUploadField().enable();
		this.getAdministrativeDocumentUploadScanBtn().setVisible(this.getController('Scanner').conencted);
	},

	onAdministrativeDocumentUploadSaveBtnClick: function(){
		var me = this,
			win = me.getAdministrativeDocumentUploadWindow(),
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
				me.doNewAdministrativeDocumentRecordSave(record);
			}else{
				reader.onload = function(e){
					record.set({document: e.target.result});
					me.doNewAdministrativeDocumentRecordSave(record);
				};
				reader.readAsDataURL(uploadField.extractFileInput().files[0]);
			}
		}else{
			me.doNewAdministrativeDocumentRecordSave(record);
		}
	},

	doNewAdministrativeDocumentRecordSave: function(record){
		var me = this,
			store = me.getAdministrativeDocumentGrid().getStore(),
			index = store.indexOf(record);

		//record.set({facility_id: app.user.facility});

		if(index == -1){
			store.add(record);
		}

		store.sync({
			success: function(){
				app.msg(_('sweet'), _('document_added'));
				me.getAdministrativeDocumentUploadWindow().close();
				me.getAdministrativeDocumentGrid().getSelectionModel().select(record);

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

	onAdministrativeDocumentGridGroupBtnBeforeRender: function (btn){
		var me = this,
			admin_doc_window = btn.up('window'),
			docTypeCode = admin_doc_window.doc_type_code;

		//me.getAdministrativeDocumentsOptionsByListId(admin_doc_window.doc_type_code);

		CombosData.getAdministrativeDocumentsOptionsByListId({list_key : 'doc_type_admin_cat', code: docTypeCode}, function (groups){
			groups.forEach(function (group){

				var stateId = ((group.option_value+ '-' + group.option_name + '_').replace(' ', '_') + app.user.id),
					state = Ext.state.Manager.get(stateId, {});

				me.document_groups[group.option_value] = state.checked || false;

				me.document_group_menu.push({
					xtype: 'menucheckitem',
					text: group.option_value + ' - ' + group.option_name,
					stateful: true,
					stateId: stateId,
					_document_group: group.option_value
				});

				btn.menu.add({
					xtype: 'menucheckitem',
					text: group.option_value + ' - ' + group.option_name,
					stateful: true,
					stateId: stateId,
					_document_group: group.option_value
				});
			});
		});

		//btn.menu.add(Ext.clone(me.document_group_menu));
	},

	onAdministrativeDocumentGridGroupBtnCheckChange: function (btn){
		var me = this;

		me.document_groups[btn._document_group] = btn.checked;
	},

	onAdministrativeDocumentErrorNoteSaveBtnClick: function(){
		var me = this,
			win = me.getAdministrativeDocumentErrorNoteWindow(),
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

	onAdministrativeDocumentViewerOpacityFieldAfterRender: function (field) {
		field.el.set({'data-qtip': _('document_brightness')});
	},

	onAdministrativeDocumentViewerOpacityFieldChange: function (feild, value) {
		var frame = feild.up('panel').query('#AdministrativeDocumentViewerFrame')[0];
		if(frame) this.setOpacity(frame, value);
	},

	setOpacity: function (frame, opacity) {
		frame.el.applyStyles({ opacity: opacity });
	},

	onAdministrativeDocumentViewerFrameRender:function (frame) {
		var field = frame.up('panel').query('#AdministrativeDocumentViewerOpacityField')[0];
		if(field) this.setOpacity(frame, field.getValue());
	},

	onAdministrativeDocumentEnteredInErrorBtnClick: function (btn) {

		var me = this,
			sm = btn.up('grid').getSelectionModel(),
			document_records = sm.getSelection();

		if(document_records.length === 0) return;

		var document_record = document_records[0];

		if(app.fireEvent('beforeadministrativedocumententeredinerror', me, document_record) === false){
			return;
		}

		if(document_record.get('entered_in_error')){
			app.msg(_('oops'), _('document_entered_in_error'), true);
			return;
		}

		me.setAdministrativeDocumentInError(document_record);

	},

	setAdministrativeDocumentInError: function(document_record){
		var me = this;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('document_entered_in_error_message'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if(btn == 'yes'){
					var win = me.showAdministrativeDocumentErrorNoteWindow();
					win.down('form').getForm().loadRecord(document_record);
				}
			}
		});
	},

	showAdministrativeDocumentErrorNoteWindow: function(){
		if(!this.getAdministrativeDocumentErrorNoteWindow()){
			Ext.create('App.view.documents.windows.AdministrativeDocumentErrorNote');
		}
		return this.getAdministrativeDocumentErrorNoteWindow().show();
	},

	onAdministrativeDocumentWindowShow: function (win) {
		var panel = win.down('panel');
		this.onAdministrativeDocumentPanelActive(panel, win);
	},

	showAdministrativeDocumentWindow: function (configs) {
		if(!this.getAdministrativeDocumentWindow()){
			configs = configs || {};
			Ext.create('App.view.documents.windows.AdministrativeDocumentWindow', configs);
		}
		return this.getAdministrativeDocumentWindow().show();
	},

	setViewerSite: function(site){
		this.site = site;
	}
});