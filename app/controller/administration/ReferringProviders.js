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

Ext.define('App.controller.administration.ReferringProviders', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'ReferringProvidersPanel',
			selector: 'referringproviderspanel'
		},
		{
			ref: 'ReferringProviderAddBtn',
			selector: '#referringProviderAddBtn'
		},
		{
			ref: 'ReferringProviderWindow',
			selector: '#ReferringProviderWindow'
		},
		{
			ref: 'ReferringProviderWindowForm',
			selector: '#ReferringProviderWindowForm'
		},
		{
			ref: 'ReferringProviderWindowGrid',
			selector: '#ReferringProviderWindowGrid'
		},
		{
			ref: 'ReferringProviderInsuranceBlacklistGrid',
			selector: '#ReferringProviderInsuranceBlacklistGrid'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				referringproviderddbtnclick: me.onReferringProviderSearchAddBtnClick
			},
			'referringproviderspanel': {
				itemdblclick: me.onReferringProvidersPanelItemDblClick
			},
			'#referringProviderAddBtn': {
				click: me.onReferringProviderAddBtnClick
			},
			'#ReferringProviderWindow': {
				close: me.onReferringProviderWindowClose
			},
			'#ReferringProviderWindowCancelBtn': {
				click: me.onReferringProviderWindowCancelBtnClick
			},
			'#ReferringProviderWindowSaveBtn': {
				click: me.onReferringProviderWindowSaveBtnClick
			},
			'#ReferringProviderFacilityAddBtn': {
				click: me.onReferringProviderFacilityAddBtnClick
			},
			'#ReferringProviderAuthyRegisterBtn': {
				click: me.onReferringProviderAuthyRegisterBtnClick
			},
			'#ReferringProviderWindowFormNpiSearchField': {
				searchresponse: me.onReferringProviderWindowFormNpiSearchFieldSearchResponse
			},
			'#ProceduresHistoryGridPerformerField': {
				select: me.onProceduresHistoryGridPerformerFieldSelect
			},
			'#ProceduresHistoryGridServiceLocationField': {
				select: me.onProceduresHistoryGridServiceLocationFieldSelect
			},

			'#ReferringProviderInsuranceBlacklistGrid': {
				beforeedit: me.onReferringProviderInsuranceBlacklistGridBeforeEdit,
				validateedit: me.onReferringProviderInsuranceBlacklistGridValidateEdit
			},
			'#ReferringProviderInsuranceBlacklistAddBtn': {
				click: me.onReferringProviderInsuranceBlacklistAddBtnClick
			},
			'#ReferringProviderInsuranceBlacklistInsuranceCmb': {
				select: me.onReferringProviderInsuranceBlacklistInsuranceCmbSelect
			},
			'#ReferringProviderInsuranceBlacklistSpecialtyCmb': {
				select: me.onReferringProviderInsuranceBlacklistSpecialtyCmbSelect
			}
		});
	},

	onReferringProviderInsuranceBlacklistGridBeforeEdit: function (plugin, context){
		return a('allow_edit_referring_physician_blacklist');
	},

	onReferringProviderInsuranceBlacklistGridValidateEdit: function (plugin, context){

		//say('onReferringProviderInsuranceBlacklistGridValidateEdit');
		//say(plugin);
		//say(context);
		var form = plugin.editor.getForm(),
			specialty_combo = form.findField('specialty_id'),
			specialty_combo_record = specialty_combo.findRecordByValue(specialty_combo.getValue()),
			insurance_combo = form.findField('insurance_id'),
			insurance_combo_record = insurance_combo.findRecordByValue(insurance_combo.getValue());

		// say(form);
		// say(insurance_combo);
		// say(insurance_combo_record);

		if(specialty_combo_record){
			context.record.set({specialty_name: specialty_combo_record.get('title')});
		}else{
			context.record.set({specialty_name: ''});
		}

		if(insurance_combo_record){
			context.record.set({insurance_name: insurance_combo_record.get('name')});
		}else{
			context.record.set({insurance_name: ''});
		}

	},

	onReferringProviderInsuranceBlacklistAddBtnClick: function (btn){
		var form = btn.up('window').down('form').getForm(),
			grid = btn.up('grid'),
			store = grid.getStore();

		if(!form.isValid()){
			app.msg(_('oops'), _('invalid_values_found'), true);
			return;
		}

		grid.editingPlugin.cancelEdit();
		grid.editingPlugin.startEdit(store.add({
			npi: form.findField('npi').getValue()
		})[0], 1);
	},

	onReferringProviderInsuranceBlacklistInsuranceCmbSelect: function (cmb){

	},

	onReferringProviderInsuranceBlacklistSpecialtyCmbSelect: function (cmb){

	},

	onProceduresHistoryGridPerformerFieldSelect: function(field, selection){

		if(selection.length === 0) return;

		var record = field.up('form').getForm().getRecord();

		record.set({
			performer_id: selection[0].get('id')
		});
	},

	onProceduresHistoryGridServiceLocationFieldSelect: function(field, selection){

		if(selection.length === 0) return;

		var record = field.up('form').getForm().getRecord();

		record.set({
			service_location_id: selection[0].get('id')
		});
	},

	onReferringProviderAuthyRegisterBtnClick: function(){
		var referring_grid = this.getReferringProvidersPanel(),
			referring_store = referring_grid.getStore(),
			referring_record = referring_grid.getSelectionModel().getLastSelected();

		//say(referring_record);

		TwoFactorAuthentication.registerUserByIdAndType(
			referring_record.get('id'),
			'referring',
			referring_record.get('email'),
			referring_record.get('cel_number').replace(/[-() ]/g,''),
			function (response) {
				say(response);
				if(!response.success){
					app.msg(_('oops'), response.errors.replace(/,/g, '<br>'), true);
				}else {
					referring_record.set({authy_id: response.authy_id});
					referring_record.commit();
					referring_grid.view.refresh();
				}
			}
		);
	},

	onReferringProviderWindowFormNpiSearchFieldSearchResponse: function (field, result) {

		if (!result.success) {
			app.msg(_('oops'), result.error, true);
			return;
		}

		if (result.data === false) {
			app.msg(_('oops'), _('no_provider_found'), true);
			return;
		}

		var values = {
			global_id: null,
			title: result.data.basic.name_prefix,
			fname: result.data.basic.first_name || '',
			mname: '',
			lname: result.data.basic.last_name || '',
			organization_name: result.data.basic.organization_name || '',
			active: 1,
			npi: result.data.number,
			lic: '',
			taxonomy: '',
			upin: '',
			ssn: '',
			notes: 'NPI registry import',
			username: '',
			password: '',
			authorized: 0,
			email: '',
			phone_number: '',
			fax_number: '',
			cel_number: ''
		};

		var facilities = [];

		if(result.data.taxonomies) {
			result.data.taxonomies.forEach(function (taxonomy) {
				if (!taxonomy.primary) return;
				values.taxonomy = taxonomy.code || '';
				values.lic = taxonomy.license || '';
			});
		}

		if(result.data.addresses){
			result.data.addresses.forEach(function (address) {
				if(address.address_purpose !== 'LOCATION') return;
				values.phone_number = address.telephone_number || '';
				values.fax_number = address.fax_number || '';

				facilities = Ext.Array.push(facilities, {
					name: values.organization_name,
					address: address.address_1 || '',
					address_cont: address.address_2 || '',
					city: address.city || '',
					postal_code: address.postal_code || '',
					state: address.state || ''
				});
			});
		}

		var form = field.up('form').getForm(),
			store = this.getReferringProviderWindowGrid().getStore();
		form.setValues(values);
		store.add(facilities);

	},

	onReferringProviderSearchAddBtnClick: function (field, field_record) {
		var me = this;

		if(field_record){
			App.model.administration.ReferringProvider.load(field_record.get('id'), {
				success: function(referring_record) {
					me.doReferringProviderWindow(referring_record);
				}
			});
		}else{
			me.doReferringProviderWindow();
		}

		me.triggerField = field;
	},

	onReferringProviderAddBtnClick: function () {
		this.doReferringProviderWindow();
		this.triggerField = undefined;
	},

	onReferringProvidersPanelItemDblClick: function (grid, referring_record) {
		this.doReferringProviderWindow(referring_record);
		this.triggerField = undefined;
	},

	doReferringProviderWindowByReferringId: function (referring_id, callback) {
		var me = this;

		App.model.administration.ReferringProvider.load(referring_id, {
			success: function (referring_record) {
				referring_record.saveCallback = callback;
				me.doReferringProviderWindow(referring_record);
			}
		});
	},


	doReferringProviderWindow: function (referring_record) {

		referring_record = referring_record || Ext.create('App.model.administration.ReferringProvider', {
				global_id: null,
				create_date: new Date(),
				update_date: new Date(),
				create_uid: app.user.id,
				update_uid: app.user.id,
				username: null,
				active: 1
			});

		this.showReferringProviderWindow();

		var form = this.getReferringProviderWindowForm().getForm(),
			facilities_grid = this.getReferringProviderWindowGrid(),
			facilities_store = referring_record.facilities(),
			insurance_blacklist_grid = this.getReferringProviderInsuranceBlacklistGrid(),
			insurance_blacklist_store = referring_record.blacklist();

		form.loadRecord(referring_record);

		facilities_grid.reconfigure(facilities_store);
		facilities_store.load();

		insurance_blacklist_grid.reconfigure(insurance_blacklist_store);
		insurance_blacklist_store.load();
	},

	onReferringProviderWindowClose: function () {
		this.getReferringProviderWindowForm().getForm().reset();
		this.getReferringProviderWindowGrid().getStore().removeAll();
	},

	onReferringProviderWindowCancelBtnClick: function () {
		this.getReferringProviderWindow().close();
	},

	onReferringProviderWindowSaveBtnClick: function () {
		var me = this,
			form = me.getReferringProviderWindowForm().getForm(),
			store = me.getReferringProviderWindowGrid().getStore(),
			record = form.getRecord(),
			values = form.getValues();

		if(!form.isValid()) return;

		if((values.lname.trim() != '' || values.fname.trim() != '') && values.organization_name.trim() == ''){
			app.msg(_('oops'), _('referring_name_validation_msg'), true);
			return;
		}

		values.update_date = new Date();
		values.update_uid = app.user.id;
		if(values.username === '') values.username = null;

		record.set(values);

		record.save({
			callback: function (provider_record) {
				var sync_records = Ext.Array.merge(store.getUpdatedRecords(), store.getNewRecords());

				if(sync_records.length > 0){
					sync_records.forEach(function (sync_record) {
						sync_record.set({referring_provider_id: provider_record.get('id')})
					});

					store.sync({
						callback: function () {
							me.getReferringProviderWindow().close();
							me.recordSaveHandler(provider_record);
						}
					});
				}else {
					me.getReferringProviderWindow().close();
					me.recordSaveHandler(provider_record);
				}
			}
		});
	},

	recordSaveHandler: function (provider_record) {
		if(this.triggerField){
			var records = this.triggerField.store.add(provider_record.data);
			this.triggerField.select(records[0]);
			this.triggerField.fireEvent('select', this.triggerField, records);
		}else if(provider_record.saveCallback && typeof provider_record.saveCallback == 'function'){
			provider_record.saveCallback();
		}else if(!provider_record.store){
			this.getReferringProvidersPanel().getStore().insert(0, provider_record);
		}else{
			this.getReferringProvidersPanel().view.refresh();
		}
	},

	onReferringProviderFacilityAddBtnClick: function () {
		var grid = this.getReferringProviderWindowGrid();
		grid.editingPlugin.cancelEdit();

		var records = grid.getStore().add({
			create_date: new Date(),
			update_date: new Date(),
			create_uid: app.user.id,
			update_uid: app.user.id
		});

		grid.editingPlugin.startEdit(records[0], 0);
	},

	showReferringProviderWindow: function () {
		if(!this.getReferringProviderWindow()){
			Ext.create('App.view.administration.ReferringProviderWindow');
		}
		return this.getReferringProviderWindow().show();
	}

});
