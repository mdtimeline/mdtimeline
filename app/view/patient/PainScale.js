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

Ext.define('App.view.patient.PainScale', {
	extend: 'Ext.grid.Panel',
	requires: [

	],
	xtype: 'painscalepanel',
	itemId: 'PainScaleGrid',
	title: _('pain_scale'),
	store: Ext.create('App.store.patient.PainScales',{
		remoteFilter: true,
		remoteSort: true
	}),
	plugins: [{
		ptype: 'rowediting'
	}],
	columns: [
		{
			xtype:'griddeletecolumn',
			acl: a('delete_patient_pain_scales'),
			width: 25,

		},
		{
			text: _('anatomical_region'),
			dataIndex: 'anatomical_region_code_text',
			flex: 1,
			editor: {
				xtype: 'snomedlivebodysitesearch',
				name: 'anatomical_region_code_text',
				displayField: 'Term',
				valueField: 'Term',
				allowBlank: false
			}
		},
		{
			text: _('pain_scale'),
			dataIndex: 'pain_scale',
			flex: 2,
			renderer: function(val, meta, rec){
				var percent = val + '0';

				say(val);
				say(percent);

				meta.style = Ext.String.format('background: linear-gradient(90deg, rgb(37 157 249) 0%, rgb(37 157 249) {0}%, rgb(255 255 255) {0}%)',percent);

				return val;
			},
			editor: {
				xtype: 'slider',
				value: 0,
				increment: 1,
				minValue: 0,
				maxValue: 10
			}
		},
		{
			xtype: 'datecolumn',
			text: _('service_date'),
			dataIndex: 'service_date',
			format: g('date_display_format')
		}
	],
	tbar: [
		'->',
		{
			xtype: 'button',
			acl: a('add_patient_pain_scales'),
			text: _('add'),
			iconCls: 'icoAdd',
			itemId: 'PainScaleGridAddBtn'
		}
	],
	bbar: [
		{
			text: _('review'),
			itemId: 'PainScaleReviewBtn'
		}
	]
});