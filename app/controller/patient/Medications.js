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

Ext.define('App.controller.patient.Medications', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'MedicationsPanel',
			selector: 'patientmedicationspanel'
		},
		{
			ref: 'PatientMedicationsGrid',
			selector: '#patientMedicationsGrid'
		},
		{
			ref: 'addPatientMedicationBtn',
			selector: '#addPatientMedicationBtn'
		},
		{
			ref: 'PatientMedicationReconciledBtn',
			selector: '#PatientMedicationReconciledBtn'
		},
        {
            ref: 'PatientMedicationActiveBtn',
            selector: '#PatientMedicationActiveBtn'
        },
		{
			ref: 'PatientMedicationUserLiveSearch',
			selector: '#PatientMedicationUserLiveSearch'
		},
		{
			ref: 'PatientMedicationLiveSearch',
			selector: '#PatientMedicationLiveSearch'
		},

		// administer refs
		{
			ref: 'AdministeredMedicationsGrid',
			selector: '#AdministeredMedicationsGrid'
		},
		{
			ref: 'AdministeredMedicationsLiveSearch',
			selector: '#AdministeredMedicationsLiveSearch'
		},
		{
			ref: 'AdministeredMedicationsUserLiveSearch',
			selector: '#AdministeredMedicationsUserLiveSearch'
		},
		{
			ref: 'AdministeredMedicationsAddBtn',
			selector: '#AdministeredMedicationsAddBtn'
		},
		{
			ref: 'ReviewMedicationsBtn',
			selector: '#ReviewMedicationsBtn'
		},
		{
			ref: 'EncounterProcedureGrid',
			selector: '#EncounterProcedureGrid'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'viewport': {
				encounterload: me.onViewportEncounterLoad
			},
            'patientmedicationspanel': {
                activate: me.onMedicationsPanelActive
            },
			'#patientMedicationsGrid': {
				beforeedit: me.onPatientMedicationsGridBeforeEdit,
				beforeitemcontextmenu: me.onPatientMedicationsGridBeforeItemContextMenu
			},
			'#PatientMedicationEndDateField': {
				select: me.onPatientMedicationEndDateFieldSelect
			},
			'#addPatientMedicationBtn': {
				click: me.onAddPatientMedicationBtnClick
			},
			'#PatientMedicationLiveSearch': {
				select: me.onMedicationLiveSearchSelect
			},
			'#PatientMedicationActiveBtn': {
				click: me.onPatientMedicationActiveBtnClick
			},
            '#PatientMedicationReconciledBtn': {
                click: me.onPatientMedicationReconciledBtnClick
            },
			'#PatientMedicationUserLiveSearch': {
				select: me.onPatientMedicationUserLiveSearchSelect,
                reset: me.onPatientMedicationUserLiveSearchReset
			},

			// Administrator controls
			'#AdministeredMedicationsGrid': {
				beforeedit: me.onAdministeredMedicationsGridBeforeEdit
			},
			'#AdministeredMedicationsLiveSearch': {
				select: me.onAdministeredMedicationsLiveSearchSelect
			},
			'#AdministeredMedicationsUserLiveSearch': {
				select: me.onAdministeredMedicationsUserLiveSearchSelect
			},
			'#AdministeredMedicationsAddBtn': {
				click: me.onAdministeredMedicationsAddBtnClick
			},
			'#PatientMedicationsGridActivateMenu': {
				click: me.onPatientMedicationsGridActivateMenuClick
			},
			'#ReviewMedicationsBtn': {
				click: me.onReviewMedicationsBtnClick
			},
			'#PatientMedicationsGridInactivateMenu': {
				click: me.onPatientMedicationsGridInactivateMenu
			}
		});
	},

	onPatientMedicationEndDateFieldSelect: function (field){
		var form = field.up('form').getForm();

		form.findField('is_active').setValue(false);

		form.getRecord().set({
			is_active: false,
			active: false
		});
	},

	onPatientMedicationsGridBeforeItemContextMenu: function (grid, record, item, index, e, eOpts){
		e.preventDefault();
		this.showContextMenu(grid, record, e);
	},

	showContextMenu: function (grid, record, e){
		if(!grid.context_menu){
			grid.context_menu = Ext.widget('menu', {
				margin: '0 0 10 0',
				items: [
					{
						text: _('activate'),
						itemId: 'PatientMedicationsGridActivateMenu'
					},
					{
						text: _('inactivate'),
						itemId: 'PatientMedicationsGridInactivateMenu'
					}
				]
			});
		}
		grid.context_menu.record = record;
		grid.context_menu.showAt(e.getXY())
	},

	onPatientMedicationsGridActivateMenuClick: function (item){
		var record = item.up('menu').record;

		record.set({
			is_active: true,
			end_date: null
		});

		record.store.sync();
	},

	onPatientMedicationsGridInactivateMenu: function (item){
		var record = item.up('menu').record;

		record.set({
			is_active: false,
			end_date: app.getDate()
		});

		record.store.sync();
	},


	onViewportEncounterLoad: function(encounter){

	},

	onAdministeredMedicationsGridBeforeEdit: function(plugin, context){
		var me = this,
			field = me.getAdministeredMedicationsUserLiveSearch();

		field.forceSelection = false;
		field.setValue(context.record.data.administered_by);
		Ext.Function.defer(function(){
			field.forceSelection = true;
		}, 200);

	},

	onAdministeredMedicationsLiveSearchSelect: function(cmb, records){
		var form = cmb.up('form').getForm();

		form.getRecord().set({
			RXCUI: records[0].data.RXCUI,
			CODE: records[0].data.CODE,
            GS_CODE: records[0].data.GS_CODE,
			NDC: records[0].data.NDC,
			TTY: records[0].data.TTY
		});
	},

	onAdministeredMedicationsUserLiveSearchSelect: function(cmb, records){
		var administered_by = records[0],
			record = cmb.up('form').getForm().getRecord();

		record.set({administered_uid: administered_by.data.id});
	},

	onAdministeredMedicationsAddBtnClick: function(){
		var me = this,
			grid = me.getAdministeredMedicationsGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			created_uid: app.user.id,
			created_date: new Date(),
			begin_date: new Date(),
			end_date: new Date(),
			administered_date: new Date(),
			administered_uid: app.user.id,
			title: app.user.title,
			fname: app.user.fname,
			mname: app.user.mname,
			lname: app.user.lname
		});

		grid.editingPlugin.startEdit(0, 0);
	},

	onPatientMedicationsGridBeforeEdit: function(plugin, context){

	},

	onPatientMedicationUserLiveSearchSelect: function(cmb, records){
		var user = records[0],
			record = cmb.up('form').getForm().getRecord();
        record.set({fname: user.data.fname});
        record.set({lname: user.data.lname});
        record.set({mname: user.data.mname});
        record.set({title: user.data.title});
		record.set({administered_uid: user.data.id});
	},

    onPatientMedicationUserLiveSearchReset: function(cmb){
        var record = cmb.up('form').getForm().getRecord();
        record.set({fname: ''});
        record.set({lname: ''});
        record.set({mname: ''});
        record.set({title: ''});
        record.set({administered_uid: ''});
    },

	onAddPatientMedicationBtnClick: function(){
		var me = this,
			grid = me.getPatientMedicationsGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			created_uid: app.user.id,
			create_date: new Date(),
			created_date: new Date(),
			begin_date: new Date(),
			is_active: true
		});
		grid.editingPlugin.startEdit(0, 0);
	},

	onReviewMedicationsBtnClick: function(){
		var me = this;
		var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();
		encounter.set({review_medications: true});
		encounter.save({
			success: function(){
				var store = me.getEncounterProcedureGrid().getStore(),
					now = new Date();

				app.msg(_('sweet'), _('items_to_review_save_and_review'));

				var record_index = store.findBy(function(record, id){
					var enc_id = record.get('eid');
					var procedure_code = record.get('code')

					return enc_id === app.patient.eid && procedure_code === '428191000124101';
				});

				if(record_index === -1) {

					store.add({
						pid: app.patient.pid,
						eid: app.patient.eid,
						create_uid: app.user.id,
						update_uid: app.user.id,
						create_date: now,
						update_date: now,
						code: '428191000124101',
						code_text: 'Documentation of current medications (procedure)',
						code_type: 'SNOMED-CT',
						performer_id: 0,
						procedure_date: now,
						encounter_dx_id:0
					});

					// store.add(procedure_record);
					store.sync({
						callback: function (){
							app.msg(_('sweet'), 'Documentation of current medications (procedure) Added');
						}
					});


				}
			},
			failure: function(){
				app.msg(_('oops'), _('items_to_review_entry_error'));
			}
		});
	},

	onMedicationLiveSearchSelect: function(cmb, records){
		var form = cmb.up('form').getForm(),
			record = records[0],
			order_record = form.getRecord();

		order_record.set({
			RXCUI: record.data.RXCUI,
			CODE: record.data.CODE,
            GS_CODE: record.data.GS_CODE,
			NDC: record.data.NDC,
			TTY: record.data.TTY
		});

		var data = {};

		Rxnorm.getMedicationAttributesByRxcuiApi(record.data.RXCUI, function(response){

			if(response.propConceptGroup){
				response.propConceptGroup.propConcept.forEach(function(propConcept){

					if(propConcept.propCategory != 'ATTRIBUTES' && propConcept.propCategory != 'CODES') return;

					if(!data[propConcept.propCategory]){
						data[propConcept.propCategory] = {};
					}
					var propName = propConcept.propName.replace(' ', '_');
					data[propConcept.propCategory][propName] = propConcept.propValue;
				});
			}

			if(data.ATTRIBUTES && data.ATTRIBUTES.SCHEDULE && data.ATTRIBUTES.SCHEDULE != '0'){
				order_record.set({ is_controlled: true });
			}
		});

	},

	onPatientMedicationReconciledBtnClick: function(){
		this.onMedicationsPanelActive();
	},

    onPatientMedicationActiveBtnClick: function(){
        this.onMedicationsPanelActive();
    },

    onMedicationsPanelActive: function(){
        var store = this.getPatientMedicationsGrid().getStore(),
            reconciled = this.getPatientMedicationReconciledBtn().pressed,
            active = this.getPatientMedicationActiveBtn().pressed;

        store.clearFilter(true);
        store.load({
            filters: [
                {
                    property: 'pid',
                    value: app.patient.pid
                }
            ],
            params: {
                reconciled: reconciled,
                active: active
            }
        });
    }

});
