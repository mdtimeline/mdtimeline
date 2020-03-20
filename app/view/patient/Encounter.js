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

Ext.define('App.view.patient.Encounter', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('encounter'),
	pageLayout: 'border',
	itemId: 'encounterPanel',
	requires: [
		'App.store.patient.Encounters',
		'App.store.patient.Vitals',
        'App.store.administration.EncounterEventHistory',
		'App.view.patient.encounter.SOAP',
		'App.view.patient.encounter.HealthCareFinancingAdministrationOptions',
		'App.view.patient.encounter.CurrentProceduralTerminology',
		'App.view.patient.encounter.ProgressNotesHistory',
		'App.view.patient.encounter.DictationPanel',
		'App.view.patient.encounter.NursesNotesGrid',
		'App.view.patient.ProgressNote',
		'App.view.patient.DecisionSupportWarningPanel',
		'App.ux.combo.EncounterPriority',
		'App.ux.combo.ActiveProviders'
	],

	enableCPT: eval(g('enable_encounter_cpt')),
	enableHCFA: eval(g('enable_encounter_hcfa')),
	enableSOAP: eval(g('enable_encounter_soap')),
	enableNurseNotes: true, // eval(g('enable_encounter_nurse_notes')),
	enableVitals: eval(g('enable_encounter_vitals')),
	enableEncHistory: eval(g('enable_encounter_history')),
	enableFamilyHistory: eval(g('enable_encounter_family_history')),
	enableItemsToReview: eval(g('enable_encounter_items_to_review')),
	enableReviewOfSystem: eval(g('enable_encounter_review_of_systems')),
	enableReviewOfSystemChecks: eval(g('enable_encounter_review_of_systems_cks')),
	enableClinicalDecisionSupport: eval(g('enable_clinical_decision_support')),

	showRating: true,
	conversionMethod: 'english',

	pid: null,
	eid: null,
	encounter: null,

	currEncounterStartDate: null,
	initComponent: function(){
		var me = this;

		say('ReviewOfSystems');
		say(App.model.patient.ReviewOfSystems);
		App.model.patient.ReviewOfSystems.getFields().forEach(function(field){
			field.useNull = true;
		});


		me.renderAdministrative = a('access_enc_hcfa') || a('access_enc_cpt') || a('access_enc_history');
		me.encounterCtrl = app.getController('patient.encounter.Encounter');

		me.timerTask = {
			scope: me,
			run: function(){
				me.encounterTimer();
			},
			interval: 1000 //1 second
		};

		/**
		 * stores
		 * @type {*}
		 */
		me.encounterStore = Ext.create('App.store.patient.Encounters', {
			listeners: {
				scope: me,
				datachanged: me.getProgressNote
			}
		});

        me.encounterEventHistoryStore = Ext.create('App.store.administration.EncounterEventHistory');

		if(me.renderAdministrative){
			me.centerPanel = Ext.create('Ext.tab.Panel', {
				region: 'center',
				margin: '1 0 0 0',
				activeTab: 0,
				bodyPadding: 5,
				listeners: {
					render: function(){
						this.items.each(function(i){
							i.tab.on('click', function(){
								me.onTapPanelChange(this);
							});
						});
					}
				}
			});
		}else{
			me.centerPanel = Ext.create('Ext.panel.Panel', {
				region: 'center',
				margin: '1 0 0 0',
				layout: 'fit',
				bodyPadding: 5
			});
		}

		/**
		 * Encounter Tab Panel and its Panels...
		 * @type {*}
		 */
		me.encounterTabPanel = me.centerPanel.add(
			Ext.create('Ext.tab.Panel', {
				title: me.renderAdministrative ? _('encounter') : false,
				itemId: 'encounter',
				plain: true,
				activeItem: 0,
				border: false,
				action: 'encounterTabPanel',
				defaults: {
					bodyStyle: 'padding:15px',
					bodyBorder: true,
					layout: 'fit'
				}
			})
		);

		if(me.enableClinicalDecisionSupport && a('access_clinical_decision_support')){
			me.encounterTabPanel.addDocked({
				xtype: 'decisionsupportwarningpanel',
				itemId: 'DecisionSupportWarningPanel',
				dock: 'top'
			});
		}

		if(me.enableVitals && a('access_patient_vitals')){
			me.vitalsPanel = me.encounterTabPanel.add(
				Ext.create('App.view.patient.Vitals')
			);
		}

		//if(me.enableNurseNotes && a('access_enc_nurse_notes')){
			me.encounterTabPanel.add(
				Ext.create('App.view.patient.encounter.NursesNotesGrid', {
					padding: 0,
					bodyPadding: 0
				})
			);
		//}

		if(me.enableReviewOfSystem && a('access_review_of_systems')){
			me.reviewSysPanel = me.encounterTabPanel.add(
				Ext.create('Ext.form.Panel', {
					autoScroll: true,
					action: 'encounter',
					itemId: 'ReviewOfSystemForm',
					title: _('review_of_systems'),
					frame: true,
					bodyPadding: 5,
					//bodyStyle: 'background-color:white',
					fieldDefaults: {
						msgTarget: 'side'
					},

					plugins: {
						ptype: 'advanceform',
						autoSync: g('autosave'),
						syncAcl: a('edit_encounters')
					},
					buttons: [
						{
							xtype: 'button',
							icon: 'resources/images/icons/gear.png',
							itemId: 'ReviewOfSystemSetupBtn',
							minWidth: null
						},
						'->',
						{
							text: _('save'),
							iconCls: 'save',
							action: 'reviewOfSystems',
							scope: me,
							itemId: 'encounterRecordAdd',
							handler: me.onEncounterUpdate
						}
					]
				})
			);
		}

		if(me.enableReviewOfSystemChecks && a('access_review_of_systems_checks')){
			me.reviewSysCkPanel = me.encounterTabPanel.add(
				Ext.create('Ext.form.Panel', {
					autoScroll: true,
					action: 'encounter',
					title: _('review_of_systems_checks'),
					frame: true,
					bodyPadding: 5,
					bodyStyle: 'background-color:white',
					fieldDefaults: {
						msgTarget: 'side'
					},
					plugins: {
						ptype: 'advanceform',
						autoSync: g('autosave'),
						syncAcl: a('edit_encounters')
					},
					buttons: [
						{
							text: _('save'),
							iconCls: 'save',
							action: 'reviewOfSystemsChecks',
							scope: me,
							itemId: 'encounterRecordAdd',
							handler: me.onEncounterUpdate
						}
					]
				})
			);
		}

		if(me.enableItemsToReview && a('access_itmes_to_review')){
			me.itemsToReview = me.encounterTabPanel.add(
				Ext.create('App.view.patient.ItemsToReview', {
					title: _('items_to_review'),
					bodyPadding: '7 5 2 5'
				})
			);
		}

		if(a('access_patient_psy_behavioral')){
			me.encounterTabPanel.add(
				Ext.create('App.view.patient.SocialPsychologicalBehavioral', {
					itemId: 'SocialPsychologicalBehavioralPanel',
					bodyPadding: '7 5 2 5'
				})
			);
		}

		if(a('access_patient_mini_mental_state_exam')){
			me.encounterTabPanel.add(
				Ext.create('App.view.patient.MiniMentalStateExam', {
					padding: 0,
					bodyPadding: 0
				})
			);
		}

		if(me.enableSOAP && a('access_soap')){
			me.soapPanel = me.encounterTabPanel.add(
				Ext.create('App.view.patient.encounter.SOAP', {
					bodyStyle: 'padding:0',
					enc: me
				})
			);
		}

		if(me.enableSOAP && a('access_dictation')){
			me.dicatationPanel = me.encounterTabPanel.add(
				Ext.create('App.view.patient.encounter.DictationPanel', {
					bodyStyle: 'padding:0',
					enc: me
				})
			);
		}

		/**
		 * Administravive Tab Panel and its Panels
		 * @type {*}
		 */
		if((me.enableHCFA && a('access_enc_hcfa')) ||
			(me.enableCPT && a('access_enc_cpt')) ||
			(me.enableEncHistory && a('access_enc_history'))){
			me.administrativeTabPanel = me.centerPanel.add(
				Ext.create('Ext.tab.Panel', {
					title: _('administrative'),
					itemId: 'administrative',
					plain: true,
					activeItem: 0,
					defaults: {
						bodyStyle: 'padding:15px',
						bodyBorder: true,
						layout: 'fit'
					}
				})
			);
		}

		if(me.enableHCFA && a('access_enc_hcfa')){
			me.MiscBillingOptionsPanel = me.administrativeTabPanel.add(
				Ext.create('App.view.patient.encounter.HealthCareFinancingAdministrationOptions', {
					autoScroll: true,
					title: _('misc_billing_options_HCFA_1500'),
					frame: true,
					bodyPadding: 5,
					bodyStyle: 'background-color:white',
					fieldDefaults: {
						msgTarget: 'side'
					},
					plugins: {
						ptype: 'advanceform',
						autoSync: g('autosave'),
						syncAcl: a('edit_enc_hcfa')
					},
					buttons: [
						{
							text: _('save'),
							iconCls: 'save',
							action: 'soap',
							scope: me,
							handler: me.onEncounterUpdate
						}
					]
				})
			);
		}

		if(me.enableCPT && a('access_enc_cpt')){
			me.CurrentProceduralTerminology = me.administrativeTabPanel.add(
				Ext.create('App.view.patient.encounter.CurrentProceduralTerminology', {
					title: _('current_procedural_terminology')
				})
			);
		}

		if(me.enableEncHistory && a('access_enc_history')){
			me.EncounterEventHistory = me.administrativeTabPanel.add(
				Ext.create('App.ux.grid.EventHistory', {
					bodyStyle: 0,
					title: _('encounter_history'),
					store: me.encounterEventHistoryStore
				})
			);
		}

		/**
		 * Progress Note
		 */
		me.rightPanel = Ext.create('Ext.tab.Panel', {
			title: _('encounter_progress_note'),
			width: 500,
			region: 'east',
			split: true,
			collapsible: true,
			animCollapse: false,
			collapsed: true,
			deferredRender: false,
			bodyPadding: 0,
			margin: 0,
			padding: 0,
			stateful: true,
			stateId: 'EncounterProgressNotesPanel',
			itemId: 'EncounterProgressNotesPanel',
			listeners: {
				scope: this,
				collapse: me.progressNoteCollapseExpand,
				expand: me.progressNoteCollapseExpand
			},
			items: [
				{
					xtype:'progressnoteshistory',
					itemId: 'EncounterProgressNotesHistoryGrid'
				},
				me.progressNote = Ext.create('App.view.patient.ProgressNote', {
					title: _('progress_note'),
					itemId: 'EncounterProgressNotePanel',
					autoScroll: true,
					bodyPadding: 0,
					margin: 0,
					padding: 0,
					cls: 'progress-motes-history',
					tbar: [
						'->',
						{
							xtype: 'button',
							type: 'print',
							text: _('print'),
							icon: 'resources/images/icons/printer.png',
							tooltip: _('print_progress_note'),
							scope: me,
							handler: function(){
								var win = window.open(
                                    'print.html',
                                    'win',
                                    'left=20,top=20,width=700,height=700,toolbar=0,resizable=1,location=1,scrollbars=1,menubar=0,directories=0'
                                );
								var dom = me.progressNote.body.dom;
								var wrap = document.createElement('div');
								var html = wrap.appendChild(dom.cloneNode(true));
								win.document.write(html.innerHTML);
								Ext.Function.defer(function(){
									win.print();
								}, 1000);
							}
						}
					]
				}),
				{
					xtype: 'patientdocumentspanel',
					orientation: 'east'
				}
			]
		});

		var medical_btns = app.getController('patient.Medical').getMedicalTabButtons();

		Ext.Array.push(medical_btns, [
			'-',
			'->',
			'-',
			{
				xtype:'button',
				action: 'ccda',
				itemId: 'EncounterCDAImportBtn',
				tooltip: _('ccda_import'),
				icon: 'resources/images/icons/icoOutbox.png'
			},
			'-',
			{
				xtype:'button',
				itemId: 'EncounterNewProgressNoteBtn',
				text: _('new_progress_note')
			},
			'-',
			{
				xtype:'button',
				action: 'encounter',
				text: _('encounter_details')
			},
			'-',
			me.priorityCombo = Ext.create('App.ux.combo.EncounterPriority', {
				listeners: {
					scope: me,
					select: me.prioritySelect
				}
			}),
			'-'
		]);

		me.panelToolBar = Ext.create('Ext.toolbar.Toolbar', {
			dock: 'top',
			itemId: 'EncounterMedicalToolbar',
			defaults: {
				scope: me,
				handler: me.onToolbarBtnHandler
			},
			items: medical_btns
		});

		if(a('access_encounter_checkout')){
			me.panelToolBar.add({
				text: _('sign'),
				icon: 'resources/images/icons/edit.png',
				handler: me.onSignEncounter
			}, '-');
		}

		me.pageBody = [me.centerPanel, me.rightPanel];

		me.listeners = {
			beforerender: me.beforePanelRender
		};

		me.callParent();
		me.down('panel').addDocked(me.panelToolBar);

	},

	/**
	 * opens the Medical window
	 * @param btn
	 */
	onToolbarBtnHandler: function(btn){
		if(btn.action === 'encounter'){
			app.updateEncounter(this.encounter);
		}else if(btn.action === 'ccda'){
			// this will be handled at controller/CCDImport.js
		}else if(btn.itemId === 'EncounterPatientDocumentsBtn'){
			// do nothing
		}else{
			app.onMedicalWin(btn.action);
		}
	},

	/**
	 * opens the Chart window
	 */
	onChartWindowShow: function(){
		app.onChartsWin();
	},

	prioritySelect: function(cmb, records){
		this.changeEncounterPriority(records[0].data.option_value);
	},

	changeEncounterPriority: function(priority){
		var me = this, params = {
			pid: me.pid,
			eid: me.eid,
			priority: priority
		};
		Encounter.updateEncounterPriority(params, function(){
			app.patientButtonRemoveCls();
			app.patientBtn.addCls(priority);
		});
		me.getProgressNote();
	},

	/**
	 * Sends the data to the server to be saved.
	 * This function needs the button action to determine
	 * which form  to save.
	 * @param SaveBtn
	 */
	onEncounterUpdate: function(SaveBtn){
		var me = this,
			form,
            values,
            store,
            record,
            storeIndex;

		if(SaveBtn.action === "encounter"){
			form = me.newEncounterWindow.down('form').getForm();
		}else{
			form = SaveBtn.up('form').getForm();
		}

		if(form.isValid()){
			values = form.getValues(false, true);

			if(SaveBtn.action === 'encounter'){

				if(a('add_encounters')){
					record = form.getRecord();
					record.set(values);
					record.save({
						callback: function(record){
							var data = record.data;
							app.patientButtonRemoveCls();
							app.patientBtn.addCls(data.priority);
							me.openEncounter(data.eid);
							SaveBtn.up('window').hide();
						}
					});
				}else{
					SaveBtn.up('window').close();
					app.accessDenied();
				}

			}else{

				if(a('edit_encounters')){
					record = form.getRecord();
					store = record.store;
					values = me.addDefaultData(values);
					record.set(values);
					app.fireEvent('encounterbeforesync', me, store, form, record);
					store.sync({
						callback: function(){
							app.fireEvent('encountersync', me, store, form, record);
							me.msg(_('sweet'), _('encounter_updated'));
						}
					});

					me.encounterEventHistoryStore.load({
						filters: [
							{
								property: 'eid',
								value: me.eid
							}
						]
					});

				}else{
					app.accessDenied();
				}
			}
		}
	},

	/**
	 * Takes the form data to be send and adds the default
	 * data used by every encounter form. For example
	 * pid (Patient ID), eid (Encounter ID), uid (User ID),
	 * and date (Current datetime as 00-00-00 00:00:00)
	 * @param data
	 */
	addDefaultData: function(data){
		data.pid = this.pid;
		data.eid = this.eid;
		data.uid = user.id;
		data.date = Ext.Date.format(new Date(), 'Y-m-d H:i:s');
		return data;
	},

	/**
	 *
	 * @param eid
	 */
	openEncounter: function(eid){
		var me = this,
			vitals,
			record,
			store;

		me.el.mask(_('loading...') + ' ' + _('encounter') + ' - ' + eid);
		me.resetTabs();

		if(me.encounter) delete me.encounter;

		App.model.patient.Encounter.load(eid, {
			scope: me,
			callback: function(record){
				var timer,
					data;

				if(record.get('is_private') && record.get('provider_uid') !== app.user.id && !a('global_private_encounter_access')){
					// go back...
					app.msg('Private Encounter', Ext.String.format('Encounter can only be accessed by {0}, {1}', record.get('provider_lname'), record.get('provider_fname')), true);
					app.getController('Navigation').goBack();
					return;
				}

				me.encounter = record;
				data = me.encounter.data;

				// set pid globally for convenient use
				me.pid = data.pid;
				me.eid = data.eid;

				me.currEncounterStartDate = data.service_date;

				app.fireEvent('beforeencounterload', me.encounter, me);

				/** get progress note **/
				me.getProgressNote();
				var patient = app.patient;

				me.pageTitle = _('encounter');

				if(!data.close_date){
					me.pageTitle =  patient.name + ' - ' + patient.sexSymbol + ' - ' + patient.age.str + ' - ' + Ext.Date.format(data.service_date, 'F j, Y, g:i:s a');
					if(me.encounter.get('is_private')){
						me.pageTitle += ' <img height="20px" src="resources/images/icons/icoShield.png" data-qtip="Private Encounter">';
					}
					me.startTimer();
					me.setButtonsDisabled(me.getButtonsToDisable());
				}else{
					if(me.stopTimer()){
						timer = me.timer(data.service_date, data.close_date);
						me.pageTitle =  patient.name + ' - ' + patient.sexSymbol + ' - ' + patient.age.str + ' - ' + Ext.Date.format(data.service_date, 'F j, Y, g:i:s a') + ' (' + _('closed_encounter') + ')';
						if(me.encounter.get('is_private')){
							me.pageTitle += ' <img src="resources/images/icons/icoShield.png">';
						}
						me.updateTitle(me.pageTitle, app.patient.readOnly, timer);
						me.setButtonsDisabled(me.getButtonsToDisable(), true);
					}
				}

				if(me.reviewSysPanel){
					store = me.encounter.reviewofsystems();
					store.on('write', me.getProgressNote, me);
					me.reviewSysPanel.getForm().loadRecord(store.getAt(0));
				}

				if(me.reviewSysCkPanel){
					store = me.encounter.reviewofsystemschecks();
					store.on('write', me.getProgressNote, me);
					me.reviewSysCkPanel.getForm().loadRecord(store.getAt(0));
				}

				if(me.soapPanel){
					store = me.encounter.soap();
					store.on('write', me.getProgressNote, me);
					me.soapPanel.down('form').getForm().loadRecord(store.getAt(0));
				}

				if(me.MiscBillingOptionsPanel){
					store = me.encounter.hcfaoptions();
					me.MiscBillingOptionsPanel.getForm().loadRecord(store.getAt(0));
				}

				me.priorityCombo.setValue(data.priority);

				me.encounterEventHistoryStore.load({
					filters: [
						{
							property: 'eid', value: me.eid
						}
					]
				});

				if(me.CurrentProceduralTerminology){
					me.CurrentProceduralTerminology.encounterCptStoreLoad(me.pid, me.eid, function(){
						me.CurrentProceduralTerminology.setDefaultQRCptCodes();
					});
				}

				App.app.getController('patient.ProgressNotesHistory').loadPatientProgressHistory(data.pid, data.eid);

				me.encounterCtrl.setEncounterClose(record);

				app.fireEvent('encounterload', me.encounter, me);
				me.el.unmask();

			}
		});

	},

	/**
	 * Function to close the encounter..
	 */
	doSignEncounter: function(isSupervisor, callback){
		var me = this,
			form,
			values;

		if(app.fireEvent('beforeecountersign', me, me.encounter) === false) return;

		me.passwordVerificationWin(function(btn, password){
			if(btn === 'ok'){

				form = app.checkoutWindow.down('form').getForm();
				values = form.getValues();
				values.eid = me.eid;
				values.close_date = new Date();
				values.signature = password;
				values.isSupervisor = isSupervisor;

				if(a('require_enc_supervisor') || isSupervisor){
					var cmb = app.checkoutWindow.query('#EncounterCoSignSupervisorCombo')[0];
					values.requires_supervisor = true;
					values.supervisor_uid = cmb.getValue();
				}else if(!isSupervisor && !a('require_enc_supervisor')){
					values.requires_supervisor = false;
				}

				Encounter.signEncounter(values, function(provider, response){
                    var params;
					if(response.result.success){

						app.fireEvent('aferecountersign', me, me.encounter);

						if(me.stopTimer()){

							/** default data for notes and reminder **/
							params = {
								pid: me.pid,
								eid: me.eid,
								uid: app.user.id,
								type: 'checkout',
								date: new Date()
							};

							/** create a new note if not blank **/
							params.body = values.note;
							if(params.body !== '') Ext.create('App.model.patient.Notes', params).save();
							/** create a new reminder if not blank **/
							params.body = values.reminder;
							if(params.body !== '') Ext.create('App.model.patient.Reminders', params).save();

							me.msg(_('sweet'), _('encounter_closed'));
							app.checkoutWindow.close();
							app.stowPatientRecord();
							app.getController('areas.PatientPoolAreas').doSendPatientToNextArea(me.pid);

						}
					}else{
						Ext.Msg.show({
							title: _('oops'),
							msg: _(response.result.error),
							buttons: Ext.Msg.OK,
							icon: Ext.Msg.ERROR
						});
					}
				});
			}
		});
	},

	/**
	 * CheckOut Functions
	 */
	onSignEncounter: function(){
		var title = app.patient.name + ' #' + app.patient.pid + ' - ' + Ext.Date.format(this.currEncounterStartDate, 'F j, Y, g:i:s a') + ' (' + _('checkout') + ')';
		app.checkoutWindow.enc = this;
		app.checkoutWindow.setTitle(title);
		app.checkoutWindow.show();
	},

	isClose: function(){
		return typeof this.encounter.data.close_date != 'undefined' && this.encounter.data.close_date != null;
	},

	isSigned: function(){
		return typeof this.encounter.data.provider_uid != 'undefined' && this.encounter.data.provider_uid != null && this.encounter.data.provider_uid != 0;
	},

	/**
	 * listen for the progress note panel and runs the
	 * doLayout function to re-adjust the dimensions.
	 */
	progressNoteCollapseExpand: function(){
		this.centerPanel.doLayout();
	},

	getProgressNote: function(){
		var me = this;
		Encounter.getProgressNoteByEid(me.eid, function(provider, response){
			me.progressNote.tpl.overwrite(me.progressNote.body, response.result);
		});
	},

	onTapPanelChange: function(panel){
		//if(panel.card.itemId == 'encounter'){
		//	this.setEncounterProgressCollapsed(false);
		//}else{
		//	this.setEncounterProgressCollapsed(true);
		//}
	},

	//setEncounterProgressCollapsed: function(ans){
	//	//ans ? this.rightPanel.collapse() : this.rightPanel.expand();
	//},

	//***************************************************************************************************//
	//***************************************************************************************************//
	//*********    *****  ******    ****** **************************************************************//
	//*********  *  ****  ****  ***  ***** **************************************************************//
	//*********  **  ***  ***  *****  **** **************************************************************//
	//*********  ***  **  ***  *****  **** **************************************************************//
	//*********  ****  *  ****  ***  ********************************************************************//
	//*********  *****    *****    ******* **************************************************************//
	//***************************************************************************************************//
	//***************************************************************************************************//

	/**
	 * Start the timerTask
	 */
	startTimer: function(){
		Ext.TaskManager.start(this.timerTask);
		return true;
	},

	/**
	 * stops the timerTask
	 */
	stopTimer: function(){
		Ext.TaskManager.stop(this.timerTask);
		return true;
	},

	/**
	 * This will update the timer every sec
	 */
	encounterTimer: function(){
		var me = this, timer = me.timer(me.currEncounterStartDate, new Date());
		if(app.patient.pid !== null){
			me.updateTitle(me.pageTitle, app.patient.readOnly, timer);
		}else{
			me.stopTimer();
		}
	},

	/**
	 * This function use the "start time" and "stop time"
	 * and gets the time elapsed between the two then
	 * returns it as a timer (00:00:00)  or (1 day(s) 00:00:00)
	 * if more than 24 hrs
	 *
	 * @param start
	 * @param stop
	 */
	timer: function(start, stop){
		var ms = Ext.Date.getElapsed(start, stop), t, sec = Math.floor(ms / 1000);

		function twoDigit(d){
			return (d >= 10) ? d : '0' + d;
		}

		var min = Math.floor(sec / 60);
		sec = sec % 60;
		t = twoDigit(sec);
		var hr = Math.floor(min / 60);
		min = min % 60;
		t = twoDigit(min) + ":" + t;
		var day = Math.floor(hr / 24);
		hr = hr % 24;
		t = twoDigit(hr) + ":" + t;
		t = (day == 0 ) ? '<span class="time">' + t + '</span>' : '<span class="day">' + day + ' ' + _('day_s') + '</span><span class="time">' + t + '</span>';
		return t;
	},

	/**
	 * After this panel is render add the forms and listeners for conventions
	 */
	beforePanelRender: function(){
		var me = this,
			form,
			defaultFields = function(){
				return [
					{
						name: 'id',
						type: 'int'
					},
					{
						name: 'pid',
						type: 'int'
					},
					{
						name: 'eid',
						type: 'int'
					},
					{
						name: 'uid',
						type: 'int'
					}
				]
			};

		/**
		 * Get 'Review of Systems' Form and define the Model using the form fields
		 */
		if(me.reviewSysPanel){
			me.getFormItems(me.reviewSysPanel, 8, function(){

			});
		}

		/**
		 * Get 'Review of Systems Check' Form and define the Model using the form fields
		 */
		if(me.reviewSysCkPanel){
			me.getFormItems(me.reviewSysCkPanel, 9, function(){
				var formFields = me.reviewSysCkPanel.getForm().getFields(), modelFields = new defaultFields;
				for(var i = 0; i < formFields.items.length; i++){
					modelFields.push({
						name: formFields.items[i].name,
						type: 'auto'
					});
				}
				Ext.define('App.model.patient.ReviewOfSystemsCheck', {
					extend: 'Ext.data.Model',
					fields: modelFields,
					proxy: {
						type: 'direct',
						api: {
							update: 'Encounter.updateReviewOfSystemsChecks'
						}
					},
					belongsTo: {
						model: 'App.model.patient.Encounter',
						foreignKey: 'eid'
					}
				});
			});
		}
	},

	getButtonsToDisable: function(){
		var me = this,
			buttons = [];

		if(me.ButtonsToDisable === null || typeof me.ButtonsToDisable == 'undefined'){
			if(me.vitalsPanel) buttons.concat(buttons, me.vitalsPanel.query('button'));
			if(me.reviewSysPanel) buttons.concat(buttons, me.reviewSysPanel.query('button'));
			if(me.reviewSysCkPanel) buttons.concat(buttons, me.reviewSysCkPanel.query('button'));
			if(me.soapPanel) buttons.concat(buttons, me.soapPanel.down('form').query('button'));
			if(me.MiscBillingOptionsPanel) buttons.concat(buttons, me.MiscBillingOptionsPanel.query('button'));
			if(me.CurrentProceduralTerminology) buttons.concat(buttons, me.CurrentProceduralTerminology.query('button'));
			if(me.EncounterEventHistory) buttons.concat(buttons, me.EncounterEventHistory.query('button'));
			if(me.newEncounterWindow) buttons.concat(buttons, me.newEncounterWindow.query('button'));
			if(app.checkoutWindow) buttons.concat(buttons, app.checkoutWindow.query('button'));
			me.ButtonsToDisable = buttons;
		}

		return me.ButtonsToDisable;
	},

	resetTabs: function(){
		var me = this;
		if(me.renderAdministrative) me.centerPanel.setActiveTab(0);
		if(me.encounterTabPanel) me.encounterTabPanel.setActiveTab(0);
		if(me.administrativeTabPanel) me.administrativeTabPanel.setActiveTab(0);
		if(me.rightPanel) me.rightPanel.setActiveTab(0);
	},

	onDocumentView: function(grid, rowIndex){
		var rec = grid.getStore().getAt(rowIndex), src = rec.data.url;
		app.onDocumentView(src);
	},

	/**
	 * This function is called from Viewport.js when
	 * this panel is selected in the navigation panel.
	 * place inside this function all the functions you want
	 * to call every this panel becomes active
	 */
	onActive: function(callback){
		var me = this,
			patient = app.patient;

		if(patient.pid && patient.eid){
			me.updateTitle(patient.name + ' (' + _('visits') + ')', patient.readOnly, null);
			me.setReadOnly(patient.readOnly);
			callback(true);
		}else{
			callback(false);
			var msg = patient.eid === null ? 'Please create a new encounter or select one from the patient encounter history' : null;
			me.currPatientError(msg);
		}
	}
});
