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

Ext.define('App.controller.administration.Update', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'AdminUpdatePanel',
			selector: '#AdminUpdatePanel'
		},
		{
			ref: 'AdminUpdateGrid',
			selector: '#AdminUpdateGrid'
		},
		{
			ref: 'AdminUpdateGridContextMenuGitLog',
			selector: '#AdminUpdateGridContextMenuGitLog'
		},
		{
			ref: 'AdminUpdateGridContextMenuGitInstall',
			selector: '#AdminUpdateGridContextMenuGitInstall'
		},
		{
			ref: 'AdminUpdateGridContextMenuGitDiff',
			selector: '#AdminUpdateGridContextMenuGitDiff'
		},
		{
			ref: 'AdminUpdateGridContextMenuScriptUpdateInfo',
			selector: '#AdminUpdateGridContextMenuScriptUpdateInfo'
		},
		{
			ref: 'AdminUpdateGridContextMenuGitPull',
			selector: '#AdminUpdateGridContextMenuGitPull'
		},
		{
			ref: 'AdminUpdateGridContextMenuGitReset',
			selector: '#AdminUpdateGridContextMenuGitReset'
		},
		{
			ref: 'AdminUpdateGridContextMenuGitBranch',
			selector: '#AdminUpdateGridContextMenuGitBranch'
		},
		{
			ref: 'AdminUpdateGridContextMenuGitStatus',
			selector: '#AdminUpdateGridContextMenuGitStatus'
		},
		{
			ref: 'UpdateWindowButtonCloseBtn',
			selector: '#UpdateWindowButtonCloseBtn'
		},
		{
			ref: 'UpdateWindowButtonExecuteScriptBtn',
			selector: '#UpdateWindowButtonExecuteScriptBtn'
		},
		{
			ref: 'AdminUpdateScriptGrid',
			selector: '#AdminUpdateScriptGrid'
		},
		{
			ref: 'UpdateWindow',
			selector: '#UpdateWindow'
		},
	],

	init: function(){
		var me = this;

		me.control({
			'#AdminUpdatePanel': {
				activate: me.onAdminUpdatePanelActivate
			},
			'#AdminUpdateGrid': {
				itemdblclick: me.onAdminUpdateGridItemDblClick,
				beforeitemcontextmenu: me.onAdminUpdateGridContextMenu,
			},
			'#AdminUpdateGridContextMenuGitLog': {
				click: me.onAdminUpdateGridContextMenuGitLogClick,
			},
			'#AdminUpdateGridContextMenuGitInstall': {
				click: me.onAdminUpdateGridContextMenuGitInstallClick,
			},
			'#AdminUpdateGridContextMenuGitDiff': {
				click: me.onAdminUpdateGridContextMenuGitDiffClick,
			},
			'#AdminUpdateGridContextMenuScriptUpdateInfo': {
				click: me.onAdminUpdateGridContextMenuScriptUpdateInfoClick,
			},
			'#AdminUpdateGridContextMenuGitPull': {
				click: me.onAdminUpdateGridContextMenuGitPullClick,
			},
			'#AdminUpdateGridContextMenuGitReset': {
				click: me.onAdminUpdateGridContextMenuGitResetClick,
			},
			'#AdminUpdateGridContextMenuGitBranch': {
				click: me.onAdminUpdateGridContextMenuGitBranchClick,
			},
			'#AdminUpdateGridContextMenuGitStatus': {
				click: me.onAdminUpdateGridContextMenuGitStatusClick,
			},
			'#UpdateWindowButtonCloseBtn': {
				click: me.onUpdateWindowButtonCloseBtnClick,
			},
			'#UpdateWindowButtonExecuteScriptBtn': {
				click: me.onUpdateWindowButtonExecuteScriptBtnClick,
			}
		});
	},

	onAdminUpdatePanelActivate: function (){
		this.doAdminUpdateGridReload();
	},

	doAdminUpdateGridReload: function (){
		//this.getAdminUpdateGrid().getStore().load();
	},

	onAdminUpdateGridItemDblClick: function () {
		var me = this;
	},

	onAdminUpdateGridContextMenu: function (grid, record, item, index, e) {
		var me = this;

		e.preventDefault();

		me.showAdminUpdateGridContextMenu(grid, record, e)
	},

	showAdminUpdateGridContextMenu: function(grid, record, e) {
		var me = this;

		me.AdminUpdateGridContextMenu = Ext.widget('menu', {
			margin: '0 0 10 0',
			items: [
				{
					text: _('git_status'),
					iconCls: 'far fa-info-circle',
					itemId: 'AdminUpdateGridContextMenuGitStatus'
				},
				{
					text: _('git_log'),
					iconCls: 'far fa-file-minus',
					itemId: 'AdminUpdateGridContextMenuGitLog'
				},
				{
					text: _('git_diff'),
					iconCls: 'far fa-file-code',
					itemId: 'AdminUpdateGridContextMenuGitDiff'
				},
				{
					text: _('scripts_update_info'),
					iconCls: 'far fa-info-circle',
					itemId: 'AdminUpdateGridContextMenuScriptUpdateInfo'
				},
				{
					xtype: 'menuseparator'
				},
				{
					text: _('git_pull'),
					iconCls: 'fad fa-code-merge',
					itemId: 'AdminUpdateGridContextMenuGitPull'
				},
				{
					text: _('git_branch'),
					iconCls: 'fas fa-code-branch',
					menu: {
						xtype: 'menu',
						items: record.get('branches'),
						listeners: {
							click: function(menu, item, e, eOpts ) {
								console.log(menu);
								console.log(item);

								me.showCheckoutBranchMessageBox(menu, item);
							}
						}
					}
				},
				{
					text: _('git_tags'),
					iconCls: 'fas fa-tag',
					menu: {
						xtype: 'menu',
						items: record.get('tags'),
						listeners: {
							click: function(menu, item, e, eOpts ) {
								console.log(menu);
								console.log(item);

								me.showCheckoutTagMessageBox(menu, item);
							}
						}
					}
					// itemId: 'AdminUpdateGridContextMenuGitBranch'
				},
				{
					xtype: 'menuseparator'
				},
				{
					text: _('git_reset'),
					iconCls: 'fab fa-digital-ocean',
					itemId: 'AdminUpdateGridContextMenuGitReset'
				},
				{
					text: _('git_install'),
					iconCls: 'fas fa-cloud-download',
					itemId: 'AdminUpdateGridContextMenuGitInstall'
				}
			]
		});

		me.AdminUpdateGridContextMenu.grid = grid;
		me.AdminUpdateGridContextMenu.record = record;

		me.AdminUpdateGridContextMenu.showAt(e.getXY());

		return me.AdminUpdateGridContextMenu;
	},

	showCheckoutTagMessageBox: function(menu, item) {
		var me = this;

		Ext.MessageBox.show({
			title: 'Warning!',
			msg: 'Are you sure you want to checkout from the selected tag? ' + item.text + '. You will lose all the current local changes.',
			buttons: Ext.MessageBox.OKCANCEL,
			icon: Ext.MessageBox.WARNING,
			fn: function(btn){
				if(btn == 'ok'){
					me.checkoutFromGitTag(item);
				} else {
					return;
				}
			}
		});
	},

	showCheckoutBranchMessageBox: function(menu, item) {
		var me = this;

		Ext.MessageBox.show({
			title: 'Warning!',
			msg: 'Are you sure you want to checkout from the selected branch? ' + item.text + '. You will lose all the current local changes.',
			buttons: Ext.MessageBox.OKCANCEL,
			icon: Ext.MessageBox.WARNING,
			fn: function(btn){
				if(btn == 'ok'){
					me.checkoutFromGitBranch(item);
				} else {
					return;
				}
			}
		});
	},

	checkoutFromGitTag: function(item) {
		var me = this,
		update_grid = me.getAdminUpdateGrid();

		Gitter.doTagCheckout(item.text, item.module, null, function(r) {

			say(r);
			say(r.output.join("\n"));

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			update_grid.getStore().reload();
		});
	},

	checkoutFromGitBranch: function(item) {
		var me = this,
		update_grid = me.getAdminUpdateGrid();

		Gitter.doBranchCheckout(item.text, item.module, null, function(r) {

			say(r);
			say(r.output.join("\n"));

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			update_grid.getStore().reload();
		});
	},

	onAdminUpdateGridContextMenuGitLogClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitLog(record);
	},

	onAdminUpdateGridContextMenuGitInstallClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		Ext.MessageBox.show({
			title: 'Warning!',
			msg: 'Are you sure you want to do a git install?',
			buttons: Ext.MessageBox.OKCANCEL,
			icon: Ext.MessageBox.WARNING,
			fn: function(btn){
				if(btn == 'ok'){
					me.doAGitInstall(record);
				} else {
					return;
				}
			}
		});
	},

	onAdminUpdateGridContextMenuGitDiffClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitDifF(record);
	},

	onAdminUpdateGridContextMenuScriptUpdateInfoClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doADatabaseUpdateInfo(record);
	},

	onAdminUpdateGridContextMenuGitPullClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		Ext.MessageBox.show({
			title: 'Warning!',
			msg: 'Are you sure you want to do a git pull? You will lose all the current local changes.',
			buttons: Ext.MessageBox.OKCANCEL,
			icon: Ext.MessageBox.WARNING,
			fn: function(btn){
				if(btn == 'ok'){
					me.doAGitPull(record);
				} else {
					return;
				}
			}
		});
	},

	onAdminUpdateGridContextMenuGitResetClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		Ext.MessageBox.show({
			title: 'Warning!',
			msg: 'Are you sure you want to do a git reset? You will lose all the current local changes.',
			buttons: Ext.MessageBox.OKCANCEL,
			icon: Ext.MessageBox.WARNING,
			fn: function(btn){
				if(btn == 'ok'){
					me.doAGitReset(record);
				} else {
					return;
				}
			}
		});
	},

	onAdminUpdateGridContextMenuGitBranchClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitBranch(record);
	},

	onAdminUpdateGridContextMenuGitStatusClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitStatus(record);
	},

	onUpdateGridContextMenuLogClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;
			//user_username = print_jobs_record.get('user_username');

		if(!me.getUpdateWindow()){
			Ext.create('App.view.administration.UpdateWindow');
		}

		me.getUpdateWindow().show();
	},

	onUpdateWindowButtonCloseBtnClick: function (btn) {
		var win = btn.up('window');

		win.close();
	},

	onUpdateWindowButtonExecuteScriptBtnClick: function (btn) {
		var me = this,
			update_scripts_grid = me.getAdminUpdateScriptGrid(),
			selected_records = update_scripts_grid.getSelectionModel().getSelection();

		if (selected_records.length == 0) {
			app.msg(_('warning'), 'Select at least 1 script to execute!', 'yellow');
			return;
		}

		me.doExecuteDatabaseUpdateScript(selected_records);
	},

	doExecuteDatabaseUpdateScript: function(update_script_records) {
		var me = this,
		update_scripts = [];

		update_script_records.forEach(function(update_script) {
			update_scripts.push({
				module: update_script.get('module'),
				version: update_script.get('version'),
				script: update_script.get('script')
			});
		});

		Update.doDatabaseUpdateScripts(update_scripts, function(r) {
			say(r);

			me.getUpdateWindow().close();

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			var update_script_grid = me.getUpdateWindow().down('grid');
			update_script_grid.setVisible(false);
			me.getUpdateWindow().down('textfield').setVisible(true);
			me.getUpdateWindowButtonExecuteScriptBtn().hidden = true;

			say(me.getUpdateWindowButtonExecuteScriptBtn());

			var strMessage = '';
			if (r.success.length > 0) {
				strMessage += 'The following scripts have executed successfully: \n\n';
				strMessage += r.success.join('\n');
			}

			if (r.error.length > 0) {
				strMessage += '\n\n\n';
				strMessage += 'The following scripts have failed: \n';
				strMessage += r.error.join('\n');
			}

			me.getUpdateWindow().down('textfield').setValue(strMessage);
			me.getUpdateWindow().show();
		});
	},

	doAGitLog: function (module_record){
		var me = this,
		update_grid = me.getAdminUpdateGrid();

		Gitter.doLog(module_record.get('module'), null, function(r) {

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

			update_grid.getStore().reload();
		});
	},

	doAGitInstall: function (module_record){
		var me = this,
			update_grid = me.getAdminUpdateGrid();

		Gitter.doInstall(module_record.get('module'), null, function(r) {

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

			update_grid.getStore().reload();
		});
	},

	doAGitDifF: function (module_record){
		var me = this,
		update_grid = me.getAdminUpdateGrid();

		Gitter.doDiff(module_record.get('module'), null, function(r) {

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

			update_grid.getStore().reload();
		});
	},

	doADatabaseUpdateInfo: function (module_record){
		var me = this,
		update_grid = me.getAdminUpdateGrid();

		Update.doGetDatabaseUpdateScripts(module_record.get('module'), function(r) {

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			var strMessage = '';
			if (r.length > 0) {
				strMessage += 'The following scripts are available: \n\n';
				strMessage += r.join('\n');
			} else {
				strMessage = 'No database update scripts available!';
			}

			me.getUpdateWindow().down('textfield').setValue(strMessage);
			me.getUpdateWindow().show();

			update_grid.getStore().reload();
		});
	},

	doAGitPull: function (module_record){
		var me = this,
			update_grid = me.getAdminUpdateGrid();

		Update.doGitUpdate(module_record.get('module'), null, function(r) {

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			// Check if there are update database scripts available...
			if (r.databaseUpdateScripts.length > 0) {
				var update_script_grid = me.getUpdateWindow().down('grid');
				update_script_grid.setVisible(true);
				update_script_grid.getStore().loadData(r.databaseUpdateScripts);

				me.getUpdateWindow().down('textfield').setVisible(false);
				me.getUpdateWindowButtonExecuteScriptBtn().hidden = false;
			} else {
				me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));
			}

			me.getUpdateWindow().show();
			update_grid.getStore().reload();
		});
	},

	doAGitReset: function (module_record){
		var me = this,
			update_grid = me.getAdminUpdateGrid();

		Gitter.doReset(module_record.get('module'), null, function(r) {

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

			update_grid.getStore().reload();
		});
	},

	doAGitBranch: function (module_record){
		var me = this,
			update_grid = me.getAdminUpdateGrid();

		Gitter.doBranch(module_record.get('module'), null, function(r) {
			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

			update_grid.getStore().reload();
		});
	},

	doAGitBranches: function (module_record){
		var me = this,
			update_grid = me.getAdminUpdateGrid();

		Gitter.doBranches(module_record.get('module'), null, function(r) {
			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

			update_grid.getStore().reload();
		});
	},

	doAGitStatus: function (module_record){
		var me = this,
			update_grid = me.getAdminUpdateGrid();

		Gitter.doStatus(module_record.get('module'), null, function(r) {
			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

			update_grid.getStore().reload();
		});
	}
});