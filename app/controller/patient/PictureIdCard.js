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

Ext.define('App.controller.patient.PictureIdCard', {
	extend: 'Ext.app.Controller',
	requires: [
	],
	refs: [
		{
			ref: 'PatientSummaryImagesPanel',
			selector: '#PatientSummaryImagesPanel'
		},
		{
			ref: 'PatientSummaryImagesPictureIdCardPrintersCombo',
			selector: '#PatientSummaryImagesPictureIdCardPrintersCombo'
		},
		{
			ref: 'PatientSummaryImagesPictureIdCardPrinterImage',
			selector: '#PatientSummaryImagesPictureIdCardPrinterImage'
		},
		{
			ref: 'PatientSummaryImagesPictureIdCardPrintBtn',
			selector: '#PatientSummaryImagesPictureIdCardPrintBtn'
		},

	],

	init: function(){
		var me = this;

		me.control({
			'#PatientSummaryImagesPanel':{
				beforerender: me.onPatientSummaryImagesPanelBeforeRender
			},
			'#PatientSummaryImagesPictureIdCardBtn':{
				click: me.onPatientSummaryImagesPictureIdCardBtnClick
			},
			'#PatientSummaryImagesPictureIdCardPrintBtn':{
				click: me.onPatientSummaryImagesPictureIdCardPrintBtnClick
			}
		});
	},

	onPatientSummaryImagesPictureIdCardBtnClick: function(btn){

		var patient_img = btn.up('patientdeomgraphics').down('#PatientSummaryImagesPanel').down('image'),
			data = {
			'[PATIENT_NAME]': app.patient.name,
			'[RECORD_NUMBER]': app.patient.pubpid,
			'[FACILITY]': app.getController('Main').getCurrentFacilityName(),
			'[IMAGE]': patient_img.src
		};

		PictureIdCard.Create(data, function (response) {

			if(response.success){

				Ext.create('Ext.window.Window', {
					title: _('picture_id_card'),
					layout: 'fit',
					items: [
						{
							xtype:'image',
							itemId: 'PatientSummaryImagesPictureIdCardPrinterImage',
							base64data: response.base64data,
							src: ('data:image/jpg;base64,' + response.base64data),
							width: response.width/4,
							height: response.height/4
						}
					],
					dockedItems: [{
						xtype: 'toolbar',
						dock: 'bottom',
						ui: 'footer',
						defaults: { minWidth: 70 },
						items: [
							'->',
							{
								xtype: 'printerscombo',
								itemId: 'PatientSummaryImagesPictureIdCardPrintersCombo',
							},
							{
								text: _('print'),
								itemId: 'PatientSummaryImagesPictureIdCardPrintBtn'
							}
						]
					}]
				}).show();

			}
		});

	},


	onPatientSummaryImagesPictureIdCardPrintBtnClick: function(btn){

		var printer_record = this.getPatientSummaryImagesPictureIdCardPrintersCombo().findRecordByValue(this.getPatientSummaryImagesPictureIdCardPrintersCombo().getValue()),
			img = this.getPatientSummaryImagesPictureIdCardPrinterImage();

		if(!printer_record){
			app.msg(_('oops'), _('no_printer_selected'), true);
			return;
		}

		app.getController('Print').doPrint(printer_record, img.base64data);

	},


	onPatientSummaryImagesPanelBeforeRender: function (panel) {
		panel.addDocked({
			xtype: 'toolbar',
			dock: 'top',
			items: [
				{
					xtype: 'button',
					text: _('picture_id_card'),
					itemId: 'PatientSummaryImagesPictureIdCardBtn',
					flex: 1
				}
			]
		});

	},



});