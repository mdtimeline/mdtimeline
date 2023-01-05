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

Ext.define('App.view.administration.practice.Facilities', {
	extend: 'Ext.grid.Panel',
	xtype: 'facilitiespanel',
	title: _('facilities'),

	requires: [
		'App.ux.combo.ServiceLocation',
		'App.ux.form.fields.InputTextMask'
	],


	initComponent: function(){
		var me = this;

		Ext.apply(me, {
			store: me.store = Ext.create('App.store.administration.Facility'),
			columns: [
				{
					text: _('id'),
					sortable: true,
					dataIndex: 'id'
				},
				{
					text: _('code'),
					sortable: true,
					dataIndex: 'code'
				},
				{
					text: _('name'),
					flex: 1,
					sortable: true,
					dataIndex: 'name'
				},
				{
					text: _('region'),
					width: 100,
					sortable: true,
					dataIndex: 'region'
				},
				{
					text: _('phone'),
					width: 100,
					sortable: true,
					dataIndex: 'phone'
				},
				{
					text: _('fax'),
					width: 100,
					sortable: true,
					dataIndex: 'fax'
				},
				{
					text: _('city'),
					width: 100,
					sortable: true,
					dataIndex: 'city'
				},
				{
					text: _('pos_code'),
					width: 100,
					sortable: true,
					dataIndex: 'pos_code'
				},
				{
					text: _('ein'),
					width: 100,
					sortable: true,
					dataIndex: 'ein'
				},
				{
					text: _('clia'),
					width: 100,
					sortable: true,
					dataIndex: 'clia'
				},
				{
					text: _('fda'),
					width: 100,
					sortable: true,
					dataIndex: 'fda'
				},
				{
					text: _('npi'),
					width: 100,
					sortable: true,
					dataIndex: 'npi'
				},
				{
					text: _('tin'),
					width: 100,
					sortable: true,
					dataIndex: 'ess'
				},

                {
                    text: _('active'),
                    sortable: true,
                    dataIndex: 'active',
                    renderer: function(v){
                        return app.boolRenderer(v);
                    },
                    editor: {
                        xtype: 'checkboxfield'
                    }
                }
			],
			plugins: Ext.create('App.ux.grid.RowFormEditing', {
				autoCancel: false,
				errorSummary: false,
				clicksToEdit: 1,
				items: [
					{
						xtype: 'container',
						layout: 'column',
						defaults: {
							xtype: 'container',
							padding: 5,
                            labelWidth: 100,
							layout: 'anchor',
							defaultType: 'textfield'
						},
						items: [
							{
								defaults: {
									anchor: '100%'
								},
                                columnWidth: 0.4,
								items: [
									{
                                        xtype: 'textfield',
										fieldLabel: _('name'),
										name: 'name',
										allowBlank: false
									},
									{
                                        xtype: 'textfield',
										fieldLabel: _('phone'),
										name: 'phone',
										plugins: [Ext.create('App.ux.form.fields.InputTextMask', '999-999-9999')]
									},
									{
                                        xtype: 'textfield',
										fieldLabel: _('fax'),
										name: 'fax'
									},
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('address'),
                                        name: 'address'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('address'),
                                        name: 'address_cont'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('city'),
                                        name: 'city'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('state'),
                                        name: 'state'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('postal_code'),
                                        name: 'postal_code'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('country_code'),
                                        name: 'country_code'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'facility_entity',
                                        width: 100,
                                        fieldLabel: _('entity')
                                    },
                                    {
                                        xtype: 'gaiaehr.combo',
                                        fieldLabel: _('region'),
                                        editable: false,
                                        listKey: 'regions',
                                        name: 'region'
                                    }

								]
							},
                            {
                                defaults: {
                                    anchor: '100%'
                                },

                                columnWidth: 0.4,
                                items: [
                                    {
                                        xtype: 'fieldcontainer',
                                        layout: 'hbox',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: _('contact') + ' ' + _('name'),
                                                flex: 2,
                                                name: 'lname',
                                                margin: '0 10 0 0'
                                            },
                                            {
                                                xtype: 'textfield',
                                                name: 'fname',
                                                labelWidth: 30,
                                                fieldLabel: _('first'),
                                                flex: 1,
                                                margin: '0 10 0 0'
                                            },
                                            {
                                                xtype: 'textfield',
                                                name: 'mname',
                                                flex: 1,
                                                labelWidth: 40,
                                                fieldLabel: _('middle'),
                                                width: 20
                                            }
                                        ]
                                    },

                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('ess'),
                                        name: 'ess'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('ein'),
                                        name: 'ein'
                                    },
                                    {
                                        fieldLabel: 'PO' + ' ' + _('address'),
                                        name: 'postal_address'
                                    },
                                    {
                                        fieldLabel: 'PO' + ' ' +  _('address'),
                                        name: 'postal_address_cont'
                                    },
                                    {
                                        fieldLabel: 'PO' + ' ' +  _('city'),
                                        name: 'postal_city'
                                    },
                                    {
                                        fieldLabel: 'PO' + ' ' +  _('state'),
                                        name: 'postal_state'
                                    },
                                    {
                                        fieldLabel: 'PO' + ' ' + _('postal_code'),
                                        name: 'postal_zip_code'
                                    },
                                    {
                                        fieldLabel: 'PO' + ' ' + _('country_code'),
                                        name: 'postal_country_code'
                                    }
                                ]

                            },
							{
                                defaults: {
                                    anchor: '100%'
                                },
                                columnWidth: 0.2,
								items: [
									{
										fieldLabel: _('billing_attn'),
										name: 'attn',
										anchor: '100%'
									},
									{
										xtype: 'mitos.poscodescombo',
										fieldLabel: _('pos_code'),
										name: 'pos_code',
										anchor: '100%'
									},
									{
										xtype: 'servicelocationcombo',
										fieldLabel: _('service_location'),
										name: 'service_loc_code',
										anchor: '100%'
									},
									{
										fieldLabel: _('clia_number'),
										name: 'clia',
										anchor: '100%'
									},
									{
										fieldLabel: _('npi'),
										name: 'npi',
										anchor: '100%'
									},
									{
										fieldLabel: _('fda_number'),
										name: 'fda',
										anchor: '100%'
									},
									{
										xtype: 'checkbox',
										fieldLabel: _('active'),
										name: 'active'
									},
									{
										xtype: 'checkbox',
										fieldLabel: _('billing_location'),
										name: 'billing_location'
									},
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('external_id'),
                                        name: 'external_id'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('global_id'),
                                        name: 'global_id'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: _('code'),
                                        name: 'code'
                                    }

                                    // {
                                    //     xtype: 'checkbox',
                                    //     fieldLabel: _('accepts_assignment'),
                                    //     labelWidth: 120,
                                    //     width: 140,
                                    //     name: 'accepts_assignment',
                                    //     margin: '10 10 0 0'
                                    // },



                                    // {
                                    //     xtype: 'fieldcontainer',
                                    //     layout: 'hbox',
                                    //     items: [
                                    //     ]
                                    // }


								]
							}
						]
					}
				]
			}),
			tbar: Ext.create('Ext.PagingToolbar', {
				pageSize: 30,
				store: me.store,
				displayInfo: true,
				plugins: Ext.create('Ext.ux.SlidingPager', {
				}),
				items: ['-', {
					text: _('add_new_facility'),
					iconCls: 'save',
					scope: me,
					handler: me.addFacility
				}, '-', {
					text: _('show_active_facilities'),
					action: 'active',
					scope: me,
					handler: me.filterFacilitiesby
				}, '-', {
					text: _('show_inactive_facilities'),
					action: 'inactive',
					scope: me,
					handler: me.filterFacilitiesby
				}]

			})
		});

		me.callParent(arguments);
	},

	filterFacilitiesby: function(btn){

//		this.setTitle(_('facilities') + ' (' + Ext.String.capitalize(btn.action) + ')');

		this.store.load({
			filters: [
				{
					property: 'active',
					value: btn.action == 'active' ? 1 : 0
				}
			]
		});
	},

	addFacility: function(){
		var me = this,
			grid = me,
			store = grid.store;

		grid.editingPlugin.cancelEdit();
		store.insert(0, {
			active: 1,
			service_location: 1,
			billing_location: 0,
			accepts_assignment: 0
		});
		grid.editingPlugin.startEdit(0, 0);
	}
});
