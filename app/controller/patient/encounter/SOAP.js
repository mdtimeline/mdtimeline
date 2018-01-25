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
		},

		{
			ref: 'EncounterPanel',
			selector: '#encounterPanel'
		},
		{
			ref: 'EncounterDetailForm',
			selector: '#EncounterDetailForm'
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
				render: me.onPanelFormRender,
				write: me.onSoapFormWrite
			},
			'#SoapFormReformatTextBtn': {
				render: me.onSoapFormReformatTextBtnRender,
				click: me.onSoapFormReformatTextBtnClick
			},
			'#soapPanel button[action=speechBtn]': {
				toggle: me.onSpeechBtnToggle
			},
			'#soapForm > fieldset > textarea': {
				focus: me.onSoapTextFieldFocus
			},
			'#soapProcedureWindow > form > textarea': {
				focus: me.onProcedureTextFieldFocus
			},
			'#SoapDxCodesField': {
				recordadd: me.onSoapDxCodesFieldRecordAdd
			}
		});


	},

	onSoapFormReformatTextBtnRender: function () {
		app.on('KEY-ALT-W', this.onSoapFormReformatTextBtnClick, this);
	},

	onSoapFormReformatTextBtnClick: function () {
		var me = this,
			form = me.getSoapForm().getForm(),
			subjectiveField = form.findField('subjective'),
			objectiveField = form.findField('objective'),
			assessmentField = form.findField('assessment'),
			instructionsField = form.findField('instructions');

		// say('onSoapFormReformatTextBtnClick');
		// say(me);
		// say(form);
		// say(subjectiveField);
		// say(objectiveField);
		// say(assessmentField);
		// say(instructionsField);

		subjectiveField.setValue(me.textParser(subjectiveField.getValue(),false,false,true,true,true,true));
		objectiveField.setValue(me.textParser(objectiveField.getValue(),false,false,true,true,true,true));
		assessmentField.setValue(me.textParser(assessmentField.getValue(),false,false,true,true,true,true));
		instructionsField.setValue(me.textParser(instructionsField.getValue(),false,false,true,true,true,true));

	},

	onSoapDxCodesFieldRecordAdd: function (field, record) {
		if(this.getSoapForm().autoSync) this.getSoapDxCodesField().sync();

	},

	doChiefComplaintHandler: function (soap_record) {
		var encounter_record = this.getEncounterPanel().encounter;
		encounter_record.set({brief_description: soap_record.get('chief_complaint')});
		if(Ext.Object.isEmpty(encounter_record.getChanges())) return;
		encounter_record.save();
	},

	onSoapFormWrite: function (store, operation) {
		this.doChiefComplaintHandler(operation.records[0]);
	},

	onEncounterBeforeSync: function(panel, store, form, record){

		if(form.owner.itemId !== 'soapForm') return;

		this.doChiefComplaintHandler(record);

		this.getSoapDxCodesField().sync();

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
				icon: 'modules/worklist/resources/images/wand.png',
				itemId: 'SoapFormReformatTextBtn',
				tooltip: 'Reformat Text :: ALT-W',
				minWidth: null
			},
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
	},

	textParser:  function (report, upperFullTitle, capsFullText, fixEndSentences, fixStartSentences, fixEndSentences2Spaces, fixTextAfterColon) {

		report = report.replace(/^[a-z]/, function(str){
			return str.toUpperCase();
		});

		if(upperFullTitle){
			report = report.replace(/.*:.*/g, function(str){
				return str.toUpperCase();
			});
		}else{
			report = report.replace(/.*:/g, function(str){
				return str.toUpperCase();
			});
		}

		if(capsFullText){
			report = report.replace(/[.\n]\s*[a-z]/g, function(str){
				return str.toUpperCase();
			});
		}

		if(fixEndSentences){
			if(fixEndSentences2Spaces){
				report = report.replace(/([a-z]\.)( |)([a-z])/gi, function(str, p1, p2, p3){
					return p1 + '  ' + p3.toUpperCase();
				});
			}else{
				report = report.replace(/([a-z]\.)(  |)([a-z])/gi, function(str, p1, p2, p3){
					return p1 + ' ' + p3.toUpperCase();
				});
			}
		}

		if(fixStartSentences){
			report = report.replace(/([a-z]\.)( |  )([a-z])/gi, function(str, p1, p2, p3){
				return p1 + '  ' + p3.toUpperCase();
			});

			report = report.replace(/(\r|\n|\r\n)([a-z])/gi, function(str, p1, p2, p3){
				return p1 + p2.toUpperCase();
			});
		}

		if(fixTextAfterColon){
			report = report.replace(/:( |  )[a-z]/g, function(str){
				return str.toUpperCase();
			});
		}



		return report;
	}

});
