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

Ext.define('App.view.reports.AppointmentRequestReport', {
	extend: 'App.ux.RenderPanel',
	pageTitle: _('appointment_request_report'),
	pageLayout: 'border',
	itemId: 'AppointmentRequestReport',
	requires: [
		'Ext.form.field.Date',
		'Ext.ux.SlidingPager',
		'App.ux.combo.ActiveProviders',
		'App.ux.LiveSnomedProblemMultipleSearch',
		'App.ux.LiveRXNORMAllergyMultipleSearch',
		'App.ux.LiveRXNORMMultipleSearch',
		'App.ux.LiveProviderMultipleSearch',
		'App.ux.LiveSexMultipleSearch',
		'App.ux.LiveEthnicityMultipleSearch',
		'App.ux.LiveRaceMultipleSearch',
		'App.ux.LiveMaritalMultipleSearch',
		'App.ux.LiveLanguageMultipleSearch',
		'App.ux.LivePhoneCommunicationMultipleSearch',
		'App.ux.LabResultValuesFilter'
	],
	initComponent: function () {
		var me = this;

		me.store = Ext.create('App.store.reports.AppointmentRequests', {
			remoteFilter: true,
			remoteSort: true
		});

		me.pageBody = [
			{
				xtype: 'form',
				region: 'west',
				collapsible: true,
				border: true,
				split: true,
				bodyPadding: 5,
				width: 200,
				itemId: 'AppointmentRequestReportFrom',
				defaults: {
					enableReset: true,
					anchor: '100%',
					labelAlign: 'top'
				},
				items: [
					{
						xtype: 'datefield',
						name: 'date_from',
						fieldLabel: _('date_from'),
						labelWidth: 100,
						format: g('date_display_format'),
						allowBlank : false,
						submitFormat: 'Y-m-d',
						value: new Date()
					},
					{
						xtype: 'datefield',
						name: 'date_to',
						fieldLabel: _('date_to'),
						labelWidth: 100,
						allowBlank : false,
						format: g('date_display_format'),
						submitFormat: 'Y-m-d',
						value: new Date()
					},
					{
						xtype: 'liveprovidermultiple',
						name: 'requested_uid',
						fieldLabel: _('provider'),
						hideLabel: false
					}
				],
				bbar: [
					{
						text: _('search'),
						itemId: 'AppointmentRequestReportSearchBtn',
						flex: 1
					}
				]
			},
			{
				xtype: 'grid',
				region: 'center',
				frame: true,
				title: _('results'),
				itemId: 'AppointmentRequestReportGrid',
				store: me.store,
				columns: [
					{
						xtype: 'datecolumn',
						text: _('date'),
						dataIndex: 'create_date',
						format: 'M j, Y'
					},
					{
						text: _('patient'),
						dataIndex: 'lname',
						width: 300,
						renderer: function (v,meta,rec) {

							var phones = [];

							if(rec.get('phone_home') !== ''){
								phones.push(rec.get('phone_home') + ' (H)');
							}
							if(rec.get('phone_mobile') !== ''){
								phones.push(rec.get('phone_mobile') + ' (M)');
							}

							return Ext.String.format(
								'MRN: {0}<br>NAME: {1}, {2} {3} ({4})<br>DOB: {5}<br>PHONES: {6}',
								rec.get('pubpid'),
								rec.get('lname'),
								rec.get('fname'),
								rec.get('mname'),
								rec.get('sex'),
								Ext.String.format('{0} ({1})', rec.get('DOBFormatted'),	rec.getAge(rec.get('DOB'))),
								phones.join(', ')
							);
						}
					},
					{
						text: _('insurance'),
						dataIndex: 'insurance_companies',
						width: 250,
						renderer: function (v) {
							if(Ext.isString(v)){
								return v.split(',').join('<br>');
							}
							return ''
						}
					},
					{
						text: _('provider'),
						dataIndex: 'provider_lname',
						width: 250,
						renderer: function (v,meta,rec) {
							return Ext.String.format(
								'{0}, {1} {2} ({3})',
								rec.get('provider_lname'),
								rec.get('provider_fname'),
								rec.get('provider_mname'),
								rec.get('provider_npi')
							);
						}
					},
					{
						text: _('procedures'),
						dataIndex: 'id',
						flex: 1,
						renderer: function (v, meta, rec) {

							var procedures = [];

							if(rec.get('procedure1') !== ''){
								procedures.push(rec.get('procedure1'));
							}
							if(rec.get('procedure2') !== ''){
								procedures.push(rec.get('procedure2'));
							}
							if(rec.get('procedure3') !== ''){
								procedures.push(rec.get('procedure3'));
							}

							return procedures.join('<br>');
						}
					},
					// {
					// 	text: _('allergies'),
					// 	dataIndex: 'allergies',
					// 	width: 200
					// },
					// {
					// 	text: _('problems'),
					// 	dataIndex: 'problems',
					// 	width: 200
					// },
					// {
					// 	text: _('medications'),
					// 	dataIndex: 'medications',
					// 	width: 200
					// }
				],
				tbar: [
					'->',
					{
						xtype: 'button',
						text: _('print'),
						iconCls: 'icoPrint',
						itemId: 'AppointmentRequestReportGridPrintBtn'
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 10,
					store: me.store,
					displayInfo: true,
					plugins: new Ext.ux.SlidingPager()
				}
			}
		];

		me.callParent(arguments);

	}
});
