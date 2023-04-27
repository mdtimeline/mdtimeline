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
Ext.define('App.ux.combo.Insurances', {
	extend: 'App.ux.combo.ComboResettable',
	alias: 'widget.insurancescombo',
	displayField: 'combo_text',
	valueField: 'id',
	emptyText: _('select'),
	editable: false,

	store: Ext.create('App.store.administration.InsuranceCompanies', {
		autoLoad: true,
		pageSize: 500
	}),

	initComponent: function (){
		this.callParent();
		this.on('beforeselect', this.validateActiveInsurance, this);
	},

	validateActiveInsurance: function (cmb, selection){
		if(selection.get('active') === false && !a('allow_select_inactive_insurance')){
			app.msg(_('oops'), 'unable_select_inactive_insurance', true);
			return false;
		}
	}

}); 