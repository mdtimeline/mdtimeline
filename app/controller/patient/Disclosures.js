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
			'#DisclosuresRecipientCancelBtn': {
				click: me.onDisclosuresRecipientCancelBtnClick
			},
			'#DisclosuresRecipientSaveBtn': {
				click: me.onDisclosuresRecipientSaveBtnClick
			}
		});
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