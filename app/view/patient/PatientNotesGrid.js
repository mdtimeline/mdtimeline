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

Ext.define('App.view.patient.PatientNotesGrid', {
	extend: 'Ext.grid.Panel',
	requires: [
		'Ext.grid.feature.Grouping',
		'App.ux.grid.RowFormEditing',
	],
	xtype: 'patientnotesgrid',

	initComponent: function (){
		var me = this;

		me.store = Ext.create('App.store.patient.Notes', {
			autoSync: false,
			remoteFilter: true,
			pageSize: 50
		});

		me.columns = [
			{
				xtype: 'griddeletecolumn',
				acl: a('delete_patient_notes'),
				width: 20
			},
			{
				xtype: 'datecolumn',
				text: _('date'),
				format: 'Y-m-d',
				dataIndex: 'date'
			},
			{
				text: _('type'),
				dataIndex: 'type'
			},
			{
				text: _('note'),
				dataIndex: 'body',
				flex: 1
			},
			{
				text: _('user'),
				width: 200,
				dataIndex: 'user_name'
			},
			{
				text: _('update') + ' ' + _('user'),
				width: 200,
				dataIndex: 'update_user_name'
			},
			{
				xtype: 'datecolumn',
				text: _('update') + ' ' + _('date'),
				format: 'Y-m-d',
				dataIndex: 'update_date'
			}
		];

		me.plugins = Ext.create('App.ux.grid.RowFormEditing', {
			// autoCancel: false,
			// errorSummary: false,
			clicksToEdit: 2,
			items: [
				{
					xtype: 'gaiaehr.combo',
					// xtype: 'textfield',
					fieldLabel: _('type'),
					name: 'type',
					emptyText: _('type'),
					queryMode: 'local',
					listKey: 'admin_note_types',
					width: 300,
					margin: '0 0 5 0',
					loadStore: true,
					editable: false
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('note'),
					name: 'body',
					height: 50,
					anchor: '100%'
				}
			]
		});



		me.tbar = [
			'->',
			{
				text: _('add_note'),
				iconCls: 'icoAdd',
				action: 'note',
				itemId: 'PatientNotesGridAddNotesBtn',
				//handler: me.onAddNew,
				acl: a('add_patient_notes')
			}
		];

		me.bbar = {
			xtype: 'pagingtoolbar',
			store: me.store,
			displayInfo: true,
			plugins: new Ext.ux.SlidingPager()
		};

		me.callParent();
	}
});