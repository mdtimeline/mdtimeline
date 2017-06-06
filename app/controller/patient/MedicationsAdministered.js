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

Ext.define('App.controller.patient.MedicationsAdministered', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'MedicationsAdministeredGrid',
			selector: '#MedicationsAdministeredGrid'
		},
		{
			ref: 'MedicationsAdministeredGridAddBtn',
			selector: '#MedicationsAdministeredGridAddBtn'
		},
		{
			ref: 'MedicationsAdministeredEditWindow',
			selector: '#MedicationsAdministeredEditWindow'
		},
		{
			ref: 'MedicationsAdministeredEditForm',
			selector: '#MedicationsAdministeredEditForm'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'viewport': {
				encounterload: me.onViewportEncounterLoad
			},
			'#MedicationsAdministeredGrid': {
				itemdblclick: me.onMedicationsAdministeredGridItemDblClick
			},
			'#MedicationsAdministeredEditCancelBtn': {
				click: me.onMedicationsAdministeredEditCancelBtnClick
			},
			'#MedicationsAdministeredEditSaveBtn': {
				click: me.onMedicationsAdministeredEditSaveBtnClick
			},
			'#MedicationsAdministeredEditAdministerSearchField': {
				select: me.onMedicationsAdministeredEditAdministerSearchFieldSelect
			},
			'#MedicationsAdministeredEditAdverseReactionField': {
				select: me.onMedicationsAdministeredEditAdverseReactionFieldSelect
			}
		});
	},

	onViewportEncounterLoad: function(encounter){
		var store = this.getMedicationsAdministeredGrid().getStore();

		store.clearFilter(true);
		store.load({
			filters: [
				{
					property: 'pid',
					value: encounter.get('pid')
				},
				{
					property: 'eid',
					value: encounter.get('eid')
				}
			],
			params: {
				pid: encounter.get('pid'),
				eid: encounter.get('eid'),
				include_not_administered: true
			}
		});
	},

	onMedicationsAdministeredEditAdministerSearchFieldSelect: function (field, selection) {
		var me = this,
			form = me.getMedicationsAdministeredEditForm().getForm(),
			record = form.getRecord();

		record.set({
			administered_uid: selection[0].get('id'),
			administered_title: selection[0].get('title'),
			administered_fname: selection[0].get('fname'),
			administered_mname: selection[0].get('mname'),
			administered_lname: selection[0].get('lname')
		});

	},

	onMedicationsAdministeredEditAdverseReactionFieldSelect: function (field, selection) {
		var me = this,
			form = me.getMedicationsAdministeredEditForm().getForm(),
			record = form.getRecord();

		record.set({
			adverse_reaction_text: selection[0].get('option_name'),
			adverse_reaction_code: selection[0].get('code'),
			adverse_reaction_code_type: selection[0].get('code_type')
		});
	},
	
	onMedicationsAdministeredGridItemDblClick: function (grid, record) {

		this.showMedicationsAdministeredEditWindow();

		var form = this.getMedicationsAdministeredEditForm().getForm();
		form.loadRecord(record);
	},

	onMedicationsAdministeredEditCancelBtnClick: function () {
		this.getMedicationsAdministeredEditForm().getForm().reset();
		this.getMedicationsAdministeredEditWindow().close();
	},

	onMedicationsAdministeredEditSaveBtnClick: function () {
		var me = this,
			form = me.getMedicationsAdministeredEditForm().getForm(),
			record = form.getRecord(),
			values = form.getValues();

		if(!form.isValid()) return;
		record.set(values);

		record.save({
			callback:  function () {
				me.getMedicationsAdministeredEditForm().getForm().reset();
				me.getMedicationsAdministeredEditWindow().close();
			}
		});
	},

	showMedicationsAdministeredEditWindow: function () {
		if(!this.getMedicationsAdministeredEditWindow()){
			Ext.create('App.view.patient.windows.MedicationsAdministeredEditWindow')
		}
		return this.getMedicationsAdministeredEditWindow().show();
	},

});
