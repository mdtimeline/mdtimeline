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

Ext.define('App.ux.NPIRegistrySearch', {
	extend: 'Ext.form.field.Trigger',
	xtype: 'npiregistrysearch',
	trigger1Cls: Ext.baseCSSPrefix + 'form-search-trigger',
	initComponent: function() {
		var me = this;
		me.vtype = 'npi';
		me.callParent(arguments);
	},

	onTrigger1Click : function(){
		var me = this,
			value = me.getValue();

		if (value.length > 0) {
			Providers.npiRegistrySearchByNpi(value, function (response) {
				me.fireEvent('searchresponse', me, response);
			});

			me.hasSearch = true;
			me.triggerCell.item(0).setDisplayed(true);
			me.updateLayout();
		}
	}
});