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

Ext.define('App.view.patient.Visits', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('visits_history'),
	uses: [
		'Ext.grid.Panel',
		'Ext.ux.PreviewPlugin'
	],
	itemId: 'PatientVisitsPanel',
	showRating: true,
	initComponent: function(){
		var me = this;

		me.store = Ext.create('App.store.patient.Encounters', {
			remoteFilter: true
		});

		//******************************************************************
		// Visit History Grid
		//******************************************************************
		me.historyGrid = Ext.create('Ext.grid.Panel', {
			title: _('encounter_history'),
			store: me.store,
			itemId: 'PatientVisitsGrid',
			columns: [
				{
					header: 'eid',
					sortable: false,
					dataIndex: 'eid',
					hidden: true
				},
				{
					width: 150,
					header: _('date'),
					sortable: true,
					dataIndex: 'service_date',
					// renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s')
				},
				{
					flex: 1,
					header: _('reason'),
					sortable: true,
					dataIndex: 'brief_description'
				},
				{
					width: 180,
					header: _('provider'),
					sortable: false,
					dataIndex: 'provider'
				},
				{
					width: 120,
					header: _('facility'),
					sortable: false,
					dataIndex: 'facility'
				},
				{
					width: 120,
					header: _('billing_facility'),
					sortable: true,
					dataIndex: 'billing_facility'
				},
				{
					width: 45,
					header: _('close') + '?',
					sortable: true,
					dataIndex: 'close_date',
					renderer: app.boolRenderer
				}
			],
			tbar: Ext.create('Ext.PagingToolbar', {
				store: me.store,
				displayInfo: true,
				emptyMsg: 'No Encounters Found',
				plugins: Ext.create('Ext.ux.SlidingPager', {}),
				items: [
					'-',
					{
						text: _('new_encounter'),
						itemId: 'PatientVisitsNewEncounterBtn'
					},
					'-',
					{
						text: _('progress_report'),
						disabled: true,
						itemId: 'PatientVisitsNewProgressReportsBtn'
					}
				]
			})
		});
		me.pageBody = [me.historyGrid];

		me.callParent(arguments);
	}
});
