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

Ext.define('App.controller.patient.PatientSearch', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'PatientSearchFrom',
			selector: '#PatientSearchFrom'
		},
		{
			ref: 'PatientSearchGrid',
			selector: '#PatientSearchGrid'
		},
		{
			ref: 'PatientSearchFromSearchBtn',
			selector: '#PatientSearchFromSearchBtn'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'#PatientSearchFromSearchBtn': {
				click: me.onPatientSearchFromSearchBtnClick
			}
		});
	},

	onPatientSearchFromSearchBtnClick:function() {
		var me = this,
			store = me.getPatientSearchGrid().getStore(),
			form = me.getPatientSearchFrom().getForm(),
			values = form.getValues(),
			searchValues = {};


		if(!form.isValid()) return;

		Ext.Object.each(values, function (key, value) {
			if (value == '') return;
			if (key == 'lab_results' && Ext.isObject(value)) {
				value = [value];
			}
			searchValues[key] = value;
		});

		store.getProxy().extraParams = searchValues;
		store.load();
	}

});
