Ext.define('App.controller.DualScreen', {
    extend: 'Ext.app.Controller',
	requires:[

	],
	refs: [
        {
            ref:'DualViewport',
            selector:'#dualViewport'
        },
        {
            ref:'Header',
            selector:'#RenderPanel-header'
        },
        {
            ref:'TabPanel',
            selector:'#dualViewport tabpanel'
        },
        {
            ref:'ApplicationDualScreenBtn',
            selector:'#ApplicationDualScreenBtn'
        }
	],

	isDual: false,
	appMask: null,
	pid: null,
	init: function() {
		var me = this;

		me._loggedout = false;
		me._enable = true;
		me._screen = null;

		me.control({
			'#ApplicationDualScreenBtn':{
				toggle: me.onApplicationDualScreenBtnToggle
			},
			'#dualViewport':{
				render:me.onDualViewportRender,
				beforerender:me.onDualViewportBeforeRender
			}
		});

	},

	onApplicationDualScreenBtnToggle: function (btn){
		if(btn.pressed){
			this.startDual();
		}else{
			this.stopDual();
		}
	},

	startDual:function(){
		var me = this;
		me.enable();
		if(me._screen == null || me._screen.closed){
			me._screen = window.open(('./?site='+ app.user.site + '&dual=true'),'_target','fullscreen=yes,menubar=no',true);
		}
	},

	stopDual:function(){
		this.disable();
		//if(this._screen) this._screen.close();
		this._screen = null;
	},

	enable:function(){
		app._dual_enable = true;
		this._enable = true;
	},

	disable:function(){
		app._dual_enable = false;
		this._enable = false;
	},

	isEnabled:function(){
		return this._enable;
	},

	onDualViewportBeforeRender:function(){
		this.isDual = true;
		window.app = window.opener.app;
		// app.on('patientset', this.onPatientSet, this);
		// app.on('patientunset', this.onPatientUnset, this);
	},

	onDualViewportRender:function(){
		Ext.get('mainapp-loading').remove();
		Ext.get('mainapp-loading-mask').fadeOut({
			remove: true
		});
		this.onPatientUnset(false);
		this.initHandShakeTask();
	},

	onPatientSet:function(){
        var title,
            store;

		if(!this.isDual || this._loggedout) return;
		title = app.patient.name + ' - #' + app.patient.pid + ' - ' + app.patient.age.str,
			store = this.getActiveStore();

		this.unmask();
		this.getHeader().update(title);
		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	onPatientUnset:function(filter){
        var store;

		if(!this.isDual || this._loggedout) return;
		store = this.getActiveStore();

		this.mask(_('no_patient_selected'));
		this.getHeader().update('');

		if(filter === false) return;
		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	getActiveStore:function(){
		var panel = this.getTabPanel().getActiveTab();

		if(panel.getStore){
			return panel.getStore();
		}if(panel.xtype == 'patientdocumentspanel' ||
			panel.xtype == 'patientimmunizationspanel' ||
			panel.xtype == 'patientmedicationspanel' ||
			panel.xtype == 'patientimmunizationspanel'){
			return panel.down('grid').getStore();
		}
	},

	mask:function(msg){
		var me = this;
		if(me.appMask == null){
			me.appMask = new Ext.LoadMask(me.getDualViewport(), {
				msg : '<img height="190" width="190" src="resources/images/logo_190_190.jpg"><p>' + msg + '</p>',
				maskCls: 'dualAppMask',
				cls: 'dualAppMaskMsg',
				autoShow: true
			});
		}else{
			me.appMask.show();
			me.appMask.msgEl.query('p')[0].innerHTML = msg;
		}
	},

	unmask:function(){
		if(this.appMask) this.appMask.hide();
	},

	initHandShakeTask:function(){
		var me = this,
			task = {
			run: function(){
				if(window.opener == null || !window.app._dual_enable){
					// window.app.un('patientset', this.onPatientSet, this);
					// window.app.un('patientunset', this.onPatientUnset, this);
					window.app = null;
					me.pid = null;
					window.close();
				}else if(!window.opener.app.logged && !me._loggedout){
					me.pid = null;
					me.mask(_('logged_out'));
					me._loggedout = true;
				}else if(window.opener.app.patient.pid === null){
					me.pid = null;
					me.onPatientUnset();
				}else if(window.opener.app.patient.pid !== me.pid){
					me.pid = Ext.clone(window.opener.app.patient.pid);
					me.onPatientSet(true);
				}
			},
			interval: 1000,
			scope: me
		};
		Ext.TaskManager.start(task);
	}

});
