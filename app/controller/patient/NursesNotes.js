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

Ext.define('App.controller.patient.NursesNotes', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'NursesNotesGrid',
			selector: '#NursesNotesGrid'
		},
		{
			ref: 'NursesNoteWindow',
			selector: '#NursesNoteWindow'
		},
	],

	init: function(){
		var me = this;
		me.control({
			'#NursesNotesGrid': {
				activate: me.onNursesNotesGridActive
			},
			'#NursesNotesGridAddBtn': {
				click: me.onNursesNotesGridAddBtnClick
			},
		});
	},

	showNursesNotesWindow: function(){
		if(!this.getNursesNoteWindow()){
			Ext.create('App.view.patient.NursesNoteWindow');
		}
		return this.getNursesNoteWindow().show();
	},

	onNursesNotesGridAddBtnClick: function(btn){

	},

	onNursesNotesGridActive: function(grid){

		grid.getStore().load({
			filters: [
				{
					property: 'pid',
					value: app.patient.pid
				},
				{
					property: 'eid',
					value: app.patient.eid
				}
			]
		});
	}

});