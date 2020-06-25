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

Ext.define('App.controller.patient.RadOrders', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'RadOrdersGrid',
			selector: 'patientradorderspanel'
		},
		{
			ref: 'PrintRadOrderBtn',
			selector: 'patientradorderspanel #printRadOrderBtn'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientradorderspanel': {
				activate: me.onRadOrdersGridActive,
				selectionchange: me.onRadOrdersGridSelectionChange,
				beforerender: me.onRadOrdersGridBeforeRender
			},
			'#radOrderliveSearch': {
				select: me.onRadSearchSelect
			},
			'patientradorderspanel #newRadOrderBtn': {
				click: me.onNewRadOrderBtnClick
			},
			'patientradorderspanel #printRadOrderBtn': {
				click: me.onPrintRadOrderBtnClick
			},
			'#RadOrdersUnableToPerformField': {
				select: me.onRadOrdersUnableToPerformFieldSelect,
				fieldreset: me.onRadOrdersUnableToPerformFieldReset
			}
		});

		me.encounterCtl = me.getController('patient.encounter.Encounter');

	},

	onRadOrdersUnableToPerformFieldSelect: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord(),
			selected_record = combo.findRecordByValue(combo.getValue());

		form_record.set({
			not_performed_code: selected_record.get('code'),
			not_performed_code_type: selected_record.get('code_type'),
			not_performed_code_text: selected_record.get('option_name'),
		});
	},

	onRadOrdersUnableToPerformFieldReset: function(combo){
		var form = combo.up('form').getForm(),
			form_record = form.getRecord();

		combo.setValue(null);
		form_record.set({
			not_performed_code: null,
			not_performed_code_type: null,
			not_performed_code_text: null,
		});
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

	onRadOrdersGridBeforeRender: function(grid){
		app.on('patientunset', function(){
			grid.editingPlugin.cancelEdit();
			grid.getStore().removeAll();
		});
	},

	onRadSearchSelect: function(cmb, records){
		var form = cmb.up('form').getForm();
		form.getRecord().set({
			code: records[0].data.loinc_number,
			code_type: records[0].data.code_type
		});
		if(form.findField('code')) form.findField('code').setValue(records[0].data.code);
		if(form.findField('note')) form.findField('note').focus(false, 200);
	},

	onRadOrdersGridSelectionChange: function(sm, selected){
		this.getPrintRadOrderBtn().setDisabled(selected.length === 0);
	},

	onNewRadOrderBtnClick: function(){
		var me = this,
			grid = me.getRadOrdersGrid(),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			date_ordered: new Date(),
			order_type: 'rad',
			status: 'Pending',
			priority: 'Normal'
		});
		grid.editingPlugin.startEdit(0, 0);
	},

	onPrintRadOrderBtnClick: function(input, print){
		var me = this,
			grid = me.getRadOrdersGrid(),
			orders = (Ext.isArray(input) ? input : grid.getSelectionModel().getSelection()),
			documents = {};

		orders.forEach(function(order){

			var date_ordered = Ext.Date.format(order.get('date_ordered'),'Y-m-d'),
				pdf_format = (g('order_pdf_format') || null),
				doc_key = '_' + order.get('eid') + order.get('pid') + order.get('uid') + date_ordered;

			if(!documents[doc_key]){
				documents[doc_key] = {};
				documents[doc_key].pid = order.get('pid');
				documents[doc_key].eid = order.get('eid');
				documents[doc_key].date_ordered = date_ordered;
				documents[doc_key].provider_uid = order.get('uid');
				documents[doc_key].orderItems = [];
				documents[doc_key].docType = 'Rad';
				documents[doc_key].templateId = 6;
				documents[doc_key].pdf_format = pdf_format;
				documents[doc_key].dx_required = true;
				documents[doc_key].orderItems.push(['Description', 'Notes']);
			}

			documents[doc_key].orderItems.push([
				order.get('description') + ' [' + order.get('code_type') + ':' + order.get('code') + ']',
				order.get('note')
			]);
		});

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
						dual.onDocumentView(response.id, 'Rad');
					}else{
						app.onDocumentView(response.id, 'Rad');
					}
				}

			});
		});
	},

	onRadOrdersGridActive: function(grid){
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
					value: 'rad'
				}
			]);
		}
	},

	radOrdersGridStatusColumnRenderer: function(v){
		var color = 'black';

		if(v == 'Canceled'){
			color = 'red';
		}else if(v == 'Pending'){
			color = 'orange';
		}else if(v == 'Routed'){
			color = 'blue';
		}else if(v == 'Complete'){
			color = 'green';
		}

		return '<div style="color:' + color + '">' + v + '</div>';
	},

	doAddOrderByTemplate: function(data){
		var me = this,
			grid = me.getRadOrdersGrid(),
			store = grid.getStore();

		data.pid = app.patient.pid;
		data.eid = app.patient.eid;
		data.uid = app.user.id;
		data.date_ordered = new Date();
		data.order_type = 'rad';
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
