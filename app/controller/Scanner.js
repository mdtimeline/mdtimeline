Ext.define('App.controller.Scanner', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.view.scanner.Window'
	],
	refs: [
		{
			ref: 'ScannerWindow',
			selector: '#ScannerWindow'
		},
		{
			ref: 'ScannerImageThumbsDataView',
			selector: '#ScannerImageThumbsDataView'
		},
		{
			ref: 'ScannerImageViewerPanelImage',
			selector: '#ScannerImageViewerPanel image'
		},
		{
			ref: 'ScannerSourceCombo',
			selector: '#ScannerSourceCombo'
		},
		{
			ref: 'ScannerImageScanBtn',
			selector: '#ScannerImageScanBtn'
		},
		{
			ref: 'ScannerOkBtn',
			selector: '#ScannerOkBtn'
		}
	],


	init: function(){
		var me = this;

		me.control({
			'#ScannerWindow': {
				show: me.onScannerWindowShow,
				close: me.onScannerWindowClose
			},
			'#ScannerImageScanBtn': {
				click: me.onScannerImageScanBtnClick
			},
			'#ScannerImageThumbsDataView': {
				itemclick: me.onScannerImageThumbsDataViewItemClick
			},
			'#ScannerImageArchiveBtn': {
				toggle: me.onScannerImageArchiveBtnClick
			},
			'#ScannerImageEditBtn': {
				toggle: me.onScannerImageEditBtnClick
			},
			'#ScannerOkBtn': {
				click: me.onScannerOkBtnClick
			}
		});

		me.helperCtrl = me.getController('App.controller.BrowserHelper');

		// me.showScanWindow();
		// me.doScannerComboLoad();

	},

	doScan: function () {
		var me = this,
			scannerId = me.getScannerSourceCombo().getValue(),
			url = Ext.String.format('http://localhost:8686/scanner/scan/{0}', scannerId);

		me.helperCtrl.sendMessage({
			url: url,
			timeout: (1000 * 60)
		}, function (response) {

			say('response');
			say(response);

			if(response == null || response.code != 200)  return;
			var response_object = JSON.parse(response.response);

			say('response_object');
			say(response_object);

			me.loadDocuments(response_object.data);

		});

	},

	loadDocuments: function (data) {

		say('loadDocuments');
		say(data);

		var me = this,
			documents = [];


		data.forEach(function (document) {
			Ext.Array.push(documents, {
				id: '',
				src: 'data:image/jpeg;base64,' + document
			});
		});

		var view = me.getScannerImageThumbsDataView(),
			store = view.getStore();


		store.loadData(documents);
		view.refresh();
	},

	onScannerImageThumbsDataViewItemClick: function (view, record) {

		say(record);

		this.getScannerImageViewerPanelImage().setSrc(record.get('src'));

	},

	showScanWindow: function(){
		if(!this.getScannerWindow()){
			Ext.create('App.view.scanner.Window');
		}
		return this.getScannerWindow().show();
	},

	doScannerComboLoad: function () {
		var me = this;

		me.helperCtrl.sendMessage({ url: 'http://localhost:8686/scanner/list' }, function (response) {

			if(response == null || response.code != 200)  return;
			var response_object = JSON.parse(response.response);

			if(response_object.success){
				me.getScannerSourceCombo().store.loadRawData(response_object.data);
				me.getScannerSourceCombo().select(me.getScannerSourceCombo().store.getAt(0));
			}

		});
	},

	onScannerImageScanBtnClick: function(){
		this.doScan();
	},

	onScannerImageArchiveBtnClick: function () {

	},

	onScannerWindowShow: function(){
		// this.doScannerComboLoad();
	},

	onScannerWindowClose: function(){
		//this.ws.close();
	},

	onScannerOkBtnClick: function () {

	},

	onScannerImageEditBtnClick: function(btn, pressed){
		if(pressed){
			this.dkrm = new Darkroom('#ScannerImage', {
				save: false,
				replaceDom: false
			});
			btn.setText(_('editing'));
		}else{
			this.dkrm.selfDestroy();
			delete this.dkrm;
			btn.setText(_('edit'));
		}

		this.getScannerScanBtn().setDisabled(pressed);
		this.getScannerOkBtn().setDisabled(pressed);
	},

	getDocument: function(){
		return this.getScannerImage().imgEl.dom.src;
	},



});
