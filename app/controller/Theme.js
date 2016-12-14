Ext.define('App.controller.Theme', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'viewport',
			selector: 'viewport'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#AppThemeSwitcher': {
				click: me.onAppThemeSwitcherClick,
				beforerender: me.onAppThemeSwitcherBeforeRender
			}
		});

	},

	onAppThemeSwitcherBeforeRender: function(btn){

		btn.action = g('mdtimeline_theme');
		btn.setText(btn.action == 'dark' ? _('light_theme') : _('dark_theme'));

		// say('onAppThemeSwitcherBeforeRender');
		// say(btn.action);
	},

	onAppThemeSwitcherClick: function(btn){

		var me = this;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('theme_change_warning'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(answer){
				if(answer == 'yes'){
					if(btn.action == 'dark'){
						me.goLight(btn);
					}else{
						me.goDark(btn);
					}
				}
			}
		});
	},

	goLight: function(btn){
		btn.action = 'light';
		Ext.state.Manager.set('mdtimeline_theme', 'light');
		window.location.reload();
	},

	goDark: function(btn){
		btn.action = 'dark';
		Ext.state.Manager.set('mdtimeline_theme', 'dark');
		window.location.reload();
	}

});
