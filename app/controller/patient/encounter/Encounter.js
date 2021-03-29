Ext.define('App.controller.patient.encounter.Encounter', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.ux.combo.ActiveSpecialties'
	],
	refs: [
		{
			ref: 'EncounterPanel',
			selector: '#encounterPanel'
		},
		{
			ref: 'EncounterProgressNotePanel',
			selector: '#EncounterProgressNotePanel'
		},
		{
			ref: 'EncounterDetailWindow',
			selector: '#EncounterDetailWindow'
		},
		{
			ref: 'EncounterProviderCmb',
			selector: '#EncounterProviderCmb'
		},
		{
			ref: 'EncounterSpecialtyCmb',
			selector: '#EncounterSpecialtyCmb'
		},
		{
			ref: 'EncounterDetailForm',
			selector: '#EncounterDetailForm'
		},
		{
			ref: 'EncounterTransferWindow',
			selector: '#EncounterTransferWindow'
		},
		{
			ref: 'EncounterTransferPatientSearchField',
			selector: '#EncounterTransferPatientSearchField'
		},
		{
			ref: 'PatientSummaryEncountersPanel',
			selector: '#PatientSummaryEncountersPanel'
		},
		{
			ref: 'EncounterMedicalToolbarSignBtn',
			selector: '#EncounterMedicalToolbarSignBtn'
		},
		{
			ref: 'EncounterMedicalToolbarAddendumAddBtn',
			selector: '#EncounterMedicalToolbarAddendumAddBtn'
		}
	],

	init: function(){
		var me = this;

		this.control({
			'viewport':{
				patientunset: me.onPatientUnset,
				encounterload: me.onEncounterLoad
			},
			'#EncounterDetailForm combobox[name=visit_category]':{
				select: me.onEncounterDetailFormVisitCategoryComboSelect
			},
			'#EncounterDetailForm combobox[name=referring_physician]':{
				beforerender: me.onEncounterDetailFormReferringComboSelect
			},
			'#EncounterDetailWindow': {
				show: me.onEncounterDetailWindowShow
			},
			'#EncounterProviderCmb': {
				beforerender: me.onEncounterProviderCmbBeforeRender,
				select: me.onEncounterProviderCmbSelect
			},
			'#EncounterCDAImportBtn': {
				click: me.onEncounterCDAImportBtnClick
			},
			'#EncounterDeletetBtn': {
				click: me.onEncounterDeleteBtnClick
			},
			'#EncounterTransferBtn': {
				click: me.onEncounterTransferBtnClick
			},
			'#EncounterTransferWindowCancelBtn': {
				click: me.onEncounterTransferWindowCancelBtnClick
			},
			'#EncounterTransferWindowTransferBtn': {
				click: me.onEncounterTransferWindowTransferBtnlick
			},
			'#EncounterNewProgressNoteBtn': {
				click: me.onEncounterNewProgressNoteBtnClick
			},
			'#PatientSummeryNewProgressNoteBtn': {
				click: me.onPatientSummeryNewProgressNoteBtnClick
			}
		});

		me.importCtrl = this.getController('patient.CCDImport');
		me.auditLogCtrl = this.getController('administration.AuditLog');

		//me.showEncounterTransferWindow();
	},

	doNewEncounterWindow: function(default_values){
		var me = this,
			win = me.getEncounterDetailWindow(),
			form = win.down('form').getForm();

		default_values = default_values || {};

		if(a('add_encounters')){
			win.show();
			form.loadRecord(Ext.create('App.model.patient.Encounter', default_values));
		}else{
			app.accessDenied();
		}
	},

	onEncounterNewProgressNoteBtnClick: function(btn){

		var encounter = this.getEncounterPanel().encounter,
			default_values = {
				pid: app.patient.pid,
				parent_eid: app.patient.eid,
				brief_description: Ext.String.format('FOLLOW-UP: {0}',encounter.get('brief_description')),
				open_uid: app.user.id,
				service_date: app.getDate(),
				onset_date: encounter.get('onset_date'),
				patient_class: encounter.get('patient_class'),
				specialty_id: encounter.get('specialty_id'),
				provider_uid: encounter.get('provider_uid'),
				provider_title: encounter.get('provider_title'),
				provider_fname: encounter.get('provider_fname'),
				provider_mname: encounter.get('provider_mname'),
				provider_lname: encounter.get('provider_lname'),
				priority: encounter.get('priority'),
				facility: encounter.get('facility'),
				referring_physician: encounter.get('referring_physician'),
		};

		this.doNewEncounterWindow(default_values);

	},

	onPatientSummeryNewProgressNoteBtnClick: function(){
		var me = this,
			selected_encounters = me.getPatientSummaryEncountersPanel().getSelectionModel().getSelection(),
			encounter;

		if(selected_encounters.length === 0){
			app.msg(_('oops'), _('no_encounter_selected'), true);
			return;
		}

		var encounter = selected_encounters[0],
			default_values = {
				pid: app.patient.pid,
				parent_eid: app.patient.eid,
				brief_description: Ext.String.format('FOLLOW-UP: {0}',encounter.get('brief_description')),
				open_uid: app.user.id,
				service_date: app.getDate(),
				onset_date: encounter.get('onset_date'),
				patient_class: encounter.get('patient_class'),
				specialty_id: encounter.get('specialty_id'),
				provider_uid: encounter.get('provider_uid'),
				provider_title: encounter.get('provider_title'),
				provider_fname: encounter.get('provider_fname'),
				provider_mname: encounter.get('provider_mname'),
				provider_lname: encounter.get('provider_lname'),
				priority: encounter.get('priority'),
				facility: encounter.get('facility'),
				referring_physician: encounter.get('referring_physician'),
			};

		this.doNewEncounterWindow(default_values);




	},

	onEncounterDeleteBtnClick: function (btn) {
		// TODO
	},

	onEncounterTransferBtnClick: function (btn) {
		this.showEncounterTransferWindow();
	},

	onEncounterTransferWindowCancelBtnClick: function (btn) {
		this.getEncounterTransferPatientSearchField().reset();
		this.getEncounterTransferWindow().close();
	},

	onEncounterTransferWindowTransferBtnlick: function (btn) {

		var me = this,
			patient_field = me.getEncounterTransferPatientSearchField(),
			patient_pid = patient_field.getValue(),
			patient_eid = app.patient.eid,
			patient_record = patient_field.findRecordByValue(patient_pid);

		if(!patient_field.isValid()) return;

		me.getEncounterTransferPatientSearchField().reset();
		me.getEncounterTransferWindow().close();


		var from_name = app.patient.name,
			to_name = patient_record.get('fullname');

		Ext.Msg.show({
			title: _('wait'),
			msg: Ext.String.format(_('encounter_transfer_from_x_to_x_msg'), from_name, to_name),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn) {

				if(btn !== 'yes') return;

				me.getEncounterDetailWindow().close();

				Ext.getBody().mask(_('be_right_back'));

				Encounter.TransferEncounter(patient_eid, patient_pid, function (success) {
					Ext.getBody().unmask();

					if(success){
						me.auditLogCtrl.addLog(
							patient_pid,
							app.user.id,
							patient_eid,
							patient_eid,
							'encounters',
							'ENCOUNTER_TRANSFER',
							Ext.String.format('Encounter trasnfer from {0} to {1}', from_name, to_name)
						);

						app.setPatient(patient_pid, patient_eid, app.user.site, function () {
							app.openEncounter(patient_eid);
						});
					}
				});
			}
		});

	},

	showEncounterTransferWindow: function () {
		if(!this.getEncounterTransferWindow()){
			Ext.create('App.view.patient.windows.EncounterTransferWindow');
		}
		return this.getEncounterTransferWindow().show();
	},

	onEncounterLoad: function (encounter, encounter_panel) {
		app.onMedicalWin();
		this.setEncounterState(encounter, encounter_panel);
	},

	setEncounterState: function (encounter_record, encounter_panel){
		// say('setEncounterState');
		// say(encounter_record);
		// say(encounter_panel);

		var me = this;

		app.patient.encounterIsClose = encounter_record.isClose();
		app.patient.encounterAllowEdit = app.user.id == encounter_record.get('provider_uid') || !app.patient.encounterIsClose || app.patient.eid == null;;

		/**
		 * set if encounter can be edited
		 */
		me.setEncounterEditState(app.patient.encounterAllowEdit);

		/**
		 * Set Sign or Addendum Btn
		 */
		me.setEncounterSignAddendumState(app.patient.encounterIsClose);


		/**
		 * Set Not Same Day Warning
		 */
		me.setEncounterSayDayWarningState(encounter_record, encounter_panel);
	},

	setEncounterEditState:function(allowEdit){

		var buttons = Ext.ComponentQuery.query('#encounterRecordAdd, button[action=encounterRecordAdd]'),
			forms = Ext.ComponentQuery.query('#encounterPanel form[advanceFormPlugin]');

		buttons.forEach(function (button) {
			button.setDisabled(!allowEdit);
		});

		forms.forEach(function (form) {
			form.advanceFormPlugin.enabled = allowEdit;
		});
	},

	setEncounterSignAddendumState: function (isClose){
		var signBtn = this.getEncounterMedicalToolbarSignBtn(),
			addendumBtn = this.getEncounterMedicalToolbarAddendumAddBtn();

		if(signBtn){
			signBtn.setVisible(!isClose);
		}
		if(addendumBtn) {
			addendumBtn.setVisible(isClose);
		}
	},

	setEncounterSayDayWarningState: function (encounter_record, encounter_panel){
		if(encounter_record.get('service_date').toLocaleDateString() !== new Date().toLocaleDateString()){
			encounter_panel.encounterTabPanel.ownerCt.addBodyCls('encounter-not-same-day');
			encounter_panel.getPageBodyContainer().addCls('encounter-not-same-day');
			app.msg(_('warning'),_('encounter_service_date_error_msg'), true);
		}else{
			encounter_panel.encounterTabPanel.ownerCt.removeBodyCls('encounter-not-same-day');
			encounter_panel.getPageBodyContainer().removeCls('encounter-not-same-day');
		}
	},

	getProgressNote: function(){
		var me = this,
		    encounterPanel = me.getEncounterPanel();

		Encounter.getProgressNoteByEid(encounterPanel.eid, function(provider, response){
			encounterPanel.progressNote.tpl.overwrite(encounterPanel.progressNote.body, response.result);
		});
	},

	/**
	 * set the encounter record to null when the patient is closed
	 */
	onPatientUnset:function(){
		if(this.getEncounterPanel()) this.getEncounterPanel().encounter = null;
	},

	onEncounterDetailFormVisitCategoryComboSelect: function (combo, records) {
		var encounter_record = combo.up('form').getForm().getRecord();

		encounter_record.set({
			visit_category_code: records[0].get('code'),
			visit_category_code_type: records[0].get('code_type')
		});
	},

	/**
	 * get the encounter record form the encounter panel or return null
	 * @returns {*}
	 */
	getEncounterRecord: function(){
		return !Ext.isEmpty(this.getEncounterPanel()) ? this.getEncounterPanel().encounter : null;
	},

	onEncounterProviderCmbBeforeRender: function(cmb){
		var container = cmb.up('container');

		container.setFieldLabel(''); // label showing bug

		container.insert((container.items.indexOf(cmb) + 1), {
			xtype: 'activespecialtiescombo',
			itemId: 'EncounterSpecialtyCmb',
			fieldLabel: _('specialty'),
			labelWidth: cmb.labelWidth,
			width: cmb.width,
			name: 'specialty_id',
			allowBlank: false
		});

	},

	onEncounterDetailFormReferringComboSelect: function(cmb){

	},

	onEncounterProviderCmbSelect: function(cmb, slected){
		var me = this;

		User.getUser(slected[0].data.option_value, function(provider){
			me.setSpecialtyCombo(provider);
		});
	},

	onEncounterDetailWindowShow: function(){
		var me = this,
			record = me.getEncounterDetailForm().getForm().getRecord();

		if(record.data.provider_uid === 0){
			if(me.getEncounterSpecialtyCmb()) me.getEncounterSpecialtyCmb().setVisible(false);

		}else{
			User.getUser(record.data.provider_uid, function(provider){
				me.setSpecialtyCombo(provider, record.data.specialty_id);
			});
		}

	},

	setSpecialtyCombo: function(provider, specialty){
		var show = this.reloadSpecialityCmbBySpecialty(provider.specialty, specialty);
		this.getEncounterSpecialtyCmb().setVisible(show);
		this.getEncounterSpecialtyCmb().setDisabled(!show);
	},

	reloadSpecialityCmbBySpecialty: function(specialties, specialty){
		var me = this,
			show = false;

		if(Ext.isNumeric(specialty) && specialty > 0){
			me.getEncounterSpecialtyCmb().setValue(eval(specialty));

		}else if(Ext.isArray(specialties) && specialties.length == 1){
			me.getEncounterSpecialtyCmb().setValue(eval(specialties[0]));

		}else{
			me.getEncounterSpecialtyCmb().setValue(null);
		}


		if(Ext.isArray(specialties)){

			var store = this.getEncounterSpecialtyCmb().getStore(),
				filters = [],
				show = true;

			for(var i = 0; i < specialties.length; i++){
				Ext.Array.push(filters, specialties[i]);
			}

			store.clearFilter(true);
			store.filter([
				{
					property: 'active',
					value: true
				},
				{
					property: 'id',
					value: new RegExp(filters.join('|'))
				}
			]);
		}

		return show;
	},

	onEncounterCDAImportBtnClick: function(btn){

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
			me.importCtrl.CcdImport(ccdData, app.patient.pid, stringXml);
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
	},



});
