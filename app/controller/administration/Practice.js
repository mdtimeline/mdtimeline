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

Ext.define('App.controller.administration.Practice', {
    extend: 'Ext.app.Controller',

	refs: [
		{
			ref:'PracticePanel',
			selector:'practicepanel'
		},
		{
			ref:'PharmaciesPanel',
			selector:'pharmaciespanel'
		},
		{
			ref:'LaboratoriesPanel',
			selector:'laboratoriespanel'
		},
		{
			ref:'InsuranceCompaniesPanel',
			selector:'insurancecompaniespanel'
		},
		{
			ref:'ReferringProvidersPanel',
			selector:'referringproviderspanel'
		},
		{
			ref:'FacilitiesPanel',
			selector:'facilitiespanel'
		},
		{
			ref:'FacilityDepartmentsGrid',
			selector:'#FacilityDepartmentsGrid'
		},
		{
			ref:'FacilitySpecialtiesGrid',
			selector:'#FacilitySpecialtiesGrid'
		},
		{
			ref:'DecisionAidsPanel',
			selector:'decisionaidspanel'
		},
		{
			ref:'PharmacyGridPrintBtn',
			selector:'#PharmacyGridPrintBtn'
		},
		{
			ref:'LaboratoryGridPrintBtn',
			selector:'#LaboratoryGridPrintBtn'
		},
		{
			ref:'InsuranceCompaniesGridPrintBtn',
			selector:'#InsuranceCompaniesGridPrintBtn'
		},
		{
			ref:'ReferringProviderGridPrintBtn',
			selector:'#ReferringProviderGridPrintBtn'
		},
		{
			ref:'FacilitiesGridPrintBtn',
			selector:'#FacilitiesGridPrintBtn'
		},
		{
			ref:'DepartmentsGridPrintBtn',
			selector:'#DepartmentsGridPrintBtn'
		},
		{
			ref:'SpecialitiesAddBtn',
			selector:'#SpecialitiesAddBtn'
		},
		{
			ref:'DecisionAidsGridPrintBtn',
			selector:'#DecisionAidsGridPrintBtn'
		}

	],

	init: function() {
		var me = this;

		me.control({
			'practicepanel grid':{
				activate: me.onPracticeGridPanelsActive,
			},
			'practicepanel button[toggleGroup=insurance_number_group]':{
				toggle: me.onInsuranceNumberGroupToggle
			},
			'practicepanel toolbar > #addBtn':{
				click: me.onAddBtnClick
			},
			'#PharmacyGridPrintBtn':{
				click: me.onPharmaciesGridPrintBtnClick
			},
			'#LaboratoryGridPrintBtn':{
				click: me.onLaboratoryGridPrintBtnClick
			},
			'#InsuranceCompaniesGridPrintBtn':{
				click: me.onInsuranceCompaniesGridPrintBtnClick
			},
			'#ReferringProviderGridPrintBtn':{
				click: me.onReferringProviderGridPrintBtnClick
			},
			'#FacilitiesGridPrintBtn':{
				click: me.onFacilitiesGridPrintBtnClick
			},
			'#DepartmentsGridPrintBtn':{
				click: me.onDepartmentsGridPrintBtnClick
			},
			'#SpecialtiesGridPrintBtn':{
				click: me.onSpecialtiesGridPrintBtnClick
			},
			'#DecisionAidsGridPrintBtn':{
				click: me.onDecisionAidsGridPrintBtnClick
			}
		});
	},

	onPharmaciesGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getPharmaciesPanel();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onLaboratoryGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getLaboratoriesPanel();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onInsuranceCompaniesGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getInsuranceCompaniesPanel();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onReferringProviderGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getReferringProvidersPanel();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onFacilitiesGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getFacilitiesPanel();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onDepartmentsGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getFacilityDepartmentsGrid();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onSpecialtiesGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getFacilitySpecialtiesGrid();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onDecisionAidsGridPrintBtnClick: function(btn)
	{
		var me = this,
			grid = me.getDecisionAidsPanel();

		// App.ux.grid.Printer.mainTitle = listParams; //optional
		// App.ux.grid.Printer.filtersHtml = ''; //optional
		App.ux.grid.Printer.print(grid);
	},

	onPracticeGridPanelsActive: function(grid){
		grid.getStore().load();
	},

	onAddBtnClick: function(btn){
		var	grid = btn.up('grid'),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			active: 1
		});
		grid.editingPlugin.startEdit(0, 0);
	},

	onInsuranceNumberGroupToggle:function(btn, pressed){
		var grid = btn.up('grid');

		if(pressed) {
			grid.view.features[0].enable();
			grid.getStore().group(btn.action);
		}else{
			grid.view.features[0].disable();
		}
	}

});
