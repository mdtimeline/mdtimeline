Ext.define('App.controller.Burner', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'BurnerStatusWindow',
			selector: '#BurnerStatusWindow'
		},
		{
			ref: 'BurnerStatusWindowProgressbar',
			selector: '#BurnerStatusWindowProgressbar'
		}
	],


	// Remote.PortalStudy.studyDownload(params, function(response){
	// 	me.getMainView().unmask();
	// 	burnerCtl.doCDBurner(response.download_url);
	// });

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				burnermsg: me.onBurnerMsg
			},
			'#BurnerStatusWindowCancelBtn': {
				click: me.onBurnerStatusWindowCancelBtnClick
			}
		});

		me.helperCtrl = me.getController('App.controller.BrowserHelper');

	},

	onBurnerMsg: function(ctl, data){

		if(data.event === 'progress'){
			this.getBurnerStatusWindowProgressbar().updateProgress(data.progress, data.message)
		}else{
			//app.msg('CD Burner', data.message, 'blue');
		}

		app.msg('CD Burner', data.message, 'blue');

	},

	doLocalBurn: function (zip_file_url, media_label) {
		var me = this;

		me.helperCtrl.sendMessage({
			action: 'burn',
			data: {
				zipfileurl: zip_file_url,
				medialabel: media_label
			}
		});
	},

	onBurnerStatusWindowCancelBtnClick: function(btn){
		this.helperCtrl.sendMessage({
			action: 'calcelburn'
		});
	},

	showBurnerStatusWindow: function () {
		Ext.create('Ext.window.Window', {
			title: 'CD Burner',
			itemId: 'BurnerStatusWindow',
			layout: 'fit',
			items: {  // Let's put an empty grid in just to illustrate fit layout
				xtype: 'progressbar',
				itemId: 'BurnerStatusWindowProgressbar'
			},
			buttons: [
				{
					text: _('cancel'),
					itemId: 'BurnerStatusWindowCancelBtn'
				}
			]
		}).show();
	}


});
