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
			}
		});
	},

	onAdministrationDocumentsActive: function(){

	},

	onAdministrationDocumentsNewTemplateBtnClick: function(){
		var me = this,
			grid = me.getAdministrationDocumentsNewTemplateBtn(),
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
