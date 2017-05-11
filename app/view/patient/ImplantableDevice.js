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

Ext.define('App.view.patient.ImplantableDevice', {
	extend: 'Ext.grid.Panel',
	requires: [

	],
	xtype: 'implantabledevicepanel',
	itemId: 'ImplantableDeviceGrid',
	title: _('imp_devs'),
	store: Ext.create('App.store.patient.ImplantableDevices',{
		remoteFilter: true,
		remoteSort: true
	}),
	plugins: [{
		ptype: 'rowediting'
	}],
	columns: [
		{
			xtype:'actioncolumn',
			width: 25,
			icon: 'resources/images/icons/icoPatientInfo.png',
			tooltip: _('device_data'),
			handler: function(grid, rowIndex, colIndex, item, e, record) {
				app.getController('patient.ImplantableDevice').onImplantableDeviceGridActionClick(grid, record);
			}
		},
		{
			text: _('description'),
			dataIndex: 'description',
			flex: 1
		},
		{
			text: _('udi'),
			dataIndex: 'udi',
			flex: 2
		},
		{
			xtype: 'datecolumn',
			text: _('implanted'),
			dataIndex: 'implanted_date',
			format: g('date_display_format'),
			editor: {
				xtype: 'datefield',
				format: g('date_display_format')
			}
		},
		{
			xtype: 'datecolumn',
			text: _('removed'),
			dataIndex: 'removed_date',
			format: g('date_display_format'),
			editor: {
				xtype: 'datefield',
				format: g('date_display_format')
			}
		},
		{
			text: _('note'),
			dataIndex: 'note',
			flex: 2,
			editor: {
				xtype: 'textfield'
			}
		},
		{
			text: _('status'),
			dataIndex: 'status',
			editor: {
				xtype: 'gaiaehr.combo',
				list: 113
			}
		}
	],
	tbar: [
		'->',
		{
			xtype: 'button',
			text: _('add'),
			iconCls: 'icoAdd',
			itemId: 'ImplantableDeviceGridAddBtn'
		}
	],
	bbar: [
		{
			xtype: 'button',
			text: _('active'),
			enableToggle: true,
			itemId: 'ImplantableDeviceGridActiveBtn'
		}
	]
});
