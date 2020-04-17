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

Ext.define('App.controller.administration.Printers', {
	extend: 'Ext.app.Controller',
	refs: [
		{
			ref: 'AdministrationPrintersGrid',
			selector: '#AdministrationPrintersGrid'
		},
	],

	init: function(){

		var me = this;

		me.control({
			'#AdministrationPrintersAddBtn': {
				click: me.onAdministrationPrintersAddBtnClick
			},
			'#AdministrationPrintersRemoveBtn': {
				click: me.onAdministrationPrintersRemoveBtnClick
			}
		});

	},

	onAdministrationPrintersAddBtnClick: function () {
		var editingPlugin = this.getAdministrationPrintersGrid().editingPlugin,
			records;

		editingPlugin.cancelEdit();

		records = this.getAdministrationPrintersGrid().getStore().add({});

		editingPlugin.startEdit(records[0], 0);
	},

	onAdministrationPrintersRemoveBtnClick: function () {
		var me = this,
			grid = me.getAdministrationPrintersGrid(),
			editingPlugin = grid.editingPlugin,
			store = grid.getStore(),
			selection = grid.getSelectionModel().getSelection()[0];

		editingPlugin.cancelEdit();

		store.remove(selection);
		store.sync();
	}

});