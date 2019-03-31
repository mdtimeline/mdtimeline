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

Ext.define('App.controller.administration.EmailTemplates', {
	extend: 'Ext.app.Controller',

	uses: [

	],

	refs: [
		{
			ref: 'AdministrationEmailTemplates',
			selector: '#AdministrationEmailTemplates'
		},
		{
			ref: 'AdministrationEmailTemplatesGrid',
			selector: '#AdministrationEmailTemplatesGrid'
		},
		{
			ref: 'AdministrationEmailTemplateWindow',
			selector: '#AdministrationEmailTemplateWindow'
		},
		{
			ref: 'AdministrationEmailTemplateForm',
			selector: '#AdministrationEmailTemplateForm'
		}
	],

	init: function () {
		var me = this;

		me.control({
			'#AdministrationEmailTemplates': {
				activate: me.onAdministrationEmailTemplatesActivate
			},
			'#AdministrationEmailTemplatesGrid': {
				itemdblclick: me.onAdministrationEmailTemplatesGridItemDblClick
			},
			'#AdministrationEmailTemplateCancelBtn': {
				click: me.onAdministrationEmailTemplateCancelBtnClick
			},
			'#AdministrationEmailTemplateSaveBtn': {
				click: me.onAdministrationEmailTemplateSaveBtnClick
			},
			'#AdministrationEmailTemplateBodyField': {
				push: me.onAdministrationEmailTemplateBodyFieldRender
			}
		});

	},

	onAdministrationEmailTemplatesActivate: function () {
		this.getAdministrationEmailTemplatesGrid().getStore().load();
	},

	onAdministrationEmailTemplatesGridItemDblClick: function (grid, email_template_record) {
		this.showAdministrationEmailTemplateWindow();

		this.getAdministrationEmailTemplateForm().getForm().loadRecord(email_template_record);
	},

	onAdministrationEmailTemplateCancelBtnClick: function(btn){
		this.getAdministrationEmailTemplateForm().getForm().reset(true);
		this.getAdministrationEmailTemplateWindow().close();
	},

	onAdministrationEmailTemplateSaveBtnClick: function(btn){

		var win = this.getAdministrationEmailTemplateWindow(),
			form = this.getAdministrationEmailTemplateForm().getForm(),
			record = form.getRecord(),
			values = form.getValues();


		if(!form.isValid()) return;

		record.set(values);

		if(!Ext.Object.isEmpty(record.getChanges())){
			record.store.sync({
				callback: function () {
					form.reset(true);
					win.close();
				}
			});
		}else{
			form.reset(true);
			win.close();
		}
	},

	onAdministrationEmailTemplateBodyFieldRender: function(editor){

		say('onAdministrationEmailTemplateBodyFieldRender');

		if (editor.iframeEl) {
			/* This is very hacky... we're getting the textareaEl, which
			 * was provided to us, and getting its next sibling, which is
			 * the iframe... and then we're probing the iframe for the
			 * body and changing its background-color to the selected hex */
			var iframe = editor.iframeEl.dom;
			if (iframe) {
				var doc = (iframe.contentDocument) ? iframe.contentDocument : iframe.contentWindow.document;
				if (doc && doc.body && doc.body.style) {
					doc.body.style['color'] = null;
					doc.body.style['background-color'] = null;
				}
			}
		}
	},

	showAdministrationEmailTemplateWindow: function () {
		if(!this.getAdministrationEmailTemplateWindow()){
			Ext.create('App.view.administration.EmailTemplateWindow');
		}
		return this.getAdministrationEmailTemplateWindow().show();
	}

});