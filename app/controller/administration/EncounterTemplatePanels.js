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

	requires: [],

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
		}
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
			refer_by_text: "",
			referral_date: '',
			create_uid: 0,
			update_uid: 0,
			create_date: null,
			update_date: null,

			is_external_referral: true,
			refer_to: "",
			refer_to_text: "",

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
	}

});