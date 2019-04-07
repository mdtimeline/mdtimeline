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

Ext.define('App.controller.administration.EncounterTemplatePanels', {
	extend: 'Ext.app.Controller',

	uses: [
		'App.ux.LiveRadsSearch',
		'App.ux.LiveLabsSearch',
	],

	refs: [
		{
			ref: 'TemplatePanelsWindow',
			selector: '#TemplatePanelsWindow'
		},
		{
			ref: 'TemplatePanelsGrid',
			selector: '#TemplatePanelsGrid'
		},
		{
			ref: 'TemplatePanelsCombo',
			selector: '#TemplatePanelsCombo'
		},
		{
			ref: 'SoapTemplatesBtn',
			selector: '#SoapTemplatesBtn'
		},
		{
			ref: 'encounterPanel',
			selector: '#encounterPanel'
		},
		{
			ref: 'encounterPanel',
			selector: '#encounterPanel'
		},
		{
			ref: 'soapPanel',
			selector: '#soapPanel'
		},
		{
			ref: 'soapForm',
			selector: '#soapForm'
		},

		// Admin
		{
			ref: 'AdministrationEncounterTemplatesPanel',
			selector: '#AdministrationEncounterTemplatesPanel'
		},
		{
			ref: 'AdministrationEncounterTemplatesGrid',
			selector: '#AdministrationEncounterTemplatesGrid'
		},
		{
			ref: 'AdministrationEncounterTemplateWindow',
			selector: '#AdministrationEncounterTemplateWindow'
		},
		{
			ref: 'AdministrationEncounterTemplateForm',
			selector: '#AdministrationEncounterTemplateForm'
		},
		{
			ref: 'AdministrationEncounterTemplateGrid',
			selector: '#AdministrationEncounterTemplateGrid'
		},
	],

	init: function () {
		var me = this;

		me.control({
			'viewport': {
				encounterload: me.onEncounterLoad
			},
			'#encounterPanel': {
				// activate: me.onSoapPanelActivate,
				beforerender: me.onEncounterPanelBeforeRender
			},
			'#TemplatePanelsCombo': {
				select: me.onTemplatePanelsComboSelect
			},
			'#SoapTemplatesBtn': {
				click: me.onSoapTemplatesBtnClick
			},
			'#TemplatePanelsAddBtn': {
				click: me.onTemplatePanelsAddBtnClick
			},
			'#TemplatePanelsCancelBtn': {
				click: me.onTemplatePanelsCancelBtnClick
			},

			// Admin
			'#AdministrationEncounterTemplatesPanel': {
				activate: me.onAdministrationEncounterTemplatesPanelActivate
			},
			'#AdministrationEncounterTemplatesGrid': {
				itemdblclick: me.onAdministrationEncounterTemplatesGridItemDblClick
			},
			'#AdministrationEncounterTemplateForm': {
				loadrecord: me.onAdministrationEncounterTemplateFormLoadRecord
			},
			'#EncounterTemplatesAddBtn': {
				click: me.onEncounterTemplatesAddBtnClick
			},
			'#AdministrationEncounterTemplateRadOrderAddBtn': {
				click: me.onAdministrationEncounterTemplateRadOrderAddBtnClick
			},
			'#AdministrationEncounterTemplateLabOrderAddBtn': {
				click: me.onAdministrationEncounterTemplateLabOrderAddBtnClick
			},
			'#AdministrationEncounterTemplateCancelBtn': {
				click: me.onAdministrationEncounterTemplateCancelBtnClick
			},
			'#AdministrationEncounterTemplateSaveBtn': {
				click: me.onAdministrationEncounterTemplateSaveBtnClick
			}
		});

	},

	onEncounterLoad: function (encounter) {

		if (!this.getTemplatePanelsWindow()) {
			Ext.create('App.view.patient.windows.EncounterTemplatePanels');
		}

		var me = this,
			store = me.getTemplatePanelsCombo().getStore(),
			btn = me.getSoapTemplatesBtn();

		store.load({
			filters: [
				{
					property: 'specialty_id',
					value: encounter.get('specialty_id')
				},
				{
					property: 'active',
					value: 1
				}
			],
			callback: function (records) {
				if (records.length > 0) {
					btn.disabled = false;
					btn.setDisabled(false);
					btn.setTooltip(_('clinical_templates'));
				} else {
					btn.disabled = true;
					btn.setDisabled(true);
					btn.setTooltip(_('no_templates_found'));
				}
			}
		});
	},

	onEncounterPanelBeforeRender: function () {
		this.getSoapForm().getDockedItems('toolbar[dock="bottom"]')[0].insert(0, [{
			xtype: 'button',
			text: _('templates'),
			itemId: 'SoapTemplatesBtn'
		}]);
	},

	// onSoapPanelActivate: function () {
	// 	var hasTemplates = this.getTemplatePanelsCombo().getStore().data.items.length > 0,
	// 		btn = this.getSoapTemplatesBtn();
	//
	// 	if (hasTemplates) {
	// 		btn.disabled = false;
	// 		btn.setDisabled(false);
	// 		btn.setTooltip(_('clinical_templates'));
	// 	} else {
	// 		btn.disabled = true;
	// 		btn.setDisabled(true);
	// 		btn.setTooltip(_('no_templates_found'));
	// 	}
	//
	// },

	onSoapTemplatesBtnClick: function () {
		this.doTemplatePanelsWindowShow();
	},

	onTemplatePanelsComboSelect: function (cmb, records) {
		var me = this,
			grid = me.getTemplatePanelsGrid(),
			sm = grid.getSelectionModel(),
			store = records[0].templates();

		grid.reconfigure(store);
		store.load({
			callback: function () {
				sm.selectAll();
			}
		});
	},

	doTemplatePanelsWindowShow: function () {
		this.getTemplatePanelsGrid().getStore().removeAll();
		this.getTemplatePanelsCombo().reset();
		return this.getTemplatePanelsWindow().show();
	},

	onTemplatePanelsAddBtnClick: function () {
		var me = this,
			cmb = me.getTemplatePanelsCombo(),
			records = me.getTemplatePanelsGrid().getSelectionModel().getSelection();

		if (!cmb.isValid()) return;

		if (records.length === 0) {
			app.msg(_('oops'), _('no_templates_to_add'), true);
			return;
		}

		Ext.Msg.show({
			title: _('wait'),
			msg: _('add_templates_message'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn) {
				if (btn == 'yes') {
					me.doAddTemplates(records);
					me.getTemplatePanelsWindow().close();
				}
			}
		});
	},

	doAddTemplates: function (templates) {

		app.onMedicalWin();

		templates.forEach(function (template) {

			var type = template.get('template_type'),
				data = eval('(' + template.get('template_data') + ')');

			if (!data) {
				say('Error: data eval issue -- ' + data);
				return;
			}

			data.pid = app.patient.pid;
			data.eid = app.patient.eid;
			data.uid = app.user.id;
			data.date_ordered = new Date();

			switch (type) {

				case 'LAB':
					App.app.getController('patient.LabOrders').doAddOrderByTemplate(data);
					break;
				case 'RAD':
					App.app.getController('patient.RadOrders').doAddOrderByTemplate(data);
					break;
				case 'RX':
					App.app.getController('patient.RxOrders').doAddOrderByTemplate(data);
					break;
				case 'REF':
					App.app.getController('patient.Referrals').doAddReferralByTemplate(data);
					break;
				default:
					say('Error: no template_type found -- ' + type);
					break;
			}

		});


		Ext.Function.defer(function () {
			app.getActivePanel().getProgressNote();
		}, 500);


	},

	onTemplatePanelsCancelBtnClick: function () {
		this.getTemplatePanelsWindow().close();
	},

	getLabTemplate: function (code, description) {
		return {
			pid: 0,
			eid: 0,
			uid: 0,
			code: code, //                  58410-2,    24356-8,    24321-2,    34529-8
			code_type: "LOINC",
			date_collected: null,
			date_ordered: '',
			description: description, //    CBC,        U/A,        BMP         PT & PPT
			hl7_recipient_id: 0,
			note: "",
			order_type: "lab",
			priority: "Normal",
			status: "Pending",
			type: "Laboratory",
			void: false,
			void_comment: ""
		};
	},

	getRadTemplate: function (code, description) {
		return {
			pid: 0,
			eid: 0,
			uid: 0,
			code: code,//               24650-4,                        36572-6
			code_type: "LOINC",
			date_collected: null,
			date_ordered: '',
			description: description,// Chest X-Ray AP & Lateral,       Chest X-Ray AP 1 View
			hl7_recipient_id: 0,
			note: "",
			order_type: "rad",
			priority: "Normal",
			status: "Pending",
			type: "Radiology",
			void: false,
			void_comment: ""
		};
	},

	getRefTemplate: function (service_code, service_description, referal_reason) {

		return {
			pid: 0,
			eid: 0,
			refer_by: 0,
			refer_by_text: '',
			referral_date: '',
			create_uid: 0,
			update_uid: 0,
			create_date: null,
			update_date: null,

			is_external_referral: true,
			refer_to: '',
			refer_to_text: '',

			send_record: false,
			service_code: service_code || '',   // 29303009
			service_code_type: "SNOMED-CT",
			service_text: service_description || '', // Electrocardiographic procedure (procedure)
			risk_level: "low",
			referal_reason: referal_reason || '', // PRE-OP
			diagnosis_code: "",
			diagnosis_code_type: "",
			diagnosis_text: ""

		};
	},


	onAdministrationEncounterTemplatesPanelActivate: function(panel){
		this.getAdministrationEncounterTemplatesGrid().getStore().load();
	},

	onEncounterTemplatesAddBtnClick: function(){
		this.showEncounterTemplateWindow();
		this.getAdministrationEncounterTemplateForm().getForm().loadRecord(
			Ext.create('App.model.administration.EncounterTemplatePanel')
		);
	},

	onAdministrationEncounterTemplatesGridItemDblClick: function(gird, record){
		this.showEncounterTemplateWindow();
		this.getAdministrationEncounterTemplateForm().getForm().loadRecord(record);
	},

	onAdministrationEncounterTemplateFormLoadRecord: function(form, record){
		say('onAdministrationEncounterTemplateFormLoadRecord');
		say(form);
		say(record);

		var store = this.getAdministrationEncounterTemplateGrid().getStore();

		if(record.get('id') > 0){
			store.load({
				filters: [
					{
						property: 'panel_id',
						value: record.get('id')
					}
				]
			});
		} else {
			store.removeAll();
			store.commitChanges();
		}
	},

	onAdministrationEncounterTemplateRadOrderAddBtnClick: function(btn){
		this.getRadOrderWindow();
	},

	onAdministrationEncounterTemplateLabOrderAddBtnClick: function(btn){
		this.getLadOrderWindow();
	},

	onAdministrationEncounterTemplateCancelBtnClick: function(btn){
		this.getAdministrationEncounterTemplateForm().getForm().reset(true);
		this.getAdministrationEncounterTemplateWindow().close();
	},

	onAdministrationEncounterTemplateSaveBtnClick: function(btn){

		var me = this,
			form = this.getAdministrationEncounterTemplateForm().getForm(),
			template_record = form.getRecord(),
			template_values = form.getValues();

		if(!form.isValid()) return;

		template_record.set(template_values);

		var template_record_changes = template_record.getChanges();

		if(!Ext.Object.isEmpty(template_record_changes)){
			template_record.save({
				callback: function (record) {
					me.onAdministrationEncounterTemplateItemsSave(template_record);
				}
			});
		}else{
			me.onAdministrationEncounterTemplateItemsSave(template_record);
		}

	},

	onAdministrationEncounterTemplateItemsSave: function(template_record){
		var me = this,
			items_store = this.getAdministrationEncounterTemplateGrid().getStore(),
			items_modified_records = items_store.getModifiedRecords();


		items_modified_records.forEach(function (items_modified_record) {
			items_modified_record.set({panel_id: template_record.get('id')});
		});

		if(items_modified_records.length > 0){

			items_store.sync({
				callback: function () {
					me.getAdministrationEncounterTemplateForm().getForm().reset(true);
					me.getAdministrationEncounterTemplateWindow().close();
				}
			});

		}else{
			me.getAdministrationEncounterTemplateForm().getForm().reset(true);
			me.getAdministrationEncounterTemplateWindow().close();
		}








	},


	showEncounterTemplateWindow: function () {

		if(!this.getAdministrationEncounterTemplateWindow()){
			Ext.create('App.view.administration.EncounterTemplateWindow');
		}
		return this.getAdministrationEncounterTemplateWindow().show();
	},

	addAdministrationEncounterTemplateGridRecord: function(description, template_type, template_data){
		var store = this.getAdministrationEncounterTemplateGrid().getStore();


		store.add({
			description: description,
			template_type: template_type,
			template_data: JSON.stringify(template_data),
			active: 1,
		});

	},

	getRadOrderWindow: function () {

		var me = this;

		Ext.create('Ext.window.Window',{
			resizable: false,
			items:[
				{
					xtype: 'form',
					bodyPadding: 10,
					items: [
						{
							xtype: 'radslivetsearch',
							fieldLabel: _('study'),
							labelAlign: 'top',
							hideLabel: false,
							width: 300
						}
					]
				}
			],
			buttons:[
				{
					xtype: 'button',
					text: _('cancel'),
					handler: function () {
						this.up('window').close();
					}
				},
				{
					xtype: 'button',
					text: _('add'),
					handler: function () {
						var field = this.up('window').down('radslivetsearch'),
							record = field.findRecordByValue(field.getValue());

						if(record){
							var tpl = me.getRadTemplate(record.get('loinc_number'), record.get('loinc_name'));
							me.addAdministrationEncounterTemplateGridRecord(tpl.description, 'RAD', tpl);
						}

						this.up('window').close();
					}
				}
			]
		}).show();
	},

	getLadOrderWindow: function () {

		var me = this;

		Ext.create('Ext.window.Window',{
			resizable: false,
			items:[
				{
					xtype: 'form',
					bodyPadding: 10,
					items: [
						{
							xtype: 'labslivetsearch',
							fieldLabel: _('study'),
							labelAlign: 'top',
							hideLabel: false,
							width: 300
						}
					]
				}
			],
			buttons:[
				{
					xtype: 'button',
					text: _('cancel'),
					handler: function () {
						this.up('window').close();
					}
				},
				{
					xtype: 'button',
					text: _('add'),
					handler: function () {
						var field = this.up('window').down('labslivetsearch'),
							record = field.findRecordByValue(field.getValue());

						if(record){
							var tpl = me.getRadTemplate(record.get('loinc_number'), record.get('loinc_name'));
							me.addAdministrationEncounterTemplateGridRecord(tpl.description, 'LAB', tpl);
						}

						this.up('window').close();
					}
				}
			]
		}).show();
	}

});