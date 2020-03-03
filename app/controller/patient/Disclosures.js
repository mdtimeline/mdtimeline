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

Ext.define('App.controller.patient.Disclosures', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'PatientDisclosuresGrid',
			selector: '#PatientDisclosuresGrid'
		},
		{
			ref: 'DisclosuresRecipientWindow',
			selector: '#DisclosuresRecipientWindow'
		},
		{
			ref: 'DisclosuresRecipientForm',
			selector: '#DisclosuresRecipientForm'
		},
		{
			ref: 'DisclosuresRecipientField',
			selector: '#DisclosuresRecipientField'
		},
		{
			ref: 'DisclosuresDescriptionField',
			selector: '#DisclosuresDescriptionField'
		},
		{
			ref: 'DisclosuresRecipientCancelBtn',
			selector: '#DisclosuresRecipientCancelBtn'
		},
		{
			ref: 'DisclosuresRecipientSaveBtn',
			selector: '#DisclosuresRecipientSaveBtn'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#PatientDisclosuresGrid': {
				activate: me.onPatientDisclosuresGridActivate,
				beforeitemcontextmenu: me.onPatientDisclosuresGridBeforeItemContextMenu
			},
			'#PatientDisclosuresGridAddBtn': {
				click: me.onPatientDisclosuresGridAddBtnClick
			},
			'#DisclosuresRecipientCancelBtn': {
				click: me.onDisclosuresRecipientCancelBtnClick
			},
			'#DisclosuresRecipientSaveBtn': {
				click: me.onDisclosuresRecipientSaveBtnClick
			},
			'#PatientDisclosuresAttacheDocumentsMenu': {
				click: me.onPatientDisclosuresAttacheDocumentsMenuClick
			},
			'#PatientDisclosuresAttacheDocumentsCancelBtn': {
				click: me.onPatientDisclosuresAttacheDocumentsCancelBtnClick
			},
			'#PatientDisclosuresAttacheDocumentsSaveBtn': {
				click: me.onPatientDisclosuresAttacheDocumentsSaveBtnClick
			},
			'#PatientDisclosuresPrintBtn': {
				click: me.onPatientDisclosuresPrintBtnClick
			},
			'#PatientDisclosuresDownloadBtn': {
				click: me.onPatientDisclosuresDownloadBtnClick
			},
			'#PatientDisclosuresBurnBtn': {
				click: me.onPatientDisclosuresBurnBtnClick
			}
		});
	},

	onPatientDisclosuresPrintBtnClick: function(btn){
		// TODO
	},

	onPatientDisclosuresDownloadBtnClick: function(btn){
		// TODO
	},

	onPatientDisclosuresBurnBtnClick: function(btn){
		// TODO
	},

	onPatientDisclosuresAttacheDocumentsCancelBtnClick: function(btn){
		btn.up('window').close();
	},

	onPatientDisclosuresAttacheDocumentsSaveBtnClick: function(btn){
		var win = btn.up('window'),
			grid = win.down('grid'),
			selection = grid.getSelectionModel().getSelection(),
			disclosure_grid = this.getPatientDisclosuresGrid(),
			disclosure_record = disclosure_grid.getSelectionModel().getLastSelected(),
			disclosure_documents = [];

		selection.forEach(function (document_record) {
			disclosure_documents.push({
				disclosure_id: disclosure_record.get('id'),
				document_id: document_record.get('id')
			});
		});

		Disclosure.removeDisclosuresDocumentsById(disclosure_record.get('id'));
		Disclosure.addDisclosuresDocument(disclosure_documents, function (response) {
			win.close();
			disclosure_grid.getStore().reload();
		});

	},

	onDocumentsLoad: function(document_grid, document_store, document_records){
		// TODO select records already added to disclosure

		var document_sm = document_grid.getSelectionModel(),
			disclosure_grid = this.getPatientDisclosuresGrid(),
			disclosure_record = disclosure_grid.getSelectionModel().getLastSelected(),
			document_inventory_ids = disclosure_record.get('document_inventory_ids').split(','),
			suppress_event = false;

		document_inventory_ids.forEach(function (document_inventory_id) {
			document_sm.select([document_store.getById(parseInt(document_inventory_id))], true, suppress_event);
			suppress_event= true;
		});

	},

	onPatientDisclosuresGridBeforeItemContextMenu: function(grid, record, item, index, e){
		e.preventDefault();
		this.showPatientDisclosuresGridBeforeItemContextMenu(record, e);
	},

	showPatientDisclosuresGridBeforeItemContextMenu: function(disclosure_record, e){

		if(!this.patientDisclosuresGridMenu){
			this.patientDisclosuresGridMenu = Ext.widget('menu', {
				margin: '0 0 10 0',
				items: [
					{
						text: _('attach_documents'),
						itemId: 'PatientDisclosuresAttacheDocumentsMenu'
					}
				]
			});
		}

		return this.patientDisclosuresGridMenu.showAt(e.getXY());
	},

	onPatientDisclosuresAttacheDocumentsMenuClick: function(){
		var me = this,
			configs = {
				buttons: [
					{
						xtype: 'button',
						text: _('cancel'),
						width: 70,
						itemId: 'PatientDisclosuresAttacheDocumentsCancelBtn'
					},
					{
						xtype: 'button',
						text: _('save'),
						width: 70,
						itemId: 'PatientDisclosuresAttacheDocumentsSaveBtn'
					}
				]
			},
			doc_win = me.getController('patient.Documents').showDocumentWindow(configs),
			doc_grid = doc_win.down('grid');

		doc_grid.getStore().on('load', function (store, records) {
			me.onDocumentsLoad(doc_grid, store, records);
		});
	},

	onPatientDisclosuresGridActivate: function(grid){
		grid.store.load({
			filters:[
				{
					property: 'pid',
					value: app.patient.pid
				}
			]
		});
	},

	onPatientDisclosuresGridAddBtnClick: function(btn){
		var grid = btn.up('grid'),
			store = grid.store;

		grid.plugins[0].cancelEdit();
		store.insert(0, {
			request_date: app.getDate(),
			pid: app.patient.pid,
			iud: app.user.id,
			active: 1
		});
		grid.plugins[0].startEdit(0, 0);
	},

	onDisclosuresRecipientCancelBtnClick: function(){
		this.getDisclosuresRecipientWindow().close();
	},

	onDisclosuresRecipientSaveBtnClick: function(){

		var win = this.getDisclosuresRecipientWindow(),
			form = this.getDisclosuresRecipientForm().getForm(),
			values = form.getValues(),
			disclosure_data = win.disclosure_data;

		if(!form.isValid()) return;

		disclosure_data.recipient = values.recipient;
		disclosure_data.description = values.description;

		Disclosure.addDisclosure(disclosure_data, win.disclosure_callback);

		win.close();

	},

	addRawDisclosure: function(data, callback){
		var me = this;

		if(!data.pid) return;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('raw_disclosure_message'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if(btn == 'yes'){
					me.promptRecipient(data, callback);
				}
			}
		});
	},

	promptRecipient: function(data, callback){
		var win = this.showRecipientWindow();
		win.disclosure_data = data;
		win.disclosure_callback = callback;

		this.getDisclosuresRecipientField().reset();
		this.getDisclosuresDescriptionField().setValue(data.description);
	},

	showRecipientWindow: function(){

		if(!this.getDisclosuresRecipientWindow()){
			Ext.create('Ext.window.Window', {
				title: _('disclosures'),
				layout: 'fit',
				itemId: 'DisclosuresRecipientWindow',
				items: [
					{
						xtype: 'form',
						itemId: 'DisclosuresRecipientForm',
						width: 300,
						bodyPadding: 10,
						items: [
							{
								xtype: 'combobox',
								labelAlign: 'top',
								fieldLabel: 'Choose Recipient',
								queryMode: 'local',
								displayField: 'text',
								valueField: 'value',
								itemId: 'DisclosuresRecipientField',
								allowBlank: false,
								anchor: '100%',
								editable: false,
								name: 'recipient',
								store: Ext.create('Ext.data.Store', {
									fields: ['text', 'value'],
									data: [
										{ text: _('emer_contact'), value: 'emer_contact' },
										{ text: _('father'), value: 'father' },
										{ text: _('guardian'), value: 'guardian' },
										{ text: _('mother'), value: 'mother' },
										{ text: _('patient'), value: 'patient' }
									]
								})
							},
							{
								xtype: 'textareafield',
								labelAlign: 'top',
								fieldLabel: 'Description',
								name: 'description',
								itemId: 'DisclosuresDescriptionField',
								allowBlank: false,
								anchor: '100%'
							}
						]
					}
				],
				buttons: [
					{
						text: _('cancel'),
						itemId: 'DisclosuresRecipientCancelBtn'
					},
					{
						text: _('save'),
						itemId: 'DisclosuresRecipientSaveBtn'
					}
				]
			});
		}

		return this.getDisclosuresRecipientWindow().show();

	}

});
