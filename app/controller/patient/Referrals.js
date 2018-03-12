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

Ext.define('App.controller.patient.Referrals', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'ReferralPanelGrid',
			selector: 'patientreferralspanel'
		},
		{
			ref: 'AddReferralBtn',
			selector: 'button[action=addReferralBtn]'
		},
		{
			ref: 'PrintReferralBtn',
			selector: '#printReferralBtn'
		},
		{
			ref: 'ReferralProviderCombo',
			selector: '#ReferralProviderCombo'
		},
		{
			ref: 'ReferralLocalProviderCombo',
			selector: '#ReferralLocalProviderCombo'
		},
		{
			ref: 'ReferralExternalProviderCombo',
			selector: '#ReferralExternalProviderCombo'
		},
		{
			ref: 'ReferralExternalReferralCheckbox',
			selector: '#ReferralExternalReferralCheckbox'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'patientreferralspanel': {
				activate: me.onReferralActive,
				selectionchange: me.onGridSelectionChange,
				beforeedit: me.onGridBeforeEdit
			},
			'button[action=addReferralBtn]': {
				click: me.onAddReferralBtnClicked
			},
			'#ReferralServiceSearch': {
				select: me.onReferralServiceSearchSelect
			},
			'#referralDiagnosisSearch': {
				select: me.onReferralDiagnosisSearchSelect
			},
			'#ReferralExternalReferralCheckbox': {
				change: me.onReferralExternalReferralCheckboxChange
			},
			'#printReferralBtn': {
				click: me.onPrintReferralBtnClick
			},
			'#ReferralProviderCombo':{
				select: me.onReferralProviderComboSelect
			},
			'#ReferralLocalProviderCombo':{
				select: me.onReferralLocalProviderComboSelect
			},
			'#ReferralExternalProviderCombo':{
				select: me.onReferralExternalProviderComboSelect
			}
		});
	},

	onGridBeforeEdit: function(editor, context, eOpts){
		this.getReferralExternalReferralCheckbox().setValue(context.record.data.is_external_referral);
		this.getReferralLocalProviderCombo().setValue(context.record.data.refer_to_text);
		this.getReferralExternalProviderCombo().setValue(context.record.data.refer_to_text);
	},

	onReferralProviderComboSelect: function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();
		record.set({refer_by: records[0].data.option_value});
	},

	onReferralLocalProviderComboSelect: function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();
		record.set({refer_to: records[0].data.id});
	},

	onReferralExternalProviderComboSelect: function(cmb, records){
		var record = cmb.up('form').getForm().getRecord();
		record.set({refer_to: records[0].data.id});
	},

	onPrintReferralBtnClick:function(referral, print){
		var me = this,
			grid = me.getReferralPanelGrid(),
			sm = grid.getSelectionModel(),
			selection = (referral.isModel ? [referral] : sm.getSelection()),
            params,
            i;

		if(grid.view.el) grid.view.el.mask(_('generating_documents'));

		for(i=0; i < selection.length; i++){
			params = {
                pid: app.patient.pid,
                eid: app.patient.eid,
                referralId: selection[i].data.id,
                templateId: 10,
                docType: 'Referral'
            };
			DocumentHandler.createTempDocument(params, function(provider, response){

				if(print === true){
					Printer.doTempDocumentPrint(1, response.result.id);
				}else {
					if(window.dual){
						dual.onDocumentView(response.result.id, 'Referral');
					}else{
						app.onDocumentView(response.result.id, 'Referral');
					}
				}
				if(grid.view.el) grid.view.el.unmask();
			});
		}
	},

	onGridSelectionChange:function(grid, models){
		this.getPrintReferralBtn().setDisabled(models.length == 0);
	},

	onReferralServiceSearchSelect: function(cmb, records){
		var referral = cmb.up('form').getForm().getRecord();
		referral.set({
			service_code: records[0].get('ConceptId'),
			service_code_type: records[0].get('CodeType')
		});
	},

	onReferralDiagnosisSearchSelect: function(cmb, records){
		var referral = cmb.up('form').getForm().getRecord();
		referral.set({
			diagnosis_code: records[0].data.code,
			diagnosis_code_type: records[0].data.code_type
		});
	},

	onReferralExternalReferralCheckboxChange: function(checkbox, isExternal){
		this.getReferralLocalProviderCombo().setVisible(!isExternal);
		this.getReferralLocalProviderCombo().setDisabled(isExternal);
		this.getReferralExternalProviderCombo().setVisible(isExternal);
		this.getReferralExternalProviderCombo().setDisabled(!isExternal);
	},

	onReferralActive: function(grid){
		var store = grid.getStore();
		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	onAddReferralBtnClicked: function(){
		var me = this,
			store = me.getReferralPanelGrid().getStore(),
			plugin = me.getReferralPanelGrid().editingPlugin,
			records;

		plugin.cancelEdit();
		records = store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			create_date: app.getDate(),
			create_uid: app.user.id,
			referral_date: app.getDate()
		});
		plugin.startEdit(records[0], 0);
	},

	doAddReferralByTemplate: function (data) {
		var me = this,
			grid = me.getReferralPanelGrid(),
			store = grid.getStore();

		data.pid = app.patient.pid;
		data.eid = app.patient.eid;
		data.refer_by = app.user.id;
		data.refer_by_text = "";
		data.referral_date = app.getDate();
		data.create_uid =  app.user.id;
		data.update_uid =  app.user.id;
		data.create_date = app.getDate();
		data.update_date = app.getDate();

		var record = store.add(data)[0];

		record.save({
			success: function(){
				app.msg(_('sweet'), data.service_text + ' Referral ' + _('added'));
			}
		});

	}

});
