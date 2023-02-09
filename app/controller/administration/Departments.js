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

Ext.define('App.controller.administration.Departments', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'DepartmentsPanel',
			selector: 'departmentsspanel'
		},
		{
			ref: 'DepartmentsAddBtn',
			selector: '#DepartmentsAddBtn'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#DepartmentsAddBtn': {
				click: me.onDepartmentsAddBtnClick
			}
		});

	},

	onDepartmentsAddBtnClick: function(btn){

		var grid = btn.up('grid');

		grid.editingPlugin.cancelEdit();
		var recs = grid.getStore().insert(0, {
			create_date: new Date(),
			update_date: new Date(),
			create_uid: app.user.id,
			update_uid: app.user.id,
			active: 1
		});
		grid.editingPlugin.startEdit(recs[0], 0);
	}

});