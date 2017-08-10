Ext.define('App.controller.Scanner', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.view.scanner.Window',
		'App.view.patient.windows.ArchiveDocument'
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
			ref: 'ScannerImageViewer',
			selector: '#ScannerImageViewer'
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
			ref: 'ScannerImageScanBtn',
			selector: '#ScannerImageScanBtn'
		},
		{
			ref: 'ScannerImageArchiveBtn',
			selector: '#ScannerImageArchiveBtn'
		}
	],

	init: function(){
		var me = this;

		me.documents_defautls = {};
		me.is_chrome_scan = window.chrome && window.chrome.documentScan;

		me.control({
			'#ScannerWindow': {
				afterrender: me.onScannerWindowAfterRender,
				show: me.onScannerWindowShow,
				close: me.onScannerWindowClose,
				beforeclose: me.onScannerWindowBeforeClose
			},
			'#ScannerImageScanBtn': {
				click: me.onScannerImageScanBtnClick
			},
			'#ScannerImageThumbsDataView': {
				itemclick: me.onScannerImageThumbsDataViewItemClick,
				itemdblclick: me.onScannerImageThumbsDataViewItemDblClick
			},
			'#ScannerImageArchiveBtn': {
				click: me.onScannerImageArchiveBtnClick
			},
			'#ScannerImageEditBtn': {
				toggle: me.onScannerImageEditBtnToggle
			},
			'#ScannerImageCloseBtn': {
				click: me.onScannerImageCloseBtn
			}
		});

		me.helperCtrl = me.getController('App.controller.BrowserHelper');

	},

	doScan: function () {
		var me = this,
			scannerId = me.getScannerSourceCombo().getValue();

		if(!me.is_chrome_scan){
			me.helperCtrl.sendMessage({
				action: 'scan',
				data: scannerId
			}, function (response) {


				if(response.documents && response.documents.length > 0){
					me.loadDocuments(response.documents, false);
				} else {

					if(response.error && response.error != ''){
						app.msg(_('oops'), response.error, true);
					}

					if(response.error && response.error != ''){
						say(response.error)
					}

					if(response.stacktrace && response.stacktrace != ''){
						say(response.stacktrace)
					}

					return;
				}

				if(response.error && response.error != ''){
					say(response.error)
				}

			});
		}else {
			chrome.documentScan.scan({ maxImages: 10 }, function(result){
				me.loadDocuments(result.dataUrls, true);
			});
		}



	},

	loadDocuments: function (data, mime_type_included) {

		var me = this,
			documents = [];

		data.forEach(function (document) {
			Ext.Array.push(documents, {
				id: '',
				archived: false,
				src: mime_type_included ? document : ('data:image/jpeg;base64,' + document)
			});
		});

		var view = me.getScannerImageThumbsDataView(),
			store = view.getStore();

		store.loadData(documents, true);
		view.refresh();
	},

	onScannerImageThumbsDataViewItemClick: function (view, record) {
		this.getScannerImageViewerPanelImage().setSrc(record.get('src'));
	},

	onScannerImageThumbsDataViewItemDblClick: function (view) {
		this.onArchive(view);
	},

	showScanWindow: function(closeCallback, archivedCallback){

		if(!this.is_chrome_scan && !this.helperCtrl.connected){
			app.msg(_('oops'), _('browser_helper_not_connected'), true);
			return false;
		}

		if(!this.getScannerWindow()){
			Ext.create('App.view.scanner.Window');
		}

		this.getScannerWindow().skip_validation = false;
		this.closeCallback = closeCallback ? closeCallback : Ext.emptyFn;
		this.archivedCallback = archivedCallback ? archivedCallback : Ext.emptyFn;

		return this.getScannerWindow().show();
	},

	onScannerImageScanBtnClick: function(){
		this.doScan();
	},

	onScannerImageArchiveBtnClick: function (btn) {
		this.onArchive(btn);
	},

	onArchive: function (cmp) {
		var win = cmp.up('window');
		var archive = Ext.widget('patientarchivedocumentwindow',{
			documentWindow: win
		});
		archive.down('form').getForm().setValues(this.documents_defautls);
	},

	onScannerWindowAfterRender: function(){
		if(!this.is_chrome_scan) return;
		this.getScannerSourceCombo().hide();
	},

	onScannerWindowShow: function (win) {
		this.currentonScannerWindow = win;
		if(this.is_chrome_scan) return;
		this.doScannerComboLoad();
	},

	doScannerComboLoad: function () {
		var me = this;

		me.helperCtrl.sendMessage(
			{
				action: 'scanner/list'
			}, function (response) {

				if(response){
					me.getScannerSourceCombo().store.loadRawData(response);
					me.getScannerSourceCombo().select(me.getScannerSourceCombo().store.getAt(0));
				}
			});
	},

	onScannerWindowClose: function(win){
		win.skip_validation = false;
		this.currentonScannerWindow = null;
		this.documents_defautls = {};
		this.getScannerImageThumbsDataView().store.removeAll();
		this.getScannerImageViewerPanelImage().setSrc('');
		this.closeCallback();
	},

	onScannerWindowBeforeClose: function(win){
		if(win.skip_validation === true || this.allArchived()){
			return true;
		}else{
			Ext.Msg.show({
				title: _('wait'),
				msg: _('document_not_archived_error'),
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.QUESTION,
				fn: function (btn) {
					if(btn == 'yes'){
						win.skip_validation = true;
						win.close();
					}
				}
			});

			return false;
		}
	},

	onScannerImageCloseBtn: function () {
		this.getScannerWindow().close();
	},

	doArchive: function (values, callback) {

		var me = this;

		if(!values.pid){
			app.msg(_('oops'), _('no_patient_found'), true);
			callback(false);
			return;
		}

		var model = Ext.create('App.model.patient.PatientDocuments', values);

		model.set({
			date: new Date(),
			document: this.getDocument()
		});

		model.save({
		 	success: function () {

		 		var selected = me.getLastSelectedDocument(),
			        store = selected.store;

			    me.archivedCallback(model);


			    store.remove(selected);
			    store.commitChanges();
			    me.getScannerImageThumbsDataView().refresh();

			    // send callback to close window
			    callback(true);

			    if(me.allArchived() && me.currentonScannerWindow){
				    me.currentonScannerWindow.close();
			    }
			}
		});
	},
	
	allArchived: function () {
		var store = this.getScannerImageThumbsDataView().store;
		return store.data.items.length == 0;
	},

	onScannerImageEditBtnToggle: function(btn, pressed){
		if(pressed){
			var target = '#' + this.getScannerImageViewer().id;

			this.dkrm = new Darkroom(target, {
				save: false,
				replaceDom: false
			});
			btn.setText(_('editing'));
		}else{
			this.dkrm.selfDestroy();
			delete this.dkrm;
			btn.setText(_('edit'));
		}

		this.getScannerImageScanBtn().setDisabled(pressed);
		this.getScannerImageArchiveBtn().setDisabled(pressed);
	},

	getLastSelectedDocument: function(){
		return this.getScannerImageThumbsDataView().getSelectionModel().getLastSelected();
	},

	getDocument: function(){
		return this.getScannerImageViewer().imgEl.dom.src;
	}
});
