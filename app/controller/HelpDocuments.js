Ext.define('App.controller.HelpDocuments', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'AppHelpDocumentMenu',
			selector: '#AppHelpDocumentMenu'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'#AppHelpDocumentMenu > menuitem': {
				click: me.onAppHelpDocumentMenuClick
			},
			'button[action=helpdocument]': {
				click: me.onAppHelpDocumentBtnClick
			}
		});

	},

	onAppHelpDocumentBtnClick: function (btn){

		if(! btn.documentUrl){
			return;
		}

		this.showHelpDocumentWindow(btn.documentTitle || 'Help Document', btn.documentUrl);
	},

	onAppHelpDocumentMenuClick: function (item){
		this.showHelpDocumentWindow(item.documentTitle, item.documentUrl);
	},

	addDocumentMenuItem: function (title, url){

		if(!this.getAppHelpDocumentMenu()) return;

		this.getAppHelpDocumentMenu().menu.add({
			text: title,
			icon: 'resources/images/icons/icohelp.png',
			documentTitle: title,
			documentUrl: url
		});
	},

	showHelpDocumentWindow: function (title, url){
		Ext.create('Ext.window.Window',{
			title: title,
			width: 900,
			height: 620,
			layout: 'fit',
			html: '<iframe allowtransparency="true" style="background: #FFFFFF;" width="100%" height="100%" src="'+ url +'"></iframe>'
		}).show();
	}
});
