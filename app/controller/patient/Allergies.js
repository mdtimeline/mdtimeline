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

Ext.define('App.controller.patient.Allergies', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'AllergiesGrid',
			selector: 'patientallergiespanel'
		},
		{
			ref: 'AddAllergyBtn',
			selector: 'patientallergiespanel #addAllergyBtn'
		},
		{
			ref: 'ReviewAllergiesBtn',
			selector: 'patientallergiespanel #reviewAllergiesBtn'
		},
		{
			ref: 'ActiveAllergyBtn',
			selector: 'patientallergiespanel #activeAllergyBtn'
		},
		{
			ref: 'PatientAllergyReconciledBtn',
			selector: '#PatientAllergyReconciledBtn'
		},
		{
			ref: 'AllergyCombo',
			selector: '#allergyCombo'
		},
		{
			ref: 'AllergyTypesCombo',
			selector: '#allergyTypesCombo'
		},
		{
			ref: 'AllergySearchCombo',
			selector: '#allergySearchCombo'
		},
		{
			ref: 'AllergyMedicationCombo',
			selector: '#allergyMedicationCombo'
		},
		{
			ref: 'allergyCvxLiveSearch',
			selector: '#allergyCvxLiveSearch'
		},
		{
			ref: 'AllergyFoodCombo',
			selector: '#allergyFoodCombo'
		},
		{
			ref: 'AllergyMetalCombo',
			selector: '#allergyMetalCombo'
		},
		{
			ref: 'AllergyReactionCombo',
			selector: '#allergyReactionCombo'
		},
		{
			ref: 'AllergySeverityCombo',
			selector: '#allergySeverityCombo'
		},
		{
			ref: 'AllergyLocationCombo',
			selector: '#allergyLocationCombo'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientallergiespanel': {
				activate: me.onAllergiesGridActivate,
                beforeedit: me.onAllergiesGridBeforeEdit
			},
			'patientallergiespanel #addAllergyBtn': {
				click: me.onAddAllergyBtnClick
			},
			'patientallergiespanel #activeAllergyBtn': {
				toggle: me.onActiveAllergyBtnToggle
			},
			'patientallergiespanel #PatientAllergyReconciledBtn': {
				toggle: me.onPatientAllergyReconciledBtnToggle
			},
			'patientallergiespanel #reviewAllergiesBtn': {
				click: me.onReviewAllergiesBtnClick
			},
			'#allergyTypeCombo': {
				select: me.onAllergyTypeComboSelect
			},
			'#allergyMedicationCombo': {
				select: me.onAllergyLiveSearchSelect
			},
			'#allergyCvxLiveSearch': {
				select: me.onAllergyCvxLiveSearchSelect
			},
			'#allergyMetalCombo': {
				select: me.onAllergyMetalComboSelect
			},
			'#allergyFoodCombo': {
				select: me.onAllergyFoodComboSelect
			},
			'#allergySearchCombo': {
				select: me.onAllergySearchComboSelect
			},
			'#allergyLocationCombo': {
				change: me.onAllergyLocationComboChange
			},
			'#allergyReactionCombo': {
				select: me.onAllergyReactionComboSelect
			},
			'#allergySeverityCombo': {
				select: me.onAllergySeverityComboSelect
			},
			'#allergyStatusCombo': {
				select: me.onAllergyStatusComboSelect
			}
		});
	},

	onAllergySearchComboSelect:function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();
		record.set({
			allergy_code: records[0].data.allergy_code,
			allergy_code_type: records[0].data.allergy_code_type
		});
	},

	onAllergyReactionComboSelect:function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();
		record.set({
			reaction_code: records[0].data.code,
			reaction_code_type: records[0].data.code_type
		});
	},

	onAllergySeverityComboSelect:function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();
		record.set({
			severity_code: records[0].data.code,
			severity_code_type: records[0].data.code_type
		});
	},

	onAllergyStatusComboSelect:function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();

		record.set({
			status_code: records[0].data.code,
			status_code_type: records[0].data.code_type
		});
	},

	onAllergyLiveSearchSelect: function(cmb, records){
		var form = cmb.up('form').getForm();

		form.getRecord().set({
			allergy: records[0].data.STR,
			allergy_code: records[0].data.RXCUI,
			allergy_code_type: records[0].data.CodeType
		});
	},

	onAllergyCvxLiveSearchSelect: function(cmb, records){
		var form = cmb.up('form').getForm();

		form.getRecord().set({
			allergy: records[0].data.name,
			allergy_code: records[0].data.cvx_code,
			allergy_code_type: 'CVX'
		});
	},

	onAllergyMetalComboSelect: function(cmb, records){
		var form = cmb.up('form').getForm();

		form.getRecord().set({
			allergy: records[0].get('Term'),
			allergy_code: records[0].get('ConceptId'),
			allergy_code_type: records[0].get('CodeType')
		});
	},

	onAllergyFoodComboSelect: function (cmb, records) {
		var form = cmb.up('form').getForm();

		form.getRecord().set({
			allergy: records[0].get('option_name'),
			allergy_code: records[0].get('code'),
			allergy_code_type: records[0].get('code_type')
		});
	},

	onAllergyTypeComboSelect: function(combo, records){

		var me = this,
			record = records[0],
			code = record.data.code,
			isDrug = (code === '419511003' || code === '416098002' || code === '59037007'),
			isFood = (code === '414285001' || code === '235719002' || code === '418471000'),
			isMetal = code === '300915004',
			isDrugCvx = isDrug && record.data.option_value.search(/vaccine/i) !== -1,
			isElse = !isDrug && !isMetal && !isFood;

		// if is CVX can not be Drug
		// this is here because a Vaccine is a drug but not for this purpose
		if(isDrugCvx){
			isDrug = false;
		}

		me.getAllergyMedicationCombo().setVisible(isDrug);
		me.getAllergyMedicationCombo().setDisabled(!isDrug);

		me.getAllergyCvxLiveSearch().setVisible(isDrugCvx);
		me.getAllergyCvxLiveSearch().setDisabled(!isDrugCvx);

		me.getAllergyMetalCombo().setVisible(isMetal);
		me.getAllergyMetalCombo().setDisabled(!isMetal);

		me.getAllergyFoodCombo().setVisible(isFood);
		me.getAllergyFoodCombo().setDisabled(!isFood);

		me.getAllergySearchCombo().setVisible(isElse);
		me.getAllergySearchCombo().setDisabled(!isElse);

		if(isDrug){
			me.getAllergyMedicationCombo().reset();
		}else if(isDrugCvx) {
			me.getAllergyCvxLiveSearch().reset();
		}else if(isMetal) {
			me.getAllergyMedicationCombo().reset();
		}else if(isFood) {
			me.getAllergyFoodCombo().reset();
		}else{
			me.getAllergySearchCombo().store.load();
		}

		combo.up('form').getForm().getRecord().set({
			allergy_type_code: record.data.code,
			allergy_type_code_type: record.data.code_type
		});
	},

	onAllergyLocationComboChange: function(combo, record){
		var me = this,
			list,
			value = combo.getValue();

		if(value == 'Skin'){
			list = 80;
		}else if(value == 'Local'){
			list = 81;
		}else if(value == 'Abdominal'){
			list = 82;
		}else if(value == 'Systemic / Anaphylactic'){
			list = 83;
		}

		me.getAllergyReactionCombo().getStore().load({
			params: {
				list_id: list
			}
		});
	},

    /**
     * When a row is selected, it will look for the RowEditor plugin and get the form, then set the apropreate COMBO
     * for the Allergy, if it is a RxNorm medication or from the Allergy Combo. Finally set it's original value.
     * @param plugin
     * @param context
     */
    onAllergiesGridBeforeEdit: function(plugin, context){
        var RowForm = plugin.editor.editingPlugin.getEditor(),
            AllergyMedicationCombo = RowForm.query('#allergyMedicationCombo')[0],
            AllergyMetalCombo = RowForm.query('#allergyMetalCombo')[0],
            AllergySearchCombo = RowForm.query('#allergySearchCombo')[0],
	        AllergyFoodCombo = RowForm.query('#allergyFoodCombo')[0],
			AllergyCvxLiveSearch = RowForm.query('#allergyCvxLiveSearch')[0],
	        allergy_type_code = context.record.get('allergy_type_code'),
			allergy_type = context.record.get('allergy_type'),
	        isDrug = (allergy_type_code === '419511003' || allergy_type_code === '416098002' || allergy_type_code === '59037007'),
	        isMetal = allergy_type_code === '300915004',
	        isFood = allergy_type_code === '414285001' || allergy_type_code === '235719002' || allergy_type_code === '418471000',
			isDrugCvx = isDrug && allergy_type.search(/vaccine/i) !== -1,
	        isElse = !isDrug && !isMetal && !isFood;

		// if is CVX can not be Drug
		// this is here because a Vaccine is a drug but not for this purpose
		if(isDrugCvx){
			isDrug = false;
		}

	    AllergyMedicationCombo.setVisible(isDrug);
	    AllergyMedicationCombo.setDisabled(!isDrug);

		AllergyCvxLiveSearch.setVisible(isDrugCvx);
		AllergyCvxLiveSearch.setDisabled(!isDrugCvx);

	    AllergyMetalCombo.setVisible(isMetal);
	    AllergyMetalCombo.setDisabled(!isMetal);

	    AllergyFoodCombo.setVisible(isFood);
	    AllergyFoodCombo.setDisabled(!isFood);

	    AllergySearchCombo.setVisible(isElse);
	    AllergySearchCombo.setDisabled(!isElse);

	    if(isDrug){
		    AllergyMedicationCombo.setValue(context.record.get('allergy'));
	    }else if(isDrugCvx){
			AllergyCvxLiveSearch.setValue(context.record.get('allergy'));
	    }else if(isMetal){
		    AllergyMetalCombo.setValue(context.record.get('allergy'));
	    }else if(isFood){
		    AllergyFoodCombo.setValue(context.record.get('allergy'));
	    }else {
		    AllergySearchCombo.setValue(context.record.get('allergy'));
	    }
    },

	onAllergiesGridActivate: function(){
		var store = this.getAllergiesGrid().getStore(),
			reconciled = this.getPatientAllergyReconciledBtn().pressed,
			active = this.getActiveAllergyBtn().pressed,
			filters = [
				{
					property: 'pid',
					value: app.patient.pid
				}
			];

		if(reconciled){
			filters = Ext.Array.push(filters, {
				property: 'reconciled',
				operator: '!=',
				value: '1'
			});
		}

		if(active){
			filters = Ext.Array.push(filters, {
				property: 'status',
				value: 'Active'
			});
		}

		store.clearFilter(true);
		store.filter(filters);
	},

	onAddAllergyBtnClick: function(){
		var me = this,
			grid = me.getAllergiesGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			created_uid: app.user.id,
			uid: app.user.id,
			pid: app.patient.pid,
			eid: app.patient.eid,
			create_date: new Date(),
			begin_date: new Date()
		});
		grid.editingPlugin.startEdit(0, 0);
	},

	onActiveAllergyBtnToggle: function(btn, pressed){
		this.onAllergiesGridActivate();
	},

	onPatientAllergyReconciledBtnToggle: function(btn, pressed){
		this.onAllergiesGridActivate();
	},

	beforeAllergyEdit: function(editor, e){
		this.allergieMedication.setValue(e.record.data.allergy);
	},

	onReviewAllergiesBtnClick: function(){
		var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();
		encounter.set({review_allergies: true});
		encounter.save({
			success: function(){
				app.msg(_('sweet'), _('items_to_review_save_and_review'));
			},
			failure: function(){
				app.msg(_('oops'), _('items_to_review_entry_error'));
			}
		});
	}

});
