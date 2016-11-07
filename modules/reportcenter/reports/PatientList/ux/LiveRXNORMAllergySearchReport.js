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

Ext.define('Modules.reportcenter.reports.PatientList.ux.LiveRXNORMAllergySearchReport', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.rxnormallergylivetsearchreport',
	hideLabel: true,
	initComponent: function(){
		var me = this;

		Ext.define('liveRXNORMAllergySearchModel', {
			extend: 'Ext.data.Model',
			fields: [
				{
				    name: 'RXCUI',
                    type: 'string'
				},
				{
				    name: 'CODE',
                    type: 'string'
				},
				{
				    name: 'STR',
                    type: 'string'
				},
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'Rxnorm.getRXNORMAllergyLiveSearch'
				},
				reader: {
					totalProperty: 'totals',
					root: 'rows'
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model: 'liveRXNORMAllergySearchModel',
			pageSize: 25,
			autoLoad: false
		});

		Ext.apply(this, {
			store: me.store,
			emptyText: _('allergy_search')+'...',
			typeAhead: false,
			hideTrigger: true,
			minChars: 3,
            maxLength: 255,
			listConfig: {
				loadingText: _('searching')+'...',
				getInnerTpl: function(){
					return '<div class="search-item"><h3>{STR}<span style="font-weight: normal"> ({RXCUI}) </span></h3></div>';
				}
			},
			pageSize: 25
		});

		me.callParent();
	}
});