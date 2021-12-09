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

Ext.define('App.view.patient.windows.PatientNotesWindow', {
	extend: 'Ext.window.Window',
	requires: [
		'App.view.patient.PatientNotesGrid'
	],
	title: _('notes'),
	itemId: 'PatientNotesWindow',
	layout: 'fit',
	height: 700,
	width: 1200,
	closeAction: 'hide',
	modal: true,
	closable: true,
	bodyPadding: 5,
	items: [
		{
			xtype: 'patientnotesgrid',
			itemId: 'PatientNotesGrid',
			frame: true
			// sortableColumns: false,
			// headerPosition: 'center',
			// selType: 'rowmodel',

			// xtype: 'form',
			// itemId: 'PatientNotesWindowForm',
			// layout: {
			// 	type: 'vbox',
			// 	align: 'stretch'
			// },
			// items: [
			// 	{
			// 		xtype: 'fieldset',
			// 		layout: 'fit',
			// 		title: _('general'),
			// 		// height: 145,
			// 		margin: 5,
			// 		defaults: {
			// 			margin: '0 0 5 0'
			// 		},
			// 		items: [
			// 			{
			// 				xtype: 'patientNotesGrid',
			// 				itemId: 'PatientNotesGrid',
			// 				sortableColumns: false,
			// 				flex: 1,
			// 				headerPosition: 'center',
			// 				selType: 'rowmodel',
			// 				fieldDefaults: {
			// 					labelAlign: 'top'
			// 				}
			// 			}
			// 		]
			// 	}
			// ]
		}
	],
	buttons: [
		{
			text: _('close'),
			itemId: 'PatientNotesWindowCloseBtn'
		}
	]
});