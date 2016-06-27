/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (Cformattedertun, LLC.
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

Ext.define('App.ux.combo.EducationResources', {
	extend: 'Ext.form.ComboBox',
	xtype: 'educationresourcescombo',
	editable: false,
	queryMode: 'local',
	displayField: 'title',
	valueField: 'id',
	emptyText: _('select'),
	loadStore: true,
	triggerAction: 'all',
	requires: [
		'App.model.administration.EducationResource'
	],
	codeType: undefined,
	tpl: Ext.create('Ext.XTemplate',
		'<tpl for=".">',
		'<div class="x-boundlist-item">{title} - {publication_date_formatted}</div>',
		'</tpl>'
	),
	initComponent: function(){
		var me = this,
			filters;

		if(me.codeType){
			filters = [
				{
					property: 'code_type',
					value: me.codeType
				}
			];
		}
		
		me.store = Ext.create('App.store.administration.EducationResources', {
			autoLoad: me.loadStore,
			filters: filters
		});

		me.callParent(arguments);
	}
});