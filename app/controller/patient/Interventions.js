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

Ext.define('App.controller.patient.Interventions', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'InterventionsGrid',
			selector: 'interventionsgrid'
		},
		{
			ref: 'InterventionWindow',
			selector: '#InterventionWindow'
		},
		{
			ref: 'InterventionForm',
			selector: '#InterventionForm'
		},
		{
			ref: 'InterventionGridAddBtn',
			selector: '#InterventionGridAddBtn'
		},
		{
			ref: 'SoapPanelForm',
			selector: '#soapPanel form'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'viewport': {
				'beforeencounterload': me.onBeforeOpenEncounter
			},
			'#InterventionsGrid': {
				'itemdblclick': me.onInterventionsGridItemDblClick
			},
			'#InterventionGridAddBtn': {
				'click': me.onInterventionGridAddBtnClick
			},
			'#InterventionSearchField': {
				'select': me.onInterventionSearchFieldSelect
			},
			'#InterventionFormCancelBtn': {
				'click': me.onInterventionFormCancelBtnClick
			},
			'#InterventionFormSaveBtn': {
				'click': me.onInterventionFormSaveBtnClick
			},


			// '#CarePlanGoalSearchField': {
			// 	'select': me.onCarePlanGoalSearchFieldSelect
			// },
			//

			// '#CarePlanGoalPlanDateContainer > button': {
			// 	'click': me.onCarePlanGoalPlanDateContainerButtonsClick
			// }
		});
	},

	onBeforeOpenEncounter: function(encounter){
		if(this.getInterventionsGrid()){
			this.getInterventionsGrid().getStore().load({
				filters:[
					{
						property: 'eid',
						value: encounter.data.eid
					}
				]
			});
		}
	},

	onInterventionsGridItemDblClick: function(grid, record){
		this.showInterventionsGrid();
		this.getInterventionForm().getForm().loadRecord(record);
	},

	onInterventionGridAddBtnClick: function(btn){
		var grid = btn.up('grid'),
			store = grid.getStore(),
			records;

		records = store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			intervention_type: 'RecommendedNutrition',
			create_uid: app.user.id,
			create_date: app.getDate(),
			update_uid: app.user.id,
			update_date: app.getDate()
		});

		this.showInterventionsGrid();
		this.getInterventionForm().getForm().loadRecord(records[0]);
	},

	onInterventionSearchFieldSelect: function(cmb, selection){
		var record = cmb.up('form').getForm().getRecord();

		record.set({
			'code': selection[0].get('ConceptId'),
			'code_text': selection[0].get('Term'),
			'code_type': selection[0].get('CodeType')
		});

	},

	onInterventionFormCancelBtnClick: function(btn){
		this.getInterventionsGrid().getStore().rejectChanges();
		this.getInterventionForm().getForm().reset();
		this.getInterventionWindow().close();
	},

	onInterventionFormSaveBtnClick: function(btn){
		var me = this,
			form = me.getInterventionForm().getForm(),
			record = form.getRecord(),
			values = form.getValues();

		if(form.isValid()){

			record.set(values);

			if(Ext.Object.isEmpty(record.getChanges())){
				form.reset();
				me.getInterventionWindow().close();
				return;
			}

			record.set({
				update_uid: app.user.id,
				update_date: app.getDate()
			});

			record.store.sync({
				success:function(){
					app.msg(_('sweet'), _('record_saved'));
					form.reset();
					me.getInterventionWindow().close();
				},
				failure:function(){
					app.msg(_('oops'), _('record_error'), true);
					form.reset();
					me.getInterventionWindow().close();
				}
			});


		}
	},

	showInterventionsGrid: function(){
		if(!this.getInterventionWindow()){
			Ext.create('App.view.patient.encounter.InterventionWindow');
		}
		return this.getInterventionWindow().show();
	},




	onCarePlanGoalSearchFieldSelect: function(cmb, records){
		var me = this,
			form = me.getCarePlanGoalsNewForm().getForm(),
			record = form.getRecord();

		record.set({
			'goal_code': records[0].data.ConceptId,
			'goal_code_type': records[0].data.CodeType
		});
	},







});
