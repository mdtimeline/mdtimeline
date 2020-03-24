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

Ext.define('App.controller.patient.NursesNotes', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'NursesNotesGrid',
			selector: '#NursesNotesGrid'
		},
		{
			ref: 'NursesNoteEditWindow',
			selector: '#NursesNoteEditWindow'
		},
		{
			ref: 'NursesNoteSnippetsGrid',
			selector: '#NursesNoteSnippetsGrid'
		},
		{
			ref: 'NursesNoteSnippetEditWindow',
			selector: '#NursesNoteSnippetEditWindow'
		},
		{
			ref: 'NursesNoteSnippetEditWindowForm',
			selector: '#NursesNoteSnippetEditWindowForm'
		},
		{
			ref: 'NursesNoteEditWindowFormNoteField',
			selector: '#NursesNoteEditWindowFormNoteField'
		},
	],

	init: function(){
		var me = this;
		me.control({
			'#NursesNotesGrid': {
				activate: me.onNursesNotesGridActive,
				itemdblclick: me.onNursesNotesGridItemDblClick
			},
			'#NursesNotesGridAddBtn': {
				click: me.onNursesNotesGridAddBtnClick
			},
			'#NursesNoteEditWindowCancelBtn': {
				click: me.onNursesNoteEditWindowCancelBtnClick
			},
			'#NursesNoteEditWindowSaveBtn': {
				click: me.onNursesNoteEditWindowSaveBtnClick
			},
			'#NursesNoteSnippetAddBtn': {
				click: me.onNursesNoteSnippetAddBtnClick
			},
			'#NursesNoteSnippetsGrid': {
				render: me.onNursesNoteEditWindowRender,
				itemdblclick: me.onNursesNoteSnippetsGridItemDblClick
			},
			'#NursesNoteSnippetEditWindowCancelBtn': {
				click: me.onNursesNoteSnippetEditWindowCancelBtnClick
			},
			'#NursesNoteSnippetEditWindowSaveBtn': {
				click: me.onNursesNoteSnippetEditWindowSaveBtnClick
			},
		});
	},

	onNursesNoteSnippetsGridItemDblClick: function(grid, record){

		var snippet = record.get('snippet'),
			field = this.getNursesNoteEditWindowFormNoteField();

		if(Ext.isChrome){
			field.inputEl.dom.focus();
			document.execCommand("insertText", false, snippet);
		}else{
			field.inputEl.dom.value += ' ' + snippet;
		}
	},

	onNursesNoteSnippetEditWindowCancelBtnClick: function(btn){
		btn.up('window').close();
		this.getNursesNoteSnippetsGrid().getStore().rejectChanges();
	},

	onNursesNoteSnippetEditWindowSaveBtnClick: function(btn){
		var win = btn.up('window'),
			form = win.down('form').getForm(),
			values = form.getValues(),
			record = form.getRecord(),
			store = this.getNursesNoteSnippetsGrid().getStore();

		record.set(values);
		store.sync({
			callback: function () {
				win.close();
			}
		});
	},

	onNursesNoteSnippetAddBtnClick: function(btn){
		var form  = this.showNursesNoteSnippetEditWindow().down('form').getForm(),
			store = btn.up('grid').getStore(),
			records = store.add({
				uid: app.user.id,
				index: 0,
				create_uid: app.user.id,
				update_uid: app.user.id,
				create_date: app.getDate(),
				update_date: app.getDate()
			});

		form.loadRecord(records[0]);

	},

	onSnippetBtnEdit: function(grid, rowIndex, colIndex, actionItem, event, record){
		var form  = this.showNursesNoteSnippetEditWindow().down('form').getForm();
		form.loadRecord(record);
	},

	onNursesNoteEditWindowRender: function(grid){
		grid.getStore().load();
	},

	onNursesNoteEditWindowCancelBtnClick: function(btn){
		this.getNursesNotesGrid().getStore().rejectChanges();
		btn.up('window').close();
	},

	onNursesNoteEditWindowSaveBtnClick: function(btn){
		var win = btn.up('window'),
			form = win.down('form').getForm(),
			values = form.getValues(),
			record = form.getRecord(),
			store = this.getNursesNotesGrid().getStore();

		values.nurse_fname = app.user.fname;
		values.nurse_mname = app.user.mname;
		values.nurse_lname = app.user.lname;

		record.set(values);
		store.sync({
			callback: function () {
				win.close();
			}
		});
	},

	onNursesNotesGridAddBtnClick: function(btn){

		var form  = this.showNursesNotesWindow().down('form').getForm(),
			store = btn.up('grid').getStore(),
			records = store.add({
				pid: app.patient.pid,
				eid: app.patient.eid,
				in_error: false,
				create_uid: app.user.id,
				update_uid: app.user.id,
				create_date: app.getDate(),
				update_date: app.getDate(),
				nurse_name: app.user.getFullName()
			});

		form.loadRecord(records[0]);
	},

	onNursesNotesGridItemDblClick: function(grid, record){
		var form  = this.showNursesNotesWindow().down('form').getForm();
		form.loadRecord(record);
	},

	showNursesNotesWindow: function(){
		if(!this.getNursesNoteEditWindow()){
			Ext.create('App.view.patient.windows.NursesNoteEditWindow');
		}
		return this.getNursesNoteEditWindow().show();
	},

	showNursesNoteSnippetEditWindow: function(){
		if(!this.getNursesNoteSnippetEditWindow()){
			Ext.create('App.view.patient.windows.NursesNoteSnippetEditWindow');
		}
		return this.getNursesNoteSnippetEditWindow().show();
	},

	onNursesNotesGridActive: function(grid){

		grid.getStore().load({
			filters: [
				{
					property: 'pid',
					value: app.patient.pid
				},
				{
					property: 'eid',
					value: app.patient.eid
				}
			]
		});
	}

});