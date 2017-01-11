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

		me.control({
			'#ScannerWindow': {
				afterrender: me.onScannerWindowAfterRender,
				close: me.onScannerWindowClose,
				beforeclose: me.onScannerWindowBeforeClose
			},
			'#ScannerImageScanBtn': {
				click: me.onScannerImageScanBtnClick
			},
			'#ScannerImageThumbsDataView': {
				itemclick: me.onScannerImageThumbsDataViewItemClick
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

		// Ext.Function.defer(function () {
		// 	me.showScanWindow();
		// 	me.doScannerComboLoad();
		// }, 1000);

	},

	doScan: function () {
		var me = this,
			scannerId = me.getScannerSourceCombo().getValue();

		me.helperCtrl.sendMessage({
			action: 'scan',
			data: scannerId
		}, function (response) {

			if(!response.success)  return;

			me.loadDocuments(response.documents);
		});
	},

	loadDocuments: function (data) {

		var me = this,
			documents = [];

		data.forEach(function (document) {
			Ext.Array.push(documents, {
				id: '',
				archived: false,
				src: 'data:image/jpeg;base64,' + document
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

	showScanWindow: function(){

		if(!this.helperCtrl.connected){
			app.msg(_('oops'), _('browser_helper_not_connected'), true);
			return false;
		}

		if(!this.getScannerWindow()){
			Ext.create('App.view.scanner.Window');
		}
		return this.getScannerWindow().show();
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

	onScannerImageScanBtnClick: function(){
		this.doScan();
	},

	onScannerImageArchiveBtnClick: function (btn) {

		var win = btn.up('window'),
			lastSelected = this.getLastSelectedDocument();

		if(lastSelected.get('archived')){
			app.msg(_('oops'), _('document_archived'), true);
			return;
		}

		var archive = Ext.widget('patientarchivedocumentwindow',{
			documentWindow: win
		});
		archive.down('form').getForm().setValues(this.documents_defautls);
	},

	onScannerWindowAfterRender: function(){
		this.doScannerComboLoad();
	},

	onScannerWindowClose: function(win){
		this.documents_defautls = {};
		this.getScannerSourceCombo().store.removeAll();
		this.getScannerImageViewerPanelImage().setSrc('');
	},

	onScannerWindowBeforeClose: function(win){
		if(win.skip_validation === true || this.allArchived()){
			win.skip_validation = false;
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

		var model = Ext.create('App.model.patient.PatientDocuments',values);

		model.set({
			date: new Date(),
			document: this.getDocument()
		});

		model.save({
		 	success: function () {
				me.getLastSelectedDocument().set({
					archive: true,
					style: 'background-color:lightgreen;'
				});
			    me.getScannerImageThumbsDataView().refresh();
			    callback(true);
			}
		});
	},
	
	allArchived: function () {
		var store = this.getScannerSourceCombo().store;

		return store.find('archived', false) != -1;
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
