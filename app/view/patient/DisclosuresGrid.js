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

Ext.define('App.view.patient.DisclosuresGrid', {
	extend: 'Ext.grid.Panel',
	requires: [
	],
	xtype: 'patientdisclosuresgrid',
	title: _('disclosures'),
	itemId: 'PatientDisclosuresGrid',
	bodyPadding: 0,
	plugins: Ext.create('Ext.grid.plugin.RowEditing', {
		autoCancel: false,
		errorSummary: false,
		clicksToEdit: 2
	}),
	columns: [
		{
			xtype: 'datecolumn',
			format: 'Y-m-d H:i:s',
			text: _('date'),
			with: 220,
			dataIndex: 'request_date',
			editor: {
				xtype: 'datefield'
			},
		},
		{
			header: _('type'),
			dataIndex: 'type',
			editor: {
				xtype: 'gaiaehr.combo',
				list_key: 'disclosures_types'
			},
			renderer: function(v){
				return _(v);
			}
		},
		{
			header: _('recipient'),
			dataIndex: 'recipient',
			editor: {
				xtype: 'textfield'
			},
			renderer: function(v){
				return _(v);
			}
		},
		{
			text: _('description'),
			dataIndex: 'description',
			flex: 1,
			editor: {
				xtype: 'textfield'
			}
		},
		{
			text: _('document_inventory'),
			dataIndex: 'document_inventory',
			flex: 1
		},
		{
			text: _('document_count'),
			dataIndex: 'total_document_inventory',
			width: 100
		}
	],
	tbar: [
		'->',
		{
			xtype: 'button',
			text: _('print'),
			itemId: 'PatientDisclosuresPrintBtn',
		},
		{
			xtype: 'button',
			text: _('download'),
			itemId: 'PatientDisclosuresDownloadBtn',
		},
		{
			xtype: 'button',
			text: _('burn'),
			itemId: 'PatientDisclosuresBurnBtn',
		},
		'-',
		{
			text: _('disclosure'),
			iconCls: 'icoAdd',
			itemId: 'PatientDisclosuresGridAddBtn',
		}
	],

	initComponent: function () {
		var me = this;

		me.store = Ext.create('App.store.patient.Disclosures', {
			autoSync: false,
			autoLoad: false
		});

		me.callParent();


	}
});