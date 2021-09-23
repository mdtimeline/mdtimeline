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

Ext.define('App.controller.patient.PdfForms', {
	extend: 'Ext.app.Controller',
	requires: [
	],
	refs: [
		{
			ref: 'PatientSummaryImagesPanel',
			selector: '#PatientSummaryImagesPanel'
		},
		{
			ref: 'PatientPdfFormsWindow',
			selector: '#PatientPdfFormsWindow'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#PatientSummaryImagesPanel':{
				beforerender: me.onPatientSummaryImagesPanelBeforeRender
			},
			'#PatientPdfFormsBtn':{
				click: me.onPatientPdfFormsBtnClick
			},
			'#PatientPdfFormsWindowCancelBtn':{
				click: me.onPatientPdfFormsWindowCancelBtnClick
			},
			'#PatientPdfFormsWindowGenerateBtn':{
				click: me.onPatientPdfFormsWindowGenerateBtnClick
			}
		});

		me.pdf_forms_fieldsets = [];

		me.getDocumentPdfFormFieldsets();

	},

	onPatientPdfFormsWindowCancelBtnClick: function (btn){
		var win = btn.up('window'),
			form = win.down('form').getForm();

		win.pid = undefined;
		win.referring_id = undefined;

		form.reset();
		win.close();
	},

	onPatientPdfFormsWindowGenerateBtnClick: function (btn){
		var win = btn.up('window'),
			form = win.down('form').getForm(),
			values = form.getValues(),
			pdf_form_ids = [];

		if(Ext.isArray(values.pdf_form_id)){
			values.pdf_form_id.forEach(function (pdf_form_id){
				if(pdf_form_id > 0){
					pdf_form_ids.push(pdf_form_id);
				}
			});
		}else{
			pdf_form_ids.push(values.pdf_form_id);
		}


		if(pdf_form_ids.length === 0){
			app.msg(_('oops'), 'Nothing to Generate', 'yellow');
			return;
		}

		win.el.mask('Generating...');

		DocumentPdfForms.generatePdfForms(pdf_form_ids, win.pid, win.referring_id, function (document){

			win.el.unmask();
			form.reset();
			win.pid = undefined;
			win.referring_id = undefined;
			win.close();

			app.getController('DocumentViewer').doDocumentView(document.id, 'pdf_form');


		});


		// DocumentPdfForms.generatePdfForm();
		// form.reset();
		// win.close();

	},


	getDocumentPdfFormFieldsets: function (){

		var me = this,
			fieldset_map = {};

		DocumentPdfForms.getDocumentPdfForms({
			is_active: true
		}, function (pdf_forms){

			pdf_forms.forEach(function (pdf_form){

				var fieldset_mapped = fieldset_map[pdf_form.document_type],
					checkbox = {
					xtype: 'checkboxfield',
					name: 'pdf_form_id',
					inputValue: pdf_form.id,
					boxLabel: pdf_form.document_title
				};

				if(fieldset_mapped !== undefined){
					me.pdf_forms_fieldsets[fieldset_mapped].items.push(checkbox);
				}else{

					var fieldset = {
						xtype: 'fieldset',
						title: pdf_form.document_type,
						items: [ checkbox ]
					};

					me.pdf_forms_fieldsets.push(fieldset);
					fieldset_map[pdf_form.document_type] = me.pdf_forms_fieldsets.indexOf(fieldset);
				}

			});

		});
	},

	doShowPdfForm: function (pid, referring_id){
		var  win = this.showPatientPdfFormsWindow();

		win.pid = pid;
		win.referring_id = referring_id;

		win.el.unmask();
		win.down('form').getForm().reset();
	},

	onPatientPdfFormsBtnClick: function (btn){
		this.doShowPdfForm(app.patient.pid);
	},

	showPatientPdfFormsWindow: function (){
		var me = this;

		if(!me.getPatientPdfFormsWindow()){
			Ext.create('Ext.window.Window', {
				title: 'Patient Forms',
				itemId: 'PatientPdfFormsWindow',
				width: 600,
				layout: 'fit',
				closeAction: 'hide',
				bodyPadding: 5,
				items: [
					{
						xtype: 'form',
						itemId: 'PatientPdfFormsWindowForm',
						bodyPadding: 10,
						items: me.pdf_forms_fieldsets
					}
				],
				buttons: [
					{
						xtype: 'button',
						text: _('cancel'),
						itemId: 'PatientPdfFormsWindowCancelBtn',
					},
					{
						xtype: 'button',
						text: _('generate'),
						itemId: 'PatientPdfFormsWindowGenerateBtn',
					}
				]
			});
		}
		return me.getPatientPdfFormsWindow().show();
	},

	onPatientSummaryImagesPanelBeforeRender: function (panel) {
		panel.addDocked({
			xtype: 'toolbar',
			dock: 'bottom',
			items: [
				{
					xtype: 'button',
					text: _('pdf_forms'),
					itemId: 'PatientPdfFormsBtn',
					iconCls: 'far fa-file-alt',
					flex: 1
				}
			]
		}, 0);
	},



});