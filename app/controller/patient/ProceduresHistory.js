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

Ext.define('App.controller.patient.ProceduresHistory', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref:'ProceduresHistoryGrid',
			selector: 'patientprocedureshistorygrid'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientprocedureshistorygrid': {
				activate: me.onProceduresHistoryGridActivate
			},
			'#ProceduresHistoryGridAddBtn': {
				click: me.onProceduresHistoryGridAddBtnClick
			},
			'#ProceduresHistoryGridProcedureField': {
				select: me.onProceduresHistoryGridProcedureFieldSelect
			},
			'#ProceduresHistoryGridTargetSiteField': {
				select: me.onProceduresHistoryGridTargetSiteFieldSelect
			}
		});
	},

	onProceduresHistoryGridProcedureFieldSelect: function(cmb, selection){
		var procedure_record = cmb.up('form').getForm().getRecord();
		procedure_record.set({
			procedure_code: selection[0].get('ConceptId'),
			procedure_code_type: selection[0].get('CodeType'),
			procedure: selection[0].get('Term')
		});
	},

	onProceduresHistoryGridTargetSiteFieldSelect: function(cmb, selection){
		var procedure_record = cmb.up('form').getForm().getRecord();
		procedure_record.set({
			target_site_code: selection[0].get('ConceptId'),
			target_site_code_type: selection[0].get('CodeType'),
			target_site_code_text: selection[0].get('Term')
		});
	},

	onProceduresHistoryGridAddBtnClick: function (btn) {
		var grid = btn.up('grid'),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			created_uid: app.user.id,
			create_date: new Date()
		});
		grid.editingPlugin.startEdit(0, 0);
	},

	onProceduresHistoryGridActivate: function(grid){
		var store = grid.getStore();
		store.clearFilter(true);
		store.load({
			filters: [
				{
					property: 'pid',
					value: app.patient.pid
				}
			]
		});
	}

});
