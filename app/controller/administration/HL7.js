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
			ref: 'HL7MessageViewerWindowWarnings',
			selector: '#HL7MessageViewerWindowWarnings'
		},
		{
			ref: 'HL7MessageViewerWindowMessageField',
			selector: '#HL7MessageViewerWindowMessageField'
		},
		{
			ref: 'HL7MessageViewerWindowAcknowledgeField',
			selector: '#HL7MessageViewerWindowAcknowledgeField'
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
			}
		});

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
		this.showHL7MessagesWindow();
		this.getHL7MessagesGrid().getStore().load();
	},

	onHL7MessagesGridItemDblClick: function(grid, record){
		this.viewHL7MessageDetailById(record.get('id'));
	},

	viewHL7MessageDetailById: function(message_id){
		var me = this;

		me.showHL7MessageDetailWindow();

		HL7Messages.getMessageById(message_id, function(provider, response){

			var warnings = (response.result.hash !== response.result.current_hash) ?
				'<span style="color: red">' : '<span style="color: green">';
			warnings += '<b>' + _('stored_hash') + ':</b> ' + response.result.hash + '<br>';
			warnings += '<b>' + _('current_hash') + ':</b> ' + response.result.current_hash + '<br>';
			warnings += '</span>';

			me.getHL7MessageViewerWindowWarnings().update(warnings);
			me.getHL7MessageViewerWindowMessageField().setValue(response.result.message);
			me.getHL7MessageViewerWindowAcknowledgeField().setValue(response.result.response);

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
	}

});
