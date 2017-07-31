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
			'#ReferringProviderWindowFormNpiSearchField': {
				searchresponse: me.onReferringProviderWindowFormNpiSearchFieldSearchResponse
			}
		});
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
			title: result.data.basic.name_prefix,
			fname: result.data.basic.first_name,
			mname: '',
			lname: result.data.basic.last_name,
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
					name: '',
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
	
	onReferringProviderSearchAddBtnClick: function (field) {
		this.doReferringProviderWindow();
		this.triggerField = field;
	},

	onReferringProviderAddBtnClick: function () {
		this.doReferringProviderWindow();
		this.triggerField = undefined;
	},

	onReferringProvidersPanelItemDblClick: function (grid, referring_record) {
		this.doReferringProviderWindow(referring_record);
		this.triggerField = undefined;
	},
	
	doReferringProviderWindow: function (referring_record) {

		referring_record = referring_record || Ext.create('App.model.administration.ReferringProvider', {
				create_date: new Date(),
				update_date: new Date(),
				create_uid: app.user.id,
				update_uid: app.user.id,
				username: null,
				active: 1
			});

		this.showReferringProviderWindow();

		var form = this.getReferringProviderWindowForm().getForm(),
			grid = this.getReferringProviderWindowGrid(),
			facilities_store = referring_record.facilities();

		form.loadRecord(referring_record);
		grid.reconfigure(facilities_store);
		facilities_store.load();
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

		values.update_date = new Date();
		values.update_uid = app.user.id;
		if(values.username === '') values.username = null;

		record.set(values);

		record.save({
			callback: function () {
				var sync_records = Ext.Array.merge(store.getUpdatedRecords(), store.getNewRecords());

				if(sync_records.length > 0){
					sync_records.forEach(function (sync_record) {
						sync_record.set({referring_provider_id: record.get('id')})
					});

					store.sync({
						callback: function () {
							me.getReferringProviderWindow().close();
							me.recordSaveHandler(record);
						}
					});
				}else {
					me.getReferringProviderWindow().close();
					me.recordSaveHandler(record);
				}
			}
		});
	},

	recordSaveHandler: function (provider_record) {
		if(this.triggerField){
			var records = this.triggerField.store.add(provider_record.data);
			this.triggerField.select(records[0]);
			this.triggerField.fireEvent('select', this.triggerField, records);
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