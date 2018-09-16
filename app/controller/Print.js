Ext.define('App.controller.Print', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.ux.combo.Printers'
	],
	refs: [


	],

	printers: [],

	browserHelperCtl: undefined,

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				browserhelperopen: me.onBrowserHelperOpen
			},
			'printerscombo': {
				render: me.onPrintersComboBeforeRender
			}
		});

	},

	doLocalPrint: function(printer, base64data){
		var me = this;

		if(!me.browserHelperCtl) {
			app.msg(_('oops'), _('no_browser_helper_found'), true);
			return;
		}

		me.browserHelperCtl.send('{"action":"print", "printer": "' + printer + '", "payload": "' + base64data + '"}', function (response) {
			say(response);
		});
	},

	onBrowserHelperOpen: function(ctl){
		var me = this;

		me.browserHelperCtl = ctl;

		me.browserHelperCtl .send('{"action":"printer/list"}', function (printers) {
			me.printers = printers;
		});

	},

	onPrintersComboBeforeRender: function(cmb){

		say('onPrintersComboBeforeRender');
		say(cmb);
		say(this.printers);


		cmb.store.loadRawData(this.printers);

	},

	promptPrint: function () {
		
	}


});
