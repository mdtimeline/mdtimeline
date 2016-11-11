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

Ext.define('App.view.administration.Documents', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('document_template_editor'),
	pageLayout: 'border',
	requires: [
		'App.ux.grid.Button',
		'Ext.grid.Panel'
	],
	itemId: 'AdministrationDocuments',
	initComponent: function(){

		var me = this;

		// *************************************************************************************
		// Documents Stores
		// *************************************************************************************
		me.templatesDocumentsStore = Ext.create('App.store.administration.DocumentsTemplates');
		me.defaultsDocumentsStore = Ext.create('App.store.administration.DefaultDocuments');

		me.DocumentsDefaultsGrid = Ext.create('Ext.grid.Panel', {
			title: _('documents_defaults'),
			frame: true,
			store: me.defaultsDocumentsStore,
			hideHeaders: true,
			flex: 1,
			itemId: 'AdministrationDocumentsDefaultsGrid',
			columns: [
				{
					flex: 1,
					sortable: true,
					dataIndex: 'title',
					editor: {
						xtype: 'textfield',
						allowBlank: false
					}
				},
				{
					icon: 'resources/images/icons/delete.png',
					tooltip: _('remove'),
					scope: me,
					handler: me.onRemoveDocument
				}
			],
			listeners: {
				scope: me,
				itemclick: me.onDocumentsGridItemClick
			},
			tbar: ['->',
				{
					text: _('new'),
					scope: me,
					handler: me.newDefaultTemplates,
					itemId: 'AdministrationDocumentsNewDefaulTemplateBtn',
				}],
			plugins: [me.rowEditor3 = Ext.create('Ext.grid.plugin.RowEditing',
				{
					clicksToEdit: 2
				})]
		});

		me.DocumentsGrid = Ext.create('Ext.grid.Panel', {
			title: _('document_templates'),
			frame: true,
			store: me.templatesDocumentsStore,
			hideHeaders: true,
			flex: 1,
			itemId: 'AdministrationDocumentsTemplatesGrid',
			columns: [
				{
					flex: 1,
					sortable: true,
					dataIndex: 'title',
					editor: {
						xtype: 'textfield',
						allowBlank: false
					}
				},
				{
					icon: 'resources/images/icons/delete.png',
					tooltip: _('remove'),
					scope: me,
					handler: me.onRemoveDocument
				}
			],
			listeners: {
				scope: me,
				itemclick: me.onDocumentsGridItemClick
			},
			tbar: ['->',
				{
					text: _('new'),
					scope: me,
					itemId: 'AdministrationDocumentsNewTemplateBtn',
					//handler: me.newDocumentTemplate
				}],
			plugins: [me.rowEditor = Ext.create('Ext.grid.plugin.RowEditing',
				{
					clicksToEdit: 2
				})]
		});

		me.LeftCol = Ext.create('Ext.container.Container', {
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			region: 'west',
			width: 250,
			border: false,
			split: true,
			items: [
				me.DocumentsDefaultsGrid,
				me.DocumentsGrid,
				{
					xtype:'grid',
					title: _('pdf_templates'),
					itemId: 'AdministrationDocumentsPdfTemplatesGrid',
					store: Ext.create('App.store.administration.DocumentsPdfTemplates'),
					flex: 1,
					frame: true,
					hideHeaders: true,
					columns:[
						{
							flex: 1,
							dataIndex: 'facility'
						}
					],
					tbar: [
						'->',
						{
							xtype:'button',
							text: _('new'),
							itemId: 'AdministrationDocumentsPdfTemplatesAddBtn'
						}
					]
				}
			]
		});

		me.TeamplateEditor = Ext.create('Ext.form.Panel', {
			title: _('document_editor'),
			region: 'center',
			layout: 'fit',
			autoScroll: false,
			border: true,
			split: true,
			hideHeaders: true,
			itemId: 'AdministrationDocumentsTemplatesEditorForm',
			items: {
				xtype: 'htmleditor',
				enableFontSize: false,
				name: 'body',
				margin: 5
			},
			buttons: [
				{
					text: _('save'),
					scope: me,
					handler: me.onSaveEditor
				},
				{
					text: _('cancel'),
					scope: me,
					handler: me.onCancelEditor
				}
			]
		});

		me.TokensGrid = Ext.create('Ext.grid.Panel', {
			title: _('available_tokens'),
			region: 'east',
			width: 250,
			border: true,
			split: true,
			hideHeaders: true,
			store: Ext.create('App.store.administration.DocumentToken'),
			disableSelection: true,
			itemId: 'AdministrationDocumentsTokensGrid',
			viewConfig: {
				stripeRows: false
			},
			plugins: [
				{
					ptype: 'cellediting'

				}
			],
			columns: [
				{
					flex: 1,
					sortable: false,
					dataIndex: 'token',
					editor: {
						xtype: 'textfield',
						editable: false,
						itemId: 'AdministrationDocumentsTokenTextField'
					}
				},
				{
					xtype: 'actioncolumn',
					width: 50,
					items: [
						{
							icon: 'resources/images/icons/copy.png',
							tooltip: _('copy'),
							margin: '0 5 0 0',
							handler: function(grid, rowIndex, colIndex, item, e, record){
								app.getController('administration.Documents').doCopy(grid, record);

							}
						}
					]
				}
			]
		});

		me.pageBody = [me.LeftCol, me.TeamplateEditor, me.TokensGrid];
		me.callParent();
	},
	/**
	 * Delete logic
	 */
	onDelete: function(){

	},

	onTokensGridItemClick: function(){

	},

	onSaveEditor: function(){
		var me = this,
			form = me.down('form').getForm(),
			record = form.getRecord(),
			values = form.getValues();
		record.set(values);
		app.msg(_('sweet'), _('record_saved'));
	},

	onCancelEditor: function(){
		var me = this, form = me.down('form').getForm(), grid = me.DocumentsGrid;
		form.reset();
		grid.getSelectionModel().deselectAll();
	},

	onDocumentsGridItemClick: function(grid, record){
		var me = this;
		var form = me.down('form').getForm();
		form.loadRecord(record);

	},
	newDocumentTemplate: function(){
		var me = this, store = me.templatesDocumentsStore;
		me.rowEditor.cancelEdit();
		store.insert(0,
			{
				title: _('new_document'),
				template_type: 'documenttemplate',
				date: new Date(),
				type: 1
			});
		me.rowEditor.startEdit(0, 0);

	},

	newDefaultTemplates: function(){
		var me = this, store = me.defaultsDocumentsStore;
		me.rowEditor3.cancelEdit();
		store.insert(0,
			{
				title: _('new_defaults'),
				template_type: 'defaulttemplate',
				date: new Date(),
				type: 1
			});
		me.rowEditor3.startEdit(0, 0);

	},

	//	newHeaderOrFooterTemplate:function(){
	//        var me = this,
	//            store = me.headersAndFooterStore;
	//        me.rowEditor2.cancelEdit();
	//        store.insert(0,{
	//            title: _('new_header_or_footer'),
	//	        template_type:'headerorfootertemplate',
	//            date: new Date(),
	//	        type: 2
	//        });
	//        me.rowEditor2.startEdit(0, 0);
	//
	//    },

	copyToClipBoard: function(grid, rowIndex, colIndex){
		var rec = grid.getStore().getAt(rowIndex),
			text = rec.get('token');
	},

	onRemoveDocument: function(){

	},

	/**
	 * This function is called from Viewport.js when
	 * this panel is selected in the navigation panel.
	 * place inside this function all the functions you want
	 * to call every this panel becomes active
	 */
	onActive: function(callback){
		var me = this;
		me.templatesDocumentsStore.load();
		//        me.headersAndFooterStore.load();
		me.defaultsDocumentsStore.load();
		callback(true);
	}
});
