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

Ext.define('App.controller.patient.PatientNotes', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'PatientNotesGrid',
			selector: '#PatientNotesGrid'
		},
		{
			ref: 'PatientNotesWindow',
			selector: '#PatientNotesWindow'
		},
		{
			ref: 'PatientNotesGridAddNotesBtn',
			selector: '#PatientNotesGridAddNotesBtn'
		},
		{
			ref: 'PatientNotesWindowCloseBtn',
			selector: '#PatientNotesWindowCloseBtn'
		},
		{
			ref: 'PatientNotesGridNotesContextMenuDetailLog',
			selector: '#PatientNotesGridNotesContextMenuDetailLog'
		}
	],

	init: function(){
		var me = this;
		me.auditLogCtrl = app.getController('administration.TransactionLog');

		me.control({
			'#PatientNotesGrid': {
				beforeitemcontextmenu: me.onPatientNotesGridContextMenu,
				scope: me,
				beforeedit: me.onPatientNotesGridBeforeEdit,
				validateedit: me.onPatientNotesGridValidateEdit,
				beforerender: me.onPatientNotesGridBeforeRender
			},
			'#PatientNotesWindowCloseBtn': {
				click: me.onPatientNotesWindowCloseBtnClick
			},
			'#PatientNotesGridNotesContextMenuDetailLog': {
				click: me.onPatientNotesGridContextMenuDetailLogClick
			},
			'#PatientNotesGridAddNotesBtn': {
				click: me.onPatientNotesGridAddNotesBtn
			}
		});
	},

	// onDoctorsNotesGridBeforeRender: function(grid){
	// 	app.on('patientunset', function(){
	// 		grid.getStore().removeAll();
	// 	});
	// },

	onAddNew: function(btn){
		var grid = btn.up('grid'),
			store = grid.store,
			record;

		if(btn.action == 'disclosure'){
			record = {
				date: new Date(),
				pid: app.patient.pid,
				global_id: app.uuidv4(),
				active: 1
			};
		}else if(btn.action == 'note'){
			record = {
				date: new Date(),
				create_date: new Date(),
				pid: app.patient.pid,
				create_uid: app.user.id,
				uid: app.user.id,
				eid: app.patient.eid,
				global_id: app.uuidv4(),
				user_fname: app.user.fname,
				user_mname: app.user.mname,
				user_lname: app.user.lname,
			};
		}

		grid.plugins[0].cancelEdit();
		store.insert(0, record);
		grid.plugins[0].startEdit(0, 0);
	},

	onPatientNotesGridBeforeEdit: function(editor, context, eOpts ){
		if (!a('edit_patient_notes')) {
			app.msg(_('oops'), _('not_authorized'), true);
			return false;
		}
	},

	onPatientNotesGridValidateEdit: function(editor, context, eOpts ){
		context.record.data.update_user_fname 	= app.user.fname;
		context.record.data.update_user_mname 	= app.user.mname;
		context.record.data.update_user_lname 	= app.user.lname;
		context.record.data.update_date 		= app.getDate();
		context.record.data.update_uid 			= app.user.id;
	},

	onPatientNotesGridContextMenu: function (grid, record, item, index, e) {
		e.preventDefault();

		this.showPatientNotesGridContextMenu(grid,record, e);
	},

	showPatientNotesGridContextMenu: function (patient_notes_panel_grid, patient_notes_panel_record, e) {
		if (!a('access_patient_notes_transaction_log')) return;

		var me = this;

		me.PatientNotesGridContextMenu = Ext.widget('menu', {
			margin: '0 0 10 0',
			items: [
				{
					text: _('transaction_log'),
					icon: 'modules/billing/resources/images/icoList.png',
					itemId: 'PatientNotesGridNotesContextMenuDetailLog',
					acl: true
				}
			]
		});

		me.PatientNotesGridContextMenu.patient_notes_panel_grid = patient_notes_panel_grid;
		me.PatientNotesGridContextMenu.patient_notes_panel_record = patient_notes_panel_record;

		me.PatientNotesGridContextMenu.showAt(e.getXY());

		return me.PatientNotesGridContextMenu;
	},

	onPatientNotesGridContextMenuDetailLogClick: function(btn) {
		var me = this,
			patient_notes_panel_record = btn.parentMenu.patient_notes_panel_record;

		this.auditLogCtrl.doTransactionLogDetailByTableAndPk(patient_notes_panel_record.table.name, patient_notes_panel_record.get('id'));

	},

	onPatientNotesWindowCloseBtnClick: function () {
		//this.getPatientNotesGrid().getStore().rejectChanges();
		this.getPatientNotesWindow().close();
	},

	onPatientNotesGridAddNotesBtn: function(btn){
		var grid = btn.up('grid'),
			store = grid.store,
			record;

		say(btn);

		if(btn.action == 'disclosure'){
			record = {
				date: new Date(),
				pid: app.patient.pid,
				global_id: app.uuidv4(),
				active: 1
			};
		}else if(btn.action == 'note'){
			record = {
				date: new Date(),
				create_date: new Date(),
				pid: app.patient.pid,
				create_uid: app.user.id,
				uid: app.user.id,
				eid: app.patient.eid,
				global_id: app.uuidv4(),
				user_fname: app.user.fname,
				user_mname: app.user.mname,
				user_lname: app.user.lname,
			};
		}

		grid.plugins[0].cancelEdit();
		store.insert(0, record);
		grid.plugins[0].startEdit(0, 0);
	},

	onPatientNotesGridBeforeRender: function(grid) {

		var store = grid.getStore();

		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	showPatientNotesWindow: function () {
		if(!this.getPatientNotesWindow()){
			Ext.create('App.view.patient.windows.PatientNotesWindow');
		}
		return this.getPatientNotesWindow().show();
	}
});