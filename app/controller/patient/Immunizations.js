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

Ext.define('App.controller.patient.Immunizations', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'SubmitImmunizationWindow',
			selector: '#SubmitImmunizationWindow'
		},
		{
			ref: 'SubmitImmunizationGrid',
			selector: '#SubmitImmunizationGrid'
		},
		{
			ref: 'ImmunizationPanel',
			selector: 'patientimmunizationspanel'
		},
		{
			ref: 'ImmunizationsGrid',
			selector: 'patientimmunizationspanel #patientImmunizationsGrid'
		},
		{
			ref: 'CvxGrid',
			selector: 'patientimmunizationspanel #cvxGrid'
		},
		{
			ref: 'CvxMvxCombo',
			selector: 'cvxmanufacturersforcvxcombo'
		},
		{
			ref: 'AddImmunizationBtn',
			selector: 'patientimmunizationspanel #addImmunizationBtn'
		},
		{
			ref: 'ReviewImmunizationsBtn',
			selector: 'patientimmunizationspanel #reviewImmunizationsBtn'
		},
		{
			ref: 'SubmitVxuBtn',
			selector: 'patientimmunizationspanel #submitVxuBtn'
		},
		{
			ref: 'ImmunizationsPresumedImmunityCheckbox',
			selector: '#ImmunizationsPresumedImmunityCheckbox'
		},
		{
			ref: 'ImmunizationsImmunizationSearch',
			selector: '#ImmunizationsImmunizationSearch'
		},
		{
			ref: 'ImmunizationsImmunizationNdcSearch',
			selector: '#ImmunizationsImmunizationNdcSearch'
		},
		{
			ref: 'ImmunizationsDisorderCombo',
			selector: '#ImmunizationsDisorderCombo'
		},
		{
			ref: 'ImmunizationsPresumedImmunityCheckbox',
			selector: '#ImmunizationsPresumedImmunityCheckbox'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientimmunizationspanel': {
				activate: me.onPatientImmunizationsPanelActive
			},
			'patientimmunizationspanel #patientImmunizationsGrid': {
				selectionchange: me.onPatientImmunizationsGridSelectionChange,
				beforeedit: me.onPatientImmunizationsGridBeforeEdit,
				edit: me.onPatientImmunizationsGridEdit
			},
			'patientimmunizationspanel #cvxGrid': {
				expand: me.onCvxGridExpand
			},
			'patientimmunizationspanel #submitVxuBtn': {
				click: me.onSubmitVxuBtnClick
			},
			'patientimmunizationspanel #reviewImmunizationsBtn': {
				click: me.onReviewImmunizationsBtnClick
			},
			'patientimmunizationspanel #addImmunizationBtn': {
				click: me.onAddImmunizationBtnClick
			},
			'#ImmunizationsInformationSourceCombo': {
				change: me.onInformationSourceComboChange
			},
			'#ImmunizationsImmunizationSearch': {
				select: me.onImmunizationSearchSelect
			},
			'#ImmunizationsImmunizationNdcSearch': {
				select: me.onImmunizationNdcSearchhSelect
			},
			'#patientImmunizationsEditFormAdministeredByField': {
				select: me.onPatientImmunizationsEditFormAdministeredByFieldSelect
			},
			'#SubmitImmunizationWindow #ActiveFacilitiesCombo': {
				change: me.onActiveFacilitiesChange
			},
			'#SubmitImmunizationWindow #ApplicationCombo': {
				change: me.onApplicationChange
			},
			'#ImmunizationsPresumedImmunityCheckbox': {
				afterrender: me.onImmunizationsPresumedImmunityCheckboxAfterRender
			},
			'#ImmunizationsUnableToPerformField': {
				select: me.onImmunizationsUnableToPerformFieldSelect
			},
			'#ImmunizationHistorySearchBtn': {
				click: me.onImmunizationHistorySearchBtnClick
			}
		});
	},

	onPatientImmunizationsGridEdit: function(plugin, context){
		app.fireEvent('immunizationedit', this, context.record);

		var pid = context.record.get('pid'),
			immunization_id = context.record.get('id');

		if(context.record.new_immunization === true){
			//this.onImmunizationRecordEdit(pid, immunization_id, 'NEW');
		}else if(context.record.get('is_error')){
			this.onImmunizationRecordEdit(pid, immunization_id, 'DELETE');
		}else{
			this.onImmunizationRecordEdit(pid, immunization_id, 'UPDATE');
		}
	},

	onImmunizationHistorySearchBtnClick: function(btn){
		var me = this;

		ImmunizationRegistry.getImmunizationHxByPid(app.patient.pid, function (response) {
			say(response);

			if(response.success === false || !response.messages === undefined){
				app.msg(_('oops'), 'Immunization Search Failed', true);
				return;
			}

			response.messages.forEach(function (message) {
				me.showImmunizationRegistryResponse(message);
			});
		});
	},

	showImmunizationRegistryResponse: function(message){
		say('showImmunizationHistorySearchResponse');
		say(message);

		if(message.response.success === false){
			app.msg(_('oops'), message.response.message, true);
			return;
		}

		Ext.create('App.view.patient.windows.ImmunizationRegisterResponseWindow', {
			hl7Message: message.response.message,
			hl7Printed: message.response.print
		}).show();

	},

	onImmunizationRecordEdit: function(pid, immunization_id, action){
		var me = this,
			params = {};

		params.pid = pid;
		params.immunizations = [ immunization_id ];
		params.action = action;

		ImmunizationRegistry.sendImmunization(params, function(response){

			if(response.success === false || !response.messages === undefined){
				app.msg(_('oops'), 'Immunization Search Failed', true);
				return;
			}

			response.messages.forEach(function (message) {
				me.showImmunizationRegistryResponse(message);
			});

		});
	},

	doImmunizationVxuSubmit: function(btn){
		var me = this,
			win = btn.up('window'),
			records = win.down('grid').getStore().data.items,
			params = {
				pid: app.patient.pid,
				eid: app.patient.eid,
				facility_id: app.user.facility,
				action: 'NEW',
				immunizations: []
			};

		records.forEach(function (record) {
			params.immunizations.push(record.data);
		});

		say('doImmunizationVxuSubmit');
		say(params);

		ImmunizationRegistry.sendImmunization(params, function(response){

			if(response.success){

				win.close();

				response.messages.forEach(function (message) {
					me.showImmunizationRegistryResponse(message);
				});


			}else{
				app.msg(_('oops'), _('registry_message_error'), true);
			}

		});

	},

	onImmunizationsUnableToPerformFieldSelect: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord(),
			selected_record = combo.findRecordByValue(combo.getValue());

		form_record.set({
			not_performed_code: selected_record.get('code'),
			not_performed_code_type: selected_record.get('code_type'),
			not_performed_code_text: selected_record.get('option_name'),
		});
	},


	onImmunizationsPresumedImmunityCheckboxAfterRender: function(checkbox){
		var me = this;

		checkbox.el.on('click', function(){
			Ext.Function.defer(function(){
				me.onImmunizationsPresumedImmunityCheckboxClick(checkbox);
			}, 100);

		});
	},

	onImmunizationsPresumedImmunityCheckboxClick: function(checkbox){

		var record = checkbox.up('form').getForm().getRecord(),
			checked = checkbox.getValue(),
			is_ndc = record.get('code_type') === 'NDC';

		this.setImmunizationFields(checked, is_ndc);

		this.getImmunizationsImmunizationSearch().reset();
		this.getImmunizationsDisorderCombo().reset();

		if(checked){
			record.set({
				presumed_immunity_code: '',
				code: '998',
				code_type: 'CVX',
				vaccine_name: 'no vaccine administered'
			});
		}else{
			record.set({
				code: '',
				code_type: '',
				vaccine_name: ''
			});
		}
	},

	setImmunizationFields: function(presumed_immunity, is_ndc){

		this.getImmunizationsDisorderCombo().setVisible(presumed_immunity);
		this.getImmunizationsDisorderCombo().setDisabled(!presumed_immunity);
		this.getImmunizationsImmunizationSearch().setVisible(!presumed_immunity);
		this.getImmunizationsImmunizationSearch().setDisabled(presumed_immunity);

		this.getImmunizationsImmunizationSearch().setVisible(!is_ndc);
		this.getImmunizationsImmunizationSearch().setDisabled(is_ndc);
		this.getImmunizationsImmunizationNdcSearch().setVisible(is_ndc);
		this.getImmunizationsImmunizationNdcSearch().setDisabled(!is_ndc);
	},

	onPatientImmunizationsEditFormAdministeredByFieldSelect: function(comb, records){
		var record = comb.up('form').getForm().getRecord();

		record.set({
			administered_uid: records[0].data.id,
			administered_title: records[0].data.title,
			administered_fname: records[0].data.fname,
			administered_mname: records[0].data.mname,
			administered_lname: records[0].data.lname
		});
	},

	onInformationSourceComboChange: function(combo, value){
		var cvxField = this.getImmunizationsImmunizationSearch(),
			ndcField = this.getImmunizationsImmunizationNdcSearch();

		if(value === '00'){
			cvxField.hide();
			cvxField.disable();

			ndcField.show();
			ndcField.enable();
		}else{
			cvxField.show();
			cvxField.enable();

			ndcField.hide();
			ndcField.disable();
		}
	},

	onImmunizationSearchSelect: function(combo, record){
		var form = combo.up('form').getForm();

		this.getCvxMvxCombo().getStore().load({
			params: {
				cvx_code: record[0].get('cvx_code')
			}
		});
		form.getRecord().set({
			code: record[0].get('cvx_code'),
			code_type: 'CVX'
		});
	},

	onImmunizationNdcSearchhSelect: function(combo, record){
		var form = combo.up('form').getForm();

		this.getCvxMvxCombo().getStore().load({
			params: {
				cvx_code: record[0].get('CVXCode')
			}
		});
		form.getRecord().set({
			code: record[0].get('NDC11'),
			code_type: 'NDC'
		});
	},

	onCvxGridExpand: function(grid){
		grid.getStore().load();
	},

	onPatientImmunizationsGridSelectionChange: function(sm, selected){
		this.getSubmitVxuBtn().setDisabled(selected.length === 0);
	},

	onPatientImmunizationsGridBeforeEdit: function(plugin, context){
		var record = context.record,
			is_ndc = record.get('code_type') === 'NDC',
			administer_field = plugin.editor.getForm().findField('administered_by'),
			cvx_field = this.getImmunizationsImmunizationSearch(),
			ndc_field = this.getImmunizationsImmunizationNdcSearch();

		this.setImmunizationFields(context.record.get('is_presumed_immunity'), is_ndc);

		administer_field.forceSelection = false;
		Ext.Function.defer(function(){
			administer_field.setValue(context.record.data.administered_by);
			administer_field.forceSelection = true;

			if(is_ndc){
				ndc_field.setValue(context.record.get('vaccine_name'));
			}else {
				cvx_field.setValue(context.record.get('vaccine_name'));
			}


		}, 200);
	},

	onPatientImmunizationsPanelActive: function(){
		this.loadPatientImmunizations();
	},

	onSubmitVxuBtnClick: function(){
		var me = this,
			selected = me.getImmunizationsGrid().getSelectionModel().getSelection();
		me.vxuWindow = me.getVxuWindow();
		me.vxuWindow.down('grid').getStore().loadData(selected);
	},


	onReviewImmunizationsBtnClick: function(){

	},

	onAddImmunizationBtnClick: function(){
		var grid = this.getImmunizationsGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		var records = store.insert(0, {
			created_uid: app.user.id,
			uid: app.user.id,
			pid: app.patient.pid,
			eid: app.patient.eid,
			facility_id: app.user.facility,
			create_date: new Date(),
			begin_date: new Date()

		});

		records[0].new_immunization = true;

		grid.editingPlugin.startEdit(records[0], 0);
	},

	loadPatientImmunizations: function(){
		var store = this.getImmunizationsGrid().getStore();
		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	getImmunizationHxFromRegistry: function(){

		var params = {};


		Immunizations.getImmunizationHxFromRegistry(params, function (response) {


			say(response);

		});

	},


	getVxuWindow: function(){
		var me = this;

		if(this.getSubmitImmunizationWindow()){
			return this.getSubmitImmunizationWindow().show();
		}

		return Ext.widget('window', {
			title: 'Immunization Registry Submission',
			closable: false,
			itemId: 'SubmitImmunizationWindow',
			modal: true,
			bodyStyle: 'background-color:white',
			layout: 'fit',
			items: [
				{
					xtype: 'grid',
					title: _('please_verify_the_information'),
					store: Ext.create('App.store.patient.PatientImmunization'),
					width: 800,
					minHeight: 200,
					maxHeight: 500,
					itemId: 'SubmitImmunizationGrid',
					viewConfig: {
						plugins: {
							ptype: 'gridviewdragdrop',
							dragText: 'Drag and drop to reorganize'
						}
					},
					columns: [
						{
							text: _('code'),
							dataIndex: 'code',
							width: 75
						},
						{
							text: _('vaccine_name'),
							dataIndex: 'vaccine_name',
							flex: 1
						},
						{
							text: _('administer_amount'),
							dataIndex: 'administer_amount',
							width: 130,
							renderer: function (v,m,r) {
								return v + ' ' + r.get('administer_units');
							}
						},
						{
							text: _('date_administered'),
							dataIndex: 'date_administered',
							flex: 1
						}
					]
				},
				{
					xtype: 'uxiframe',
					itemId: 'downloadHL7',
					hidden: true
				}
			],
			buttons: [
				// {
				// 	xtype: 'activefacilitiescombo',
				// 	fieldLabel: _('send_from'),
				// 	emptyText: _('select'),
				// 	itemId: 'ActiveFacilitiesCombo',
				// 	labelWidth: 60,
				// 	store: Ext.create('App.store.administration.HL7Clients', {
				// 		filters: [
				// 			{
				// 				property: 'active',
				// 				value: true
				// 			}
				// 		]
				// 	})
				// },
				// {
				// 	xtype: 'combobox',
				// 	fieldLabel: _('send_to'),
				// 	emptyText: _('select'),
				// 	allowBlank: false,
				// 	itemId: 'ApplicationCombo',
				// 	forceSelection: true,
				// 	editable: false,
				// 	labelWidth: 60,
				// 	displayField: 'application_name',
				// 	valueField: 'id',
				// 	store: Ext.create('App.store.administration.HL7Clients', {
				// 		filters: [
				// 			{
				// 				property: 'active',
				// 				value: true
				// 			}
				// 		]
				// 	})
				// },
				{
					text: _('send'),
					scope: me,
					itemId: 'send',
					handler: me.doImmunizationVxuSubmit,
					action: 'send'
				},
				{
					text: _('cancel'),
					handler: function(btn){
						btn.up('window').close();
					}
				}
			]
		}).show();
	},

	/**
	 * Only activate the send, & download button when facilities and application has been
	 * selected
	 * @param me
	 * @param newValue
	 * @param oldValue
	 */
	onActiveFacilitiesChange: function(me, newValue, oldValue){
		if(Ext.ComponentQuery.query('#ApplicationCombo')[0].getValue()){
			Ext.ComponentQuery.query('#SubmitImmunizationWindow #send')[0].setDisabled(false);
			Ext.ComponentQuery.query('#SubmitImmunizationWindow #download')[0].setDisabled(false);
		}
	},

	/**
	 * Only activate the send, & download button when facilities and application has been
	 * selected
	 * @param me
	 * @param newValue
	 * @param oldValue
	 */
	onApplicationChange: function(me, newValue, oldValue){
		if(Ext.ComponentQuery.query('#ActiveFacilitiesCombo')[0].getValue()){
			Ext.ComponentQuery.query('#SubmitImmunizationWindow #send')[0].setDisabled(false);
			Ext.ComponentQuery.query('#SubmitImmunizationWindow #download')[0].setDisabled(false);
		}
	},

	// doDownloadVxu: function(btn){
	// 	var me = this,
	// 		sm = me.getImmunizationsGrid().getSelectionModel(),
	// 		ImmunizationSelection = sm.getSelection(),
	// 		params = {},
	// 		immunizations = [],
	// 		form;
	//
	// 	if(me.vxuTo.isValid()){
	//
	// 		for(var i = 0; i < ImmunizationSelection.length; i++){
	// 			immunizations.push(ImmunizationSelection[i].data.id);
	// 			params.pid = ImmunizationSelection[i].data.pid;
	// 		}
	//
	// 		me.vxuWindow.el.mask(_('download'));
	// 		Ext.create('Ext.form.Panel', {
	// 			renderTo: Ext.ComponentQuery.query('#SubmitImmunizationWindow #downloadHL7')[0].el,
	// 			standardSubmit: true,
	// 			url: 'dataProvider/Download.php'
	// 		}).submit({
	// 			params: {
	// 				'pid': params.pid,
	// 				'from': me.vxuFrom.getValue(),
	// 				'to': me.vxuTo.getValue(),
	// 				'immunizations': Ext.encode(immunizations)
	// 			},
	// 			success: function(form, action){
	// 				// Audit log here
	// 			}
	// 		});
	//
	// 		me.vxuWindow.el.unmask();
	// 		me.vxuWindow.close();
	// 		sm.deselectAll();
	//
	// 	}
	// }
});