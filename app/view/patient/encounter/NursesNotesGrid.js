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

Ext.define('App.view.patient.encounter.NursesNotesGrid', {
	extend: 'Ext.grid.Panel',
	requires: [

	],
	xtype: 'nursesnotesgrid',
	itemId: 'NursesNotesGrid',
	title: _('nurse_notes'),
	frame: true,
	disableSelection: true,
	store: Ext.create('App.store.patient.NursesNotes'),
	columns: [
		{
			xtype: 'datecolumn',
			text: _('date'),
			width: 150,
			format: g('date_time_display_format'),
			dataIndex: 'create_date'
		},
		{
			text: _('note'),
			flex: 1,
			dataIndex: 'note'
		},
		{
			text: _('nurse'),
			width: 200,
			dataIndex: 'nurse_name'
		}
	],
	tbar: [
		'->',
		{
			text: _('note'),
			itemId: 'NursesNotesGridAddBtn',
			action: 'encounterRecordAdd',
			iconCls: 'icoAdd'
		}
	]
});
