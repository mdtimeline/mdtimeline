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

Ext.define('App.controller.LogOut', {
    extend: 'Ext.app.Controller',
	requires:[
		'App.ux.ActivityMonitor'
	],
	refs: [
		{
			ref: 'ApplicationLockWindow',
			selector: '#ApplicationLockWindow'
		},
		{
			ref: 'ApplicationLockWindowPingField',
			selector: '#ApplicationLockWindowPingField'
		}
	],

	lockMask: undefined,

	init: function() {
		var me = this;

		/**
		 * in seconds - interval to check for
		 * mouse and keyboard activity
		 */
		me.activityMonitorInterval = 10;
		/**
		 * in minutes - Maximum time application can
		 * be inactive (no mouse or keyboard input)
		 */
		me.activityMonitorMaxInactive = eval(g('timeout'));
		me.activityMonitorMaxLock = eval(g('lockscreen'));

		me.cron = me.getController('Cron');

		me.control({
			'treepanel[action=mainNav]':{
				beforerender: me.onNavigationBeforeRender
			},
			'menuitem[action=logout]':{
				click: me.appLogout
			},
			'#ApplicationLockWindowPingField':{
				keyup: me.onApplicationLockWindowPingFieldKeyUp
			},
			'#ApplicationLockWindowLogoutBtn':{
				click: me.onApplicationLockWindowLogoutBtnClick
			},
			'#ApplicationLockWindowOkBtn':{
				click: me.onApplicationLockWindowOkBtnClick
			}
		});

		window.addEventListener("message", me.receiveMessage, false);

	},

	receiveMessage: function (event) {
    	if(event.data === 'captureActivity'){
		    App.ux.ActivityMonitor.captureActivity();
	    }
	},

	onNavigationBeforeRender:function(treepanel){
		treepanel.getStore().on('load', function(){
			this.ActivityMonitor(true);
		}, this);
	},

	ActivityMonitor:function(start){
		var me = this;

		if(start){
			App.ux.ActivityMonitor.init({
				interval: me.activityMonitorInterval * 1000,
				maxInactive: (1000 * 60 * me.activityMonitorMaxInactive),
				maxLock: (1000 * 60 * me.activityMonitorMaxLock),
				verbose: false,
				controller: me,
				isInactive: function(){
					me.startAutoLogout();
				},
				isLock: function(){
					me.doApplicationLock();
				},
				// isUnLock: function(){
				// 	me.doApplicationUnLock();
				// }
			});
			me.cron.start();
			App.ux.ActivityMonitor.start();
		}else{
			me.cron.stop();
			App.ux.ActivityMonitor.stop();
		}
	},

	cancelAutoLogout: function(){
		var me = this;
		app.el.unmask();
		me.LogoutTask.stop(me.LogoutTaskTimer);
		me.logoutWarinigWindow.destroy();
		delete me.logoutWarinigWindow;
		App.ux.ActivityMonitor.start();
	},

	doApplicationLock: function () {
		this.appMask();
		this.showApplicationLockWindow();
		this.getApplicationLockWindowPingField().focus();
	},

	doApplicationUnLock: function () {
		this.appUnMask();
		this.hideApplicationLockWindow();
	},

	startAutoLogout: function(){
		var me = this;
		me.logoutWarinigWindow = Ext.create('Ext.Container', {
			floating: true,
			cls: 'logout-warning-window',
			html: 'Logging Out in...',
			seconds: 10
		}).show();

		app.el.mask();

		if(!me.LogoutTask)
			me.LogoutTask = new Ext.util.TaskRunner();
		if(!me.LogoutTaskTimer){
			me.LogoutTaskTimer = me.LogoutTask.start({
				scope: me,
				run: me.logoutCounter,
				interval: 1000
			});
		}else{
			me.LogoutTask.start(me.LogoutTaskTimer);
		}
	},

	logoutCounter: function(){
		var me = this, sec = me.logoutWarinigWindow.seconds - 1;
		if(sec <= 0){
			me.logoutWarinigWindow.update('Logging Out... Bye! Bye!');
			me.appLogout(true);
		}else{
			me.logoutWarinigWindow.update('Logging Out in ' + sec + 'sec');
			me.logoutWarinigWindow.seconds = sec;
		}
	},

	appLogout: function(force){
		var me = this,
			nav = me.getController('Navigation');

		if(force === true){
			me.ActivityMonitor(false);
			if(app.patient.pid) Patient.unsetPatient(app.patient.pid);
			authProcedures.unAuth(function(){
				nav.navigateTo('App.view.login.Login', null, true);
				window.onbeforeunload = null;
				window.location.reload();
			});
		}else{
			Ext.Msg.show({
				title: _('please_confirm') + '...',
				msg: _('are_you_sure_to_quit') + ' mdTimeline?',
				icon: Ext.MessageBox.QUESTION,
				buttons: Ext.Msg.YESNO,
				fn: function(btn){
					if(btn == 'yes'){
						if(app.patient.pid) Patient.unsetPatient(app.patient.pid);
						authProcedures.unAuth(function(){
							me.ActivityMonitor(false);
							nav.navigateTo('App.view.login.Login', null, true);
							window.onbeforeunload = null;
							window.location.reload();
						});
					}
				}
			});
		}
	},

	showApplicationLockWindow: function () {
		if(!this.getApplicationLockWindow()){
			Ext.create('App.view.administration.ApplicationLockWindow');
		}
		this.getApplicationLockWindow().setTitle('LOCKED BY: ' + app.user.getFullName());

		return this.getApplicationLockWindow().show();
	},

	hideApplicationLockWindow: function () {
		if(this.getApplicationLockWindow()){
			this.getApplicationLockWindow().close()
		}
	},
	
	appMask: function () {
		var mask = app.el.mask();
		mask.addCls('app-lock-mask');

	},
	
	appUnMask: function () {
		app.el.unmask();
	},

	onApplicationLockWindowLogoutBtnClick: function (btn) {
		this.appLogout();
	},

	onApplicationLockWindowOkBtnClick: function () {
		this.doValidate();
	},

	onApplicationLockWindowPingFieldKeyUp: function (field, e) {
		if(e.getKey() !== e.RETURN) return;
		this.doValidate(field);
	},

	doValidate: function (field) {
		var me = this;

		field = field || me.getApplicationLockWindowPingField();

		if(!field.isValid()) return;

		me.validatePin(field.getValue(), function (user) {
			if(user){
				me.doSwitchUser(user);
			}else {
				app.msg(_('oops'),_('wrong_pin'), true);
				field.reset();
			}
		});
	},

	doSwitchUser: function (user) {

		var me = this,
			params = {
			facility: app.user.facility
		};

		if(app.fireEvent('beforeusersessionswitch', user) === false) return;

		authProcedures.doAuth(params, user, function (session) {
			if(session.user.id != app.user.id){
				app.fireEvent('usersessionswitch', me, session);
			}else{
				me.doApplicationUnLock();
			}
		});
	},

	validatePin: function (pin, callback) {
		User.getUserByPin(pin, function (response) {

			if(response === false){
				callback(false);
			}else{
				callback((response.id == app.user.id || a('allow_user_switch')) ? response : false);
			}


		});
	}

});
