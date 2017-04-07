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

Ext.define('App.controller.patient.ItemsToReview', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'ItemsToReviewPanel',
			selector: '#ItemsToReviewPanel'
		},
		{
			ref: 'ItemsToReviewImmuGrid',
			selector: '#ItemsToReviewPanel #ItemsToReviewImmuGrid'
		},
		{
			ref: 'ItemsToReviewAllergiesGrid',
			selector: '#ItemsToReviewPanel #ItemsToReviewAllergiesGrid'
		},
		{
			ref: 'ItemsToReviewActiveProblemsGrid',
			selector: '#ItemsToReviewPanel #ItemsToReviewActiveProblemsGrid'
		},
		{
			ref: 'ItemsToReviewMedicationsGrid',
			selector: '#ItemsToReviewPanel #ItemsToReviewMedicationsGrid'
		},
		{
			ref: 'ReviewSmokingStatusCombo',
			selector: '#ItemsToReviewPanel #reviewsmokingstatuscombo'
		},
        {
            ref: 'ItemsToReviewEducationGivenField',
            selector: '#ItemsToReviewPanel #ItemsToReviewEducationGivenField'
        },
		{
			ref: 'EncounterMedicationReconciliations',
			selector: '#ItemsToReviewPanel #EncounterMedicationReconciliations'
		},
		{
			ref: 'EncounterMedicationReconciliationsDateField',
			selector: '#ItemsToReviewPanel #EncounterMedicationReconciliationsDateField'
		},
        {
            ref: 'EncounterSummaryCareProvided',
            selector: '#ItemsToReviewPanel #EncounterSummaryCareProvided'
        }
	],

	init: function(){
		var me = this;
		me.control({
			'#ItemsToReviewPanel':{
				activate: me.storesLoad
			},
			'#encounterRecordAdd':{
				click: me.onReviewAll
			},
			'#ItemsToReviewPanel #ItemsToReviewEducationGivenField':{
				change: me.onItemsToReviewEducationGivenFieldChange
			},
            '#ItemsToReviewPanel #EncounterMedicationReconciliations':{
                change: me.onEncounterMedicationReconciliationsChange
            },
            '#ItemsToReviewPanel #EncounterMedicationReconciliationsDateField':{
                change: me.onEncounterMedicationReconciliationsDateFieldChange
            },
            '#ItemsToReviewPanel #EncounterSummaryCareProvided':{
                change: me.onEncounterSummaryCareProvidedChange
            }
		});
	},

	storesLoad: function(){
		var me = this,
			params = {
				filters: [
					{
						property: 'pid',
						value: app.patient.pid
					}
				]
			};

		me.getReviewSmokingStatusCombo().reset();
		me.getItemsToReviewImmuGrid().getStore().load(params);
		me.getItemsToReviewAllergiesGrid().getStore().load(params);
		me.getItemsToReviewActiveProblemsGrid().getStore().load(params);
		me.getItemsToReviewMedicationsGrid().getStore().load(params);

		me.smokeStatusStore = app.getController('patient.Social').smokeStatusStore;

		/**
		 * add the callback function to handle the Smoking Status
		 */
		params.callback = function(){
			if(this.last()){
				me.getReviewSmokingStatusCombo().setValue(this.last().data.status);
			}
		};
		me.smokeStatusStore.load(params);

		var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();

        me.getEncounterMedicationReconciliations().suspendEvents(false);
        me.getEncounterMedicationReconciliationsDateField().suspendEvents(false);
        me.getEncounterSummaryCareProvided().suspendEvents(false);
        me.getItemsToReviewEducationGivenField().suspendEvents(false);

        me.getEncounterMedicationReconciliations().setValue(encounter.get('medication_reconciliations'));
        me.getEncounterMedicationReconciliationsDateField().setValue(encounter.get('medication_reconciliations_date'));
        me.getEncounterSummaryCareProvided().setValue(encounter.get('summary_care_provided'));
        me.getItemsToReviewEducationGivenField().setValue(encounter.get('patient_education_given'));

        me.getEncounterMedicationReconciliations().resumeEvents();
        me.getEncounterMedicationReconciliationsDateField().resumeEvents();
        me.getEncounterSummaryCareProvided().resumeEvents();
        me.getItemsToReviewEducationGivenField().resumeEvents();
	},

	onReviewAll: function(){

		if(this.getReviewSmokingStatusCombo().isValid()){

			var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();

			encounter.set({
				review_active_problems: true,
				review_allergies: true,
				review_dental: true,
				review_immunizations: true,
				review_medications: true,
				review_smoke: true,
				review_surgery: true,
			});

			encounter.save({
				success: function(){
					app.msg(_('sweet'), _('items_to_review_save_and_review'));
				},
				failure: function(){
					app.msg(_('oops'), _('items_to_review_entry_error'));
				}
			});
		}
	},

	onItemsToReviewEducationGivenFieldChange: function(field, newValue, oldValue, eOpts){
		var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();
		encounter.set({
			patient_education_given: newValue
		});
        this.saveEncounterChanges(encounter);
	},

    onEncounterMedicationReconciliationsChange: function(field, newValue, oldValue, eOpts){
        var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();

        encounter.set({
            medication_reconciliations: newValue
        });

        this.saveEncounterChanges(encounter);
    },

	onEncounterMedicationReconciliationsDateFieldChange: function(field, newValue, oldValue, eOpts){
        var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();

        if(!field.isValid()) return;

        encounter.set({
            medication_reconciliations_date: newValue
        });

        this.saveEncounterChanges(encounter);
    },

    onEncounterSummaryCareProvidedChange: function(field, newValue, oldValue, eOpts){
        var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();
        encounter.set({
            summary_care_provided: newValue
        });
        this.saveEncounterChanges(encounter);
    },

    saveEncounterChanges: function(encounterObject){
        encounterObject.setDirty();
        if(!Ext.Object.isEmpty(encounterObject.getChanges())){
            encounterObject.save({
                success: function(){
                    app.msg(_('sweet'), _('record_saved'));
                },
                failure: function(){
                    app.msg(_('oops'), _('record_error'));
                }
            });
        }
    }

});
