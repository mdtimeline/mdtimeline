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

Ext.define('App.view.patient.MiniMentalStateExam', {
	extend: 'Ext.grid.Panel',
	requires: [

	],
	itemId: 'MiniMentalStateExamGridPanel',
	xtype: 'minimentalstateexamgridpanel',
	title: _('mini_mental'),
	tbar: [
		{
			text: _('mini_mental'),
			iconCls: 'icoAdd',
			action: 'encounterRecordAdd',
			itemId: 'MiniMentalStateExamAddBtn'
		}
	],
	initComponent: function () {

		var me = this;

		me.store = Ext.create('App.store.patient.MiniMentalStateExams', {
			remoteFilter: true
		});

		me.columns = [
			{
				xtype: 'datecolumn',
				text: _('observation_date'),
				dataIndex: 'create_date',
				format: 'F j, Y',
				width: 110
			},
			{
				text: _('total_score'),
				dataIndex: 'total_score',
				width: 110,
				renderer: function (v) {

					return v;
				}
			},
			{
				text: _('questionary'),
				dataIndex: 'id',
				flex: 1,
				renderer: function (v, meta, record) {
					var str = '<div style="display: table">';

					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('orientation_time'), record.get('orientation_time_score'), _('notes'), record.get('orientation_time_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('orientation_place'), record.get('orientation_place_score'), _('notes'), record.get('orientation_place_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('registration'), record.get('registration_score'), _('notes'), record.get('registration_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('attention_calculation'), record.get('attention_calculation_score'), _('notes'), record.get('attention_calculation_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('recall'), record.get('recall_score'), _('notes'), record.get('recall_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('language'), record.get('language_score'), _('notes'), record.get('language_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('repetition'), record.get('repetition_score'), _('notes'), record.get('repetition_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"> - {2}: {3}</div></div>',
						_('complex_commands'), record.get('complex_commands_score'), _('notes'), record.get('complex_commands_notes')
					);
					str += Ext.String.format(
						'<div style="display: table-row"><div style="display: table-cell; padding-bottom: 3px;"><b>{0}: </b></div><div style="display: table-cell; padding-left: 5px;">{1}</div> <div style="display: table-cell"></div></div>',
						_('assess_lvl'), record.get('assess_lvl')
					);

					str += '</div>';

					return str;
				}
			}
		];

		me.callParent();
	}

});
