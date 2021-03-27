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
					width: 180,
					header: _('service_date'),
					sortable: true,
					dataIndex: 'service_date',
					renderer: Ext.util.Format.dateRenderer('F j, Y, g:i a')
				},
				{
					flex: 1,
					header: _('chief_complaint'),
					sortable: true,
					dataIndex: 'brief_description',
					renderer: function (v,m,r){
						var  str = v;
						if(r.isPrivate()){
							str = '<i class="fas fa-shield-check" style="color: red"></i> ' + str;
						}

						if(r.isClose()){
							str = '<i class="fas fa-shield-check" style="color: #1b9bfc"></i> ' + str;
						}
						return str;
					}
				},
				{
					width: 200,
					header: _('provider'),
					sortable: false,
					dataIndex: 'provider_uid',
					renderer: function (v, m, r){
						return Ext.String.format(
							'{0}, {1} {2}',
							r.get('provider_lname'),
							r.get('provider_fname'),
							r.get('provider_mname')
						);
					}
				},
				{
					width: 200,
					header: _('facility'),
					sortable: false,
					dataIndex: 'facility_name'
				},
				{
					width: 70,
					header: _('signed') + '?',
					sortable: true,
					dataIndex: 'close_date',
					renderer: function (v){
						return  app.boolRenderer(v !== null);
					}
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
