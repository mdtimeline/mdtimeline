Ext.define('App.controller.Scanner', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.view.scanner.Window',
		'App.view.patient.windows.ArchiveDocument'
	],
	refs: [
		{
			ref: 'DocumentScanWindow',
			selector: '#DocumentScanWindow'
		},
		{
			ref: 'DocumentScanDataViewPanel',
			selector: '#DocumentScanDataViewPanel'
		},
		{
			ref: 'DocumentScanThumbsDataView',
			selector: '#DocumentScanThumbsDataView'
		},
		{
			ref: 'DocumentScanForm',
			selector: '#DocumentScanForm'
		},
		{
			ref: 'DocumentScanSourceCombo',
			selector: '#DocumentScanSourceCombo'
		},
		{
			ref: 'DocumentScanDocTypeCombo',
			selector: '#DocumentScanDocTypeCombo'
		},
		{
			ref: 'DocumentScanProgressBar',
			selector: '#DocumentScanProgressBar'
		},
		{
			ref: 'DocumentScanRemoveDocumentBtn',
			selector: '#DocumentScanRemoveDocumentBtn'
		},




		// old...
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


			'#DocumentScanWindow': {
				show: me.onDocumentScanWindowShow,
				close: me.onDocumentScanWindowClose,
				beforeclose: me.onDocumentScanWindowBeforeClose
			},
			'#DocumentScanThumbsDataView': {
				beforeitemcontextmenu: me.onDocumentScanThumbsDataViewBeforeContextMenu,
				selectionchange: me.onDocumentScanThumbsDataViewSelectionChange,
			},
			'#DocumentScanSaveBtn': {
				click: me.onDocumentScanSaveBtnClick
			},
			'#DocumentScanCancelBtn': {
				click: me.onDocumentScanCancelBtnClick
			},
			'#DocumentScanStartScanBtn': {
				click: me.onDocumentScanStartScanBtnClick
			},
			'#DocumentScanRemoveDocumentBtn': {
				click: me.onDocumentScanRemoveDocumentBtnClick
			},

		});

		me.helperCtrl = me.getController('App.controller.BrowserHelper');

	},

	doScan: function (scannerId, options) {
		var me = this;

		if(!me.is_chrome_scan){

			var progress_bar = this.getDocumentScanProgressBar();

			progress_bar.wait({
				interval: 100,
				duration: (60 * 1000),
				increment: 10,
				text: _('scanning') + '...',
				fn: function(){
					progrss_bar.updateText(_('no_document_scanned'));
				}
			});

			me.helperCtrl.sendMessage({
				action: 'scan',
				data: scannerId,
				options: options.join('|')
			}, function (response) {

				progress_bar.reset();

				if(response.documents && response.documents.length > 0){
					me.loadDocuments(response.documents, false);
					progress_bar.updateProgress(1, response.documents.length + ' ' + _('documents_scanned'));

				} else {

					if(response.error && response.error != ''){
						app.msg(_('oops'), response.error, true);
						say(response.error);
						progress_bar.updateProgress(0, response.error);
					} else {
						progress_bar.updateProgress(0, _('no_document_scanned'));
					}

					if(response.stacktrace && response.stacktrace != ''){
						say(response.stacktrace);
					}
					return;
				}

				if(response.error && response.error != ''){
					progress_bar.reset();
					progress_bar.updateProgress(0, response.error);
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
			form = me.getDocumentScanForm().getForm(),
			values = form.getValues(),
			docTypeCmb = me.getDocumentScanDocTypeCombo(),
			view = me.getDocumentScanThumbsDataView(),
			store = view.getStore(),
			documents = [];

		data.forEach(function (document) {
			Ext.Array.push(documents, {
				pubpid: app.patient.pubpid,
				pid: app.patient.pid,
				eid: app.patient.eid,
				uid: app.user.id,
				global_id: app.uuidv4(),
				facility_id: app.user.facility,
				docType: docTypeCmb.findRecordByValue(values.docTypeCode).get('option_name'),
				docTypeCode: values.docTypeCode,
				date: app.getDate(),
				title: values.title,
				error_note: '',
				document: mime_type_included ? document : ('data:image/jpeg;base64,' + document)
			});
		});

		store.loadData(documents, true);
		view.refresh();
	},

	onDocumentScanStartScanBtnClick: function(){

		var me = this,
			form = this.getDocumentScanForm().getForm(),
			values = form.getValues(),
			options = [];

		if(!form.isValid()) return;

		if(values.duplex == '1'){
			options.push('CAP_DUPLEX_ENABLED');
		}
		if(values.color == '1'){
			options.push('CAP_PIXEL_TYPE_RBG');
		}
		if(values.landscape == '1'){
			options.push('CAP_ORIENTATION_LANDSCAPE');
		}
		if(values.resolution == '1'){
			options.push('CAP_RESOLUTION_NORMAL');
		}
		if(values.resolution == '2'){
			options.push('CAP_RESOLUTION_HIGH');
		}

		me.doScan(values.scannerId, options);

	},

	onDocumentScanWindowShow: function(win){
		if(this.is_chrome_scan) return;

		var progress_bar = this.getDocumentScanProgressBar();
		progress_bar.reset();
		progress_bar.updateProgress(0);
		progress_bar.updateText('');

		this.doScannerComboLoad();
	},

	onDocumentScanWindowClose: function(win){
		win.skip_validation = false;
		this.getDocumentScanThumbsDataView().store.removeAll();
		this.getDocumentScanThumbsDataView().store.commitChanges();
	},

	onDocumentScanWindowBeforeClose: function(win){
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

	onDocumentScanSaveBtnClick: function(){

		var me = this,
			win = me.getDocumentScanWindow(),
			scan_documents_view = me.getDocumentScanThumbsDataView(),
			scan_documents_store = scan_documents_view.getStore(),
			scan_documents_records = scan_documents_store.data.items;

		if(me.allArchived()) {
			app.msg(_('oops'), 'Nothing to save', true);
			return;
		}

		if(win.default_values){
			scan_documents_records.forEach(function (scan_documents_record){
				scan_documents_record.set(win.default_values);
			});
		}

		if(app.fireEvent('beforescandocumentssave', this, scan_documents_records) === false) return;

		win.mask(_('please_wait'));

		scan_documents_store.sync({
			success: function () {

				app.fireEvent('scandocumentssave', this, scan_documents_records);

				scan_documents_store.removeAll();
				scan_documents_store.commitChanges();
				win.unmask();
				win.close();
			},
			failure: function () {

				app.msg(_('oops'), _('record_error'), true);
				win.unmask();
				win.close();
			}
		});
	},

	onDocumentScanCancelBtnClick: function(){
		this.getDocumentScanWindow().close();
	},

	onDocumentScanThumbsDataViewBeforeContextMenu: function(view, record, item, index, e, eOpts ){
		e.preventDefault();
	},

	onDocumentScanThumbsDataViewSelectionChange: function(sm, selection){
		this.getDocumentScanRemoveDocumentBtn().setDisabled(selection.length === 0);
	},

	onDocumentScanRemoveDocumentBtnClick: function(){

		var me = this,
			scan_documents_view = me.getDocumentScanThumbsDataView(),
			scan_documents_store = scan_documents_view.getStore(),
			scan_documents_record = scan_documents_view.getSelectionModel().getLastSelected();

		scan_documents_store.remove(scan_documents_record);

	},

	showDocumentScanWindow: function(default_values){

		if(!this.is_chrome_scan && !this.helperCtrl.connected){
			app.msg(_('oops'), _('browser_helper_not_connected'), true);
			return false;
		}

		if(!this.getDocumentScanWindow()){
			Ext.create('App.view.scanner.DocumentScanWindow');
		}

		this.getDocumentScanWindow().default_values = default_values;
		this.getDocumentScanWindow().skip_validation = false;

		return this.getDocumentScanWindow().show();
	},

	doScannerComboLoad: function () {
		var me = this,
			cmb = me.getDocumentScanSourceCombo();

		me.helperCtrl.sendMessage(
			{
				action: 'scanner/list'
			}, function (response) {

				if(response){
					cmb.store.loadRawData(response);

					if(!cmb.getValue()) {
						cmb.select(cmb.store.getAt(0));
					}
				}
			});
	},

	allArchived: function () {
		var store = this.getDocumentScanThumbsDataView().store;
		return store.data.items.length == 0;
	},

});
