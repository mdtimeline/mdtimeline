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

Ext.define('App.view.administration.MeasureCalculation', {
	extend: 'App.ux.RenderPanel',
	requires: [
		'App.ux.combo.Insurances'
	],
	pageTitle: 'Measure Calculations',
	itemId: 'MeasureCalculation',
	initComponent: function(){
		var me = this;

		me.pageBody = [
			{
				xtype: 'tabpanel',
				items: [
					{
						xtype: 'grid',
						title: 'Meaningful Use',
						itemId: 'MeaningfulUseGrid',
						store: Ext.create('Ext.data.ArrayStore',{
							fields: [
								{name: 'group', type: 'string'},
								{name: 'provider', type: 'string'},
								{name: 'title', type: 'string'},
								{name: 'denominator', type: 'int'},
								{name: 'numerator', type: 'int'},
								{name: 'goal', type: 'string'},
								{name: 'description', type: 'string'},
								{name: 'description_denominator', type: 'string'},
								{name: 'description_numerator', type: 'string'},
								{name: 'denominator_pids'},
								{name: 'numerator_pids'},
								{name: 'numerator_types'},
							],
							groupers: [
								{
									property: 'group',
									sorterFn: function(o1, o2){
										var getRank = function(o){
												return parseInt(o.get('group').replace(/\..*/,''));
											},
											rank1 = getRank(o1),
											rank2 = getRank(o2);

										if (rank1 === rank2) {
											return 0;
										}
										return rank1 < rank2 ? -1 : 1;
									}
								}
							]
						}),
						features: [
							{
								ftype: 'grouping',
								collapsible: false,
								groupHeaderTpl: '{name}',
							}
						],
						tbar: [
							{
								xtype: 'datefield',
								fieldLabel: _('from'),
								labelWidth: 35,
								width: 135,
								allowBlank: false,
								value: Ext.Date.subtract(new Date(), Ext.Date.YEAR, 1),
								itemId: 'MeaningfulUseFromField'
							},
							{
								xtype: 'datefield',
								fieldLabel: _('to'),
								labelWidth: 25,
								width: 125,
								allowBlank: false,
								value: new Date(),
								itemId: 'MeaningfulUseToField'
							},
							{
								xtype: 'activeproviderscombo',
								fieldLabel: _('provider'),
								labelWidth: 50,
								width: 300,
								allowBlank: false,
								//multiSelect: true,
								itemId: 'MeaningfulUseProviderField'
							},
							'-',
							{
								xtype: 'button',
								text: _('refresh'),
								itemId: 'MeaningfulUseRefreshBtn'
							},
							'->',
							{
								xtype: 'button',
								text: _('print'),
								itemId: 'MeaningfulUseGridPrintBtn'
							}
						],
						columns: [
							{
								text: _('title'),
								dataIndex: 'title',
								flex: 1
							},
							{
								text: _('provider'),
								dataIndex: 'provider',
								flex: 1
							},
							{
								text: _('denominator'),
								dataIndex: 'denominator',
								width: 150
							},
							{
								text: _('numerator'),
								dataIndex: 'numerator',
								width: 150
							},
							{
								text: _('percent'),
								dataIndex: 'goal',
								width: 150,
								renderer: function (v,m,r) {
									var pct = ((r.get('numerator')/r.get('denominator')) * 100);
									return Number.isNaN(pct) ? 'N/A' : pct.toFixed(1) + '%';
								}
							},
							{
								text: _('goal'),
								dataIndex: 'goal',
								width: 150
							}
						]
					},
					{
						xtype: 'grid',
						title: 'MIPS',
						itemId: 'MIPSGrid',
						store: Ext.create('Ext.data.ArrayStore',{
							fields: [
								{name: 'group', type: 'string'},
								{name: 'provider', type: 'string'},
								{name: 'title', type: 'string'},
								{name: 'denominator', type: 'int'},
								{name: 'numerator', type: 'int'},
								{name: 'goal', type: 'string'},
								{name: 'description', type: 'string'},
								{name: 'description_denominator', type: 'string'},
								{name: 'description_numerator', type: 'string'},
								{name: 'denominator_pids'},
								{name: 'numerator_pids'},
								{name: 'numerator_types'},
							],
							groupers: [
								{
									property: 'group',
									sorterFn: function(o1, o2){
										var getRank = function(o){
												return parseInt(o.get('group').replace(/\..*/,''));
											},
											rank1 = getRank(o1),
											rank2 = getRank(o2);

										if (rank1 === rank2) {
											return 0;
										}
										return rank1 < rank2 ? -1 : 1;
									}
								}
							]
						}),
						features: [
							{
								ftype: 'grouping',
								collapsible: false,
								groupHeaderTpl: '{name}',
							}
						],
						tbar: [
							{
								xtype: 'datefield',
								fieldLabel: _('from'),
								labelWidth: 35,
								width: 135,
								allowBlank: false,
								value: Ext.Date.subtract(new Date(), Ext.Date.YEAR, 1),
								itemId: 'MIPSGridFromField'
							},
							{
								xtype: 'datefield',
								fieldLabel: _('to'),
								labelWidth: 25,
								width: 125,
								allowBlank: false,
								value: new Date(),
								itemId: 'MIPSGridToField'
							},
							{
								xtype: 'activeproviderscombo',
								fieldLabel: _('provider'),
								labelWidth: 50,
								width: 300,
								allowBlank: false,
								//multiSelect: true,
								itemId: 'MIPSGridProviderField'
							},
							{
								xtype: 'insurancescombo',
								fieldLabel: _('insurance'),
								labelWidth: 50,
								width: 300,
								//multiSelect: true,
								itemId: 'MIPSGridInsuranceField'
							},
							{
								xtype: 'gaiaehr.combo',
								name: 'sex',
								fieldLabel: _('sex'),
								labelWidth: 25,
								width: 125,
								enableKeyEvents: true,
								allowBlank: false,
								listKey: 'sex',
								loadStore: true,
								editable: false,
								resetable: true,
								itemId: 'MIPSGridSexField'
							},
							'-',
							{
								xtype: 'button',
								text: _('refresh'),
								itemId: 'MIPSGridRefreshBtn'
							},
							'->',
							{
								xtype: 'button',
								text: _('print'),
								itemId: 'MIPSGridPrintBtn'
							}
						],
						columns: [
							{
								text: _('title'),
								dataIndex: 'title',
								flex: 1
							},
							{
								text: _('provider'),
								dataIndex: 'provider',
								flex: 1
							},
							{
								text: _('denominator'),
								dataIndex: 'denominator',
								width: 150
							},
							{
								text: _('numerator'),
								dataIndex: 'numerator',
								width: 150
							},
							{
								text: _('percent'),
								dataIndex: 'goal',
								width: 150,
								renderer: function (v,m,r) {
									var pct = ((r.get('numerator')/r.get('denominator')) * 100);
									return Number.isNaN(pct) ? 'N/A' : pct.toFixed(1) + '%';
								}
							},
							{
								text: _('goal'),
								dataIndex: 'goal',
								width: 150
							}
						]
					}
				]
			}

		];
		me.callParent(arguments);

	}

});
