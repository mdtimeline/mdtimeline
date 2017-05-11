/**
 * mdTImeLine EHR (Electronic Health Records)
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

Ext.define('App.ux.LiveEducationResourceSearch', {
	extend: 'Ext.form.ComboBox',
	xtype: 'educationresourcelivetsearch',
	hideLabel: true,
	displayField: 'surgery',
	valueField: 'id',
	emptyText: _('search') + '...',
	typeAhead: false,
	hideTrigger: true,
	minChars: 1,
	queryParam: 'term',
	pageSize: 10,
	language: 'patient',
	initComponent: function () {
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			fields: [
				{
					name: 'title'
				},
				{
					name: 'url'
				},
				{
					name: 'rank'
				},
				{
					name: 'snippet'
				},
				{
					name: 'FullSummary'
				},
				{
					name: 'groupName'
				},
				{
					name: 'mesh'
				},
				{
					name: 'organizationName'
				}
			],
			model: 'liveLiveEducationResourceSearchModel',
			pageSize: me.pageSize,
			autoLoad: false,
			proxy: {
				type: 'direct',
				api: {
					read: 'EducationResources.search'
				},
				startParam: 'retstart',
				limitParam: 'retmax',
				reader: {
					root: 'data'
				}
			}
		});

		me.listConfig = {
			loadingText: _('searching') + '...',
			getInnerTpl: function () {
				return '<div class="search-item"><h3>{title}<span style="font-weight: normal"> ({snippet}) </span></h3></div>';
			}
		};

		me.listeners = {
			beforequery: function () {
				var db = 'healthTopics';

				if(me.language == 'patient'){
					if (app.patient.record && app.patient.record.get('language') == 'spa') {
						db = 'healthTopicsSpanish';
					}
				}else {
					if(me.language == 'spa' || me.language == 'es' || me.language == 'esp'){
						db = 'healthTopicsSpanish';
					}
				}

				me.store.getProxy().extraParams = { db: db };
			}
		};

		me.callParent();
	}
});
