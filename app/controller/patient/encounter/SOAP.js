Ext.define('App.controller.patient.encounter.SOAP', {
	extend: 'Ext.app.Controller',

	// defaults
	recognition: null,
	speechAction: null,
	recognizing: false,
	isError: false,

	final_transcript: '',
	interim_transcript: '',

	field: {
		name: 'subjective'
	},

	refs: [
		{
			ref: 'Viewport',
			selector: 'viewport'
		},
		{
			ref: 'SoapPanel',
			selector: '#soapPanel'
		},
		{
			ref: 'SoapForm',
			selector: '#soapPanel #soapForm'
		},
		{
			ref: 'SoapProcedureWindow',
			selector: '#soapProcedureWindow'
		},
		{
			ref: 'SoapProcedureForm',
			selector: '#soapProcedureWindow > form'
		},
		{
			ref: 'SnippetsTreePanel',
			selector: '#soapPanel #SnippetsTreePanel'
		},
		{
			ref: 'SpeechBtn',
			selector: '#soapPanel button[action=speechBtn]'
		},
		{
			ref: 'EncounterProgressNotesPanel',
			selector: '#EncounterProgressNotesPanel'
		},
		{
			ref: 'SoapDxCodesField',
			selector: '#SoapDxCodesField'
		},

		// templates specialties combo
		{
			ref: 'SoapTemplateSpecialtiesCombo',
			selector: '#SoapTemplateSpecialtiesCombo'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				'encounterbeforesync': me.onEncounterBeforeSync
			},
			'#soapPanel': {
				beforerender: me.onPanelBeforeRender
				//activate: me.onPanelActive,
				//deactivate: me.onPanelDeActive
			},
			'#soapPanel #soapForm': {
				render: me.onPanelFormRender
			},
			'#soapPanel button[action=speechBtn]': {
				toggle: me.onSpeechBtnToggle
			},
			'#soapForm > fieldset > textarea': {
				focus: me.onSoapTextFieldFocus
			},
			'#soapProcedureWindow > form > textarea': {
				focus: me.onProcedureTextFieldFocus
			}
		});
	},

	onEncounterBeforeSync: function(panel, store, form){
		if(form.owner.itemId == 'soapForm'){
			this.getSoapDxCodesField().sync();
		}
	},

	onSoapTextFieldFocus: function(field){
		this.field = field;

		if(!Ext.isWebKit) return;
		this.final_transcript = field.getValue();
		this.interim_transcript = '';

		if(this.field.tip) return;

		this.field.tip = Ext.create('Ext.tip.ToolTip', {
			target: this.field.el,
			anchor: 'top',
			anchorOffset: 85,
			disabled: true,
			html: 'Press this button to clear the form'
		});
	},

	onProcedureTextFieldFocus: function(field){
		this.field = field;

		if(!Ext.isWebKit) return;
		this.final_transcript = field.getValue();
		this.interim_transcript = '';
	},

	onPanelBeforeRender: function(panel){
		if(!Ext.isWebKit) return;

		var btn = [
			{
				xtype: 'button',
				action: 'speechBtn',
				iconCls: 'speech-icon-inactive',
				enableToggle: true,
				minWidth: null
			},
			{ xtype: 'tbfill' }
		];

		panel.down('form').getDockedItems('toolbar[dock="bottom"]')[0].insert(0, btn);
	},

	onPanelFormRender: function(panel){
		Ext.widget('careplangoalsnewwindow', {
			constrainTo: panel.el.dom
		});
	},

	onSpeechBtnToggle: function(btn, pressed){
		if(pressed){
			this.initSpeech();
		}else{
			this.stopSpeech();
		}
	},

	stopSpeech: function(){
		this.recognition.stop();
		this.final_transcript = '';
		this.interim_transcript = '';
		delete this.recognition;
	},

	initSpeech: function(){
		var me = this;
		if(me.recognition) me.stopSpeech();
		me.final_transcript = me.field.getValue();
		me.recognition = new webkitSpeechRecognition();
		me.recognition.continuous = true;
		me.recognition.interimResults = true;
		me.recognition.lang = app.user.localization;

		me.recognition.onstart = function(){
			me.recognizing = true;
			me.setRecordButton(true);
		};

		me.recognition.onerror = function(event){
			me.recognizing = false;
			me.isError = true;
			me.setRecordButton(false);
		};

		me.recognition.onend = function(){
			me.recognizing = false;
			me.setRecordButton(false);
		};

		me.recognition.onresult = function(event){
			for(var i = event.resultIndex; i < event.results.length; ++i){
				if(event.results[i].isFinal){
					if(me.field.tip){
						me.field.tip.hide();
						me.field.tip.setDisabled(true);
					}
					me.field.setValue(me.field.getValue() + event.results[i][0].transcript);
				}else{
					if(me.field.tip){
						me.field.tip.setDisabled(false);
						me.field.tip.update(event.results[i][0].transcript);
						me.field.tip.show();
					}
				}
			}
		};

		me.recognition.start();
	},

	setRecordButton: function(recording){
		this.getSpeechBtn().setIconCls(recording ? 'speech-icon-active' : 'speech-icon-inactive');
	}

});
