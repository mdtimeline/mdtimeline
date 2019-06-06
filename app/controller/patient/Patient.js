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

Ext.define('App.controller.patient.Patient', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'NewPatientWindow',
			selector: '#NewPatientWindow'
		},
		{
			ref: 'NewPatientWindowForm',
			selector: '#NewPatientWindowForm'
		},
		{
			ref: 'NewPatientWindowInsuranceForm',
			selector: '#NewPatientWindowInsuranceForm'
		},
		{
			ref: 'PossiblePatientDuplicatesWindow',
			selector: '#PossiblePatientDuplicatesWindow'
		},
		{
			ref: 'PatientDemographicForm',
			selector: '#PatientDemographicForm'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'#NewPatientWindow': {
				close: me.onNewPatientWindowClose
			},
			'#HeaderNewPatientBtn': {
				click: me.onHeaderNewPatientBtnClick
			},
			'#NewPatientWindowCancelBtn': {
				click: me.onNewPatientWindowCancelBtnClick
			},
			'#NewPatientWindowSaveBtn': {
				click: me.onNewPatientWindowSaveBtnClick
			},

			'#NewPatientWindowImportFromCdaBtn': {
				click: me.onNewPatientWindowImportFromCdaBtnClick
			},

			'#PossiblePatientDuplicatesWindow': {
				close: me.onPossiblePatientDuplicatesWindowClose
			},
			'#PossiblePatientDuplicatesWindow > grid': {
				itemdblclick: me.onPossiblePatientDuplicatesGridItemDblClick
			},
			'#PatientPossibleDuplicatesBtn': {
				click: me.onPatientPossibleDuplicatesBtnClick
			},
			'#PossiblePatientDuplicatesContinueBtn': {
				click: me.onPossiblePatientDuplicatesContinueBtnClick
			},
			'#PossiblePatientDuplicatesCancelBtn': {
				click: me.onPossiblePatientDuplicatesCancelBtnClick
			}
		});

		me.importCtrl = this.getController('patient.CCDImport');

	},

	onNewPatientWindowClose: function () {
		this.getNewPatientWindowForm().getForm().reset();
		this.getNewPatientWindowInsuranceForm().getForm().reset();
	},

	onHeaderNewPatientBtnClick: function () {
		this.showNewPatientWindow(true);
	},

	onNewPatientWindowCancelBtnClick: function () {
		this.getNewPatientWindowForm().getForm().reset(true);
		this.getNewPatientWindow().close();
	},

	onNewPatientWindowSaveBtnClick: function (btn) {

		var me = this,
			win = me.getNewPatientWindow(),
			demographics_form = me.getNewPatientWindowForm().getForm(),
			demographics_values = demographics_form.getValues(),
			demographics_params = {
				fname: demographics_values.fname,
				mname: demographics_values.mname,
				lname: demographics_values.lname,
				DOB: demographics_values.DOB,
				sex: demographics_values.sex,
				phone_mobile: demographics_values.phone_mobile,
				email: demographics_values.email
			},
			insurance_form = me.getNewPatientWindowInsuranceForm().getForm(),
			insurance_values = insurance_form.getValues(),
			has_insurance = insurance_values.insurance_id > 0;

		if(!demographics_form.isValid()) return;

		if(has_insurance){
			demographics_params.insurance = {
				insurance_id: insurance_values.insurance_id,
				insurance_type: 'P',
				effective_date: null,
				expiration_date: null,
				policy_number: insurance_values.policy_number,
				group_number: insurance_values.group_number,
				subscriber_given_name: insurance_values.subscriber_given_name,
				subscriber_middle_name: insurance_values.subscriber_middle_name,
				subscriber_surname_name: insurance_values.subscriber_surname_name,
				display_order: 1,
				create_uid: app.user.id,
				update_uid: app.user.id,
				create_date: Ext.Date.format(new Date(), 'Y-m-d H:i:s'),
				update_date: Ext.Date.format(new Date(), 'Y-m-d H:i:s')
			};

			demographics_params.policy_number = insurance_values.policy_number || null;

		}

		me.lookForPossibleDuplicates(demographics_params, null, function (duplicared_win, response) {

			if(response === true){
				win.el.mask(_('please_wait'));
				// continue clicked
				Patient.createNewPatient(demographics_params, function (response) {
					app.setPatient(response.pid, null, null, function(){
						win.el.unmask();
						win.close();
					});
				});

			}else if(response.isModel === true){
				win.el.mask(_('please_wait'));
				// duplicated record clicked
				app.setPatient(response.get('pid'), null, null, function(){
					duplicared_win.close();
					win.el.unmask();
					win.close();
				});
			}
		});

	},

	showNewPatientWindow: function () {
		if(!this.getNewPatientWindow()){
			Ext.create('App.view.patient.windows.NewPatient');
		}
		return this.getNewPatientWindow().show();
	},

	doCapitalizeEachLetterOnKeyUp: function(){

	},

	onPossiblePatientDuplicatesGridItemDblClick: function(grid, record){

		var win = this.getPossiblePatientDuplicatesWindow();

		if(typeof win.callbackFn === 'function'){
			win.callbackFn(win, record);
		}else if(win.action === 'openPatientSummary'){
			app.setPatient(record.data.pid, null, null, function(){
				app.openPatientSummary();
				grid.up('window').close();
			});
		}

	},

    onPossiblePatientDuplicatesWindowClose: function(window){
		var store = window.down('grid').getStore();
		store.removeAll();
		store.commitChanges();
	},

	checkForPossibleDuplicates: function(cmp){
		var me = this,
            params,
			form = cmp.isPanel ? cmp.getForm() : cmp.up('form').getForm();

		if(!form.isValid()) return;

		params = {
			fname: form.findField('fname').getValue(),
			lname: form.findField('lname').getValue(),
			sex: form.findField('sex').getValue(),
			DOB: form.findField('DOB').getValue()
		};

		if(form.getRecord()){
			params.pid = form.getRecord().data.pid;
		}

		me.lookForPossibleDuplicates(params, 'openPatientSummary');

	},

	lookForPossibleDuplicates: function(params, action, callback){
		var me = this,
			win = me.getPossiblePatientDuplicatesWindow() || Ext.create('App.view.patient.windows.PossibleDuplicates'),
			store = win.down('grid').getStore();

		win.action = action;
		win.callbackFn = callback;
		store.getProxy().extraParams = params;
		store.load({
			callback: function(records){
				if(records.length > 0){
					win.show();
				}else{
					app.msg(_('sweet'), _('no_possible_duplicates_found'));
					if(typeof win.callbackFn === 'function') {
						win.callbackFn(win, true);
					}
				}
			}
		});
	},

	onPatientPossibleDuplicatesBtnClick: function(btn){
		this.checkForPossibleDuplicates(btn.up('panel').down('form'));
	},

	onPossiblePatientDuplicatesContinueBtnClick: function(btn){
		var win = this.getPossiblePatientDuplicatesWindow();
		win.fireEvent('continue', win);

		if(typeof win.callbackFn === 'function') {
			win.callbackFn(win, true);
		}
		win.close();
	},

	onPossiblePatientDuplicatesCancelBtnClick: function(){
		var win = this.getPossiblePatientDuplicatesWindow();
		win.fireEvent('cancel', win);

		if(typeof win.callbackFn === 'function') {
			win.callbackFn(win, false);
		}
		win.close();
	},


	// C-CDA Patient Import
	onNewPatientWindowImportFromCdaBtnClick: function(btn){

		btn.up('window').close();

		var me = this,
			win = Ext.create('App.ux.form.fields.UploadString');

		win.allowExtensions = ['xml','ccd','cda','ccda'];
		win.on('uploadready', function(comp, stringXml){
			me.getDocumentData(stringXml);
		});

		win.show();
	},

	getDocumentData: function(stringXml){
		var me = this;

		CDA_Parser.parseDocument(stringXml, function(ccdData){
			me.importCtrl.validatePosibleDuplicates = false;
			me.importCtrl.CcdImport(ccdData, null, stringXml);
			me.importCtrl.validatePosibleDuplicates = true;
			me.promptCcdScore(stringXml, ccdData);

		});
	},

	promptCcdScore: function(xml, ccdData){

		var me = this;

		Ext.Msg.show({
			title:'C-CDA Score',
			msg: 'Would you like to see this C-CDA score?',
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn) {
				if(btn === 'yes'){
					me.doCcdScore(xml, ccdData);
				}
			}
		});
	},

	doCcdScore: function (xml, ccdData) {
		CDA_ScoreCard.getScoreDocument(xml, Ext.String.format('{0}, {1} {3} (C-CDA)', ccdData.patient.lname, ccdData.patient.fname, ccdData.patient.title), function (temp_doc) {
			if(temp_doc) {
				app.getController('DocumentViewer').doDocumentView(temp_doc.id, 'temp');
			}
		});
	}

});
