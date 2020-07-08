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

Ext.define('App.controller.administration.HL7', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'HL7ServersPanel',
			selector: 'hl7serverspanel'
		},
		{
			ref: 'HL7ServersGrid',
			selector: '#hl7serversgrid'
		},
		{
			ref: 'HL7ClientsGrid',
			selector: '#hl7clientsgrid'
		},
		{
			ref: 'HL7MessagesWindow',
			selector: '#HL7MessagesWindow'
		},
		{
			ref: 'HL7MessagesGrid',
			selector: '#HL7MessagesGrid'
		},
		{
			ref: 'HL7MessageViewerWindow',
			selector: '#HL7MessageViewerWindow'
		},
		{
			ref: 'HL7MessageViewerForm',
			selector: '#HL7MessageViewerForm'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'hl7serverspanel': {
				activate: me.onHL7ServersPanelActive
			},
			'#hl7serversgrid': {
				beforeedit: me.onHL7ServersGridBeforeEdit,
				validateedit: me.onHL7ServersGridValidateEdit
			},
			'#hl7serversgrid #addHL7ServerBtn': {
				click: me.onAddHL7ServerBtnClick
			},
			'#hl7serversgrid #removeHL7ServerBtn': {
				click: me.onRemoveHL7ServerBtnClick
			},
			'#hl7clientsgrid #addHL7ClientBtn': {
				click: me.onAddHL7ClientBtnClick
			},
			'#hl7clientsgrid #removeHL7ClientBtn': {
				click: me.onRemoveHL7ClientBtnClick
			},
			'#HL7MessagesViewBtn': {
				click: me.onHL7MessagesViewBtnClick
			},
			'#HL7MessagesGrid': {
				itemdblclick: me.onHL7MessagesGridItemDblClick
			},
			'#HL7ServerConfigBtn': {
				click: me.onHL7ServerConfigBtnClick
			},
			'#HL7ClientConfigBtn': {
				click: me.onHL7ClientConfigBtnClick
			},
			'#HL7ConfigEditorCancelBtn': {
				click: me.onHL7ConfigEditorCancelBtnClick
			},
			'#HL7ConfigEditorSaveBtn': {
				click: me.onHL7ConfigEditorSaveBtnClick
			},


			'#HL7MessageViewerWindowResendBtn': {
				click: me.onHL7MessageViewerWindowResendBtnClick
			},
			'#HL7MessageViewerWindowPreviousBtn': {
				click: me.onHL7MessageViewerWindowPreviousBtnClick
			},
			'#HL7MessageViewerWindowNextBtn': {
				click: me.onHL7MessageViewerWindowNextBtnClick
			},
			'#HL7MessageViewerWindowCloseBtn': {
				click: me.onHL7MessageViewerWindowCloseBtnClick
			}
		});

	},

	onHL7ConfigEditorCancelBtnClick: function(btn){
		btn.up('window').close();
	},

	onHL7ConfigEditorSaveBtnClick: function(btn){
		var win = btn.up('window'),
			form = win.down('form').getForm(),
			values = form.getValues(),
			record = form.getRecord();

		record.set(values);

		if(!Ext.Object.isEmpty(record.getChanges())){
			record.save({
				callback: function () {
					win.close();
					app.msg(_('sweet'), _('record_saved'));
				}
			});
		}else{
			win.close();
		}

	},

	onHL7ServerConfigBtnClick: function(btn){
		var grid = btn.up('grid'),
			selection = grid.getSelectionModel().getSelection();

		if(selection.length === 0){
			return;
		}

		var win = this.showConfigEditorWindow();
		win.down('form').getForm().loadRecord(selection[0]);
	},

	onHL7ClientConfigBtnClick: function(btn){
		var grid = btn.up('grid'),
			selection = grid.getSelectionModel().getSelection();

		if(selection.length === 0){
			return;
		}

		var win = this.showConfigEditorWindow();
		win.down('form').getForm().loadRecord(selection[0]);
	},

	showConfigEditorWindow: function(){
		return Ext.create('Ext.window.Window', {
			title: 'Configuration',
			height: 400,
			width: 600,
			layout: 'fit',
			modal: true,
			items: {
				xtype: 'form',
				layout: 'fit',
				items: [
					{
						xtype: 'textareafield',
						name: 'config'
					}
				]
			},
			buttons: [
				{
					xtype: 'button',
					text: _('cancel'),
					itemId: 'HL7ConfigEditorCancelBtn'
				},
				{
					xtype: 'button',
					text: _('save'),
					itemId: 'HL7ConfigEditorSaveBtn'
				}
			]
		}).show();
	},

	onAddHL7ServerBtnClick: function(){
		var me = this,
			grid = me.getHL7ServersGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {});
		grid.editingPlugin.startEdit(0, 0);
	},

	onAddHL7ClientBtnClick: function(){
		var me = this,
			grid = me.getHL7ClientsGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {});
		grid.editingPlugin.startEdit(0, 0);
	},

	onRemoveHL7ServerBtnClick: function(){

	},

	onRemoveHL7ClientBtnClick: function(){

	},

	serverStartHandler: function(record){
		HL7ServerHandler.start({
            id: record.data.id,
            ip: record.data.ip,
            port: record.data.port
        }, function(provider, response){
			record.set({'online': response.result.online, token: response.result.token});
			record.commit();
		});
	},

	serverStopHandler: function(record){
		HL7ServerHandler.stop({
			id: record.data.id,
            token: record.data.token,
            ip: record.data.ip,
            port: record.data.port
        }, function(provider, response){
			record.set({'online': response.result.online});
			record.commit();
		});
	},

	onHL7ServersPanelActive: function(){
		this.reloadStore();
	},

	reloadStore: function(){
		this.getHL7ServersGrid().getStore().load();
		this.getHL7ClientsGrid().getStore().load();
	},

	onHL7ServersGridBeforeEdit: function(plugin, e){
		var multiField = plugin.editor.query('multitextfield')[0],
			data = e.record.data.allow_ips;

		Ext.Function.defer(function(){
			multiField.setValue(data);
		}, 10);
	},

	onHL7ServersGridValidateEdit: function(plugin, e){
		var multiField = plugin.editor.query('multitextfield')[0],
			values = multiField.getValue();
		e.record.set({ allow_ips: values });
	},

	onHL7MessagesViewBtnClick: function(){
		this.showHL7MessagesByReference(undefined);
	},

	showHL7MessagesByReference: function(reference){
		this.showHL7MessagesWindow();
		if(reference){
			this.getHL7MessagesGrid().getStore().clearFilter(true);
			this.getHL7MessagesGrid().getStore().filter([
				{
					property: 'reference',
					value: reference
				}
			]);
		}else{
			this.getHL7MessagesGrid().getStore().clearFilter(true);
			this.getHL7MessagesGrid().getStore().load();
		}
	},

	onHL7MessagesGridItemDblClick: function(grid, record){
		this.viewHL7MessageDetailById(record.get('id'));
	},

	viewHL7MessageDetailById: function(message_id){
		var me = this,
			win = me.showHL7MessageDetailWindow(),
			form = win.down('form').getForm();

		HL7Messages.getMessageById(message_id, function(response){
			response.message = me.colourizer(response.message);
			response.response = me.colourizer(response.response);
			form.setValues(response);
		});
	},

	showHL7MessageDetailWindow: function(){
		if(!this.getHL7MessageViewerWindow()){
			Ext.create('App.view.administration.HL7MessageViewer');
		}
		return this.getHL7MessageViewerWindow().show();
	},

	showHL7MessagesWindow: function(){
		if(!this.getHL7MessagesWindow()){
			Ext.create('App.view.administration.HL7Messages');
		}
		return this.getHL7MessagesWindow().show();
	},

	onHL7MessageViewerWindowResendBtnClick: function(btn){
		var values = btn.up('window').down('form').getForm().getValues(),
			message_id = values.id,
			is_outbound = values.isOutbound;

		if(!is_outbound){
			app.msg(_('oops'), 'Only outbound messages can be resend', true);
			return;
		}

		HL7Messages.getResendMessageById(message_id, function (response) {

			say(response);


		});
	},

	onHL7MessageViewerWindowPreviousBtnClick: function(btn){
		var messages_grid = this.getHL7MessagesWindow().down('grid'),
			messages_store = messages_grid.getStore(),
			message_sm = messages_grid.getSelectionModel(),
			message_last_selected = message_sm.getLastSelected(),
			message_last_selected_index = messages_store.findBy(function (record, id) {
				return message_last_selected.get('id') === id;
			}),
			message_next_record_index = message_last_selected_index - 1,
			message_next_record = messages_store.getAt(message_next_record_index),
			message_form = btn.up('window').down('form').getForm();

		say(message_last_selected_index);
		say(message_next_record_index);
		say(message_form);

		if(!message_next_record){
			btn.up('window').close();
			return;
		}


		message_sm.select(message_next_record);

		this.viewHL7MessageDetailById(message_next_record.get('id'));

	},

	onHL7MessageViewerWindowNextBtnClick: function(btn){
		var messages_grid = this.getHL7MessagesWindow().down('grid'),
			messages_store = messages_grid.getStore(),
			message_sm = messages_grid.getSelectionModel(),
			message_last_selected = message_sm.getLastSelected(),
			message_last_selected_index = messages_store.findBy(function (record, id) {
				return message_last_selected.get('id') === id;
			}),
			message_next_record_index = message_last_selected_index + 1,
			message_next_record = messages_store.getAt(message_next_record_index),
			message_form = btn.up('window').down('form').getForm();

		say(message_last_selected_index);
		say(message_next_record_index);
		say(message_form);

		if(!message_next_record){
			btn.up('window').close();
			return;
		}

		message_sm.select(message_next_record);

		this.viewHL7MessageDetailById(message_next_record.get('id'));
	},

	onHL7MessageViewerWindowCloseBtnClick: function(btn){
		btn.up('window').close();
	},

	colourizer: function(message) {

		var lines = message.split("\r");
		var output = "";
		for (var index in lines) {
			var nextLine = lines[index];
			if (nextLine.length < 3) {
				output = output + nextLine + "<br>";
				continue;
			}

			var fields = nextLine.split("|");
			for (var fIndex in fields) {
				var nextField = fields[fIndex];
				if (fIndex == 0) {
					output = output + "<span style=\"color: #000080;\"><b>" + nextField + "</b></span>";
				} else {

					var reps = nextField.split("~");
					for (var rIndex in reps) {
						var nextRep = reps[rIndex];

						if (rIndex > 0) {
							output = output + "<span style=\"color: #808080;\">~</span>";
						}

						var comps = nextRep.split("^");
						for (var cIndex in comps) {
							var nextComp = comps[cIndex];

							if (cIndex > 0) {
								output = output + "<span style=\"color: #808080;\">^</span>";
							}

							var subcomps = nextComp.split("&");
							for (var sIndex in subcomps) {
								var nextSubComp = subcomps[sIndex];

								if (sIndex > 0) {
									output = output + "<span style=\"color: #808080;\">&</span>";
								}

								if (nextSubComp.match(/^[0-9./]+/)) {
									nextSubComp = "<span style=\"color: #990033;\">" + nextSubComp + "</span>";
								}

								output = output + nextSubComp;

							}
						}
					}
				}

				output = output + "<span style=\"color: #808080;\"><b>|</b></span>";

			}

			output = output + "<br>";
		}

		return output;

		// output = output.replace(/<br>/g, "<br>\n");
		// output = output.replace(/&/g, "&amp;");
		// output = output.replace(/>/g, "&gt;");
		// output = output.replace(/</g, "&lt;");
		// document.getElementById('outHtml').innerHTML = "<pre>" + output + "</pre>";

		//window.alert(value);

	}

});
