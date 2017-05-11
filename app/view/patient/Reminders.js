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

Ext.define('App.view.patient.Reminders', {
	extend: 'Ext.grid.Panel',
	requires: [
		'Ext.grid.plugin.RowEditing'
	],
	xtype: 'patientreminderspanel',
	title: _('reminders'),
	store: Ext.create('App.store.patient.Reminders'),
	plugins: {
		ptype:'rowediting',
		autoCancel: false,
		errorSummary: false,
		clicksToEdit: 2
	},
	columns: [
		{
			xtype: 'datecolumn',
			text: _('date'),
			dataIndex: 'reminder_date',
			editor: {
				xtype: 'datefield'
			}
		},
		{
			text: _('type'),
			dataIndex: 'reminder_type',
			editor: {
				xtype: 'combobox',
				queryMode: 'local',
				displayField: 'option',
				valueField: 'value',
				store: Ext.create('Ext.data.Store', {
					fields: ['option', 'value'],
					data : [
						{ option: 'Appointment', value: 'appointment' },
						{ option: 'Clinical', value: 'clinical' }
					]
				})
			}
		},
		{
			text: _('note'),
			flex: 1,
			dataIndex: 'reminder_note',
			editor: {
				xtype: 'textfield'
			}
		}
	],
	tbar: [
		'->',
		{
			text: _('add_reminder'),
			iconCls: 'icoAdd',
			itemId: 'PatientRemindersAddBtn'
		}
	]
});