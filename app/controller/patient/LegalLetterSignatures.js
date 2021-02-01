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
					id: letter.letter_id,
					version: letter.letter_version,
					content: letter.letter_content
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

		say('onPatientLegalLettersSignDocumentSaveBtnClick');
		say(signature_obj);

		var signature = ("data:image/png;base64," + signature_obj.imageData);

		LegalLetters.doSignDocuments(win.letters_to_sign , signature, function (){
			win.close();
			me.getPatientLegalLettersGrid().getStore().reload();
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
						pid: app.patient.pid,
						signature: ''
					});
				});
			}
		);
	}

});
