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

Ext.define('App.controller.administration.ICD10', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'Icd10AdminGrid',
			selector: 'icd10admingrid'
		},
		{
			ref: 'RevisionYearComboBox',
			selector: '#RevisionYearComboBox'
		},
		{
			ref: 'ICD10UpdateCodesBtn',
			selector: '#ICD10UpdateCodesBtn'
		},
		{
			ref: 'ICD10RevisionField',
			selector: '#ICD10RevisionField'
		},
		{
			ref: 'ICD10CodeField',
			selector: '#ICD10CodeField'
		}
	],

	init: function(){
		var me = this;

		me.doLoadFiltersGridBuff = Ext.Function.createBuffered(function(){
			me.loadFiltersGrid();
		}, 300, me);

		me.control({
			'icd10admingrid': {
				activate: me.onIcd10AdminGridActive
			},
			'#ICD10UpdateCodesBtn': {
				click: me.onIcd10UpdateCodesButtonClick
			},
			'#ICD10RevisionField, #ICD10CodeField': {
				change: me.onICD10PanelFieldChange
			},
		});
	},

	onIcd10AdminGridActive: function(grid){
		grid.getStore().load();
	},

	onIcd10UpdateCodesButtonClick: function (btn) {
		var me = this,
		revision_year = me.getRevisionYearComboBox().getValue(),
		icd10_grid = me.getIcd10AdminGrid(),
		icd10_grid_store = icd10_grid.getStore();


		icd10_grid.el.mask();
		ICD10DataUpdate.updateCodes(revision_year, function(response) {
			if (response.success) {
				Ext.Msg.alert('Success', response.message);
			} else {
				Ext.Msg.alert('Failed', response.message);
			}

			icd10_grid_store.reload();
			icd10_grid.el.unmask();
		});
	},

	onICD10PanelFieldChange: function (field) {

		if (!field.isValid()) return;

		var me = this;

		me.doLoadFiltersGridBuff();

	},

	loadFiltersGrid: function () {
		var me = this,
			icd10_grid = me.getIcd10AdminGrid(),
			icd10_grid_store = icd10_grid.getStore(),
			revision = me.getICD10RevisionField().getValue(),
			code = me.getICD10CodeField().getValue();

		icd10_grid_store.clearFilter(true);

		if (revision) {
			icd10_grid_store.filter([
				{
					property: 'revision',
					value: revision
				}
			]);
		}

		if (code) {
			icd10_grid_store.filter([
				{
					property: 'dx_code',
					value: code
				}
			]);
		}

		icd10_grid.reconfigure(icd10_grid_store);
		icd10_grid_store.load();
	}
});