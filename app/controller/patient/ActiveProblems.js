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
        }
	],

	init: function(){
		var me = this;
		me.control({
			'patientactiveproblemspanel':{
				activate: me.onActiveProblemsGridActive
			},
			'#activeProblemLiveSearch':{
				select: me.onActiveProblemLiveSearchSelect
			},
			'#ActiveProblemStatusCombo':{
				select: me.onActiveProblemStatusComboSelect
			},
			'patientactiveproblemspanel #addActiveProblemBtn':{
				click: me.onAddActiveProblemBtnClick
			},
            '#PatientProblemsReconciledBtn': {
                click: me.onPatientProblemsReconciledBtnClick
            },
            '#PatientProblemsActiveBtn': {
                click: me.onPatientProblemsActiveBtnClick
            }
		});
	},


	onAddActiveProblemBtnClick:function(){
		var me = this,
			grid = me.getActiveProblemsGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			created_uid: app.user.id,
			create_date: new Date(),
			begin_date: new Date()
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
            active = this.getPatientProblemsActiveBtn().pressed;

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
			status_code: records[0].data.code,
			status_code_type: records[0].data.code_type
		});

	}

});
