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
Ext.define('App.ux.LiveCPTRadiologyCodesSearch', {
	extend: 'Ext.form.ComboBox',
	requires: [

	],
	xtype: 'livecptradiologycodessearch',
	hideLabel: true,
	displayField: 'code_text_short',
	valueField: 'code',
	emptyText: _('search') + '...',
	typeAhead: false,
	hideTrigger: false,
	minChars: 1,
	queryDelay: 1000,
	acl: null,
	queryParam: 'radiology_query',
	trigger1Cls: Ext.baseCSSPrefix + 'form-clear-trigger',

	initComponent: function(){
		var me = this;

		me.store = Ext.create('App.store.administration.CPT');

		Ext.apply(me, {
			store: me.store,
			listConfig: {
				loadingText: _('searching') + '...',
				getInnerTpl: function(){
					return '<div class="search-item"><b>{code}:</b>  {code_text_short}</div>'
				}
			},
			pageSize: 10
		});

		me.callParent();
	},

	onTrigger1Click : function(){
		var me = this;
		me.setValue('');
		me.store.removeAll();
		me.store.commitChanges();
		me.updateLayout();
		me.fireEvent('reset', me);

	}
});