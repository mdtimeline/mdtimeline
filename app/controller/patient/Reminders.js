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

Ext.define('App.controller.patient.Reminders', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'PatientRemindersAddBtn',
			selector: '#RemindersAddBtn'
		},
		{
			ref: 'PatientSummaryRemindersPanel',
			selector: '#PatientSummaryRemindersPanel'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#PatientSummaryRemindersPanel':{
				activate: me.onPatientSummaryRemindersPanelActivate
			},
			'#PatientRemindersAddBtn':{
				click: me.onPatientRemindersAddBtnClick
			}
		});
	},

	onPatientSummaryRemindersPanelActivate: function(){
		this.getPatientSummaryRemindersPanel().getStore().load({
			filters: [
				{
					property: 'pid',
					value: app.patient.pid
				}
			]
		});
	},

	onPatientRemindersAddBtnClick: function(btn){
		var grid = btn.up('grid'),
			store = grid.store;

		grid.plugins[0].cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			reminder_type: 'appointment',
			reminder_date: new Date(),
			reminder_note: '',
			create_date: new Date(),
			create_uid: app.user.id
		});
		grid.plugins[0].startEdit(0, 0);
	}

});
