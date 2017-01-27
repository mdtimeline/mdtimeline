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

Ext.define('App.controller.patient.Results', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.view.administration.HL7MessageViewer'
	],
	refs: [
		{
			ref: 'ResultsPanel',
			selector: 'patientresultspanel'
		},
		{
			ref: 'ResultsOrdersGrid',
			selector: '#ResultsOrdersGrid'
		},
		{
			ref: 'ResultsCardPanel',
			selector: '#ResultsCardPanel'
		},
		{
			ref: 'ResultsOrderSignBtn',
			selector: '#ResultsOrderSignBtn'
		},
		{
			ref: 'ResultsOrderNewBtn',
			selector: '#ResultsOrderNewBtn'
		},
		{
			ref: 'ResultsOrderResetBtn',
			selector: '#ResultsOrderResetBtn'
		},
		{
			ref: 'ResultsOrderSaveBtn',
			selector: '#ResultsOrderSaveBtn'
		},

		// Laboratory
		{
			ref: 'ResultsLaboratoryPanel',
			selector: '#ResultsLaboratoryPanel'
		},
		{
			ref: 'ResultsLaboratoryForm',
			selector: '#ResultsLaboratoryForm'
		},
		{
			ref: 'ResultsLaboratoryFormUploadField',
			selector: '#ResultsLaboratoryFormUploadField'
		},
		{
			ref: 'ResultsLaboratoryObservationsGrid',
			selector: '#ResultsLaboratoryObservationsGrid'
		},


		// Radiology
		{
			ref: 'ResultsRadiologyPanel',
			selector: '#ResultsRadiologyPanel'
		},
		{
			ref: 'ResultsRadiologyForm',
			selector: '#ResultsRadiologyForm'
		},
		{
			ref: 'ResultsRadiologyFormUploadField',
			selector: '#ResultsRadiologyFormUploadField'
		},
		{
			ref: 'ResultsRadiologyFormViewStudyBtn',
			selector: '#ResultsRadiologyFormViewStudyBtn'
		},
		{
			ref: 'ResultsRadiologyDocumentIframe',
			selector: '#ResultsRadiologyDocumentIframe'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientresultspanel': {
				activate: me.onResultPanelActive
			},
			'#ResultsOrdersGrid': {
				selectionchange: me.onOrderSelectionChange,
				edit: me.onOrderSelectionEdit
			},
			'#ResultsLaboratoryFormUploadField': {
				change: me.onOrderDocumentChange
			},


			'#ResultsOrderResetBtn': {
				click: me.onResultsOrderResetBtnClick
			},
			'#ResultsOrderSaveBtn': {
				click: me.onResultsOrderSaveBtnClick
			},

			'#ResultsLabsLiveSearchField': {
				select: me.onResultsLabsLiveSearchFieldSelect
			},
			'#ResultsLaboratoryPanelDocumentViewBtn': {
				click: me.onOrderDocumentViewBtnClicked
			},
			'#ResultsOrderNewBtn': {
				click: me.onNewOrderResultBtnClick
			},
			'#ResultsOrderSignBtn': {
				click: me.onOrderResultSignBtnClick
			},
			'#ResultsOrdersGridOrderTypeCombo': {
				change: me.onOrderTypeSelect
			},
			'#ResultsOrdersGridRowEditor': {
				beforeedit: me.onOrderResultGridRowEdit
			},
			'#ResultsOrdersGridOrderVoidCheckbox': {
				change: me.onResultsOrdersGridOrderVoidCheckboxChange
			},
			'#ResultsRadiologyFormViewStudyBtn': {
				click: me.onResultsRadiologyFormViewStudyBtnClick
			}
		});
	},

	onResultsLabsLiveSearchFieldSelect: function(cmb, records){
		cmb.up('form').getRecord().set({
			code: records[0].get('loinc_number'),
			code_text: records[0].get('loinc_name'),
			code_type: 'LOINC'
		});
	},

	onResultsOrdersGridOrderVoidCheckboxChange: function(){

	},

	onOrderResultSignBtnClick: function(){
		var me = this,
			record;

		app.passwordVerificationWin(function(btn, password){
			if(btn == 'ok'){
				User.verifyUserPass(password, function(success){
					if(success){
						record = me.getActiveForm().getForm().getRecord();
						record.set({signed_uid: app.user.id});
						record.save({
							success: function(){
								app.msg(_('sweet'), _('result_signed'));
							},
							failure: function(){
								app.msg(_('sweet'), _('record_error'), true);
							}
						});
					}else{
						me.onOrderResultSignBtnClick();
					}
				});
			}
		});
	},

	onOrderSelectionEdit: function(editor, context){
		this.getLabOrderResult(context.record);
	},

	onNewOrderResultBtnClick: function(btn){
		var grid = this.getResultsOrdersGrid(),
			store = grid.getStore(),
			records,
			fields;

		grid.editingPlugin.cancelEdit();
		records = store.add({
			pid: app.patient.pid,
			uid: app.user.id,
            eid: app.patient.eid,
			order_type: 'lab',
			status: 'Pending'
		});
		grid.getPlugin('ResultsOrdersGridRowEditor').startEdit(records[0], 0);

		// Focus the second column when editing.
		fields = grid.getPlugin('ResultsOrdersGridRowEditor').getEditor();
		fields.items.items[2].focus();
		fields.items.items[1].setValue('lab');

		// By Default when adding a new record, it will be a Laboratory
		grid.columns[4].setEditor({
			xtype: 'labslivetsearch',
			itemId: 'ResultsLabsLiveSearchField',
			allowBlank: false,
			flex: 1
		});
	},

	onOrderResultGridRowEdit: function(editor, context, eOpts){
		//say(context);
	},

	onOrderTypeSelect: function(combo, newValue, oldValue, eOpts){
		var grid = combo.up('grid');

		if(newValue === 'lab'){
			// Change the Card panel, to show the Laboratory results form
			this.getResultsCardPanel().getLayout().setActiveItem('ResultsLaboratoryPanel');
			// Change the field to look for laboratories
			grid.columns[4].setEditor({
				xtype: 'labslivetsearch',
				itemId: 'ResultsLabsLiveSearchField',
				allowBlank: false,
				flex: 1,
				value: ''
			});
			// Enabled the New Order Result Properties
			//this.getResultsOrderNewBtn().disable(false);
		}

		if(newValue === 'rad'){
			// Change the Card panel, to show the Radiology results form
			this.getResultsCardPanel().getLayout().setActiveItem('ResultsRadiologyPanel');
			// Change the field to look for radiologists
			grid.columns[4].setEditor({
				xtype: 'radslivetsearch',
				allowBlank: false,
				flex: 1,
				value: ''
			});
			// Enabled the New Order Result Properties
			//this.getResultsOrderNewBtn().disable(false);
		}
	},

	onResultPanelActive: function(){
		this.setResultPanel();
	},

	setResultPanel: function(){
		var me = this,
			ordersStore = me.getResultsOrdersGrid().getStore();

		if(app.patient){
			ordersStore.clearFilter(true);
			ordersStore.filter([
				{
					property: 'pid',
					value: app.patient.pid
				}
			]);
		}
		else{
			ordersStore.clearFilter(true);
			ordersStore.load();
		}
	},

	onOrderSelectionChange: function(model, records){

		var carDpanel = this.getResultsCardPanel();

		if(!carDpanel.isVisible())
			carDpanel.setVisible(true);

		if(records.length > 0){
			if(records[0].data.order_type === 'lab'){
				carDpanel.getLayout().setActiveItem('ResultsLaboratoryPanel');
				if(records.length > 0){
					this.getLabOrderResult(records[0]);
				}
			}else if(records[0].data.order_type === 'rad'){
				carDpanel.getLayout().setActiveItem('ResultsRadiologyPanel');
				if(records.length > 0){
					this.getRadOrderResult(records[0]);
				}
			}else{
				this.resetOrderResultForm();
			}
		}else{
			this.resetOrderResultForm();
		}
	},

	getLabOrderResult: function(order_record){
		var me = this,
			form = me.getResultsLaboratoryForm(),
			results_store = order_record.results(),
			observationGrid = me.getResultsLaboratoryObservationsGrid(),
			observationStore,
			newResult,
			i;

		observationGrid.editingPlugin.cancelEdit();
		results_store.load({
			callback: function(records){
				if(records.length > 0){
					var last_result = records.length - 1;

					form.loadRecord(records[last_result]);
					me.getResultsOrderSignBtn().setDisabled(records[last_result].data.signed_uid > 0);
					observationStore = records[last_result].observations();
					observationGrid.reconfigure(observationStore);
					observationStore.load();
				}else{
					newResult = results_store.add({
						pid: order_record.data.pid,
						code: order_record.data.code,
						code_text: order_record.data.description,
						code_type: order_record.data.code_type,
						ordered_uid: order_record.data.uid,
						create_date: new Date()
					});
					form.loadRecord(newResult[0]);
					me.getResultsOrderSignBtn().setDisabled(true);
					observationStore = newResult[0].observations();
					observationGrid.reconfigure(observationStore);
					observationStore.load({

						params: {
							loinc: order_record.data.code
						},
						callback: function(ObsRecords){
							for(i = 0; i < ObsRecords.length; i++){
								ObsRecords[i].phantom = true;
							}
						}
					});
				}
			}
		});
	},

	getRadOrderResult: function(order_record){

		var me = this,
			form = me.getResultsRadiologyForm().getForm(),
			results_store = order_record.results();

		results_store.load({
			callback: function(records){
				if(records.length > 0){
					var last_result = records.length - 1;

					form.loadRecord(records[last_result]);
					me.getResultsOrderSignBtn().setDisabled(records[last_result].data.signed_uid > 0);
					me.loadRadiologyDocument(records[last_result]);
					me.setViewStudyBtn(records[last_result]);
				}else{
					var newResult = results_store.add({
						pid: order_record.data.pid,
						code: order_record.data.code,
						code_text: order_record.data.description,
						code_type: order_record.data.code_type,
						ordered_uid: order_record.data.uid,
						create_date: new Date()
					});
					form.loadRecord(newResult[0]);
					me.loadRadiologyDocument(newResult[0]);
					me.setViewStudyBtn(newResult[0]);
				}
			}
		});
	},

	setViewStudyBtn: function(result_record){
		this.getResultsRadiologyFormViewStudyBtn().setDisabled(result_record.get('study_link') == '');
	},

	onResultsRadiologyFormViewStudyBtnClick: function(){
		var record = this.getActiveForm().getForm().getRecord();
		var win = window.open(record.get('study_link'), 'dicom_viewer');

		if(win){
			win.focus();
		} else{
			app.msg(_('oops'), _('unable_to_open_new_tab'), true);
		}
	},


	/**
	 * SAVE RESULTS FOMR
	 */
	onResultsOrderSaveBtnClick: function(){
		var form = this.getActiveForm();

		if(form.itemId == 'ResultsLaboratoryForm'){
			this.saveLabOrderResultForm(form.getForm());
		}else if(form.itemId == 'ResultsRadiologyForm'){
			this.saveRadOrderResultForm(form.getForm());
		}
	},

	saveLabOrderResultForm: function(form){
		var me = this,
			result_record = form.getRecord(),
			sm = me.getResultsOrdersGrid().getSelectionModel(),
			order = sm.getSelection(),
			values = form.getValues(),
			observationData = [];

		if(!form.isValid()) return;

		say(result_record.observations());

		var observationStore = result_record.observations(),
			observations = observationStore.data.items;

		result_record.set(values);
		result_record.save({
			success: function(rec){

				for(var i = 0; i < observations.length; i++){
					observations[i].set({result_id: rec.data.id});
				}
				observationStore.sync({
					callback: function(batch, options){

					}
				});
				order[0].set({status: 'Received'});
				order[0].save();
				app.msg(_('sweet'), _('record_saved'));
			}
		});
	},

	saveRadOrderResultForm: function(form){
		if(!form.isValid()) return;

		var me = this,
			result_record = form.getRecord(),
			values = form.getValues(),
			reader = new FileReader();

		reader.onload = function(e){
			values.upload = e.target.result;
			result_record.set(values);
			result_record.save({
				callback: function(){
					me.loadRadiologyDocument(result_record);
					app.msg(_('sweet'), _('record_save'));
				}
			});
		};

		reader.readAsDataURL(me.getResultsRadiologyFormUploadField().extractFileInput().files[0]);
	},

	loadRadiologyDocument: function(result_record){

		var document_id = result_record.get('documentId'),
			frame = this.getResultsRadiologyDocumentIframe();

		if(document_id != ''){
			var doc_id = document_id.split('|');
			if(doc_id.length == 2){
				frame.setSrc(
					Ext.String.format(
						'dataProvider/DocumentViewer.php?site={0}&token={1}&id={2}',
						app.user.site,
						app.user.token,
						doc_id[1]
					)
				);
			}
		}else{
			frame.setSrc('about:blank');
		}
	},

	/**
	 * RESET RESULTS FORM
	 */
	onResultsOrderResetBtnClick: function(){
		this.resetOrderResultForm();
	},

	resetOrderResultForm: function(){
		var form = this.getActiveForm();

		if(form.itemId == 'ResultsLaboratoryForm'){
			this.resetLabOrderResultForm(form.getForm());
		}else if(form.itemId == 'ResultsRadiologyForm'){
			this.resetRadOrderResultForm(form.getForm());
		}

		var card_panel = this.getResultsCardPanel();

		if(card_panel.isVisible()){
			card_panel.setVisible(false);
		}
	},

	resetLabOrderResultForm: function(form){
		var me = this,
			observationGrid = me.getResultsLaboratoryObservationsGrid(),
			store = Ext.create('App.store.patient.PatientsOrderObservations');

		form.reset();
		observationGrid.editingPlugin.cancelEdit();
		observationGrid.reconfigure(store);
	},

	resetRadOrderResultForm: function(form){
		form.reset();
		this.getResultsRadiologyDocumentIframe().setSrc('about:blank');
	},


	getActiveForm: function(){
		return this.getResultsCardPanel().getLayout().getActiveItem().down('form');
	},

	onOrderDocumentViewBtnClicked: function(){
		var me = this,
			form = me.getResultsLaboratoryForm(),
			record = form.getRecord(),
			recordData = record.data.documentId.split('|'),
			type, id;

		if(recordData[0]) type = recordData[0];
		if(recordData[1]) id = recordData[1];

		if(type && id){
			if(type == 'hl7'){
				app.getController('administration.HL7').viewHL7MessageDetailById(id);
			} else if(type == 'doc'){
				app.onDocumentView(id);
			}
		}else{
			app.msg(_('oops'), _('no_document_found'), true)
		}
	},

	onOrderDocumentChange: function(field){
		//		say(field);
		//		say(document.getElementById(field.inputEl.id).files[0]);
		//		say(field.inputEl);
		//
		//		var fr = new FileReader();
		//
		//
		//		fr.onload = function(e) {
		//			say(e.target.result);
		//		};
		//
		//		fr.readAsDataURL( field.value );
	}

});
