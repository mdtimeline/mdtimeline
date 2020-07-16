Ext.define('App.controller.patient.encounter.Procedure', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'EncounterProcedureWindow',
			selector: '#EncounterProcedureWindow'
		},
		{
			ref: 'EncounterProcedureForm',
			selector: '#EncounterProcedureForm'
		},
		{
			ref: 'EncounterProcedureSnomedProblemSearch',
			selector: '#EncounterProcedureSnomedProblemSearch'
		},
		{
			ref: 'EncounterProcedureSnomedSiteSearch',
			selector: '#EncounterProcedureSnomedSiteSearch'
		},
		{
			ref: 'EncounterProcedureGrid',
			selector: '#EncounterProcedureGrid'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#EncounterProcedureSnomedProcedureSearch': {
				select: me.onEEncounterProcedureSnomedProcedureSearchSelect
			},
			'#EncounterProcedureSnomedSiteSearch': {
				select: me.onEncounterProcedureSnomedSiteSearchSelect
			},
			'#EncounterProcedureStatusField': {
				select: me.onEncounterProcedureStatusFieldSelect
			},
			'#EncounterProcedureFormSaveBtn': {
				click: me.onEncounterProcedureFormSaveBtnClick
			},
			'#EncounterProcedureFormCancelBtn': {
				click: me.onEncounterProcedureFormCancelBtnClick
			},
			'#EncounterProcedureGrid': {
				itemdblclick: me.onEncounterProcedureGridItemDblClick
			},
			'#EncounterProcedureGridAddBtn': {
				click: me.onEncounterProcedureGridAddBtnClick
			},
			'#EncounterProcedureUnableToPerformField': {
				select: me.onEncounterProcedureUnableToPerformFieldSelect
			}
		});

	},

	onEncounterProcedureUnableToPerformFieldSelect: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord(),
			selected_record = combo.findRecordByValue(combo.getValue());

		form_record.set({
			not_performed_code: selected_record.get('code'),
			not_performed_code_type: selected_record.get('code_type'),
			not_performed_code_text: selected_record.get('option_name'),
		});
	},

	onEEncounterProcedureSnomedProcedureSearchSelect: function(cmb, selection){
		var soap_record = cmb.up('form').getForm().getRecord();
		soap_record.set({
			code: selection[0].get('ConceptId'),
			code_type: selection[0].get('CodeType'),
			code_text: selection[0].get('Term')
		});
	},

	onEncounterProcedureSnomedSiteSearchSelect: function(cmb, selection){
		var soap_record = cmb.up('form').getForm().getRecord();
		soap_record.set({
			target_site_code: selection[0].get('ConceptId'),
			target_site_code_type: selection[0].get('CodeType'),
			target_site_code_text: selection[0].get('Term')
		});
	},

	onEncounterProcedureStatusFieldSelect: function(cmb, selection){
		var record = cmb.up('form').getForm().getRecord();
		record.set({
			status_code: selection[0].get('code'),
			status_code_type: selection[0].get('code_type'),
			status_code_text: selection[0].get('code_type')
		});
	},

	onEncounterProcedureFormSaveBtnClick: function(btn){
		var win = this.getEncounterProcedureWindow(),
			form = this.getEncounterProcedureForm().getForm(),
			values = form.getValues(),
			record = form.getRecord(),
			encounter_ctl = app.getController('patient.encounter.Encounter');

		if(!form.isValid()) return;

		record.set(values);

		if(Ext.Object.isEmpty(record.getChanges())){
			form.reset();
			win.close();
		}else{
			record.store.sync({
				callback: function () {
					form.reset();
					win.close();
					encounter_ctl.getProgressNote();
				}
			});
		}

	},

	onEncounterProcedureFormCancelBtnClick: function(btn){
		var win = this.EncounterProcedureWindow(),
			form = this.getEncounterProcedureForm().getForm(),
			record = form.getRecord();

		record.store.rejectChanges();
		form.reset();
		win.close();

	},

	onEncounterProcedureGridItemDblClick: function(grid, procedure_record){
		this.showEncounterProcedureWindow();
		this.getEncounterProcedureForm().getForm().loadRecord(procedure_record);

	},

	onEncounterProcedureGridAddBtnClick: function(btn){

		var gird = this.getEncounterProcedureGrid(),
			store = gird.getStore(),
			procedure_record = Ext.create('App.model.patient.encounter.Procedures', {
			pid: app.patient.pid,
			eid: app.patient.eid,
			create_uid: app.user.id,
			update_uid: app.user.id,
			create_date: new Date(),
			update_date: new Date()
		});

		store.add(procedure_record);

		this.onEncounterProcedureGridItemDblClick(gird, procedure_record);
	},

	showEncounterProcedureWindow: function () {
		if(!this.getEncounterProcedureWindow()){
			Ext.create('App.view.patient.windows.EncounterProcedureWindow');
		}
		return this.getEncounterProcedureWindow().show();
	}

});
