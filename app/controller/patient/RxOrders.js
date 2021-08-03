/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.controller.patient.RxOrders', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'RxOrdersGrid',
			selector: 'patientrxorderspanel'
		},
		{
			ref: 'RxOrdersGridTopToolbar',
			selector: '#RxOrdersGridTopToolbar'
		},
		{
			ref: 'RxNormOrderLiveSearch',
			selector: '#RxNormOrderLiveSearch'
		},
		{
			ref: 'CloneRxOrderBtn',
			selector: '#cloneRxOrderBtn'
		},
		{
			ref: 'PrintRxOrderBtn',
			selector: '#printRxOrderBtn'
		},
		{
			ref: 'RxEncounterDxLiveSearch',
			selector: '#rxEncounterDxLiveSearch'
		},
		{
			ref: 'RxEncounterDxCombo',
			selector: '#RxEncounterDxCombo'
		},
		{
			ref: 'RxOrderMedicationInstructionsCombo',
			selector: '#RxOrderMedicationInstructionsCombo'
		},
		{
			ref: 'RxOrderGridFormNotesField',
			selector: '#RxOrderGridFormNotesField'
		},
		{
			ref: 'RxOrderCompCheckBox',
			selector: '#RxOrderCompCheckBox'
		},
		{
			ref: 'RxOrderSplyCheckBox',
			selector: '#RxOrderSplyCheckBox'
		},
		{
			ref: 'RxOrdersShowAllMedicationsBtn',
			selector: '#RxOrdersShowAllMedicationsBtn'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientrxorderspanel': {
				activate: me.onRxOrdersGridActive,
				selectionchange: me.onRxOrdersGridSelectionChange,
				beforerender: me.onRxOrdersGridBeforeRender,
				beforeedit: me.onRxOrdersGridBeforeEdit,
				edit: me.onRxOrdersGridEdit,
				validedit: me.onRxOrdersGridValidEdit
			},
			'#RxOrdersGridTopToolbar > button[action=rx_show]': {
				toggle: me.onRxOrdersGridShowBtnToggle
			},
			'#RxOrderEndDateField': {
				select: me.onRxOrderEndDateFieldSelect
			},
			'#RxNormOrderLiveSearch': {
				beforeselect: me.onRxNormOrderLiveSearchBeforeSelect
			},
			'#newRxOrderBtn': {
				click: me.onNewRxOrderBtnClick
			},
			'#cloneRxOrderBtn': {
				click: me.onCloneRxOrderBtnClick
			},
			'#printRxOrderBtn': {
				click: me.onPrintRxOrderBtnClick
			},
			'#RxOrderCompCheckBox': {
				change: me.onRxOrderCompCheckBoxChange
			},
			'#RxOrderSplyCheckBox': {
				change: me.onRxOrderSplyCheckBoxChange
			},
			'#RxOrdersShowAllMedicationsBtn': {
				toggle: me.onRxOrdersShowAllMedicationsBtnToggle
			},
			'#RxOrderGridFormUnableToPerformField': {
				select: me.onRxOrderGridFormUnableToPerformFieldSelect,
				fieldreset: me.onRxOrderGridFormUnableToPerformFieldReset
			}
		});
	},

	onRxOrderEndDateFieldSelect: function (field){
		var form = field.up('form').getForm();

		say('onRxOrderEndDateFieldSelect');
		say(field);

		form.getRecord().set({
			is_active: false,
			active: false
		});
	},

	onRxOrderGridFormUnableToPerformFieldSelect: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord(),
			selected_record = combo.findRecordByValue(combo.getValue());

		form_record.set({
			not_performed_code: selected_record.get('code'),
			not_performed_code_type: selected_record.get('code_type'),
			not_performed_code_text: selected_record.get('option_name'),
		});
	},

	onRxOrderGridFormUnableToPerformFieldReset: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord();

		combo.setValue(null);
		form_record.set({
			not_performed_code: null,
			not_performed_code_type: null,
			not_performed_code_text: null,
		});
	},

	onRxOrdersShowAllMedicationsBtnToggle: function (btn, pressed) {
		this.doLoadOrdersGrid();
	},

	onRxOrdersDeleteActionHandler: function (grid, rowIndex, colIndex, item, e, record) {

		if(!a('remove_patient_medication')){
			app.msg(_('oops'), _('not_authorized'), true);
			return;
		}

		var me = this,
			store = grid.getStore();

		Ext.Msg.show({
			title: _('wait'),
			msg: ('<b>' + record.get('STR') + '</b><br><br>' + _('delete_this_record')),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn1) {
				if(btn1 === 'yes'){
					Ext.Msg.show({
						title: _('wait'),
						msg: _('this_action_can_not_be_undone_continue'),
						buttons: Ext.Msg.YESNO,
						icon: Ext.Msg.QUESTION,
						fn: function (btn2) {
							if(btn2 === 'yes'){
								store.remove(record);
								store.sync({
									callback: function () {
										store.remove(record);
										store.sync({
											callback: function () {

											}
										});
									}
								});
							}
						}
					});
				}
			}
		});
	},

	onRxOrderCompCheckBoxChange: function(field, value){
		if(value){
			this.getRxOrderSplyCheckBox().setValue(false);
		}
	},

	onRxOrderSplyCheckBoxChange: function(field, value){
		if(value){
			this.getRxOrderCompCheckBox().setValue(false);
		}
	},

	doSelectOrderByOrderId: function(id){
		var sm = this.getRxOrdersGrid().getSelectionModel(),
			record = this.getRxOrdersGrid().getStore().getById(id);

		if(record){
			sm.select(record);
			return record;
		}

		return false;
	},

	onRxOrdersGridBeforeRender: function(grid){
		app.on('patientunset', function(){
			grid.editingPlugin.cancelEdit();
			grid.getStore().removeAll();
		});
	},

	onRxOrdersGridSelectionChange: function(sm, selected){
		this.getCloneRxOrderBtn().setDisabled(selected.length === 0);
		this.getPrintRxOrderBtn().setDisabled(selected.length === 0);
	},

	onRxNormOrderLiveSearchBeforeSelect: function(combo, record){
		var form = combo.up('form').getForm(),
			insCmb = this.getRxOrderMedicationInstructionsCombo(),
			order_record = form.getRecord(),
            store;

		order_record.set({
			STR: record.data.STR,
			RXCUI: record.data.RXCUI,
			CODE: record.data.CODE,
            GS_CODE: record.data.GS_CODE,
			NDC: record.data.NDC,
			TTY: record.data.TTY
		});

		try{
			var data = {};

			Rxnorm.getMedicationAttributesByRxcuiApi(record.data.RXCUI, function(response){

				if(response.propConceptGroup){
					response.propConceptGroup.propConcept.forEach(function(propConcept){

						if(propConcept.propCategory !== 'ATTRIBUTES' && propConcept.propCategory !== 'CODES') return;

						if(!data[propConcept.propCategory]){
							data[propConcept.propCategory] = {};
						}
						var propName = propConcept.propName.replace(' ', '_');
						data[propConcept.propCategory][propName] = propConcept.propValue;
					});
				}

				if(data.ATTRIBUTES && data.ATTRIBUTES.SCHEDULE && data.ATTRIBUTES.SCHEDULE != '0'){
					order_record.set({ is_controlled: true });
				}
			});


			store = record.instructions();
			insCmb.bindStore(store, true);
			insCmb.store = store;
			insCmb.store.load();
			form.findField('dispense').focus(false, 200);

		}catch (e){

		}

	},

	onRxOrdersGridBeforeEdit: function(plugin, context){

		if(!context.record.data.date_ordered){
			app.msg(_('oops'), _('unable_to_edit_non_ordered_medication'), true);
			return false;
		}

		this.getRxEncounterDxCombo().getStore().load({
			filters: [
				{
					property: 'eid',
					value: context.record.data.eid
				}
			]
		});

		this.getRxOrderMedicationInstructionsCombo().getStore().load({
			filters: [
				{
					property: 'rxcui',
					value: context.record.data.RXCUI
				}
			]
		});
	},

	onRxOrdersGridValidEdit: function (){

	},

	onRxOrdersGridEdit: function(plugin, context){
		var insCmb = this.getRxOrderMedicationInstructionsCombo(),
			instructions = context.record.data.directions,
			record = insCmb.findRecordByValue(instructions),
            store;

		// record found
		if(record !== false) return true;

		Ext.Msg.show({
			title: _('new_instruction'),
			msg: '<p>' + instructions + '</p><p>' + _('would_you_like_to_save_it') + '</p>',
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if(btn === 'yes'){
					store = insCmb.getStore();
					store.add({
						rxcui: context.record.data.RXCUI,
						occurrence: '1',
						instruction: instructions
					});
					store.sync();
				}
			}
		});
		return true;
	},

	onNewRxOrderBtnClick: function(btn){
		var grid = btn.up('grid');

		grid.editingPlugin.cancelEdit();

		grid.getStore().insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			refill: 0,
			daw: null,
			date_ordered: new Date(),
			begin_date: new Date(),
			created_date: new Date(),
			is_active: true,
			active: true,
		});

		grid.editingPlugin.startEdit(0, 0);
	},

	onCloneRxOrderBtnClick: function(btn){

		var me = this;

		Ext.Msg.show({
			title: _('wait'),
			msg: _('sure_you_want_clone_prescription'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if(btn === 'yes'){
					me.doCloneOrder();
				}
			}
		});
	},

	doCloneOrder: function(additionalReference){

		var me = this,
			grid = me.getRxOrdersGrid(),
			sm = grid.getSelectionModel(),
			store = grid.getStore(),
			selection = sm.getSelection(),
			newDate = app.getDate(),
			records_data = [],
			records,
			data;

		grid.editingPlugin.cancelEdit();
		sm.deselectAll();

		for(var i = 0; i < selection.length; i++){

			data = Ext.clone(selection[i].data);

			// inactivate previous order
			selection[i].set({
				is_active: false,
				active: false,
				end_date: newDate
			});

			// make sure new order is active
			data.is_active = true;
			data.active = true;
			data.end_date = null

			data.pid = app.patient.pid;
			data.eid = app.patient.eid;
			data.uid = app.user.id;

			data.ref_order = data.id;
			if(typeof additionalReference === 'string'){
				data.ref_order += ('~' + additionalReference);
			}

			data.date_ordered = newDate;
			data.begin_date = newDate;
			data.created_date = newDate;

			// clear the id
			data.id = null;
			records_data.push(data);
		}

		records = store.insert(0, records_data);

		store.sync();

		//grid.editingPlugin.startEdit(records[0], 0);

		return records;
	},

	onPrintRxOrderBtnClick: function(input, print){
		var me = this,
			grid = me.getRxOrdersGrid(),
			orders = (Ext.isArray(input) ? input : grid.getSelectionModel().getSelection()),
			pdf_format = (g('order_pdf_format') || null),
			isSingleColumnTable = true,
			references = '',
			documents = {},
			columns,
            refs,
            text;

		orders.forEach(function(order){

			var date_ordered = Ext.Date.format(order.get('date_ordered'),'Y-m-d'),
				doc_key = '_' + order.get('eid') +
					order.get('pid') +
					order.get('uid') +
					date_ordered;

			if(!documents[doc_key]){
				documents[doc_key] = {};
				documents[doc_key].pid = order.get('pid');
				documents[doc_key].eid = order.get('eid');
				documents[doc_key].date_ordered = date_ordered;
				documents[doc_key].provider_uid = order.get('uid');
				documents[doc_key].orderItems = [];
				documents[doc_key].docType = 'Rx';
				documents[doc_key].templateId = 5;
				documents[doc_key].pdf_format = pdf_format;
				if(isSingleColumnTable){
					columns = [''];
				}else{
					columns = [
						'Description',
						'Instructions',
						'Dispense',
						'Refill',
						'Days Supply',
						'Dx',
						'Notes',
						'References'
					];
				}
				documents[doc_key].orderItems.push(columns);
			}

			if(order.get('ref_order') !== ''){
				refs = order.get('ref_order').split('~');
				if(refs.length >= 3){
					references = 'Rx Reference#: ' + refs[2];
				}
			}

			if(isSingleColumnTable){

				var lines = [];

				lines.push('<u>' + _('order_number') + '</u>: ' + g('rx_order_number_prefix') + order.get('id'));
				lines.push('<u>' + _('description') + '</u>: ' + '<b>' + order.get('STR').toUpperCase() + '</b>');
				lines.push('<u>' + _('dispense_as_written') + '</u>: ' + (order.get('daw') ? _('yes') : _('no')));
				lines.push('<u>' + _('quantity') + '</u>: ' + order.get('dispense'));

				if(order.get('days_supply')){
					lines.push('<u>' + _('days_supply') + '</u>: ' + order.get('days_supply'));
				}

				lines.push('<u>' + _('refill') + '</u>: ' + order.get('refill'));
				lines.push('<u>' + _('instructions') + '</u>: ' + order.get('directions'));

				var dxs = (order.get('dxs').join ? order.get('dxs').join(', ') : order.get('dxs'));
				if(dxs && dxs !== ''){
					lines.push('<u>' + _('dx') + '</u>: ' + (order.get('dxs').join ? order.get('dxs').join(', ') : order.get('dxs')));
				}

				if(order.get('notes') !== ''){
					lines.push('<u>' + _('notes_to_pharmacist') + '</u>: ' + order.get('notes'));
				}

				if(references !== ''){
					lines.push('<u>References</u>: ' + references);
				}

				if(order.get('system_notes') !== ''){
					lines.push('<b>' + order.get('system_notes') + '</b>');
				}

				documents[doc_key].orderItems.push([lines.join('<br>')]);

			}else{

				documents[doc_key].orderItems.push([
					order.get('STR') + ' ' + order.get('dose') + ' ' + order.get('route') + ' ' + order.get('form'),
					order.get('directions'),
					order.get('dispense'),
					order.get('refill'),
					order.get('days_supply'),
					(order.get('dxs').join ? order.get('dxs').join(', ') : order.get('dxs')),
					order.get('notes'),
					references
				]);
			}

		});



		Ext.Object.each(documents, function(key, params){




			DocumentHandler.createTempDocument(params, function(provider, response){
				if(print === true){
					Printer.doTempDocumentPrint(1, response.result.id);
				}else{
					if(window.dual){
						dual.onDocumentView(response.result.id, 'Rx');
					}else{
						app.onDocumentView(response.result.id, 'Rx');
					}
				}

			});
		});
	},

	onRxOrdersGridShowBtnToggle: function (btn, pressed){
		if(pressed){
			this.doLoadOrdersGrid();
		}
	},

	onRxOrdersGridActive: function(){
		this.doLoadOrdersGrid();
	},

	doAddOrderByTemplate: function(data){
		var me = this,
			grid = me.getRxOrdersGrid(),
			store = grid.getStore(),
			newDate = new Date();

		data.pid = app.patient.pid;
		data.eid = app.patient.eid;
		data.uid = app.user.id;
		data.date_ordered = newDate;
		data.begin_date = newDate;
		data.created_date = newDate;

		store.add(data);
		store.sync({
			success: function(){
				app.msg(_('sweet'), data.STR + ' ' + _('added'));
			}
		});

	},

	doLoadOrdersGrid: function () {

		if(!this.getRxOrdersGrid()) return;

		var grid = this.getRxOrdersGrid(),
			store = grid.getStore(),
			show_btn = this.getRxOrdersGridTopToolbar().query('button[pressed]');

		if(!grid.editingPlugin.editing){

			var filters = [
				{
					property: 'pid',
					value: app.patient.pid
				}
			];

			if(show_btn.length > 0 && show_btn[0].action2 === 'last_6_months'){
				filters.push({
					property: 'date_ordered',
					operator: '>=',
					value: Ext.util.Format.date(Ext.Date.subtract(new Date(), Ext.Date.MONTH, 6), 'Y-m-d H:i:s')
				});
			}else if(show_btn.length > 0 && show_btn[0].action2 === 'last_year'){
				filters.push({
					property: 'date_ordered',
					operator: '>=',
					value: Ext.util.Format.date(Ext.Date.subtract(new Date(), Ext.Date.MONTH, 12), 'Y-m-d H:i:s')
				});
			}

			if(!this.getRxOrdersShowAllMedicationsBtn().pressed){
				filters.push({
					property: 'date_ordered',
					operator: '!=',
					value: null
				});
			}

			store.clearFilter(true);
			store.filter(filters);

		}
	},

	ndcConvertToElevenDigits: function (ndc){

		if(ndc === '') return ndc;

		var ndcArray = ndc.split('-');
		// if [0] equals 11 number then return that number
		if(ndcArray[0].length === 11) return ndcArray[0];

		// if the number does not have 3 groups something went wrong
		// return the number glued back
		if(ndcArray.length !== 3) return ndcArray.join('');

		if(ndcArray[0].length === 4){
			ndcArray[0] = '0' + ndcArray[0];
		}else if(ndcArray[1].length === 3){
			ndcArray[1] = '0' + ndcArray[1];
		}else if(ndcArray[2].length === 1){
			ndcArray[2] = '0' + ndcArray[2];
		}

		return ndcArray.join('');

	}

});
