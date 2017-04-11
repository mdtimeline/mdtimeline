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

Ext.define('App.view.patient.windows.ImplantableDeviceDetails', {
	extend: 'Ext.window.Window',
	xtype: 'implantabledevicedetailwindow',
	itemId: 'ImplantableDeviceDetailsWindow',
	title: _('device_data'),
	closeAction: 'hide',
	modal: true,
	width: 800,
	layout: 'fit',
	items: [
		{
			xtype:'form',
			bodyPadding: 10,
			border: false,
			itemId: 'ImplantableDeviceDetailsForm',
			items: [
				{
					xtype: 'container',
					layout: 'hbox',
					margin: '0 0 5 10',
					anchor: '100%',
					items: [
						{
							xtype: 'textfield',
							fieldLabel: _('udi'),
							itemId: 'ImplantableDeviceDetailsUDIField',
							labelWidth: 40,
							anchor: '100%',
							name: 'udi',
							flex: 1,
							submitValue: true,
							margin: '0 10 0 0',
							value: '(01)00619498372072(11)151025(10)1102397(21)20150086127'
						},
						{
							xtype: 'button',
							text: _('parse'),
							width: 70,
							itemId: 'ImplantableDeviceDetailsParseBtn'
						}
					]
				},
				{
					xtype: 'textfield',
					fieldLabel: _('di'),
					labelWidth: 40,
					anchor: '100%',
					name: 'di',
					margin: '0 0 10 10',
					submitValue: true
				},
				{
					xtype:'container',
					anchor: '100%',
					layout: {
						type: 'hbox',
						align: 'stretch'
					},
					items: [
						{
							xtype: 'fieldset',
							title: _('production_info'),
							flex: 1,
							margin: '0 3 0 0',
							items: [
								{
									xtype: 'textfield',
									fieldLabel: _('lot_number'),
									anchor: '100%',
									name: 'lot_number',
									submitValue: true
								},
								{
									xtype: 'textfield',
									fieldLabel: _('serial_number'),
									anchor: '100%',
									name: 'serial_number',
									submitValue: true
								},
								{
									xtype: 'datefield',
									fieldLabel: _('exp_date'),
									anchor: '100%',
									name: 'exp_date',
									submitValue: true
								},
								{
									xtype: 'datefield',
									fieldLabel: _('mfg_date'),
									anchor: '100%',
									name: 'mfg_date',
									submitValue: true
								},
								{
									xtype: 'textfield',
									fieldLabel: _('donation_id'),
									anchor: '100%',
									name: 'donation_id',
									submitValue: true
								}

							]
						},
						{
							xtype: 'fieldset',
							title: _('device_info'),
							margin: '0 0 0 3',
							flex: 1,
							items: [
								{
									xtype: 'textfield',
									fieldLabel: _('gmdn_pt_name'),
									anchor: '100%',
									name: 'description',
									submitValue: true
								},
								{
									xtype: 'textfield',
									fieldLabel: _('brand_name'),
									anchor: '100%',
									name: 'brand_name',
									submitValue: true
								},
								{
									xtype: 'textfield',
									fieldLabel: _('version_model'),
									anchor: '100%',
									name: 'version_model',
									submitValue: true
								},
								{
									xtype: 'textfield',
									fieldLabel: _('company_name'),
									anchor: '100%',
									name: 'company_name',
									submitValue: true
								},
								{
									xtype: 'textareafield',
									fieldLabel: _('mri_safety_info'),
									anchor: '100%',
									name: 'mri_safety_info',
									submitValue: true
								},
								{
									xtype: 'checkbox',
									fieldLabel: _('required_lnr'),
									anchor: '100%',
									name: 'required_lnr',
									submitValue: true
								}
							]
						}
					]
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'ImplantableDeviceDetailsCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'ImplantableDeviceDetailsSaveBtn'
		}
	]
});
