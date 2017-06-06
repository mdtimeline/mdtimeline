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

Ext.define('App.view.patient.windows.PatientMerge', {
	extend: 'Ext.window.Window',
	xtype: 'patientmergewindow',
	requires: [],
	title: _('patient_merge'),
	width: 800,
	modal: true,
	itemId: 'PatientMergeWindow',
	items: [
		{
			xtype: 'container',
			html: Ext.String.format(
				'<p>{0}</p>' +
				'<ol>' +
				'<li>{1}</li>' +
				'<li>{2}</li>' +
				'<li>{3}</li>' +
				'<ul>' +
				'<li>{4}</li>' +
				'<li>{5}</li>' +
				'</ul>' +
				'<li>{6}</li>' +
				'</ol>',
				_('merge_imstructions'),
				_('merge_imstructions_1'),
				_('merge_imstructions_2'),
				_('merge_imstructions_3'),
				_('merge_imstructions_4'),
				_('merge_imstructions_5'),
				_('merge_imstructions_6')
			),
			anchor: '100%',
			padding: 5,
		},
		{
			xtype: 'container',
			layout: {
				type: 'hbox',
				align: 'stretch'
			},
			items: [
				{
					xtype: 'form',
					title: _('record'),
					flex: 1,
					margin: 5,
					bodyPadding: 10,
					primaryRecord: false, // mdtimeline property required by controller
					itemId: 'PatientMergeRecordOneForm',
					anchor: '100%',
					tbar: [
						{
							xtype: 'button',
							text: _('primary'),
							enableToggle: true,
							toggleGroup: 'patientmergegroup'
						}
					],
					defaults: {
						xtype: 'displayfield'
					},
					items: [
						{
							fieldLabel: 'PID',
							name: 'pid'
						},
						{
							fieldLabel: _('record_number'),
							name: 'pubpid'
						},
						{
							fieldLabel: _('name'),
							name: 'fullname'
						},
						{
							fieldLabel: _('sex'),
							name: 'sex'
						},
						{
							fieldLabel: _('dob'),
							name: 'DOBFormatted'
						}
					]
				},
				{
					xtype: 'form',
					title: _('record'),
					flex: 1,
					margin: 5,
					bodyPadding: 10,
					primaryRecord: false, // mdtimeline property required by controller
					itemId: 'PatientMergeRecordTwoForm',
					anchor: '100%',
					tbar: [
						{
							xtype: 'button',
							text: _('primary'),
							enableToggle: true,
							toggleGroup: 'patientmergegroup'
						},
						{
							xtype: 'patienlivetsearch',
							itemId: 'PatientMergeRecordSerchField',
							flex: 1
						}
					],
					defaults: {
						xtype: 'displayfield'
					},
					items: [
						{
							fieldLabel: 'PID',
							name: 'pid'
						},
						{
							fieldLabel: _('record_number'),
							name: 'pubpid'
						},
						{
							fieldLabel: _('name'),
							name: 'fullname'
						},
						{
							fieldLabel: _('sex'),
							name: 'sex'
						},
						{
							fieldLabel: _('dob'),
							name: 'DOB'
						}
					]
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			iconCls: 'icoCancel',
			itemId: 'PatientMergeCancelBtn'
		},
		{
			text: _('merge'),
			iconCls: 'icoAdd',
			itemId: 'PatientMergeMergeBtn'
		}
	]
});
