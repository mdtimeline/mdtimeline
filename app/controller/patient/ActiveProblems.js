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

Ext.define('App.controller.patient.ActiveProblems', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'ActiveProblemsGrid',
			selector: 'patientactiveproblemspanel'
		},
		{
			ref: 'ActiveProblemLiveSearch',
			selector: '#activeProblemLiveSearch'
		},
		{
			ref: 'AddActiveProblemBtn',
			selector: 'patientactiveproblemspanel #addActiveProblemBtn'
		},
        {
            ref: 'PatientProblemsReconciledBtn',
            selector: '#PatientProblemsReconciledBtn'
        },
        {
            ref: 'PatientProblemsActiveBtn',
            selector: '#PatientProblemsActiveBtn'
        },
        {
            ref: 'EncounterPanel',
            selector: '#encounterPanel'
        }
	],

	init: function(){
		var me = this;
		me.control({
			'patientactiveproblemspanel':{
				activate: me.onActiveProblemsGridActive,
				beforeitemcontextmenu: me.onActiveProblemsGridBeforeItemContextMenu
			},
			'#activeProblemLiveSearch':{
				select: me.onActiveProblemLiveSearchSelect
			},
			'#ActiveProblemStatusCombo':{
				select: me.onActiveProblemStatusComboSelect
			},
			'#ActiveProblemTypeCmb':{
				select: me.onActiveProblemTypeCmbSelect
			},
			'patientactiveproblemspanel #addActiveProblemBtn':{
				click: me.onAddActiveProblemBtnClick
			},
            '#PatientProblemsReconciledBtn': {
                click: me.onPatientProblemsReconciledBtnClick
            },
            '#PatientProblemsActiveBtn': {
                click: me.onPatientProblemsActiveBtnClick
            },
			'#ActiveProblemsGridActivateMenu': {
				click: me.onActiveProblemsGridActivateMenuClick
			},
			'#ActiveProblemsGridInactivateMenu': {
				click: me.onActiveProblemsGridInactivateMenu
			}
		});


		me.encController = me.getController('patient.encounter.Encounter');
	},

	onActiveProblemsGridBeforeItemContextMenu: function (grid, record, item, index, e, eOpts){
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
						itemId: 'ActiveProblemsGridActivateMenu'
					},
					{
						text: _('inactivate'),
						itemId: 'ActiveProblemsGridInactivateMenu'
					}
				]
			});
		}
		grid.context_menu.record = record;
		grid.context_menu.showAt(e.getXY())
	},

	onActiveProblemsGridActivateMenuClick: function (item){
		var record = item.up('menu').record;

		record.set({
			status: 'Active',
			status_code: '55561003',
			status_code_type: 'SNOMEDCT',
		});

		record.store.sync();
	},

	onActiveProblemsGridInactivateMenu: function (item){
		var record = item.up('menu').record;

		record.set({
			status: 'Inactive',
			status_code: '73425007',
			status_code_type: 'SNOMEDCT',
		});

		record.store.sync();
	},

	onAddActiveProblemBtnClick:function(){
		var me = this,
			grid = me.getActiveProblemsGrid(),
			store = grid.getStore(),
			encounter_record = me.encController.getEncounterRecord(),
			begin_date = encounter_record ? encounter_record.get('service_date') : app.getDate();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			status: 'Active',
			status_code: '55561003',
			status_code_type: 'SNOMEDCT',
			created_uid: app.user.id,
			create_date: app.getDate(),
			begin_date: begin_date
		});
		grid.editingPlugin.startEdit(0, 0);
	},

    onPatientProblemsReconciledBtnClick: function(){
        this.onActiveProblemsGridActive();
    },

    onPatientProblemsActiveBtnClick: function(){
        this.onActiveProblemsGridActive();
    },

	onActiveProblemsGridActive:function(){
		var grid = this.getActiveProblemsGrid(),
            store = grid.getStore(),
            reconciled = this.getPatientProblemsReconciledBtn().pressed,
            onlyActive = this.getPatientProblemsActiveBtn().pressed,
			filters = [
				{
					property: 'pid',
					value: app.patient.pid
				}
			];

		if(onlyActive){
			Ext.Array.push(filters, {
				property: 'status_code',
				value: '55561003'
			});
		}

		store.clearFilter(true);
        store.load({
            filters: filters,
            params: {
                reconciled: reconciled
            }
        });
	},

	onActiveProblemLiveSearchSelect:function(cmb, records){
		var form = cmb.up('form').getForm(),
			record = form.getRecord();

		record.set({
			code: records[0].data.ConceptId,
			code_type: records[0].data.CodeType
		});
	},

	onActiveProblemStatusComboSelect:function(cmb, records){
		var form = cmb.up('form').getForm(),
			record = form.getRecord();

		record.set({
			status: records[0].data.option_name,
			status_code: records[0].data.code,
			status_code_type: records[0].data.code_type
		});
	},

	onActiveProblemTypeCmbSelect:function(cmb, records){
		var form = cmb.up('form').getForm(),
			record = form.getRecord();

		record.set({
			problem_type: records[0].data.option_name,
			problem_type_code: records[0].data.code,
			problem_type_code_type: records[0].data.code_type
		});
	}

});
