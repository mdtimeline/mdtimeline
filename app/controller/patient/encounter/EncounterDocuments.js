Ext.define('App.controller.patient.encounter.EncounterDocuments', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [],

	init: function(){
		var me = this;

		this.control({
			'#EncounterDocumentsViewBtn': {
				click: me.onEncounterDocumentsViewBtnClick
			},
			'#EncounterDocumentsPrintBtn': {
				click: me.onEncounterDocumentsPrintBtnClick
			}
		});
	},

	onEncounterDocumentsPrintBtnClick: function (btn) {
		var grid = btn.up('grid'),
			selections = grid.getSelectionModel().getSelection(),
			groups = {};

		for(var i = 0; i < selections.length; i++){
			var data = selections[i].data;

			if(!groups[data.document_type]){
				groups[data.document_type] = {};
				groups[data.document_type]['controller'] = data.controller;
				groups[data.document_type]['method'] = data.method;
				groups[data.document_type]['items'] = [];
			}

			Ext.Array.push(groups[data.document_type]['items'], data.record_id);
		}

		this.doEncounterDocumentsView(groups, true);
	},

	onEncounterDocumentsViewBtnClick: function(btn){
		var grid = btn.up('grid'),
			selections = grid.getSelectionModel().getSelection(),
			groups = {};

		for(var i = 0; i < selections.length; i++){
			var data = selections[i].data;

			if(!groups[data.document_type]){
				groups[data.document_type] = {};
				groups[data.document_type]['controller'] = data.controller;
				groups[data.document_type]['method'] = data.method;
				groups[data.document_type]['items'] = [];
			}

			Ext.Array.push(groups[data.document_type]['items'], data.record_id);
		}

		this.doEncounterDocumentsView(groups, false);
	},

	doEncounterDocumentsView: function(groups, print){
		var me = this, store, filters, i;

		Ext.Object.each(groups, function(group, data){

			if(group.toUpperCase() === 'NOTE') {
				var note_store = Ext.data.StoreManager.lookup('DoctorsNotesStore'),
					note_filters = [];

				for (i = 0; i < data.items.length; i++) {
					Ext.Array.push(note_filters, {
						property: 'id',
						value: data.items[i]
					});
				}

				note_store.load({
					filters: note_filters,
					callback: function (records) {
						me.getController(data.controller)[data.method](records[0], print);
					}
				});

			}else if(group.toUpperCase() === 'REFERRAL'){
				var ref_store = Ext.data.StoreManager.lookup('ReferralsStore'),
					referral_filters = [];

				for(i = 0; i < data.items.length; i++){
					Ext.Array.push(referral_filters, {
						property: 'id',
						value: data.items[i]
					});
				}

				ref_store.load({
					filters: referral_filters,
					callback: function(records){
						me.getController(data.controller)[data.method](records[0], print);
					}
				});

			}else if(group.toUpperCase() === 'RX'){
				var rx_store = Ext.data.StoreManager.lookup('RxOrderStore'),
					rx_filters = [];

				for(i = 0; i < data.items.length; i++){
					Ext.Array.push(rx_filters, {
						property: 'id',
						value: data.items[i]
					});
				}

				rx_store.load({
					filters: rx_filters,
					callback: function(records){
						me.getController(data.controller)[data.method](records, print);
					}
				});


			}else if(group.toUpperCase() === 'RAD'){
				var lab_store = Ext.data.StoreManager.lookup('LabOrderStore'),
					lab_filters = [];

				for(i = 0; i < data.items.length; i++){
					Ext.Array.push(lab_filters, {
						property: 'id',
						value: data.items[i]
					});
				}

				lab_store.load({
					filters: lab_filters,
					callback: function(records){
						me.getController(data.controller)[data.method](records, print);
					}
				});

			}else if(group.toUpperCase() === 'LAB'){
				var rad_store = Ext.data.StoreManager.lookup('RadOrderStore'),
					rad_filters = [];

				for(i = 0; i < data.items.length; i++){
					Ext.Array.push(rad_filters, {
						property: 'id',
						value: data.items[i]
					});
				}

				rad_store.load({
					filters: rad_filters,
					callback: function(records){
						me.getController(data.controller)[data.method](records, print);
					}
				});

			}
		});
	},


	loadDocumentsByEid: function(grid, eid){
		var store = grid.getStore();

		store.removeAll();

		Encounter.getEncounterPrintDocumentsByEid(eid, function(results){
			var data = [];

			for(var i = 0; i < results.length; i++){
				var document = results[i];

				/**
				 * This define the Controller.Method to call for this document
				 *
				 */
				if(document.document_type == 'rx'){
					document.controller = 'patient.RxOrders';
					document.method = 'onPrintRxOrderBtnClick';
				}else if(document.document_type == 'rad'){
					document.controller = 'patient.RadOrders';
					document.method = 'onPrintRadOrderBtnClick';
				}else if(document.document_type == 'lab'){
					document.controller = 'patient.LabOrders';
					document.method = 'onPrintLabOrderBtnClick';
				}else if(document.document_type == 'note'){
					document.controller = 'patient.DoctorsNotes';
					document.method = 'onPrintDoctorsNoteBtn';
				}else if(document.document_type == 'referral'){
					document.controller = 'patient.Referrals';
					document.method = 'onPrintReferralBtnClick';
				}

				document.document_type = Ext.String.capitalize(document.document_type);

				Ext.Array.push(data, document);
			}

			if(data.length > 0){
				store.loadRawData(data);
				app.fireEvent('encounterdocumentsload', store);

			}
		});

	}

});
