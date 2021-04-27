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
		'App.view.patient.encounter.EncounterAddendaGrid',
		'App.view.patient.encounter.MedicationsAdministeredGrid',
		'App.view.patient.encounter.InterventionsGrid',
		'App.view.patient.encounter.HealthConcernGrid',
		'App.ux.form.fields.plugin.FieldTab'
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
			animCollapse: false,
			hideCollapseTool: true,
			store: me.snippetStore,
			features: [{ ftype:'grouping' }],
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
			cls: 'encounter-soap-form',
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
				{
					xtype: 'fieldset',
					title: _('chief_complaint'),
					margin: 10,
					items: [
						{
							xtype: 'textarea',
							anchor: '100%',
							height: 100,
							enableKeyEvents: true,
							nusaEnabled: true,
							margin: '5 0 10 0',
							name: 'chief_complaint',
							plugins: [
								{
									ptype: 'fieldtab'
								}
							]
						}
					]
				},
				{
					xtype: 'fieldset',
					title: _('subjective'),
					margin: 10,
					items: [
						me.sField = Ext.widget('textarea', {
							name: 'subjective',
							anchor: '100%',
							height: 200,
							enableKeyEvents: true,
							nusaEnabled: true,
							margin: '5 0 10 0',
							plugins: [
								{
									ptype: 'fieldtab'
								}
							]
						})
					]
				},
				{
					xtype: 'fieldset',
					title: _('objective'),
					margin: 10,
					items: [
						me.oField = Ext.widget('textarea', {
							name: 'objective',
							anchor: '100%',
							height: 200,
							nusaEnabled: true,
							plugins: [
								{
									ptype: 'fieldtab'
								}
							]
						}),
						me.physicalExamForm = Ext.widget('form', {
							title: 'Physical Exam',
							itemId: 'EncounterPhysicalExamForm',
							layout: 'fit'
						}),
						{
							xtype: 'grid',
							frame: true,
							name: 'procedures',
							emptyText: _('no_procedures'),
							margin: '5 0 10 0',
							store: me.procedureStore,
							itemId: 'EncounterProcedureGrid',
							minHeight: 100,
							columns: [
								{
									text: _('code'),
									dataIndex: 'code'
								},
								{
									text: _('description'),
									dataIndex: 'code_text',
									flex: 2
								},
								{
									text: _('body_site'),
									dataIndex: 'target_site_code_text',
									flex: 1
								}
							],
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
											itemId: 'EncounterProcedureGridAddBtn',
											iconCls: 'icoAdd'
										}
									]
								}

							]
						}
					]
				},
				{
					xtype: 'fieldset',
					title: _('assessment'),
					margin: 10,
					items: [
						me.aField = Ext.widget('textarea', {
							name: 'assessment',
							anchor: '100%',
							height: 200,
							nusaEnabled: true,
							plugins: [
								{
									ptype: 'fieldtab'
								}
							]
						}),
						me.dxField = Ext.widget('icdsfieldset', {
							name: 'dxCodes',
							margin: '5 0 10 0',
							itemId: 'SoapDxCodesField'
						}),
						{
							xtype: 'gaiaehr.combo',
							itemId: 'SoapHealthStatusCmb',
							editable: false,
							loadStore: true,
							queryMode: 'local',
							listKey: 'health_stat',
							name: 'health_status',
							fieldLabel: _('health_status'),
							labelAlign: 'top',
							margin: '0 0 10 0',
							width: 300,
						}
					]
				},
				{
					xtype: 'fieldset',
					title: _('plan'),
					margin: 10,
					items: [
						me.pField = Ext.widget('textarea', {
							fieldLabel: _('instructions'),
							labelAlign: 'top',
							name: 'instructions',
							height: 200,
							margin: '0 0 10 0',
							anchor: '100%',
							nusaEnabled: true,
							plugins: [
								{
									ptype: 'fieldtab'
								}
							]
						}),
						{
							xtype: 'medicationsadministeredgrid',
							minHeight: 125,
							margin: '0 0 10 0'
						},
						{
							xtype: 'appointmentrequestgrid',
							minHeight: 125,
							margin: '0 0 10 0'
						},
						{
							xtype: 'interventionsgrid',
							minHeight: 125,
							margin: '0 0 10 0'
						},
						{
							xtype: 'careplangoalsgrid',
							minHeight: 125,
							margin: '0 0 10 0'
						},
						{
							xtype: 'educationresourcesgrid',
							minHeight: 125,
							margin: '0 0 10 0'
						},
						{
							xtype: 'healthconcerngird',
							minHeight: 125,
							margin: '0 0 10 0'
						}
					]
				},
				{
					xtype: 'fieldset',
					title: _('addenda'),
					margin: 10,
					itemId: 'EncounterSoapAddendaFieldSet',
					items: [
						{
							xtype: 'encounteraddendagrid',
							itemId: 'EncounterSoapAddendaGrid',
							minHeight: 125,
							margin: '0 0 10 0'
						}
					]
				}
			],
			buttons: [
				{
					xtype: 'button',
					icon: 'modules/worklist/resources/images/wand.png',
					itemId: 'SoapFormReformatTextBtn',
					tooltip: 'Reformat Text :: ALT-W',
					minWidth: null,
					hidden: !Ext.isWebKit
				},
				{
					xtype: 'button',
					action: 'speechBtn',
					iconCls: 'speech-icon-inactive',
					enableToggle: true,
					minWidth: null,
					hidden: !Ext.isWebKit
				},
				'->',
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


		me.physicalExamForm.getFormItems(me.physicalExamForm, 17, function(){


		});

		me.callParent(arguments);

	},

	/**
	//  *
	//  * @param cmb
	//  * @param record
	//  */
	// onProcedureSelect: function(cmb, record){
	// 	var me = this,
	// 		form = me.pForm.getForm(),
	// 		procedure = form.getRecord();
	// 	procedure.set({
	// 		code: record[0].data.ConceptId,
	// 		code_type: record[0].data.CodeType,
	// 		code_text: record[0].data.Term
	// 	});
	// },
	//
	// /**
	//  *
	//  */
	// onProcedureAdd: function(){
	// 	var me = this,
	// 		rec;
	// 	rec = Ext.create('App.model.patient.encounter.Procedures', {
	// 		pid: me.pid,
	// 		eid: me.eid,
	// 		create_uid: app.user.id,
	// 		update_uid: app.user.id,
	// 		create_date: new Date(),
	// 		update_date: new Date()
	// 	});
	//
	// 	me.procedureStore.add(rec);
	// 	me.procedureEdit(null, rec);
	// },

	// /**
	//  *
	//  */
	// onProcedureCancel: function(){
	// 	this.procedureStore.rejectChanges();
	// 	this.pWin.close();
	// 	this.query('button[action=soapSave]')[0].enable();
	// 	this.pWin.setTitle(_('procedure'));
	// },
	//
	// /**
	//  *
	//  */
	// onProcedureSave: function(){
	// 	var me = this,
	// 		form = me.pForm.getForm(),
	// 		record = form.getRecord(),
	// 		values = form.getValues();
	//
	// 	record.set(values);
	//
	// 	this.procedureStore.sync();
	// 	this.pWin.close();
	// 	this.query('button[action=soapSave]')[0].enable();
	// 	this.pWin.setTitle(_('procedure'));
	// },

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

	// /**
	//  *
	//  * @param view
	//  * @param record
	//  */
	// procedureEdit: function(view, record){
	// 	if(record.data.code_text !== '' || record.data.code !== ''){
	// 		this.pWin.setTitle(record.data.code_text + ' [' + record.data.code + ']');
	// 	}else{
	// 		this.pWin.setTitle(_('new_procedure'));
	// 	}
	//
	// 	this.pForm.getForm().loadRecord(record);
	// 	this.pWin.show(this.pGrid.el);
	// 	this.query('button[action=soapSave]')[0].disable();
	// },

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
