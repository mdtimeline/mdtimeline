/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

Ext.define('App.ux.combo.Race', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.racecombo',
	store: Ext.create('Ext.data.Store', {
		fields: [
			{name: 'code', type: 'string'},
			{name: 'code_type', type: 'string'},
			{name: 'code_description', type: 'string'},
			{
				name: 'indent_index',
				type: 'int',
				convert: function (v) {
					var str = '';
					while(v > 0){ str += '----'; v--; }
					return str.trim();
				}
			},
		],
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url: 'resources/code_sets/HL7v3-Race.json',
			reader: {
				type: 'json'
			}
		}
	}),
	tpl: Ext.create('Ext.XTemplate',
		'<tpl for=".">',
		'<div class="x-boundlist-item">{indent_index} <b>{code_description}</b> [{code}]</div>',
		'</tpl>'
	),
	typeAhead: true,
	typeAheadDelay: 50,
	queryMode: 'local',
	displayField: 'code_description',
	valueField: 'code',
	emptyText: _('race'),
	initComponent: function () {
		var me = this;

		me.callParent(arguments);
	}
});
