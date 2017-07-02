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
Ext.define('App.ux.combo.AllergiesMetals', {
	extend: 'Ext.form.ComboBox',
	xtype: 'allergiesmetalcombo',
	editable: false,
	queryMode: 'local',
	displayField: 'FullySpecifiedName',
	valueField: 'ConceptId',
	emptyText: _('select'),
	initComponent: function () {
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			autoLoad: true,
			fields: [
				{ name: 'ConceptId', type: 'string' },
				{ name: 'FullySpecifiedName', type: 'string' },
				{ name: 'CodeType', type: 'string' }
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'SnomedCodes.getMetalAllergiesCodes'
				}
			}
		});

		me.callParent(arguments);
	}
});