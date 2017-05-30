Ext.define('App.controller.administration.EncounterSnippets', {
	extend: 'Ext.app.Controller',
	requires: [
	],
	refs: [
		{
			ref: 'soapPanel',
			selector: '#soapPanel'
		},
		{
			ref: 'SoapForm',
			selector: '#soapForm'
		},
		{
			ref: 'SoapDxCodesField',
			selector: '#SoapDxCodesField'
		},
		{
			ref: 'SnippetsTreePanel',
			selector: '#SnippetsTreePanel'
		},
		{
			ref: 'SnippetWindow',
			selector: '#SnippetWindow'
		},
		{
			ref: 'SnippetForm',
			selector: '#SnippetForm'
		},
		{
			ref: 'SnippetFormTextField',
			selector: '#SnippetFormTextField'
		},
		{
			ref: 'SnippetDeleteBtn',
			selector: '#SnippetDeleteBtn'
		},
		{
			ref: 'SnippetCancelBtn',
			selector: '#SnippetCancelBtn'
		},
		{
			ref: 'SnippetSaveBtn',
			selector: '#SnippetSaveBtn'
		},

		// templates specialties combo
		{
			ref: 'SoapTemplateSpecialtiesCombo',
			selector: '#SoapTemplateSpecialtiesCombo'
		}
	],

	init: function(){
		var me = this;

		this.control({
			'viewport': {
				'beforeencounterload': me.onOpenEncounter
			},
			'#SnippetsTreePanel': {
				itemdblclick: me.onSnippetsTreePanelItemDblClick
			},
			'#SnippetDeleteBtn': {
				click: me.onSnippetDeleteBtnClick
			},
			'#SnippetSaveBtn': {
				click: me.onSnippetSaveBtnClick
			},
			'#SnippetCancelBtn': {
				click: me.onSnippetCancelBtnClick
			},
			'#SnippetAddBtn': {
				click: me.onSnippetAddBtnClick
			},
			'#soapProcedureWindow > form > textarea': {
				focus: me.onProcedureTextFieldFocus
			},
			'#SoapTemplateSpecialtiesCombo': {
				select: me.onSoapTemplateSpecialtiesComboChange,
				change: me.onSoapTemplateSpecialtiesComboChange
			}
		});
	},

	onOpenEncounter: function(encounter){
		this.getSoapTemplateSpecialtiesCombo().setValue(encounter.data.specialty_id);
	},

	onSoapTemplateSpecialtiesComboChange: function(cmb){
		this.loadSnippets();
	},

	loadSnippets: function() {
		var me = this;

		if (me.getSnippetsTreePanel().collapsed === false) {
			var templates = me.getSnippetsTreePanel(),
				specialty_id = me.getSoapTemplateSpecialtiesCombo().getValue();


			templates.getStore().load({
				filters: [
					{
						property: 'specialty_id',
						value: specialty_id
					},
					{
						property: 'uid',
						value: app.user.id
					},
					{
						property: 'uid',
						value: 0
					},
					{
						property: 'uid',
						value: null
					}
				]
			});
		}
	},

	/**
	 *
	 * @param view
	 * @param record
	 */
	onSnippetsTreePanelItemDblClick: function(view, record){

		var me = this,
			formPanel = me.getSoapForm(),
			form = formPanel.getForm(),
			fields = ['subjective', 'objective', 'assessment', 'instructions'];

		fields.forEach(function (field_name) {
			var field = form.findField(field_name),
				snippet = record.get(field_name),
				inputEl = field.inputEl.dom,
				value = field.getValue(),
				glue = !value[0] || value[0] === ' ' ? '' : ' ';

			if(snippet === '') return;

			me.setCursorPos(inputEl, value.length);
			document.execCommand("insertText", false, glue + snippet);
		});

		var diagnoses = record.get('diagnoses');

		if(diagnoses === '') return;

		diagnoses = diagnoses.split(',');

		var dxField = me.getSoapPanel().dxField;

		DiagnosisCodes.getICDDataByCodes(diagnoses, function (response) {

			response.forEach(function (data) {
				dxField.doAddIcd({
					code: data.code,
					code_text: data.code_text,
					code_type: data.code_type
				});
			});
		});
	},

	setCursorPos: function(input, start, end){
		input.focus();
		if(arguments.length < 3) end = start;
		if("selectionStart" in input){
			input.selectionStart = start;
			input.selectionEnd = end;
		}else if(input.createTextRange){
			var rng = input.createTextRange();
			rng.moveStart("character", start);
			rng.collapse();
			rng.moveEnd("character", end - start);
			rng.select();
		}
	},

	/**
	 *
	 * @param node
	 * @param data
	 * @param overModel
	 */
	onSnippetDrop: function(node, data, overModel){
		var me = this, pos = 10;

		say(node);
		say(data);
		say(overModel);

		// for(var i = 0; i < overModel.parentNode.childNodes.length; i++){
		// 	overModel.parentNode.childNodes[i].set({pos: pos});
		// 	pos = pos + 10;
		// }
		// me.snippetStore.sync();
	},


	onSnippetDeleteBtnClick: function(){
		var me = this,
			form = me.getSnippetForm().getForm(),
			record = form.getRecord();

		// TODO ask to make sure

		me.getSnippetsTreePanel().getStore().remove(record);
		form.reset(true);
		me.getSnippetWindow().close()
	},

	onSnippetSaveBtnClick: function(){
		var me = this,
			win = me.getSnippetWindow(),
			form = me.getSnippetForm().getForm(),
			values = form.getValues(),
			record = form.getRecord();

		if(!form.isValid())  return;

		record.set(values);

		if(Ext.Object.isEmpty(record.getChanges())){
			form.reset();
			win.close();
			return;
		}

		record.store.sync({
			callback: function(){
				form.reset();
				win.close();
				app.msg(_('sweet'), _('record_saved'));
			}
		});
	},

	onSnippetCancelBtnClick: function(){
		this.getSnippetsTreePanel().getStore().rejectChanges();
		this.getSnippetWindow().close();

	},

	showSnippetEditWindow: function(){
		if(!this.getSnippetWindow()){
			Ext.create('App.view.patient.encounter.Snippets');
		}
		return this.getSnippetWindow().show();
	},

	onSnippetAddBtnClick: function(){
		var me = this,
			store =  me.getSnippetsTreePanel().getStore();

		var records = store.add({
			uid: app.user.id,
			specialty_id: me.getSoapTemplateSpecialtiesCombo().getValue()
		});

		me.showSnippetEditWindow();
		me.getSnippetForm().getForm().loadRecord(records[0]);
	},

	onSnippetBtnEdit: function(grid, rowIndex, colIndex, actionItem, event, record){
		this.showSnippetEditWindow();
		this.getSnippetForm().getForm().loadRecord(record);
	}

});