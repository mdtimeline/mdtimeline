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

Ext.define('App.view.patient.windows.FamilyHistory', {
	extend: 'Ext.window.Window',
	xtype: 'familyhistorywindow',
	requires: [
		'App.ux.form.fields.CheckBoxWithFamilyRelation'
	],
	title: _('family_history'),
	width: 1000,
	height: 400,
	modal: true,
	bodyStyle: 'background-color:white',
	bodyPadding: 10,
	autoScroll: true,
	items: [
		{
			xtype: 'form',
			frame: false,
			border: false,
			itemId: 'FamilyHistoryForm'
		}
	],
	buttons: [
		{
			text: _('cancel'),
			iconCls: 'icoCancel',
			itemId: 'FamilyHistoryWindowCancelBtn'
		},
		{
			text: _('save'),
			iconCls: 'icoAdd',
			itemId: 'FamilyHistoryWindowSaveBtn'
		}
	],

	initComponent: function(){
		var me = this;
		me.callParent();
		var form = me.down('form');

		me.getFormItems(form, 12, function (a,b,c) {
			var fields = form.query('checkboxwithfamilyhistory');

			fields.forEach(function (field) {
				field.anchor = '100%';
				field.width = '100%';
				//field.items.items[2].flex = 2;
			});

			me.doLayout();
		});
	}
});
