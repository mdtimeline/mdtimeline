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

Ext.define('App.controller.patient.MiniMentalStateExam', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'MiniMentalStateExamGridPanel',
			selector: '#MiniMentalStateExamGridPanel'
		},
		{
			ref: 'MiniMentalStateExamWindow',
			selector: '#MiniMentalStateExamWindow'
		},
		{
			ref: 'MiniMentalStateExamForm',
			selector: '#MiniMentalStateExamForm'
		},
	],

	init: function () {
		var me = this;
		me.control({
			'#MiniMentalStateExamGridPanel': {
				activate: me.onMiniMentalStateExamGridPanelActivate,
				itemdblclick: me.onMiniMentalStateExamGridPanelItemDblClick
			},
			'#MiniMentalStateExamAddBtn': {
				click: me.onMiniMentalStateExamAddBtnClick
			},
			'#MiniMentalStateExamWindowCancelBtn': {
				click: me.onMiniMentalStateExamWindowCancelBtnClick
			},
			'#MiniMentalStateExamWindowSaveBtn': {
				click: me.onMiniMentalStateExamWindowSaveBtnClick
			}
		});

		//me.showMiniMentalStateExamWindow();

	},

	onMiniMentalStateExamGridPanelActivate: function (grid) {
		grid.getStore().load({
			filters: [
				{
					property: 'pid',
					value: app.patient.pid
				}
			]
		})
	},

	onMiniMentalStateExamGridPanelItemDblClick: function (grid, record) {
		this.showMiniMentalStateExamWindow();
		this.getMiniMentalStateExamForm().getForm().loadRecord(record);
	},


	onMiniMentalStateExamWindowSaveBtnClick: function () {
		var win = this.getMiniMentalStateExamWindow(),
			form = this.getMiniMentalStateExamForm().getForm(),
			store = this.getMiniMentalStateExamGridPanel().getStore(),
			values = form.getValues(),
			record = form.getRecord(),
			total_score = 0;

		if(!form.isValid()) return;


		if(record.get('orientation_time_score')){
			total_score = total_score + record.get('orientation_time_score');
		}
		if(record.get('orientation_place_score')){
			total_score = total_score + record.get('orientation_place_score');
		}
		if(record.get('registration_score')){
			total_score = total_score + record.get('registration_score');
		}
		if(record.get('attention_calculation_score')){
			total_score = total_score + record.get('attention_calculation_score');
		}
		if(record.get('recall_score')){
			total_score = total_score + record.get('recall_score');
		}
		if(record.get('language_score')){
			total_score = total_score + record.get('language_score');
		}
		if(record.get('repetition_score')){
			total_score = total_score + record.get('repetition_score');
		}
		if(record.get('complex_commands_score')){
			total_score = total_score + record.get('complex_commands_score');
		}

		values.total_score = total_score;

		record.set(values);

		if(!record.store){
			store.add(record);
		}

		if(
			store.getModifiedRecords().length === 0 &&
			store.getNewRecords().length === 0 &&
			store.getRemovedRecords().length === 0
		){
			form.reset();
			win.close();
		}

		store.sync({
			callback: function () {
				form.reset();
				win.close();
			}
		});

	},

	onMiniMentalStateExamAddBtnClick: function () {
		this.showMiniMentalStateExamWindow();

		var record = Ext.create('App.model.patient.MiniMentalStateExam', {
				pid: app.patient.pid,
				eid:  app.patient.eid,
				create_uid: app.user.id,
				create_date: new Date()
			}),
			form = this.getMiniMentalStateExamForm().getForm();

		form.loadRecord(record);
	},

	onMiniMentalStateExamWindowCancelBtnClick: function () {
		this.getMiniMentalStateExamWindow().close();
	},

	showMiniMentalStateExamWindow: function () {
		if(!this.getMiniMentalStateExamWindow()){
			Ext.create('App.view.patient.windows.MiniMentalStateExam');
		}
		return this.getMiniMentalStateExamWindow().show();
	}

});