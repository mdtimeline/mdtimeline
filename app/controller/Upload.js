Ext.define('App.controller.Upload', {
	extend: 'Ext.app.Controller',
	requires: [
		// 'App.view.scanner.Window',
		// 'App.view.patient.windows.ArchiveDocument'
	],
	refs: [
		{
			ref: 'UploadDocumentWindow',
			selector: '#UploadDocumentWindow'
		},
		// {
		// 	ref: 'ScannerImageThumbsDataView',
		// 	selector: '#ScannerImageThumbsDataView'
		// },
		// {
		// 	ref: 'ScannerImageViewer',
		// 	selector: '#ScannerImageViewer'
		// },
		// {
		// 	ref: 'ScannerImageViewerPanelImage',
		// 	selector: '#ScannerImageViewerPanel image'
		// },
		// {
		// 	ref: 'ScannerSourceCombo',
		// 	selector: '#ScannerSourceCombo'
		// },
		// {
		// 	ref: 'ScannerImageScanBtn',
		// 	selector: '#ScannerImageScanBtn'
		// },
		// {
		// 	ref: 'ScannerImageScanBtn',
		// 	selector: '#ScannerImageScanBtn'
		// },
		// {
		// 	ref: 'ScannerImageArchiveBtn',
		// 	selector: '#ScannerImageArchiveBtn'
		// }
	],

	init: function(){
		var me = this;

		me.documents_defautls = {};

		me.control({
			'#UploadDocumentWindow': {
				show: me.onUploadDocumentWindowShow,
				close: me.onUploadDocumentWindowClose
			},
			'#UploadCancelBtn': {
				click: me.onUploadCancelBtnClick
			},
			'#UploadSaveBtn': {
				click: me.onUploadSaveBtnClick
			}
		});

	},

	onUploadDocumentWindowShow: function (win) {
		if(win.default_values){
			win.down('form').getForm().setValues(win.default_values);
		}
	},

	onUploadDocumentWindowClose: function () {

	},

	showUploadWindow: function(default_values){

		if(!this.getUploadDocumentWindow()){
			Ext.create('App.view.upload.UploadDocumentWindow');
		}
		this.getUploadDocumentWindow().default_values = default_values;
		return this.getUploadDocumentWindow().show();
	},

	onUploadCancelBtnClick: function(){
		this.getUploadDocumentWindow().close();
	},

	onUploadSaveBtnClick: function(){
		var me = this,
			win = me.getUploadDocumentWindow(),
			form = win.down('form').getForm(),
			record = Ext.create('App.model.patient.PatientDocuments'),
			values = form.getValues(),
			reader = new FileReader(),
			docTypeCodeField = form.findField('docTypeCode'),
			uploadField = form.findField('document');

		if(!form.isValid()) return;

		values.date =  new Date();
		values.pubpid =  app.patient.pubpid;
		values.pid =  app.patient.pid;
		values.eid =  app.patient.eid;
		values.uid =  app.user.id;
		values.facility_id =  app.user.facility;
		values.global_id = app.uuidv4();

		var docTypeRecord = docTypeCodeField.findRecordByValue(values.docTypeCode);
		if(docTypeRecord){
			values.docType = docTypeRecord.get('option_name');
		}

		record.set(values);

		reader.onload = function(e){
			record.set({document: e.target.result});

			if(app.fireEvent('beforeuploaddocumentssave', this, [record]) === false) return;

			record.save({
				success: function () {
					app.fireEvent('uploaddocumentssave', this, [record]);
					win.close();
				}
			});


		};
		reader.readAsDataURL(uploadField.extractFileInput().files[0]);

	}
});
