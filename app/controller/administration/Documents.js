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

Ext.define('App.controller.administration.Documents', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'AdministrationDocuments',
			selector: '#AdministrationDocuments'
		},
		{
			ref: 'AdministrationDocumentsDefaultsGrid',
			selector: '#AdministrationDocumentsDefaultsGrid'
		},
		{
			ref: 'AdministrationDocumentsTemplatesGrid',
			selector: '#AdministrationDocumentsTemplatesGrid'
		},
		{
			ref: 'AdministrationDocumentsTemplatesEditorForm',
			selector: '#AdministrationDocumentsTemplatesEditorForm'
		},
		{
			ref: 'AdministrationDocumentsTokensGrid',
			selector: '#AdministrationDocumentsTokensGrid'
		},
		{
			ref: 'AdministrationDocumentsTokenTextField',
			selector: '#AdministrationDocumentsTokenTextField'
		},
		{
			ref: 'AdministrationDocumentsNewTemplateBtn',
			selector: '#AdministrationDocumentsNewTemplateBtn'
		},
		{
			ref: 'AdministrationDocumentsPdfTemplatesGrid',
			selector: '#AdministrationDocumentsPdfTemplatesGrid'
		},
		{
			ref: 'AdministrationDocumentsPdfTemplatesAddBtn',
			selector: '#AdministrationDocumentsPdfTemplatesAddBtn'
		},
		{
			ref: 'AdministrationDocumentsPdfTemplateWindow',
			selector: '#AdministrationDocumentsPdfTemplateWindow'
		},
		{
			ref: 'AdministrationDocumentsPdfTemplateWindowForm',
			selector: '#AdministrationDocumentsPdfTemplateWindowForm'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#AdministrationDocuments': {
				activate: me.onAdministrationDocumentsActive
			},
			'#AdministrationDocumentsTokensGrid': {
				afterrender: me.onAdministrationDocumentsTokensGridAfterRender
			},
			'#AdministrationDocumentsNewTemplateBtn': {
				click: me.onAdministrationDocumentsNewTemplateBtnClick
			},
			'#AdministrationDocumentsNewDefaulTemplateBtn': {
				click: me.onAdministrationDocumentsNewDefaulTemplateBtnClick
			},
			'#AdministrationDocumentsPdfTemplatesGrid': {
				render: me.onAdministrationDocumentsPdfTemplatesGridRender,
				itemdblclick: me.onAdministrationDocumentsPdfTemplatesGridItemDblClick
			},
			'#AdministrationDocumentsPdfTemplatesAddBtn': {
				click: me.onAdministrationDocumentsPdfTemplatesAddBtnClick
			},
			'#AdministrationDocumentsPdfTemplateWindowSaveBtn': {
				click: me.onAdministrationDocumentsPdfTemplateWindowSaveBtnClick
			},
			'#AdministrationDocumentsPdfTemplateWindowSaveCloseBtn': {
				click: me.onAdministrationDocumentsPdfTemplateWindowSaveCloseBtnClick
			},
			'#AdministrationDocumentsPdfTemplateWindowCancelBtn': {
				click: me.onAdministrationDocumentsPdfTemplateWindowCancelBtnClick
			},
			'#AdministrationDocumentsPdfTemplateWindowDeleteBtn': {
				click: me.onAdministrationDocumentsPdfTemplateWindowDeleteBtnClick
			}
		});
	},

	onAdministrationDocumentsPdfTemplatesGridRender: function(grid){
		grid.getStore().load();
	},

	onAdministrationDocumentsPdfTemplatesGridItemDblClick: function(grid, record){
		this.doEditAdministrationDocumentsPdfTemplateRecord(record);
	},

	onAdministrationDocumentsPdfTemplatesAddBtnClick: function(btn){

		var grid = this.getAdministrationDocumentsPdfTemplatesGrid(),
			store = grid.getStore(),
			records = store.add({
				facility_id: 0,
				concept: 'default',
				template: '',
				body_margin_left: 10,
				body_margin_top: 10,
				body_margin_right: 10,
				body_margin_bottom: 10,
				body_font_family: 'Arial',
				body_font_style: '',
				body_font_size: 10,
				footer_margin: 0,
				format: 'LETTER',
				is_interface_tpl: 0,
				active: 1
			});

		this.doEditAdministrationDocumentsPdfTemplateRecord(records[0]);
	},

	doEditAdministrationDocumentsPdfTemplateRecord: function (record){
		// show window
		this.showAdministrationDocumentsPdfTemplateWindow();
		// load record
		this.getAdministrationDocumentsPdfTemplateWindowForm().getForm().loadRecord(record);
	},

	showAdministrationDocumentsPdfTemplateWindow: function (){
		if(!this.getAdministrationDocumentsPdfTemplateWindow()){
			Ext.create('App.view.administration.DocumentsPdfTemplateWindow');
		}
		return this.getAdministrationDocumentsPdfTemplateWindow().show();
	},

	onAdministrationDocumentsPdfTemplateWindowSaveBtnClick: function (btn){
		this.doAdministrationDocumentsPdfTemplateWindowSave(btn, false);
	},

	onAdministrationDocumentsPdfTemplateWindowSaveCloseBtnClick: function (btn){
		this.doAdministrationDocumentsPdfTemplateWindowSave(btn, true);
	},

	doAdministrationDocumentsPdfTemplateWindowSave: function (btn, close_window){
		var win = btn.up('window'),
			form = win.down('form').getForm(),
			record = form.getRecord(),
			values = form.getValues();

		if(!form.isValid()) return;

		record.set(values);

		if(record.store.getModifiedRecords().length > 0){
			record.store.sync({
				callback: function (){
					if (close_window) win.close();
				}
			});
		}else{
			if (close_window) win.close();
		}
	},

	onAdministrationDocumentsPdfTemplateWindowCancelBtnClick: function (btn){
		btn.up('window').close();
	},

	onAdministrationDocumentsPdfTemplateWindowDeleteBtnClick: function (btn){
		var win = btn.up('window'),
			form = win.down('form').getForm(),
			record = form.getRecord(),
			store = record.store;

		Ext.Msg.show({
			title: 'Wait!',
			msg: 'Would you like to remove this record?',
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (ans){
				if(ans === 'yes'){

					store.remove(record);
					store.sync({
						callback: function (){
							win.close();
							app.msg(_('sweet'), _('record_removed'));
						}
					});
				}
			}
		});
	},

	// -------------------------
	// -------------------------
	// -------------------------

	onAdministrationDocumentsActive: function(){

	},

	onAdministrationDocumentsNewTemplateBtnClick: function(){
		var me = this,
			grid = me.getAdministrationDocumentsTemplatesGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0,
			{
				title: _('new_document'),
				template_type: 'documenttemplate',
				date: new Date(),
				type: 1
			});
		grid.editingPlugin.startEdit(0, 0);
	},

	onAdministrationDocumentsNewDefaulTemplateBtnClick: function(){
		var me = this,
			grid = me.getAdministrationDocumentsDefaultsGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0,
			{
				title: _('new_defaults'),
				template_type: 'defaulttemplate',
				date: new Date(),
				type: 1
			});
		grid.editingPlugin.startEdit(0, 0);
	},
	
	onAdministrationDocumentsTokensGridAfterRender: function(grid){

	},

	doCopy: function(grid, record){

		if(!document.queryCommandSupported('copy')){
			app.msg(_('oops'), _('text_copy_not_supported_by_browser'), true);
			return;
		}

		var me = this;
		grid.editingPlugin.startEdit(record, 0);
		me.getAdministrationDocumentsTokenTextField().inputEl.dom.select();
		document.execCommand("copy");
		app.msg(_('sweet'), _('text_copyed'));

	}


});
