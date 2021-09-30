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

Ext.define('App.controller.patient.LegalLetterSignatures', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'PatientLegalLettersGrid',
			selector: '#PatientLegalLettersGrid'
		},
		{
			ref: 'PatientLegalLettersSignDocumentBtn',
			selector: '#PatientLegalLettersSignDocumentBtn'
		},

	],

	init: function(){
		var me = this;
		me.control({
			'#PatientLegalLettersGrid': {
				activate: me.onPatientLegalLettersGridActive,
				selectionchange: me.onPatientLegalLettersGridSelectionChange,
				itemdblclick: me.onPatientLegalLettersGridItemDblClick
			},
			'#PatientLegalLettersSignDocumentBtn': {
				click: me.onPatientLegalLettersSignDocumentBtnClick
			},
			'#PatientLegalLettersSignDocumentSaveBtn': {
				click: me.onPatientLegalLettersSignDocumentSaveBtnClick
			},
			'#PatientLegalLettersPreviewDocumentBtn': {
				click: me.onPatientLegalLettersPreviewDocumentBtnClick
			},
		});

	},

	onPatientLegalLettersSignDocumentBtnClick: function (btn){
		say('onPatientLegalLettersSignDocumentBtnClick')


		var me = this,
			letters = this.getPatientLegalLettersGrid().getSelectionModel().getSelection(),
			letters_to_sign = [];

		letters.forEach(function (letter){
			if(letter.get('signature') === ''){
				letters_to_sign.push({
					pid: app.patient.pid,
					id: letter.get('letter_id'),
					version: letter.get('letter_version'),
					content: letter.get('letter_content'),
					document_code: letter.get('document_code')
				});
			}
		});

		if(letters_to_sign.length === 0){
			return;
		}

		var win = Ext.create('Ext.window.Window', {
			title: 'Capture Signature',
			layout: 'fit',
			items: [
				{
					xtype: 'topazsignature',
					firstName: app.patient.fname,
					lastName: app.patient.lname,
					eMail: app.patient.email,
					height: 120,
					width: 400
				}
			],
			letters_to_sign: letters_to_sign,
			buttons: [
				{
					xtype: 'button',
					text: 'Topaz Download',
					href: 'http://www.topazsystems.com/Software/SigPlusExtLite.exe',
				},
				{
					xtype: 'button',
					text: 'Chrome Extension',
					href: 'https://chrome.google.com/webstore/detail/Topaz-SigPlusExtLiteback/dhcpobccjkdnmibckgpejmbpmpembgco',
				},
				'->',
				{
					text: _('save'),
					itemId: 'PatientLegalLettersSignDocumentSaveBtn'
				}
			]
		});

		win.show();
		win.StartSignature();

	},

	onPatientLegalLettersSignDocumentSaveBtnClick: function (btn) {

		var me = this,
			win =  btn.up('window'),
			signature_obj = win.down('topazsignature').signatureObj;

		if (!signature_obj) {
			app.msg(_('oops'), 'No signature found', true);
			return;
		}

		var signature = ("data:image/png;base64," + signature_obj.imageData);

		LegalLetters.doSignDocuments(win.letters_to_sign , signature, function (){
			win.close();
			me.getPatientLegalLettersGrid().getStore().reload();
		});

	},

	onPatientLegalLettersPreviewDocumentBtnClick: function (btn){

		var me = this,
			letters = this.getPatientLegalLettersGrid().getSelectionModel().getSelection(),
			letters_to_sign = [],
			signature = 'data:image/jpeg;base64,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Ym9vbAAAAAAASW50cmJvb2wAAAAAAEJja2dPYmpjAAAAAQAAAAAAAFJHQkMAAAADAAAAAFJkICBkb3ViQG/gAAAAAAAAAAAAR3JuIGRvdWJAb+AAAAAAAAAAAABCbCAgZG91YkBv4AAAAAAAAAAAAEJyZFRVbnRGI1JsdAAAAAAAAAAAAAAAAEJsZCBVbnRGI1JsdAAAAAAAAAAAAAAAAFJzbHRVbnRGI1B4bEBywAAAAAAAAAAACnZlY3RvckRhdGFib29sAQAAAABQZ1BzZW51bQAAAABQZ1BzAAAAAFBnUEMAAAAATGVmdFVudEYjUmx0AAAAAAAAAAAAAAAAVG9wIFVudEYjUmx0AAAAAAAAAAAAAAAAU2NsIFVudEYjUHJjQFkAAAAAAAAAAAAQY3JvcFdoZW5QcmludGluZ2Jvb2wAAAAADmNyb3BSZWN0Qm90dG9tbG9uZwAAAAAAAAAMY3JvcFJlY3RMZWZ0bG9uZwAAAAAAAAANY3JvcFJlY3RSaWdodGxvbmcAAAAAAAAAC2Nyb3BSZWN0VG9wbG9uZwAAAAAAOEJJTQPtAAAAAAAQASwAAAABAAEBLAAAAAEAAThCSU0EJgAAAAAADgAAAAAAAAAAAAA/gAAAOEJJTQQNAAAAAAAEAAAAWjhCSU0EGQAAAAAABAAAAB44QklNA/MAAAAAAAkAAAAAAAAAAAEAOEJJTScQAAAAAAAKAAEAAAAAAAAAAThCSU0D9QAAAAAASAAvZmYAAQBsZmYABgAAAAAAAQAvZmYAAQChmZoABgAAAAAAAQAyAAAAAQBaAAAABgAAAAAAAQA1AAAAAQAtAAAABgAAAAAAAThCSU0D+AAAAAAAcAAA/////////////////////////////wPoAAAAAP////////////////////////////8D6AAAAAD/////////////////////////////A+gAAAAA/////////////////////////////wPoAAA4QklNBAAAAAAAAAIAAjhCSU0EAgAAAAAABgAAAAAAADhCSU0EMAAAAAAAAwEBAQA4QklNBC0AAAAAAAIAADhCSU0ECAAAAAAAEAAAAAEAAAJAAAACQAAAAAA4QklNBB4AAAAAAAQAAAAAOEJJTQQaAAAAAANJAAAABgAAAAAAAAAAAAAAyAAAAlgA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MCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAyMS0wOS0yOFQyMDoyMzoxMS0wNDowMCIgeG1wOk1vZGlmeURhdGU9IjIwMjEtMDktMjhUMjA6MjM6MTEtMDQ6MDAiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6YzgzZmUzYmEtNTBkYi00ZDdjLTkzNGUtNjQ3MWQyZTg4MDFlIiB4bXBNTTpEb2N1bWVudElEPSJhZG9iZTpkb2NpZDpwaG90b3Nob3A6ZjYyYWNiM2QtZTkwMC05MjQzLThlZjEtN2QyZGQ4MjZiYWMyIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9InhtcC5kaWQ6MjI1NzEyYmQtY2U5Mi00MDNiLWFhNTUtNTc1N2Y0YTkyOTE3IiBwaG90b3Nob3A6Q29sb3JNb2RlPSIzIiBwaG90b3Nob3A6SUNDUHJvZmlsZT0ic1JHQiBJRUM2MTk2Ni0yLjEiIGRjOmZvcm1hdD0iaW1hZ2UvanBlZyI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNyZWF0ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MjI1NzEyYmQtY2U5Mi00MDNiLWFhNTUtNTc1N2Y0YTkyOTE3IiBzdEV2dDp3aGVuPSIyMDIxLTA5LTI4VDIwOjIzOjExLTA0OjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjIuMiAoTWFjaW50b3NoKSIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6YzgzZmUzYmEtNTBkYi00ZDdjLTkzNGUtNjQ3MWQyZTg4MDFlIiBzdEV2dDp3aGVuPSIyMDIxLTA5LTI4VDIwOjIzOjExLTA0OjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjIuMiAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPHBob3Rvc2hvcDpUZXh0TGF5ZXJzPiA8cmRmOkJhZz4gPHJkZjpsaSBwaG90b3Nob3A6TGF5ZXJOYW1lPSJYICAgICAgICAiIHBob3Rvc2hvcDpMYXllclRleHQ9IlggICAgICAgICIvPiA8cmRmOmxpIHBob3Rvc2hvcDpMYXllck5hbWU9Il9fX19fX19fX19fXyIgcGhvdG9zaG9wOkxheWVyVGV4dD0iX19fX19fX19fX19fIi8+IDwvcmRmOkJhZz4gPC9waG90b3Nob3A6VGV4dExheWVycz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD94cGFja2V0IGVuZD0idyI/Pv/iDFhJQ0NfUFJPRklMRQABAQAADEhMaW5vAhAAAG1udHJSR0IgWFlaIAfOAAIACQAGADEAAGFjc3BNU0ZUAAAAAElFQyBzUkdCAAAAAAAAAAAAAAABAAD21gABAAAAANMtSFAgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEWNwcnQAAAFQAAAAM2Rlc2MAAAGEAAAAbHd0cHQAAAHwAAAAFGJrcHQAAAIEAAAAFHJYWVoAAAIYAAAAFGdYWVoAAAIsAAAAFGJYWVoAAAJAAAAAFGRtbmQAAAJUAAAAcGRtZGQAAALEAAAAiHZ1ZWQAAANMAAAAhnZpZXcAAAPUAAAAJGx1bWkAAAP4AAAAFG1lYXMAAAQMAAAAJHRlY2gAAAQwAAAADHJUUkMAAAQ8AAAIDGdUUkMAAAQ8AAAIDGJUUkMAAAQ8AAAIDHRleHQAAAAAQ29weXJpZ2h0IChjKSAxOTk4IEhld2xldHQtUGFja2FyZCBDb21wYW55AABkZXNjAAAAAAAAABJzUkdC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T7LY5cNaMSZGykbgfNLXI07vOYi8ooEwyL/mmNdKzrO7OmVATtyhrczW1MWMlvPADDebspYRybrjlSWxVMHJ+FctQKb45lsCyC1CG1yxrKYsgMEckCuMHu6QxQzuAk1i1zYcSba/13GEP+XORt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3ciuRt//AFig3cj/AAv/AP/Z';

		letters.forEach(function (letter){
			letters_to_sign.push({
				pid: app.patient.pid,
				id: letter.get('letter_id'),
				version: letter.get('letter_version'),
				content: letter.get('letter_content'),
				document_code: letter.get('document_code'),
				facility_id: app.user.facility
			});
		});

		LegalLetters.doPreviewDocuments(letters_to_sign, signature, function (document_ids){
			document_ids.forEach(function (document_id){
				app.onDocumentView(document_id, 'temp');
			});
		});

	},

	onPatientLegalLettersGridSelectionChange: function (grid, selection){
		var disabled = true;

		selection.forEach(function (record){
			if(!disabled) return;
			disabled = record.get('signature') !== '';
		});

		this.getPatientLegalLettersSignDocumentBtn().setDisabled(disabled);
	},

	onPatientLegalLettersGridItemDblClick: function (grid, signature_record){
		var document_id = signature_record.get('document_id');

		if(document_id > 0){
			app.onDocumentView(signature_record.get('document_id'));
		}

	},

	onPatientLegalLettersGridActive: function (grid){

		var store = grid.getStore();

		store.getProxy().extraParams = { include_not_signed: true };
		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);

		this.getLegalLettersToSign(store)

	},

	getLegalLettersToSign: function (store){

		var me = this;

		LegalLetters.getLegalLettersToSignByPid(
			app.patient.pid,
			app.user.facility,
			'PRE-REGISTRATION',
			function (letters){

				letters.forEach(function (letter){
					store.add({
						letter_id: letter.id,
						letter_version: letter.version,
						letter_title: letter.title,
						letter_content: letter.content,
						document_code: letter.document_code,
						pid: app.patient.pid,
						signature: ''
					});
				});
			}
		);
	}

});
