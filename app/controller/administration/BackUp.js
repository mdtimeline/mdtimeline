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

Ext.define('App.controller.administration.BackUp', {
    extend: 'Ext.app.Controller',

    refs: [
        {
            ref:'AdministrationBackupGrid',
            selector:'#AdministrationBackupGrid'
        },
        {
            ref:'AdministrationBackupRefreshBtn',
            selector:'#AdministrationBackupRefreshBtn'
        }
    ],

    init: function() {
        var me = this;

        me.control({
            viewport: {
                render: me.onViewportRender
            },
            '#AdministrationBackupPanel': {
                activate: me.onAdministrationBackupPanelActivate
            },
            '#AdministrationBackupRefreshBtn': {
                click: me.onAdministrationBackupRefreshBtnClick
            },
            '#AdministrationBackupAsyncBackupBtn': {
                click: me.onAdministrationBackupAsyncBackupBtnClick
            }
        });
    },

	onAdministrationBackupPanelActivate: function(){
		this.getAdministrationBackupGrid().store.load();
	},

	onViewportRender: function(){
		if(!a('access_backup_settings')) return;

		BackUp.doBackupCheck(function (backup) {
			if(backup) return;
			app.alert(_('no_backup_found_within_24_hours'), 'warning');

			Ext.Msg.show({
				title: _('no_backup_found_within_24_hours'),
				msg: _('would_you_like_to_create_one'),
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.WARNING,
				fn: function (btn) {
					if(btn === 'yes'){
						app.nav.navigateTo('App.view.administration.Backup');
					}
				}
			});
		});

	},

	onAdministrationBackupRefreshBtnClick: function () {
    	this.getAdministrationBackupGrid().getStore().load();
	},

	onAdministrationBackupAsyncBackupBtnClick: function () {

    	var me = this;

		Ext.Msg.show({
			title:  _('this_action_may_take_a_long_time'),
			msg: _('would_you_like_to_continue'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.WARNING,
			fn: function (btn) {
				if(btn === 'yes'){
					Ext.getBody().el.mask(_('creating_backup_be_right_back'));

					BackUp.doBackUp(function () {
						me.getAdministrationBackupGrid().store.load();
						Ext.getBody().el.unmask();
						Ext.Msg.alert(_('sweet'), _('backup_completed'));
					});
				}
			}
		});



	}

});
