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

Ext.define('App.controller.patient.LabOrders', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.view.patient.windows.UploadDocument'
	],
	refs: [
		{
			ref: 'LabOrdersGrid',
			selector: 'patientlaborderspanel'
		},
		{
			ref: 'ElectronicLabOrderBtn',
			selector: 'patientlaborderspanel #electronicLabOrderBtn'
		},
		{
			ref: 'NewLabOrderBtn',
			selector: 'patientlaborderspanel #newLabOrderBtn'
		},
		{
			ref: 'PrintLabOrderBtn',
			selector: 'patientlaborderspanel #printLabOrderBtn'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientlaborderspanel': {
				activate: me.onLabOrdersGridActive,
				selectionchange: me.onLabOrdersGridSelectionChange,
				beforerender: me.onLabOrdersGridBeforeRender,
				validateedit: me.onLabOrdersGridValidateEdit
			},
			'#rxLabOrderLabsLiveSearch': {
				select: me.onLoincSearchSelect
			},
			'patientlaborderspanel #electronicLabOrderBtn': {
				click: me.onElectronicLabOrderBtnClick
			},
			'patientlaborderspanel #newLabOrderBtn': {
				click: me.onNewLabOrderBtnClick
			},
			'patientlaborderspanel #printLabOrderBtn': {
				click: me.onPrintLabOrderBtnClick
			},
			'#LabOrdersUnableToPerformField': {
				select: me.onLabOrdersUnableToPerformFieldSelect,
				fieldreset: me.onLabOrdersUnableToPerformFieldReset
			}
		});

		me.encounterCtl = me.getController('patient.encounter.Encounter');

	},

	onOrdersDeleteActionHandler: function (grid, rowIndex, colIndex, item, e, record) {

		if(!a('remove_patient_order')){
			app.msg(_('oops'), _('not_authorized'), true);
			return;
		}

		var me = this,
			store = grid.getStore();

		Ext.Msg.show({
			title: _('wait'),
			msg: ('<b>' + record.get('description') + '</b><br><br>' + _('delete_this_record')),
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

	onLabOrdersGridValidateEdit: function(plugin, context){
		var form = plugin.editor.getForm(),
			combo = form.findField('description'),
			combo_value = combo.getValue(),
			selected_record = combo.findRecordByValue(combo_value);

		if(combo_value === null){
			context.record.set({
				code: null,
				description: null,
			});
		}else if(selected_record){
			context.record.set({
				code: selected_record.get('loinc_number'),
				code_type: 'LOINC',
				description: selected_record.get('loinc_name')
			});
		}
	},

	onLabOrdersUnableToPerformFieldSelect: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord(),
			selected_record = combo.findRecordByValue(combo.getValue());

		form_record.set({
			not_performed_code: selected_record.get('code'),
			not_performed_code_type: selected_record.get('code_type'),
			not_performed_code_text: selected_record.get('option_name'),
		});
	},

	onLabOrdersUnableToPerformFieldReset: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord();
		combo.setValue(null);
		form_record.set({
			not_performed_code: null,
			not_performed_code_type: null,
			not_performed_code_text: null,
		});
	},

	onLabOrdersGridBeforeRender: function(grid){
		app.on('patientunset', function(){
			grid.editingPlugin.cancelEdit();
			grid.getStore().removeAll();
		});
	},

	onLabOrdersGridSelectionChange: function(sm, selected){
		this.getPrintLabOrderBtn().setDisabled(selected.length === 0);
	},

	onLoincSearchSelect: function(cmb, records){
		var form = cmb.up('form').getForm();

		form.getRecord().set({code: records[0].data.loinc_number});
		if(form.findField('code')) form.findField('code').setValue(records[0].data.loinc_number);
		if(form.findField('note')) form.findField('note').focus(false, 200);
	},

	onElectronicLabOrderBtnClick: function(){
		// say('TODO!');
	},

	onNewLabOrderBtnClick: function(){
		var me = this,
			grid = me.getLabOrdersGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			date_ordered: new Date(),
			order_type: 'lab',
			status: 'Pending',
			priority: 'Normal'
		});
		grid.editingPlugin.startEdit(0, 0);
	},

	onPrintLabOrderBtnClick: function(input, print){
		var me = this,
			grid = me.getLabOrdersGrid(),
			orders = (Ext.isArray(input) ? input : grid.getSelectionModel().getSelection()),
			documents = {};

		orders.forEach(function(order){
			var date_ordered = Ext.Date.format(order.get('date_ordered'),'Y-m-d'),
				pdf_format = (g('order_pdf_format') || null),
				doc_key = '_' + order.get('eid') + order.get('pid') + order.get('uid') + date_ordered;

			if(order.get('void')){
				app.msg(_('oops'), Ext.String.format('Unable to print voided order #{0}', order.get('id')), true);
				return;
			}

			if(!documents[doc_key]){
				documents[doc_key] = {};
				documents[doc_key].pid = order.get('pid');
				documents[doc_key].eid = order.get('eid');
				documents[doc_key].date_ordered = date_ordered;
				documents[doc_key].provider_uid = order.get('uid');
				documents[doc_key].orderItems = [];
				documents[doc_key].docType = 'Lab';
				documents[doc_key].templateId = 4;
				documents[doc_key].pdf_format = pdf_format;
				documents[doc_key].dx_required = true;
				documents[doc_key].orderItems.push(['Description', 'Notes']);
			}

			documents[doc_key].orderItems.push([
				order.get('description') + ' [' + order.get('code_type') + ':' + order.get('code') + ']',
				order.get('note')
			]);
		});

		if(Ext.Object.isEmpty(documents)){
			return;
		}

		Ext.Object.each(documents, function(key, params){
			DocumentHandler.createTempDocument(params, function(response){

				if(Ext.isString(response)){
					app.msg(_('oops'), response, true);
					return;
				}

				if(print === true){
					Printer.doTempDocumentPrint(1, response.id);
				}else{
					if(window.dual){
						dual.onDocumentView(response.id, 'Lab');
					}else{
						app.onDocumentView(response.id, 'Lab');
					}
				}
			});
		});
	},

	onLabOrdersGridActive:function(grid){
		var store = grid.getStore();

		if(!grid.editingPlugin.editing){
			store.clearFilter(true);
			store.filter([
				{
					property: 'pid',
					value: app.patient.pid
				},
				{
					property: 'order_type',
					value: 'lab'
				},
				{
					property: 'is_external_order',
					value: 0
				}
			]);
		}
	},

	labOrdersGridStatusColumnRenderer:function(v){
		var color = 'black';

		switch(v){
			case 'Canceled':
				color = 'red';
				break;
			case 'Pending':
				color = 'orange';
				break;
			case 'Routed':
				color = 'blue';
				break;
			case 'Complete':
				color = 'green';
				break;
			default:
				color = '';
		}

		return '<div style="color:' + color + '">' + v + '</div>';
	},

	doAddOrderByTemplate: function(data){
		var me = this,
			grid = me.getLabOrdersGrid(),
			store = grid.getStore();

		data.pid = app.patient.pid;
		data.eid = app.patient.eid;
		data.uid = app.user.id;
		data.date_ordered = new Date();
		data.order_type = 'lab';
		data.status = 'Pending';
		data.priority = 'Normal';

		var record = store.add(data)[0];

		record.save({
			success: function(){
				app.msg(_('sweet'), data.description + ' ' + _('added'));
			}
		});
	}
});
