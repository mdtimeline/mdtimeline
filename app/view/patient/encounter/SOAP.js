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

Ext.define('App.view.patient.encounter.SOAP', {
	extend: 'Ext.panel.Panel',
	requires: [
		'App.ux.combo.Specialties',
		'App.ux.grid.RowFormEditing',
		'App.ux.grid.RowFormEditing',
		'App.view.patient.encounter.CarePlanGoals',
		'App.view.patient.encounter.CarePlanGoalsNewWindow',
		'App.ux.LiveSnomedProcedureSearch',
		'App.view.patient.encounter.AppointmentRequestGrid',
		'App.view.patient.encounter.EducationResourcesGrid',
		'App.view.patient.encounter.MedicationsAdministeredGrid'
	],
	action: 'patient.encounter.soap',
	itemId: 'soapPanel',
	title: _('soap'),
	layout: 'border',
	frame: true,

	pid: null,
	eid: null,

	initComponent: function(){
		var me = this;

		me.snippetStore = Ext.create('App.store.administration.EncounterSnippets', {
			autoLoad: false,
			pageSize: 1000,
			groupField: 'category'
		});

		me.procedureStore = Ext.create('App.store.patient.encounter.Procedures');

		var snippetCtrl = App.app.getController('administration.EncounterSnippets');

		me.snippets = Ext.create('Ext.grid.Panel', {
			title: _('snippets'),
			itemId: 'SnippetsTreePanel',
			region: 'west',
			width: 300,
			split: true,
			animate: false,
			hideHeaders: true,
			useArrows: true,
			rootVisible: false,
			singleExpand: true,
			collapsed: !eval(g('enable_encounter_soap_templates')),
			collapsible: true,
			collapseMode: 'mini',
			hideCollapseTool: true,
			store: me.snippetStore,
			features: [{ftype:'grouping'}],
			tools: [
				{
					xtype: 'button',
					text: _('snippet'),
					iconCls: 'icoAdd',
					itemId: 'SnippetAddBtn'
				}
			],
			columns: [
				{
					text: _('edit'),
					width: 25,
					menuDisabled: true,
					xtype: 'actioncolumn',
					tooltip: 'Edit Snippet',
					align: 'center',
					icon: 'resources/images/icons/edit.png',
					handler: function(grid, rowIndex, colIndex, actionItem, event, record){
						snippetCtrl.onSnippetBtnEdit(grid, rowIndex, colIndex, actionItem, event, record);
					}
				},
				{
					text: _('description'),
					dataIndex: 'description',
					flex: 1
				}
			],
			bbar:[
				{
					xtype: 'specialtiescombo',
					itemId: 'SoapTemplateSpecialtiesCombo',
					flex: 1
				}
			],
			viewConfig: {
				disableSelection: true,
				plugins: {
					ptype: 'treeviewdragdrop',
					dragText: _('drag_and_drop_reorganize')
				},
				listeners: {
					scope: me,
					drop: function (node, data, overModel) {
						snippetCtrl.onSnippetDrop(node, data, overModel);
					}
				}
			}
		});

		me.form = Ext.create('Ext.form.Panel', {
			autoScroll: true,
			action: 'encounter',
			region: 'center',
			itemId: 'soapForm',
			frame: true,
			fieldDefaults: {
				msgTarget: 'side'
			},
			plugins: {
				ptype: 'advanceform',
				autoSync: g('autosave'),
				syncAcl: a('edit_encounters')
			},
			items: [
				me.pWin = Ext.widget('window', {
					title: _('procedure'),
					maximized: true,
					closable: false,
					constrain: true,
					closeAction: 'hide',
					itemId: 'soapProcedureWindow',
					layout: 'fit',
					items: [
						me.pForm = Ext.widget('form', {
							bodyPadding: 10,
							layout: {
								type: 'vbox',
								align: 'stretch'
							},
							items: [
								{
									xtype: 'snomedliveproceduresearch',
									name: 'code_text',
									displayField: 'FullySpecifiedName',
									valueField: 'FullySpecifiedName',
									listeners: {
										scope: me,
										select: me.onProcedureSelect
									}
								},
								{
									xtype: 'textareafield',
									name: 'observation',
									flex: 1
								}
							]
						})
					],
					buttons: [
						{
							text: _('cancel'),
							scope: me,
							handler: me.onProcedureCancel
						},
						{
							text: _('save'),
							scope: me,
							itemId: 'encounterRecordAdd',
							handler: me.onProcedureSave
						}
					]
				}),
				{
					xtype: 'fieldset',
					title: _('subjective'),
					margin: 5,
					items: [
						me.sField = Ext.widget('textarea', {
							name: 'subjective',
							anchor: '100%',
							enableKeyEvents: true,
							margin: '5 0 10 0'
						})
					]
				},
				{
					xtype: 'fieldset',
					title: _('objective'),
					margin: 5,
					items: [
						me.oField = Ext.widget('textarea', {
							name: 'objective',
							anchor: '100%'
						}),
						me.pGrid = Ext.widget('grid', {
							frame: true,
							name: 'procedures',
							emptyText: _('no_procedures'),
							margin: '5 0 10 0',
							store: me.procedureStore,
							columns: [
								{
									text: _('code'),
									dataIndex: 'code'
								},
								{
									text: _('description'),
									dataIndex: 'code_text',
									flex: 1
								}
							],
							listeners: {
								scope: me,
								itemdblclick: me.procedureEdit
							},
							dockedItems: [
								{
									xtype: 'toolbar',
									items: [
										{
											xtype: 'tbtext',
											text: _('procedures')
										},
										'->',
										{
											text: _('new_procedure'),
											scope: me,
											handler: me.onProcedureAdd,
											iconCls: 'icoAdd'
										}
									]
								}

							]
						})
					]
				},
				{
					xtype: 'fieldset',
					title: _('assessment'),
					margin: 5,
					items: [
						me.aField = Ext.widget('textarea', {
							name: 'assessment',
							anchor: '100%'
						}),
						me.dxField = Ext.widget('icdsfieldset', {
							name: 'dxCodes',
							margin: '5 0 10 0',
							itemId: 'SoapDxCodesField'
						})
					]
				},
				{
					xtype: 'fieldset',
					title: _('plan'),
					margin: 5,
					items: [
						me.pField = Ext.widget('textarea', {
							fieldLabel: _('instructions'),
							labelAlign: 'top',
							name: 'instructions',
							margin: '0 0 10 0',
							anchor: '100%'
						}),
						{
							xtype: 'medicationsadministeredgrid',
							margin: '0 0 10 0'
						},
						{
							xtype: 'appointmentrequestgrid',
							margin: '0 0 10 0'
						},
						{
							xtype: 'careplangoalsgrid',
							margin: '0 0 10 0'
						},
						{
							xtype: 'educationresourcesgrid',
							margin: '0 0 10 0'
						}
					]
				}
			],
			buttons: [
				{
					text: _('save'),
					iconCls: 'save',
					action: 'soapSave',
					scope: me,
					itemId: 'encounterRecordAdd',
					handler: me.onSoapSave
				}
			],
			listeners: {
				scope: me,
				recordloaded: me.formRecordLoaded
			}
		});

		Ext.apply(me, {
			items: [ me.snippets, me.form ]
		});

		me.callParent(arguments);

	},

	/**
	 *
	 * @param cmb
	 * @param record
	 */
	onProcedureSelect: function(cmb, record){
		var me = this,
			form = me.pForm.getForm(),
			procedure = form.getRecord();
		procedure.set({
			code: record[0].data.ConceptId,
			code_type: record[0].data.CodeType,
			code_text: record[0].data.FullySpecifiedName
		});
	},

	/**
	 *
	 */
	onProcedureAdd: function(){
		var me = this,
			rec;
		rec = Ext.create('App.model.patient.encounter.Procedures', {
			pid: me.pid,
			eid: me.eid,
			create_uid: app.user.id,
			update_uid: app.user.id,
			create_date: new Date(),
			update_date: new Date()
		});

		me.procedureStore.add(rec);
		me.procedureEdit(null, rec);
	},

	/**
	 *
	 */
	onProcedureCancel: function(){
		this.procedureStore.rejectChanges();
		this.pWin.close();
		this.query('button[action=soapSave]')[0].enable();
		this.pWin.setTitle(_('procedure'));
	},

	/**
	 *
	 */
	onProcedureSave: function(){
		var me = this,
			form = me.pForm.getForm(),
			record = form.getRecord(),
			values = form.getValues();

		record.set(values);

		this.procedureStore.sync();
		this.pWin.close();
		this.query('button[action=soapSave]')[0].enable();
		this.pWin.setTitle(_('procedure'));
	},

	/**
	 *
	 */
	onShow: function(){
		var me = this;
		me.callParent();

		me.sField.focus();

		if(me.eid != app.patient.eid){
			me.pid = app.patient.pid;
			me.eid = app.patient.eid;
			me.procedureStore.load({
				filters: [
					{
						property: 'eid',
						value: me.eid
					}
				]
			});
		}
	},

	/**
	 *
	 * @param view
	 * @param record
	 */
	procedureEdit: function(view, record){
		if(record.data.code_text !== '' || record.data.code !== ''){
			this.pWin.setTitle(record.data.code_text + ' [' + record.data.code + ']');
		}else{
			this.pWin.setTitle(_('new_procedure'));
		}

		this.pForm.getForm().loadRecord(record);
		this.pWin.show(this.pGrid.el);
		this.query('button[action=soapSave]')[0].disable();
	},

	/**
	 *
	 * @param btn
	 */
	onSoapSave: function(btn){
		this.enc.onEncounterUpdate(btn)
	},

	/**
	 *
	 * @param form
	 * @param record
	 */
	formRecordLoaded: function(form, record){
		var store = record.dxCodes();
		store.on('write', function(){
			record.store.fireEvent('write');
		});
		this.dxField.loadIcds(record.dxCodes());
	}
});
