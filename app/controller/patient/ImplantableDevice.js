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

Ext.define('App.controller.patient.ImplantableDevice', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'ImplantableDeviceGrid',
			selector: '#ImplantableDeviceGrid'
		},
		{
			ref: 'ImplantableDeviceDetailsWindow',
			selector: '#ImplantableDeviceDetailsWindow'
		},
		{
			ref: 'ImplantableDeviceDetailsForm',
			selector: '#ImplantableDeviceDetailsForm'
		},
		{
			ref: 'ImplantableDeviceDetailsUDIField',
			selector: '#ImplantableDeviceDetailsUDIField'
		},
		{
			ref: 'ImplantableDeviceDetailsSaveBtn',
			selector: '#ImplantableDeviceDetailsSaveBtn'
		},
		{
			ref: 'ImplantableDeviceGridAddBtn',
			selector: '#ImplantableDeviceGridAddBtn'
		},
		{
			ref: 'ImplantableDeviceGridActiveBtn',
			selector: '#ImplantableDeviceGridActiveBtn'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'#ImplantableDeviceGrid': {
				'activate': me.onImplantableDeviceGridActivate
			},
			'#ImplantableDeviceDetailsCancelBtn': {
				'click': me.onImplantableDeviceDetailsCancelBtnClick
			},
			'#ImplantableDeviceDetailsSaveBtn': {
				'click': me.onImplantableDeviceDetailsSaveBtnClick
			},
			'#ImplantableDeviceGridAddBtn': {
				'click': me.onImplantableDeviceGridAddBtnClick
			},
			'#ImplantableDeviceDetailsParseBtn': {
				'click': me.onImplantableDeviceDetailsParseBtnClick
			},
			'#ImplantableDeviceGridActiveBtn': {
				'toggle': me.onImplantableDeviceGridActiveBtnToggle
			}
		});
	},

	onImplantableDeviceGridActivate: function (grid) {
		var store = grid.getStore(),
			filters = [
				{
					property: 'pid',
					value: app.patient.pid
				}
			];

		if(this.getImplantableDeviceGridActiveBtn().pressed){
			filters = Ext.Array.push(filters, {
				property: 'status',
				value: 'Active'
			});
		}

		store.clearFilter(true);
		store.filter(filters);
	},

	onImplantableDeviceGridActionClick: function (grid, device_record) {
		this.showImplantableDeviceDetailsWindow();
		this.getImplantableDeviceDetailsForm().getForm().reset();
		this.getImplantableDeviceDetailsForm().getForm().setValues(device_record.data);
		this.getImplantableDeviceDetailsSaveBtn().hide();
	},

	onImplantableDeviceGridAddBtnClick: function () {
		this.showImplantableDeviceDetailsWindow();
	},

	onImplantableDeviceDetailsParseBtnClick: function () {
		var me = this,
			udi = this.getImplantableDeviceDetailsUDIField().getValue();

		ImplantableDevice.getUidData({udi: udi}, function (response) {

			if(response.parse.data.error){
				app.msg(_('oops'), response.parse.data.error, true);
				return;
			}

			var device = response.lookup.data.gudid.device,
				unit = response.parse.data;

			var values = {
				udi: unit.udi,
				di: unit.di,
				description: device.gmdnTerms.gmdn.gmdnPTName,
				description_code: '',
				description_code_type: 'GMDN',
				lot_number: unit.lot_number | '',
				serial_number: unit.serial_number || '',
				exp_date: unit.expiration_date || null ,
				mfg_date: unit.manufacturing_date || null,
				donation_id: unit.donation_id || '',
				brand_name: device.brandName || '',
				version_model: device.versionModelNumber || '',
				company_name: device.companyName || '',
				mri_safety_info: device.MRISafetyStatus || '',
				required_lnr: device.labeledContainsNRL || false
			};

			me.getImplantableDeviceDetailsForm().getForm().setValues(values);

		});

	},

	onImplantableDeviceGridActiveBtnToggle: function (btn, pressed) {
		this.onImplantableDeviceGridActivate(this.getImplantableDeviceGrid());
	},

	onImplantableDeviceDetailsCancelBtnClick: function () {
		this.getImplantableDeviceDetailsForm().getForm().reset();
		this.getImplantableDeviceDetailsWindow().close();
	},

	onImplantableDeviceDetailsSaveBtnClick: function () {

		var me = this,
			grid = this.getImplantableDeviceGrid(),
			form = this.getImplantableDeviceDetailsForm().getForm(),
			device_record = Ext.create('App.model.patient.ImplantableDevice', form.getValues());

		device_record.set({
			pid: app.patient.pid,
			eid: app.patient.eid,
			create_uid: app.user.id,
			update_uid: app.user.id,
			create_date: new Date(),
			update_date: new Date()
		});

		grid.getStore().add(device_record);
		grid.getStore().sync({
			callback: function () {
				me.getImplantableDeviceDetailsForm().getForm().reset();
				me.getImplantableDeviceDetailsWindow().close();
			}
		});

	},

	showImplantableDeviceDetailsWindow: function () {
		if(!this.getImplantableDeviceDetailsWindow()){
			Ext.create('App.view.patient.windows.ImplantableDeviceDetails');
		}
		return this.getImplantableDeviceDetailsWindow().show();
	}

});