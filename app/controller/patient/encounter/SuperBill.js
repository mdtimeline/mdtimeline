Ext.define('App.controller.patient.encounter.SuperBill', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		// super bill stuff
		{
			ref: 'SuperBillGrid',
			selector: 'superbillpanel'
		},
		{
			ref: 'SuperBillServiceAddBtn',
			selector: '#SuperBillServiceAddBtn'
		},
		{
			ref: 'SuperBillEncounterDxCombo',
			selector: '#SuperBillEncounterDxCombo'
		},
		{
			ref: 'SuperBillPoceduresQuickPick',
			selector: '#SuperBillPoceduresQuickPick'
		},
		{
			ref: 'SuperBillPoceduresQuickPickForm',
			selector: '#SuperBillPoceduresQuickPickForm'
		}
	],

	init: function(){
		var me = this;

		this.control({
			'viewport': {
				immunizationedit: me.onImmunizationEdit
			},
			'superbillpanel': {
				beforeedit: me.onSuperBillGridBeforeEdit
			},
			'#SuperBillServiceAddBtn': {
				click: me.onSuperBillServiceAddBtnClick
			},
			'#SuperCptSearchCmb': {
				select: me.onSuperCptSearchCmbSelect
			},
			'#SuperBillServiceQuickPickBtn': {
				click: me.onSuperBillServiceQuickPickBtnClick
			},
			'#SuperBillPoceduresQuickPickCancelBtn': {
				click: me.onSuperBillPoceduresQuickPickCancelBtnClick
			},
			'#SuperBillPoceduresQuickPickSaveBtn': {
				click: me.onSuperBillPoceduresQuickPickSaveBtnClick
			}
		});


		me.encounterCtl = this.getController('patient.encounter.Encounter');
	},

	onImmunizationEdit:function(controller, record){
		var serviceRecords = this.getServiceFormEncounterRecord('cvx', record.data.id);

		if(serviceRecords.length == 0){
			this.promptAddService(record, 'cvx');
		}
	},

	promptAddService: function(record, type){
		var me = this;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('super_bill_prompt_add_question'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){

				if(btn == 'yes'){
					me.addService(record, type);
				}
			}
		});
	},

	addService: function(record, type){
		var me = this;

		if(type == 'cvx'){
			Immunizations.getCptByCvx(record.data.code, function(services){
				if(services.length == 0){
					app.msg(_('oops'), _('no_service_code_found'), true);
				} else if(services.length == 1){
					me.doAddService(record, type, services[0]);
				}else{

				}
			});
		}

	},

	doAddService: function(record, type, service, callback){
		var store = this.ecounterCtl.getEncounterRecord().services(),
			serviceData = {
				pid: record.data.pid,
				eid: record.data.eid,
				units: 1,
				reference_type: type,
				reference_id: record.data.id,
				code: service ? service.data.code : record.data.code,
				code_type: service ? service.data.code_type : record.data.code_type,
				code_text: service ? service.data.code_text : record.data.code_text,
				create_uid: app.user.id,
				date_create: new Date()
			};

		if(record.data.tooth){
			serviceData.tooth = record.data.tooth;
		}

		if(record.data.surfaceString){
			serviceData.surface = record.data.surfaceString;
		}

		if(record.data.cavity_quadrant){
			serviceData.cavity_quadrant = record.data.cavity_quadrant;
		}

		var records = store.add(serviceData);

		store.sync({
			callback:function(){
				app.msg(_('sweet'), _('service_added'));
				if(typeof callback == 'function') callback(records[0]);
			}
		});
	},

	getServiceRecord: function(reference_id){
		var store = this.getController('patient.encounter.Encounter').getEncounterRecord().services();

		return store.getById(reference_id);
	},

	onSuperBillGridBeforeEdit: function(plugin, context){

		this.getSuperBillEncounterDxCombo().getStore().load({
			filters: [
				{
					property: 'eid',
					value: context.record.data.eid
				}
			]
		});
	},

	// super bill stuff
	onSuperBillServiceAddBtnClick: function(){
		var me = this,
			grid = me.getSuperBillGrid(),
			store = grid.getStore(),
			encounter = me.getController('patient.encounter.EncounterSign').encounter;

		grid.editingPlugin.cancelEdit();
		var records = store.add({
			pid: encounter.data.pid,
			eid: encounter.data.eid,
			units: 1,
			create_uid: app.user.id,
			date_create: new Date()
		});
		grid.editingPlugin.startEdit(records[0], 0);
	},

	onSuperCptSearchCmbSelect: function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();

		record.set({
			code: records[0].data.code,
			code_type: records[0].data.code_type
		});
	},

	onRemoveService: function(record){
		var me = this;

		//TODO: handle the remove logic
		record.destroy();

	},

	reconfigureSupperBillGrid: function(store){
		this.getSuperBillGrid().reconfigure(store);
	},

	getServiceFormEncounterRecord: function(referenceType, referenceId){
		var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord(),
			services = [];

		if(encounter == null) return services;

		var store = encounter.services(),
			records = store.data.items,
			len = store.data.items.length;

		for(var i = 0; i < len; i++){
			var record = records[i];

			if(record.data.reference_type == referenceType && record.data.reference_id == referenceId){
				Ext.Array.push(services, record);
			}
		}

		return services;
	},

	onSuperBillServiceQuickPickBtnClick: function(){

		this.showoSuperBillPoceduresQuickPick();
		var form_panel = this.getSuperBillPoceduresQuickPickForm(),
			encounter_record = this.encounterCtl.getEncounterRecord();

		form_panel.getForm().reset();
		this.loadQuickProceduresFields(form_panel, encounter_record.get('specialty_id'))

	},

	onSuperBillPoceduresQuickPickCancelBtnClick: function(){
		this.getSuperBillPoceduresQuickPickForm().getForm().reset();
		this.getSuperBillPoceduresQuickPick().close();
	},

	onSuperBillPoceduresQuickPickSaveBtnClick: function(){

		var me = this,
			form_panel = me.getSuperBillPoceduresQuickPickForm(),
			form = form_panel.getForm(),
			values = form.getValues(),
			encounter_record = this.encounterCtl.getEncounterRecord(),
			pid = encounter_record.get('pid'),
			eid = encounter_record.get('eid'),
			services = [];

		if(!values.services || encounter_record == null) return;

		values.services.forEach(function (service) {
			if(service === '0') return;
			var buff = service.split(':');
			services.push({
				pid: pid,
				eid: eid,
				units: 1,
				reference_type: '',
				reference_id: 0,
				code: buff[1],
				code_type: buff[0],
				code_text: buff[2],
				create_uid: app.user.id,
				date_create: app.getDate()
			});

		});

		var services_store = encounter_record.services();
		services_store.add(services);

		form_panel.el.mask(_('saving'));

		services_store.sync({
			callback: function () {
				form_panel.el.unmask();
				me.getSuperBillPoceduresQuickPickForm().getForm().reset();
				me.getSuperBillPoceduresQuickPick().close();
			}
		});

	},

	showoSuperBillPoceduresQuickPick: function(){
		if(!this.getSuperBillPoceduresQuickPick()){
			Ext.create('App.view.patient.windows.SuperBillPoceduresQuickPick');
		}
		return this.getSuperBillPoceduresQuickPick().show();
	},

	loadQuickProceduresFields: function (form_panel, specialty_id) {

		if(form_panel.specialty_id === specialty_id) return;

		form_panel.specialty_id = specialty_id;

		form_panel.removeAll();
		form_panel.el.mask(_('loading'));

		Services.getServicesQuickPickFieldsBySpecialty(1, function (fields) {
			form_panel.add(fields);
			form_panel.el.unmask();

		});

	}


});