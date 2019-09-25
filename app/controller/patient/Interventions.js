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

	bmi_underweight_intervention: {
		description: 'Underweight Diet Education Plan Intervention',
		reason: 'BMI below 18.5 OR above > 25 kg/m2',
		code: '370847001',
		code_text: 'Provides instruction regarding dietary needs',
		code_type: 'SNOMEDCT',
		dx_code: '248342006',
		dx_code_text: 'Underweight (finding)',
		dx_code_type: 'SNOMEDCT',
		threshold_in_months: -3
	},

	bmi_overweight_intervention: {
		description: 'Overweight Diet Education Plan Intervention',
		reason: 'BMI below 18.5 OR above > 25 kg/m2',
		code: '370847001',
		code_text: 'Provides instruction regarding dietary needs',
		code_type: 'SNOMEDCT',
		dx_code: '238131007',
		dx_code_text: 'Overweight (finding)',
		dx_code_type: 'SNOMEDCT',
		threshold_in_months: -3
	},

	init: function(){
		var me = this;
		me.control({
			'viewport': {
				beforeencounterload: me.onBeforeOpenEncounter,
				patientvitalsload: me.onPatientVitalsLoad
			},
			'#InterventionsGrid': {
				itemdblclick: me.onInterventionsGridItemDblClick
			},
			'#InterventionGridAddBtn': {
				click: me.onInterventionGridAddBtnClick
			},
			'#InterventionSearchField': {
				select: me.onInterventionSearchFieldSelect
			},
			'#InterventionFormCancelBtn': {
				click: me.onInterventionFormCancelBtnClick
			},
			'#InterventionFormSaveBtn': {
				click: me.onInterventionFormSaveBtnClick
			},
			'#InterventionUnableToPerformField': {
				select: me.onInterventionUnableToPerformFieldSelect
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

	onInterventionUnableToPerformFieldSelect: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord(),
			selected_record = combo.findRecordByValue(combo.getValue());

		form_record.set({
			not_performed_code: selected_record.get('code'),
			not_performed_code_type: selected_record.get('code_type'),
			not_performed_code_text: selected_record.get('option_name'),
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

			if(Ext.Object.isEmpty(record.getChanges()) && !record.phantom){
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

	onPatientVitalsLoad: function(ctrl, encounter_record, vitals_records, vitals_store){

		var me = this,
			encounter_eid = encounter_record.get('eid'),
			patient_age_years = app.patient.age.DMY.years,

			underweight_found = vitals_store.findBy( function (vital_record) {
				var bmi = vital_record.get('bmi');
				return encounter_eid === vital_record.get('eid') && bmi > 1 && bmi <= 18.5;
			}) !== -1,
			overweight_found = vitals_store.findBy( function (vital_record) {
				var bmi = vital_record.get('bmi');
				return encounter_eid === vital_record.get('eid') && bmi >= 25;
			}) !== -1,
			intervention, params;

		// is adult of no overweight
		if(patient_age_years < 18) return;

		if(overweight_found){
			intervention = me.bmi_overweight_intervention;
		}else if(underweight_found){
			intervention = me.bmi_underweight_intervention;
		}else{
			return;
		}

		params = {
			filters: [
				{
					property: 'pid',
					value: encounter_record.get('pid')
				},
				{
					property: 'code',
					value: intervention.code
				},
				{
					property: 'dx_code',
					value: intervention.dx_code
				},
				{
					property: 'create_date',
					operator: '>=',
					value: Ext.Date.format(Ext.Date.add(new Date(), Ext.Date.MONTH, intervention.threshold_in_months), 'Y-m-d 00:00:00')
				},
			]
		};

		Interventions.getPatientIntervention(params, function(response) {
			if(response.data === false){
				me.promptIntervention(intervention);
			}
		});

	},

	promptIntervention: function (intervention) {
		var me = this;

		Ext.Msg.show({
			title: intervention.description + ' Required',
			msg: 'Would you like to record this Intervention?',
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn) {
				if(btn === 'yes'){
					me.addAutoIntervention(intervention);
				}
			}
		});
	},

	addAutoIntervention: function (intervention) {
		var grid = this.getInterventionsGrid(),
			store = grid.getStore(),
			records;

		records = store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			intervention_type: 'RecommendedNutrition',

			code: intervention.code,
			code_text: intervention.code_text,
			code_type: intervention.code_type,
			dx_code: intervention.dx_code,
			dx_code_text: intervention.dx_code_text,
			dx_code_type: intervention.dx_code_type,

			create_uid: app.user.id,
			create_date: app.getDate(),
			update_uid: app.user.id,
			update_date: app.getDate()
		});

		this.showInterventionsGrid();
		this.getInterventionForm().getForm().loadRecord(records[0]);
	}

});
