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
			ref: 'AdminUpdateGridContextMenuGitDiff',
			selector: '#AdminUpdateGridContextMenuGitDiff'
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
			'#AdminUpdateGridContextMenuGitDiff': {
				click: me.onAdminUpdateGridContextMenuGitDiffClick,
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
					text: _('git_log'),
					iconCls: 'far fa-file-minus',
					itemId: 'AdminUpdateGridContextMenuGitLog'
				},
				{
					xtype: 'menuseparator'
				},
				{
					text: _('git_diff'),
					iconCls: 'far fa-file-code',
					itemId: 'AdminUpdateGridContextMenuGitDiff'
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
					xtype: 'menuseparator'
				},
				{
					text: _('git_reset'),
					iconCls: 'fab fa-digital-ocean',
					itemId: 'AdminUpdateGridContextMenuGitReset'
				},
				{
					xtype: 'menuseparator'
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


								Gitter.doBranchCheckout(item.text, item.module, null, function(r) {

									say(r);
									say(r.output.join("\n"));

									if(!me.getUpdateWindow()){
										Ext.create('App.view.administration.UpdateWindow');
									}

									me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

									me.getUpdateWindow().show();

									grid.getStore().reload();
								});
							}
						}
					}
				},
				{
					xtype: 'menuseparator'
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

								grid.getStore().reload();

								// Gitter.doBranchCheckout(item.text, item.module, null, function(r) {
								//
								// 	say(r);
								// 	say(r.output.join("\n"));
								//
								// 	if(!me.getUpdateWindow()){
								// 		Ext.create('App.view.administration.UpdateWindow');
								// 	}
								//
								// 	me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));
								//
								// 	me.getUpdateWindow().show();
								//
								// });
							}
						}
					}
					// itemId: 'AdminUpdateGridContextMenuGitBranch'
				},
				{
					xtype: 'menuseparator'
				},
				{
					text: _('git_status'),
					iconCls: 'far fa-info-circle',
					itemId: 'AdminUpdateGridContextMenuGitStatus'
				}
			]
		});

		me.AdminUpdateGridContextMenu.grid = grid;
		me.AdminUpdateGridContextMenu.record = record;

		me.AdminUpdateGridContextMenu.showAt(e.getXY());

		return me.AdminUpdateGridContextMenu;

		// Gitter.doTags(record.get('module'), null, function(r) {
		// 	say(r);
		// 	say(r.output.join("\n"));
		//
		// 	r.output.forEach((element) => {
		// 		tags.push({
		// 			iconCls: 'fas fa-tag',
		// 			text: element,
		// 			module: record.get('module')
		// 		});
		// 	});
		// });

		// Gitter.doBranches(record.get('module'), null, function(r) {
		//
		// 	say(r);
		// 	say(r.output.join("\n"));
		//
		// 	r.output.forEach((element) => {
		// 		branches.push({
		// 			iconCls: 'fas fa-code-branch',
		// 			text: element,
		// 			module: record.get('module')
		// 		});
		// 	});
		//
		// 	me.AdminUpdateGridContextMenu = Ext.widget('menu', {
		// 		margin: '0 0 10 0',
		// 		items: [
		// 			{
		// 				text: _('git_log'),
		// 				iconCls: 'far fa-file-minus',
		// 				itemId: 'AdminUpdateGridContextMenuGitLog'
		// 			},
		// 			{
		// 				xtype: 'menuseparator'
		// 			},
		// 			{
		// 				text: _('git_diff'),
		// 				iconCls: 'far fa-file-code',
		// 				itemId: 'AdminUpdateGridContextMenuGitDiff'
		// 			},
		// 			{
		// 				xtype: 'menuseparator'
		// 			},
		// 			{
		// 				text: _('git_pull'),
		// 				iconCls: 'fad fa-code-merge',
		// 				itemId: 'AdminUpdateGridContextMenuGitPull'
		// 			},
		// 			{
		// 				xtype: 'menuseparator'
		// 			},
		// 			{
		// 				text: _('git_reset'),
		// 				iconCls: 'fab fa-digital-ocean',
		// 				itemId: 'AdminUpdateGridContextMenuGitReset'
		// 			},
		// 			{
		// 				xtype: 'menuseparator'
		// 			},
		// 			{
		// 				text: _('git_branch'),
		// 				iconCls: 'fas fa-code-branch',
		// 				menu: {
		// 					xtype: 'menu',
		// 					items: branches,
		// 					listeners: {
		// 						click: function(menu, item, e, eOpts ) {
		// 							console.log(menu);
		// 							console.log(item);
		//
		//
		// 							Gitter.doBranchCheckout(item.text, item.module, null, function(r) {
		//
		// 								say(r);
		// 								say(r.output.join("\n"));
		//
		// 								if(!me.getUpdateWindow()){
		// 									Ext.create('App.view.administration.UpdateWindow');
		// 								}
		//
		// 								me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));
		//
		// 								me.getUpdateWindow().show();
		//
		// 							});
		// 						}
		// 					}
		// 				}
		// 				// itemId: 'AdminUpdateGridContextMenuGitBranch'
		// 			},
		// 			{
		// 				xtype: 'menuseparator'
		// 			},
		// 			{
		// 				text: _('git_tags'),
		// 				iconCls: 'fas fa-tag',
		// 				menu: {
		// 					xtype: 'menu',
		// 					items: tags,
		// 					listeners: {
		// 						click: function(menu, item, e, eOpts ) {
		// 							console.log(menu);
		// 							console.log(item);
		//
		//
		// 							// Gitter.doBranchCheckout(item.text, item.module, null, function(r) {
		// 							//
		// 							// 	say(r);
		// 							// 	say(r.output.join("\n"));
		// 							//
		// 							// 	if(!me.getUpdateWindow()){
		// 							// 		Ext.create('App.view.administration.UpdateWindow');
		// 							// 	}
		// 							//
		// 							// 	me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));
		// 							//
		// 							// 	me.getUpdateWindow().show();
		// 							//
		// 							// });
		// 						}
		// 					}
		// 				}
		// 				// itemId: 'AdminUpdateGridContextMenuGitBranch'
		// 			},
		// 			{
		// 				xtype: 'menuseparator'
		// 			},
		// 			{
		// 				text: _('git_status'),
		// 				iconCls: 'far fa-info-circle',
		// 				itemId: 'AdminUpdateGridContextMenuGitStatus'
		// 			}
		// 		]
		// 	});
		//
		// 	me.AdminUpdateGridContextMenu.grid = grid;
		// 	me.AdminUpdateGridContextMenu.record = record;
		//
		// 	me.AdminUpdateGridContextMenu.showAt(e.getXY());
		//
		// 	return me.AdminUpdateGridContextMenu;
		// });
	},

	onAdminUpdateGridContextMenuGitLogClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitLog(record);
	},

	onAdminUpdateGridContextMenuGitDiffClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitDifF(record);
	},

	onAdminUpdateGridContextMenuGitPullClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitPull(record);
	},

	onAdminUpdateGridContextMenuGitResetClick: function(btn) {
		var me = this,
			record = btn.parentMenu.record;

		me.doAGitReset(record);
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

		me.getUpdateWindow().test = 'HELLO';

		me.getUpdateWindow().show();
	},

	onUpdateWindowButtonCloseBtnClick: function (btn) {
		var win = btn.up('window');

		win.close();
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

	doAGitPull: function (module_record){
		var me = this,
			update_grid = me.getAdminUpdateGrid();

		Gitter.doPull(module_record.get('module'), null, function(r) {

			if(!me.getUpdateWindow()){
				Ext.create('App.view.administration.UpdateWindow');
			}

			me.getUpdateWindow().down('textfield').setValue(r.output.join("\n"));

			me.getUpdateWindow().show();

			say(r.output.join("\n"));

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