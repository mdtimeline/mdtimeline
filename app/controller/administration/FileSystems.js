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

Ext.define('App.controller.administration.FileSystems', {
    extend: 'Ext.app.Controller',

    refs: [
        {
            ref:'AdminFileSystemsPanel',
            selector:'#AdminFileSystemsPanel'
        },
        {
            ref:'FileSystemsGrid',
            selector:'#FileSystemsGrid'
        }
    ],

    init: function() {
        var me = this;

        me.control({
            '#AdminFileSystemsPanel': {
                activate: me.onAdminFileSystemsPanelActivate
            },
            '#FileSystemsAnalyzeBtn': {
                click: me.onFileSystemsAnalyzeBtnClick
            },
            '#FileSystemsAddBtn': {
                click: me.onFileSystemsAddBtnClick
            }
        });
    },

	onAdminFileSystemsPanelActivate: function () {
		this.getFileSystemsGrid().getStore().load();
	},

	onFileSystemsAnalyzeBtnClick: function () {
		var me = this;

		FileSystem.fileSystemsSpaceAnalyzer(function () {
			me.getFileSystemsGrid().getStore().load();
		});
	},
	
	onFileSystemsAddBtnClick: function () {

    	var me = this,
		    grid = me.getFileSystemsGrid(),
		    store = grid.getStore();

		grid.editingPlugin.cancelEdit();

		var records = store.add({
			status: 'ONLINE'
		});

		grid.editingPlugin.startEdit(records[0], 0);

	}

});
