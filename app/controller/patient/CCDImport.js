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

Ext.define('App.controller.patient.CCDImport', {
	extend: 'Ext.app.Controller',
	requires: [
        'App.view.patient.windows.PossibleDuplicates'
	],
	refs: [
		{
			ref: 'CcdImportWindow',
			selector: 'ccdimportwindow'
		},
		{
			ref: 'CcdImportPreviewWindow',
			selector: 'ccdimportpreviewwindow'
		},

		// import patient...
		{
			ref: 'CcdImportPatientForm',
			selector: '#CcdImportPatientForm'
		},
		{
			ref: 'CcdImportActiveProblemsGrid',
			selector: '#CcdImportActiveProblemsGrid'
		},
		{
			ref: 'CcdImportMedicationsGrid',
			selector: '#CcdImportMedicationsGrid'
		},
		{
			ref: 'CcdImportAllergiesGrid',
			selector: '#CcdImportAllergiesGrid'
		},


		// marge patient...
		{
			ref: 'CcdPatientPatientForm',
			selector: '#CcdPatientPatientForm'
		},
		{
			ref: 'CcdPatientActiveProblemsGrid',
			selector: '#CcdPatientActiveProblemsGrid'
		},
		{
			ref: 'CcdPatientMedicationsGrid',
			selector: '#CcdPatientMedicationsGrid'
		},
		{
			ref: 'CcdPatientAllergiesGrid',
			selector: '#CcdPatientAllergiesGrid'
		},


		// preview patient...
		{
			ref: 'CcdImportPreviewPatientForm',
			selector: '#CcdImportPreviewPatientForm'
		},
		{
			ref: 'CcdImportPreviewActiveProblemsGrid',
			selector: '#CcdImportPreviewActiveProblemsGrid'
		},
		{
			ref: 'CcdImportPreviewMedicationsGrid',
			selector: '#CcdImportPreviewMedicationsGrid'
		},
		{
			ref: 'CcdImportPreviewAllergiesGrid',
			selector: '#CcdImportPreviewAllergiesGrid'
		},


		// buttons...
		{
			ref: 'CcdImportWindowPreviewBtn',
			selector: '#CcdImportWindowPreviewBtn'
		},
		{
			ref: 'CcdImportWindowImportBtn',
			selector: '#CcdImportWindowImportBtn'
		},
		{
			ref: 'CcdImportWindowCloseBtn',
			selector: '#CcdImportWindowCloseBtn'
		},
		{
			ref: 'CcdImportWindowPatientSearchField',
			selector: '#CcdImportWindowPatientSearchField'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'ccdimportwindow': {
				show: me.onCcdImportWindowShow
			},
			'#CcdImportPreviewWindowImportBtn': {
				click: me.onCcdImportPreviewWindowImportBtnClick
			},
			'#CcdImportWindowPreviewBtn': {
				click: me.onCcdImportWindowPreviewBtnClick
			},
			'#CcdImportWindowCloseBtn': {
				click: me.onCcdImportWindowCloseBtnClick
			},
			'#CcdImportWindowPatientSearchField': {
				select: me.onCcdImportWindowPatientSearchFieldSelect
			},
			'#CcdImportWindowSelectAllField': {
				change: me.onCcdImportWindowSelectAllFieldChange
			},
			'#CcdImportWindowViewRawCcdBtn': {
				click: me.onCcdImportWindowViewRawCcdBtnClick
			},
			'#PossiblePatientDuplicatesWindow > grid': {
				itemdblclick: me.onPossiblePatientDuplicatesGridItemDblClick
			},
			'#PossiblePatientDuplicatesContinueBtn': {
				click: me.onPossiblePatientDuplicatesContinueBtnClick
			},
			'#CcdImportPreviewWindowCancelBtn': {
				click: me.onCcdImportPreviewWindowCancelBtnClick
			}
		});

		me.validatePosibleDuplicates = true;

		me.on('importcomplete', me.doPatientSectionsImportComplete, me);

	},

	isSystemReconciliation: function () {
		return this.getCcdImportWindow().enableSystemReconciliation;
	},

	CcdImport: function(ccdData, mergePid){
		if(!this.getCcdImportWindow()){
			Ext.create('App.view.patient.windows.CCDImport');
		}
		this.getCcdImportWindow().ccdData = ccdData;
		this.getCcdImportWindow().show();

		if(mergePid){
			this.doLoadsystemPatientData(mergePid);
		}

	},

	onCcdImportWindowShow: function(win){
		this.doLoadCcdData(win.ccdData);
	},

    /*
    Event when the CDA Import and Viewer shows up.
    Also will check for duplicates in the database and if a possible duplicate is found
    show the possible duplicate window
     */
	doLoadCcdData: function(data){
		var me = this,
			ccdPatientForm = me.getCcdImportPatientForm().getForm(),
			patient = Ext.create('App.model.patient.Patient', data.patient),
            phone;
        ccdPatientForm.loadRecord(patient);

		if(me.validatePosibleDuplicates){
			App.app.getController('patient.Patient').lookForPossibleDuplicates(
				{
					fname: patient.data.fname,
					lname: patient.data.lname,
					sex: patient.data.sex,
					DOB: patient.data.DOB
				},
				'ccdImportDuplicateAction',
				function(patient){

				}
			);
		}

		// list 59 ethnicity
		// list 14 race
        // phone from Patient Contacts
		if(data.patient.race && data.patient.race !== ''){
			CombosData.getDisplayValueByListIdAndOptionValue(14, data.patient.race, function(response){
                ccdPatientForm.findField('race_text').setValue(response);
			});
		}

		if(data.patient.ethnicity && data.patient.ethnicity !== ''){
			CombosData.getDisplayValueByListIdAndOptionCode(59, data.patient.ethnicity, function(response){
                ccdPatientForm.findField('ethnicity_text').setValue(response);
			});
		}

        if(data.patient.pid && data.patient.pid !== '') {
	        ccdPatientForm.findField('phones').setValue(patient.get('home_phone'));
        }

		if(data){
			if(data.allergies && data.allergies.length > 0){
				me.reconfigureGrid('getCcdImportAllergiesGrid', data.allergies);
			}
			if(data.medications && data.medications.length > 0){
				me.reconfigureGrid('getCcdImportMedicationsGrid', data.medications);
			}
			if(data.problems && data.problems.length > 0){
				me.reconfigureGrid('getCcdImportActiveProblemsGrid', data.problems);
			}
		}
	},

    /*
     Event fired when in the duplicate window data grid double click on an item
     this method will copy the patient information selected from the data grid, into
     the system information panel.
     */
    onPossiblePatientDuplicatesGridItemDblClick:function(grid, record){
        var me = this,
            cmb = me.getCcdImportWindowPatientSearchField(),
            systemPatientForm = me.getCcdPatientPatientForm().getForm(),
            store = cmb.getStore(),
            win = grid.up('window');

        if(win.action != 'ccdImportDuplicateAction') return;

        store.removeAll();
        me.doLoadsystemPatientData(record.data.pid);
        cmb.select(record);
        win.close();
        //me.promptVerifyPatientImport(record);
    },

	reconfigureGrid: function(getter, data){
		var me = this,
			grid = me[getter]();
		grid.getStore().loadRawData(data);
	},

	onCcdImportWindowPatientSearchFieldSelect: function(cmb, records){
		var me = this,
			importPatient = me.getCcdImportPatientForm().getForm().getRecord();

		if(importPatient.data.sex != records[0].data.sex){
			app.msg(_('warning'), _('records_sex_are_not_equal'), true);
		}

		if(importPatient.data.DOB.getFullYear() != records[0].data.DOB.getFullYear() &&
			importPatient.data.DOB.getMonth() != records[0].data.DOB.getMonth() &&
			importPatient.data.DOB.getDate() != records[0].data.DOB.getDate()){
			app.msg(_('warning'), _('records_date_of_birth_are_not_equal'), true);
		}

		me.doLoadsystemPatientData(records[0].data.pid);

	},

	doLoadsystemPatientData: function(pid){
		var me = this,
			pForm = me.getCcdPatientPatientForm().getForm(),
            phone;

		App.model.patient.Patient.load(pid, {
			success: function(patient) {
				pForm.loadRecord(patient);

				if(patient.data.race && patient.data.race !== ''){
					CombosData.getDisplayValueByListIdAndOptionValue(14, patient.data.race, function(response){
						pForm.findField('race_text').setValue(response);
					});
				}

				if(patient.data.ethnicity && patient.data.ethnicity !== ''){
					CombosData.getDisplayValueByListIdAndOptionValue(59, patient.data.ethnicity, function(response){
						pForm.findField('ethnicity_text').setValue(response);
					});
				}


				say(patient);

				pForm.findField('phones').setValue(patient.get('home_phone'));


				me.getCcdPatientMedicationsGrid().reconfigure(patient.medications());
				patient.medications().load({
					params: { reconciled: true }
				});

				me.getCcdPatientAllergiesGrid().reconfigure(patient.allergies());
				patient.allergies().load();

				me.getCcdPatientActiveProblemsGrid().reconfigure(patient.activeproblems());
				patient.activeproblems().load();
			}
		});
	},

	onCcdImportWindowCloseBtnClick: function(){
		this.getCcdImportWindow().close();
	},

	onCcdImportWindowPreviewBtnClick: function(){
		var me = this,

			reconcile = true,

			pForm,
			importPatient = me.getCcdImportPatientForm().getForm().getRecord(),
			importActiveProblems = me.getCcdImportActiveProblemsGrid().getSelectionModel().getSelection(),
			importMedications = me.getCcdImportMedicationsGrid().getSelectionModel().getSelection(),
			importAllergies = me.getCcdImportAllergiesGrid().getSelectionModel().getSelection(),

			systemPatient = me.getCcdPatientPatientForm().getForm().getRecord(),
			systemActiveProblems = me.getCcdPatientActiveProblemsGrid().getStore().data.items,
			systemMedications = me.getCcdPatientMedicationsGrid().getStore().data.items,
			systemAllergies = me.getCcdPatientAllergiesGrid().getStore().data.items,

			systemSelectionActiveProblems = me.getCcdPatientActiveProblemsGrid().getSelectionModel().getSelection(),
			systemSelectionMedications = me.getCcdPatientMedicationsGrid().getSelectionModel().getSelection(),
			systemSelectionAllergies = me.getCcdPatientAllergiesGrid().getSelectionModel().getSelection(),

			systemReconciliationActiveProblems = [],
			systemReconciliationMedications = [],
			systemReconciliationAllergies = [],

			isMerge = systemPatient !== undefined,

			isSystemReconciliation = me.isSystemReconciliation(),

			i, store, records,

            phone;

		// check is merge and nothing is selected
		if(
			isMerge &&
			importActiveProblems.length === 0 &&
			importMedications.length === 0 &&
			importAllergies.length === 0
		){
			app.msg(_('oops'), _('nothing_to_merge'), true);
			return;
		}

		say('import');
		say(importActiveProblems);
		say(importMedications);
		say(importAllergies);

		say('system');
		say(systemActiveProblems);
		say(systemMedications);
		say(systemAllergies);

		if(!me.getCcdImportPreviewWindow()){
			Ext.create('App.view.patient.windows.CCDImportPreview');
		}
		me.getCcdImportPreviewWindow().show();

		pForm = me.getCcdImportPreviewPatientForm().getForm();

		if(isMerge){
			me.getCcdImportPreviewPatientForm().getForm().loadRecord(systemPatient);

			if(systemPatient.data.race && systemPatient.data.race !== ''){
				CombosData.getDisplayValueByListIdAndOptionValue(14, systemPatient.data.race, function(response){
					pForm.findField('race_text').setValue(response);
				});
			}

			if(systemPatient.data.ethnicity && systemPatient.data.ethnicity !== ''){
				CombosData.getDisplayValueByListIdAndOptionValue(59, systemPatient.data.ethnicity, function(response){
					pForm.findField('ethnicity_text').setValue(response);
				});
			}

			pForm.findField('phones').setValue(importPatient.get('home_phone'));

		}else{
			me.getCcdImportPreviewPatientForm().getForm().loadRecord(importPatient);

			if(importPatient.data.race && importPatient.data.race !== ''){
				CombosData.getDisplayValueByListIdAndOptionValue(14, importPatient.data.race, function(response){
					pForm.findField('race_text').setValue(response);
				});
			}

			if(importPatient.data.ethnicity && importPatient.data.ethnicity !== ''){
				CombosData.getDisplayValueByListIdAndOptionCode(59, importPatient.data.ethnicity, function(response){
					pForm.findField('ethnicity_text').setValue(response);
				});
			}

			pForm.findField('phones').setValue(importPatient.get('home_phone'));
		}

		if(reconcile){
			// reconcile active problems
			records = Ext.clone(isSystemReconciliation ? systemSelectionActiveProblems : systemActiveProblems);
			store = me.getCcdPatientActiveProblemsGrid().getStore();
			for(i=0; i < importActiveProblems.length; i++){
				if(store.find('code' , importActiveProblems[i].data.code) !== -1) continue;
				Ext.Array.insert(records, 0, [importActiveProblems[i]]);
			}
			me.getCcdImportPreviewActiveProblemsGrid().getStore().loadRecords(records);

			if(isSystemReconciliation){
				systemReconciliationActiveProblems = Ext.clone(systemActiveProblems);
				// remove is selected
				for (i = 0; i < systemSelectionActiveProblems.length; i++) {
					systemReconciliationActiveProblems = Ext.Array.remove(systemReconciliationActiveProblems, systemSelectionActiveProblems[i]);
				}
			}

			// reconcile medications
			records = Ext.clone(isSystemReconciliation ? systemSelectionMedications : systemMedications);
			store = me.getCcdPatientMedicationsGrid().getStore();
			for(i=0; i < importMedications.length; i++){
				if(store.find('RXCUI' , importMedications[i].data.RXCUI) !== -1) continue;
				Ext.Array.insert(records, 0, [importMedications[i]]);
			}
			me.getCcdImportPreviewMedicationsGrid().getStore().loadRecords(records);

			if(isSystemReconciliation) {
				systemReconciliationMedications = Ext.clone(systemMedications);
				// remove is selected
				for (i = 0; i < systemSelectionMedications.length; i++) {
					systemReconciliationMedications = Ext.Array.remove(systemReconciliationMedications, systemSelectionMedications[i]);
				}
			}

			// reconcile allergies
			records = Ext.clone(isSystemReconciliation ? systemSelectionAllergies : systemAllergies);
			store = me.getCcdPatientAllergiesGrid().getStore();
			for(i=0; i < importAllergies.length; i++){
				if(store.find('allergy_code' , importAllergies[i].data.allergy_code) !== -1) continue;
				Ext.Array.insert(records, 0, [importAllergies[i]]);
			}
			me.getCcdImportPreviewAllergiesGrid().getStore().loadRecords(records);

			if(isSystemReconciliation){
				systemReconciliationAllergies = Ext.clone(systemAllergies);
				// remove is selected
				for (i = 0; i < systemSelectionAllergies.length; i++) {
					systemReconciliationAllergies = Ext.Array.remove(systemReconciliationAllergies, systemSelectionAllergies[i]);
				}
			}

			say('reconcile');
			say(systemReconciliationActiveProblems);
			say(systemReconciliationMedications);
			say(systemReconciliationAllergies);

			var system_reconcile_records = false;
			if(isSystemReconciliation){
				system_reconcile_records = [];
				system_reconcile_records = Ext.Array.merge(system_reconcile_records, systemReconciliationActiveProblems);
				system_reconcile_records = Ext.Array.merge(system_reconcile_records, systemReconciliationMedications);
				system_reconcile_records = Ext.Array.merge(system_reconcile_records, systemReconciliationAllergies);
			}

			me.getCcdImportPreviewWindow().system_reconcile_records = system_reconcile_records;

		}else{
			me.getCcdImportPreviewActiveProblemsGrid().getStore().loadRecords(
				Ext.Array.merge(importActiveProblems, systemActiveProblems)
			);
			me.getCcdImportPreviewMedicationsGrid().getStore().loadRecords(
				Ext.Array.merge(importMedications, systemMedications)
			);
			me.getCcdImportPreviewAllergiesGrid().getStore().loadRecords(
				Ext.Array.merge(importAllergies, systemAllergies)
			);
		}
	},

	onCcdImportPreviewWindowImportBtnClick: function(){
		var me = this,
			patient = me.getCcdImportPreviewPatientForm().getForm().getRecord();

		if(patient.data.pid){
			me.promptVerifyPatientImport(patient);
		}else{
			App.app.getController('patient.Patient').lookForPossibleDuplicates(
				{
					fname: patient.data.fname,
					lname: patient.data.lname,
					sex: patient.data.sex,
					DOB: patient.data.DOB
				},
				'ccdImportDuplicateAction',
				function(records){
					if(records.length === 0){
						me.promptVerifyPatientImport(patient);
					}
				}
			);
		}
	},

	promptVerifyPatientImport:function(patient){
		var me = this;

		Ext.Msg.show({
			title: _('wait'),
			msg: patient.data.pid ? _('patient_merge_verification') : _('patient_import_verification'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if(btn == 'yes'){
					if(patient.data.pid){
						me.doPatientSectionsImport(patient);
					}else{
						me.doPatientImport(patient);
					}
				}
			}
		});
	},

	doPatientImport: function(patient){
		var me = this;

		patient.set({
			create_uid: app.user.id,
			create_date: new Date()
		});

		patient.save({
			callback:function(record, operation, success){
				if(success){
					me.doPatientSectionsImport(record);
				}else{
					app.msg(_('oops'), _('record_error'), true);
				}
			}
		});
	},

	onCcdImportPreviewWindowCancelBtnClick: function(btn){
		btn.up('window').close();
	},

	doPatientSectionsImport: function(patient){
		var me = this,
			system_reconcile_records = me.getCcdImportPreviewWindow().system_reconcile_records;

		if(system_reconcile_records !== false){
			Ext.Msg.show({
				title: _('wait'),
				msg: 'This action will reconcile information in system patient record.<br><br>Whould like to continue?',
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.QUESTION,
				fn: function (btn) {
					if(btn == 'yes'){
						me.doPatientSectionsImportCont(patient, system_reconcile_records);
					}
				}
			});
		}else{
			me.doPatientSectionsImportCont(patient, system_reconcile_records);
		}
	},

	doPatientSectionsImportCont: function(patient, system_reconcile_records){
		var me = this,
			now = new Date(),
			pid = patient.data.pid,
			i,

			// Get all the stores of the dataGrids
			problems = me.getCcdImportPreviewActiveProblemsGrid().getStore().data.items,
			medications = me.getCcdImportPreviewMedicationsGrid().getStore().data.items,
			allergies = me.getCcdImportPreviewAllergiesGrid().getStore().data.items;

		say('doPatientSectionsImport');
		say(problems);
		say(medications);
		say(allergies);
		say('system_reconcile_records');
		say(system_reconcile_records);

		me.importing = true;

		if(system_reconcile_records !== false){
			system_reconcile_records.forEach(function (system_reconcile_record) {
				system_reconcile_record.set({
					reconciled: true,
					reconciled_date: now
				});
				system_reconcile_record.save();
			});
		}

		// Allergies
		for(i = 0; i < allergies.length; i++){

			if(allergies[i].data.id && allergies[i].data.id > 0)  continue;

			allergies[i].set({
				pid: pid,
				created_uid: app.user.id,
				create_date: now
			});
			allergies[i].setDirty();
			allergies[i].save({
				callback: function(){
					me.fireEvent('importcomplete', pid);
				}
			});
		}

		// Medications
		for(i = 0; i < medications.length; i++){

			if(medications[i].data.id && medications[i].data.id > 0)  continue;

			medications[i].set({
				pid: pid,
				created_uid: app.user.id,
				create_date: now
			});
			medications[i].setDirty();
			medications[i].save({
				callback: function(){
					me.fireEvent('importcomplete', pid);
				}
			});
		}

		// Problems
		for(i = 0; i < problems.length; i++){

			if(problems[i].data.id && problems[i].data.id > 0)  continue;

			problems[i].set({
				pid: pid,
				created_uid: app.user.id,
				create_date: now
			});
			problems[i].setDirty();
			problems[i].save({
				callback: function(){
					me.fireEvent('importcomplete', pid);
				}
			});
		}
	},

	addCdaToPatientDocument: function (pid) {
		var me = this;

		record = Ext.create('App.model.patient.PatientDocuments', {
			code: '',
			pid: pid,
			eid: 0,
			uid: app.user.id,
			facility_id: app.user.facility,
			docType: 'C-CDA',
			docTypeCode: 'CD',
			date: new Date(),
			name: 'temp_ccd.xml',
			note: '',
			title: 'C-CDA Imported',
			encrypted: false,
			error_note: '',
			site: app.user.site,
			document: me.getCcdImportWindow().ccd
		});

		record.save({
			callback: function () {
				say(_('sweet'), 'C-CDA Imported');
			}
		})
	},

	doPatientSectionsImportComplete: function (pid) {
		var me = this;

		if(!me.importing) return;

		me.importing = false;

		me.getCcdImportWindow().close();
		me.getCcdImportPreviewWindow().close();

		var panel_cls = app.getActivePanel().$className;

		me.addCdaToPatientDocument(pid);

		if(panel_cls == 'App.view.patient.Encounter'){
			app.setPatient(app.patient.pid, app.patient.eid, null, function(){
				app.getActivePanel().openEncounter(app.patient.eid);
			});

		}else if(panel_cls == 'App.view.patient.Summary') {
			app.setPatient(pid, null, null, function(){
				app.openPatientSummary();
			});
		}

		app.msg(_('sweet'), _('patient_data_imported'));
	},

	onPossiblePatientDuplicatesContinueBtnClick:function(btn){
		if(btn.up('window').action != 'ccdImportDuplicateAction') return;
        if(this.getCcdImportPreviewPatientForm()){
            this.promptVerifyPatientImport(this.getCcdImportPreviewPatientForm().getForm().getRecord());
        }
	},

	onCcdImportWindowSelectAllFieldChange: function(field, selected){
		var me = this,
			grids = me.getCcdImportWindow().query('grid');

		for(var i = 0; i < grids.length; i++){
			var sm = grids[i].getSelectionModel();
			if(selected){
				sm.selectAll();
			}else{
				sm.deselectAll();
			}
		}
	},

	onCcdImportWindowViewRawCcdBtnClick: function(){

		say(this.getCcdImportWindow());
		say(this.getCcdImportWindow().ccd);


		var me = this,
			record = Ext.create('App.model.patient.PatientDocumentsTemp', {
				create_date: new Date(),
				document_name: 'temp_ccd.xml',
				document: me.getCcdImportWindow().ccd
			});

		record.save({
			callback: function(record){
				app.onDocumentView(record.data.id, 'ccd');
			}
		});
	}
});
