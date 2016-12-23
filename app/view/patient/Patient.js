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

Ext.define('App.view.patient.Patient', {
	extend: 'Ext.panel.Panel',
    //extend: 'Ext.form.Panel',

    requires: [
		'App.ux.AddTabButton'
		//'App.view.patient.InsuranceForm'
	],
	layout: {
		type: 'vbox',
		align: 'stretch'
	},
	xtype: 'patientdeomgraphics',
	itemId: 'PatientDemographicsPanel',

	newPatient: true,
	pid: null,

	defaultPatientImage: 'resources/images/patientPhotoPlaceholder.jpg',
	defaultQRCodeImage: 'resources/images/QRCodeImage.png',

	initComponent: function(){
		var me = this,
			configs;

		me.store = Ext.create('App.store.patient.Patient');
		me.patientAlertsStore = Ext.create('App.store.patient.MeaningfulUseAlert');
		me.patientContacsStore = Ext.create('App.store.patient.PatientContacts', {
			autoLoad: false
		});

		me.compactDemographics = eval(g('compact_demographics'));

		configs = {
			items: [
				me.demoForm = Ext.widget('form', {
					action: 'demoFormPanel',
					itemId: 'PatientDemographicForm',
					type: 'vbox',
					border: false,
                    autoScroll: true,
					padding: (me.compactDemographics ? 0 : 10),
					fieldDefaults: {
						labelAlign: 'right',
						msgTarget: 'side'
					},
					items: [
						{
							xtype: (me.compactDemographics ? 'tabpanel' : 'panel'),
							itemId: 'Demographics',
							border: false,
							defaults: {
								autoScroll: true
							},
							items: [
								{
									xtype: 'panel',
									title: _('patient_info'),
                                    layout: {
									    type: 'column'
									},
									enableKeyEvents: true,
									checkboxToggle: false,
									collapsed: false,
									action: 'DemographicWhoFieldSet',
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
                                    items: [
                                        {
                                            xtype: 'fieldcontainer',
                                            layout: 'vbox',
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    title: _('record_number'),
                                                    margin: '5 2 2 0',
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px'
                                                    },
                                                    items:[
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            defaults: {
                                                                labelWidth: 75,
                                                                hideLabel: false
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'pubpid',
                                                                    flex: 1,
                                                                    emptyText: _('medical'), //external_record
                                                                    width: 192,
                                                                    fieldLabel: _('medical'), //external_record
                                                                    margin: '10 15 2 0',
                                                                    enableKeyEvents: true
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'pubaccount',
                                                                    flex: 1,
                                                                    emptyText: _('account'), //external_account
                                                                    width: 192,
                                                                    fieldLabel: _('account'), //external_account
                                                                    margin: '10 15 2 0',
                                                                    enableKeyEvents: true
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'interface_mrn',
                                                                    flex: 1,
                                                                    emptyText: _('interface_mrn'), //external_account
                                                                    width: 251,
                                                                    labelWidth: 100,
                                                                    fieldLabel: _('interface_mrn'), //external_account
                                                                    margin: '10 15 2 0',
                                                                    enableKeyEvents: true
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    margin: '3 2 2 0',
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px'
                                                    },
                                                    items:[
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            defaults: {
                                                                labelWidth: 50,
                                                                hideLabel: true
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'title',
                                                                    emptyText: _('title'),
                                                                    width: 60,
                                                                    fieldLabel: _('title'),
                                                                    labelWidth: 60,
                                                                    list: 22,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '10 2 2 0'
                                                                    // collapsible: false,
                                                                    //checkboxToggle: false
                                                                    // collapsed: false
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'fname',
                                                                    emptyText: _('first_name'),
                                                                    width: 100,
                                                                    fieldLabel: _('first_name'),
                                                                    labelWidth: 100,
                                                                    allowBlank: false,
                                                                    maxLength: 35,
                                                                    margin: '10 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'mname',
                                                                    emptyText: _('middle_name'),
                                                                    width: 20,
                                                                    //fieldLabel: _('middle_name'),
                                                                    //labelWidth: 50,
                                                                    enableKeyEvents: true,
                                                                    maxLength: 35,
                                                                    margin: '10 2 2 0'
                                                                },
                                                                // {
                                                                //     xtype: 'splitter'
                                                                // },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'lname',
                                                                    emptyText: _('last_name'),
                                                                    width: 180,
                                                                    fieldLabel: _('last_name'),
                                                                    labelWidth: 200,
                                                                    allowBlank: false,
                                                                    maxLength: 35,
                                                                    margin: '10 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'sex',
                                                                    emptyText: _('sex'),
                                                                    width: 70,
                                                                    fieldLabel: _('sex'),
                                                                    enableKeyEvents: true,
                                                                    allowBlank: false,
                                                                    list: 95,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '10 2 2 5'
                                                                },
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'marital_status',
                                                                    emptyText: _('marital_status'),
                                                                    width: 80,
                                                                    fieldLabel: _('marital_status'),
                                                                    list: 12,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '10 2 2 5'
                                                                },
                                                                {
                                                                    xtype: 'datefield',
                                                                    name: 'DOB',
                                                                    emptyText: _('dob'),
                                                                    format: 'm/d/Y',
                                                                    width: 150,
                                                                    fieldLabel: _('dob'),
                                                                    labelAlign: 'right',
                                                                    hideLabel: false,
                                                                    enableKeyEvents: true,
                                                                    allowBlank: false,
                                                                    margin: '10 0 0 0'
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    margin: '3 2 2 0',
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px',
                                                        'text-align' : 'center'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            defaults: {
                                                                hideLabel: false,
                                                                textfieldAlign: 'center',
                                                                labelWidth: 50,
                                                                labelAlign: 'top'
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'fieldcontainer',
                                                                    fieldLabel: _('allow'),
                                                                    layout: 'hbox',
                                                                    margin: '2 2 2 0',
                                                                    items: [
                                                                        {
                                                                            xtype: 'checkbox',
                                                                            name: _('sms'),
                                                                            width: 50,
                                                                            margin: '2 2 2 0',
                                                                            boxLabel: _('sms')
                                                                        },
                                                                        {
                                                                            xtype: 'checkbox',
                                                                            name: _('email'),
                                                                            width: 50,
                                                                            margin: '2 0 2 0',
                                                                            boxLabel: _('email')
                                                                        }
                                                                    ]
                                                                },
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'phone_publicity',
                                                                    width: 200,
                                                                    fieldLabel: _('publicity'),
                                                                    list: 132,
                                                                    margin: '2 2 2 0',
                                                                    loadStore: true,
                                                                    editable: false
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'email',
                                                                    emptyText: 'example@email.com',
                                                                    width: 190,
                                                                    fieldLabel: _('email'),
                                                                    margin: '2 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'phone_mobile',
                                                                    emptyText: '000-000-0000',
                                                                    width: 90,
                                                                    fieldLabel: _('mobile'),
                                                                    margin: '2 2 2 0'

                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'phone_home',
                                                                    emptyText: '000-000-0000',
                                                                    width: 90,
                                                                    fieldLabel: _('home'),
                                                                    margin: '2 2 2 0'

                                                                }
                                                            ]
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    margin: '3 2 2 0',
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            defaults: {
                                                                hideLabel: false,
                                                                width: 150,
                                                                labelAlign: 'top'
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'race',
                                                                    emptyText: _('race'),
                                                                    fieldLabel: _('race'),
                                                                    list: 14,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '2 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: _('secondary_race'),
                                                                    emptyText: 'Secondary Race',
                                                                    fieldLabel: _('secondary_race'),
                                                                    list: 14,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '2 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'ethnicity',
                                                                    emptyText: _('ethnicity'),
                                                                    fieldLabel: _('ethnicity'),
                                                                    width: 190,
                                                                    list: 59,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '2 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'language',
                                                                    emptyText: _('language'),
                                                                    fieldLabel: _('language'),
                                                                    width: 90,
                                                                    list: 10,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '2 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'gaiaehr.combo',
                                                                    name: 'religion',
                                                                    emptyText: _('religion'),
                                                                    fieldLabel: _('religion'),
                                                                    width: 90,
                                                                    list: 10,
                                                                    loadStore: true,
                                                                    editable: false,
                                                                    margin: '2 2 2 0'
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldcontainer',
                                                    layout: 'hbox',
                                                    items: [
                                                        {
                                                            xtype: 'fieldset',
                                                            title: _('postal_address'),
                                                            width: 350,
                                                            margin: '3 2 2 0',
                                                            style: {
                                                                'background-color': 'AliceBlue',
                                                                'border-radius': '5px'
                                                            },
                                                            defaults: {
                                                                labelWidth: 50
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('street'),
                                                                    width: 325,
                                                                    name: 'postal_address'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: '(' + _('optional') + ')',
                                                                    width: 325,
                                                                    name: 'postal_address_cont'
                                                                },
                                                                {
                                                                    xtype: 'container',
                                                                    layout: 'hbox',
                                                                    width: 325,
                                                                    margin: '0 0 5 0',
                                                                    defaults: {
                                                                        labelWidth: 50
                                                                    },
                                                                    items: [
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('city'),
                                                                            margin: '0 2 2 0',
                                                                            width: 110,
                                                                            name: 'postal_city'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('state'),
                                                                            width: 30,
                                                                            margin: '0 2 2 0',
                                                                            name: 'postal_state'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('zip'),
                                                                            width: 80,
                                                                            margin: '0 2 2 0',
                                                                            name: 'postal_zip'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('country'),
                                                                            width: 90,
                                                                            name: 'postal_country'
                                                                        }
                                                                    ]
                                                                }
                                                            ]
                                                        },
                                                        {
                                                            xtype: 'fieldset',
                                                            title: _('physical_address'),
                                                            width: 350,
                                                            margin: '3 2 2 0',
                                                            style: {
                                                                'background-color': 'AliceBlue',
                                                                'border-radius': '5px'
                                                            },
                                                            defaults: {
                                                                labelWidth: 50
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('street'),
                                                                    width: 325,
                                                                    name: 'physical_address'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: '(' + _('optional') + ')',
                                                                    width: 325,
                                                                    name: 'physical_address_cont'
                                                                },
                                                                {
                                                                    xtype: 'container',
                                                                    layout: 'hbox',
                                                                    width: 325,
                                                                    margin: '0 0 5 0',
                                                                    defaults: {
                                                                        labelWidth: 50
                                                                    },
                                                                    items: [
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('city'),
                                                                            width: 110,
                                                                            margin: '0 2 2 0',
                                                                            name: 'physical_city'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('state'),
                                                                            width: 30,
                                                                            margin: '0 2 2 0',
                                                                            name: 'physical_state'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('zip'),
                                                                            width: 80,
                                                                            margin: '0 2 2 0',
                                                                            name: 'physical_zip'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            emptyText: _('country'),
                                                                            width: 90,
                                                                            name: 'physical_country'
                                                                        }
                                                                    ]
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
								},
                                {
                                    xtype: 'panel',
                                    title: _('contacts'),
                                    layout: 'column',
                                    enableKeyEvents: true,
                                    checkboxToggle: false,
                                    collapsed: false,
                                    itemId: 'DemographicsContactFieldSet',
                                    border: false,
                                    bodyBorder: false,
                                    bodyPadding: 10,
                                    items: [
                                        {
                                            xtype: 'fieldcontainer',
                                            layout: 'vbox',
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    title: _('name'),
                                                    margin: '5 2 2 0',
                                                    defaults: {
                                                        labelWidth: 50,
                                                        labelAlign: 'left'
                                                    },
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px'
                                                    },
                                                    items:[
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            fieldLabel: _('fathers'),
                                                            layout: 'hbox',
                                                            width: 660,
                                                            items: [
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('first_name'),
                                                                    width: 100,
                                                                    margin: '0 5 0 0',
                                                                    maxLength: 35,
                                                                    name: 'father_fname'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('middle_name'),
                                                                    width: 100,
                                                                    margin: '0 5 0 0',
                                                                    maxLength: 35,
                                                                    name: 'father_mname'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('last_name'),
                                                                    width: 215,
                                                                    margin: '0 5 0 0',
                                                                    maxLength: 35,
                                                                    name: 'father_lname'
                                                                }
                                                            ]
                                                        },
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            fieldLabel: _('mothers'),
                                                            layout: 'hbox',
                                                            width: 660,
                                                            items: [
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('first_name'),
                                                                    width: 100,
                                                                    margin: '0 5 0 0',
                                                                    maxLength: 35,
                                                                    name: 'mother_fname'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('middle_name'),
                                                                    width: 100,
                                                                    margin: '0 5 0 0',
                                                                    maxLength: 35,
                                                                    name: 'mother_mname'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    emptyText: _('last_name'),
                                                                    width: 215,
                                                                    margin: '0 5 0 0',
                                                                    maxLength: 35,
                                                                    name: 'mother_lname'
                                                                }
                                                            ]
                                                        }
                                                        ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    title: _('employer'),
                                                    margin: '5 2 2 0',
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            width: 660,
                                                            defaults: {
                                                                labelWidth: 50,
                                                                labelAlign: 'top',
                                                                hideLabel: false
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'employer_name',
                                                                    emptyText: _('employer_name'),
                                                                    width: 200,
                                                                    fieldLabel: _('name'),
                                                                    margin: '0 10 2 0'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'occupation',
                                                                    emptyText: _('occupation'),
                                                                    width: 150,
                                                                    fieldLabel: _('occupation'),
                                                                    margin: '0 10 2 0'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'phone_work',
                                                                    emptyText: '000-000-0000',
                                                                    width: 100,
                                                                    fieldLabel: _('work'),
                                                                    margin: '0 5 2 0'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'phone_work_ext',
                                                                    width: 50,
                                                                    fieldLabel: _('ext') + '.',
                                                                    margin: '0 10 2 0'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'phone_fax',
                                                                    emptyText: '000-000-0000',
                                                                    width: 100,
                                                                    fieldLabel: _('fax'),
                                                                    margin: '0 5 2 0'
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'container',
                                                    layout: 'vbox',
                                                    items: [
                                                        {
                                                            xtype: 'fieldset',
                                                            title: _('emer_contact'),
                                                            // collapsible: false,
                                                            // checkboxToggle: false,
                                                            // collapsed: false,
                                                            margin: '5 2 2 0',
                                                            style: {
                                                                'background-color': 'AliceBlue',
                                                                'border-radius': '5px'
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'fieldcontainer',
                                                                    layout: 'hbox',
                                                                    width: 660,
                                                                    defaults: {
                                                                        labelAlign: 'top',
                                                                        labelWidth: 50
                                                                    },
                                                                    items: [
                                                                        {
                                                                            xtype: 'gaiaehr.combo',
                                                                            name: 'emergency_contact_relation',
                                                                            emptyText: _('relationship'),
                                                                            width: 125,
                                                                            list: 70,
                                                                            loadStore: true,
                                                                            editable: false,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_fname',
                                                                            emptyText: _('first_name'),
                                                                            width: 100,
                                                                            //fieldLabel: _('name'),
                                                                            enableKeyEvents: true,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_mname',
                                                                            emptyText: _('middle_name'),
                                                                            width: 20,
                                                                            enableKeyEvents: true,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_lname',
                                                                            emptyText: _('last_name'),
                                                                            width: 180,
                                                                            enableKeyEvents: true,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_phone',
                                                                            emptyText: '000-000-0000',
                                                                            width: 90,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'gaiaehr.combo',
                                                                            name: 'emergency_contact_phone_type',
                                                                            emptyText: _('phone_type'),
                                                                            width: 113,
                                                                            list: 136,
                                                                            loadStore: true,
                                                                            editable: false,
                                                                            margin: '5 5 0 0'
                                                                        }
                                                                    ]
                                                                },
                                                                {
                                                                    xtype: 'fieldcontainer',
                                                                    layout: 'hbox',
                                                                    width: 660,
                                                                    items: [
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_address',
                                                                            emptyText: _('street'),
                                                                            width: 170,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_address_cont',
                                                                            emptyText: '(' + _('optional') + ')',
                                                                            width: 170,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_city',
                                                                            emptyText: _('city'),
                                                                            width: 90,
                                                                            margin: '2 5 5 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_state',
                                                                            emptyText: _('state'),
                                                                            width: 30,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_zip',
                                                                            emptyText: _('zip'),
                                                                            width: 80,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'emergency_contact_country',
                                                                            emptyText: _('country'),
                                                                            labelWidth: 50,
                                                                            width: 90,
                                                                            margin: '2 5 0 0'
                                                                        }
                                                                    ]
                                                                }
                                                            ]
                                                        },
                                                        {
                                                            xtype: 'fieldset',
                                                            title: _('guardians_contact'),
                                                            collapsible: false,
                                                            checkboxToggle: false,
                                                            collapsed: false,
                                                            margin: '5 2 2 0',
                                                            style: {
                                                                'background-color': 'AliceBlue',
                                                                'border-radius': '5px'
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'fieldcontainer',
                                                                    layout: 'hbox',
                                                                    width: 660,
                                                                    defaults: {
                                                                        hideLabel: true,
                                                                        labelAlign: 'top',
                                                                        labelWidth: 50
                                                                    },
                                                                    items: [
                                                                        {
                                                                            xtype: 'gaiaehr.combo',
                                                                            name: 'guardians_relation',
                                                                            fieldLabel: _('relationship'),
                                                                            emptyText: _('relationship'),
                                                                            width: 125,
                                                                            labelWidth: 80,
                                                                            list: 70,
                                                                            loadStore: true,
                                                                            editable: false,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_fname',
                                                                            emptyText: _('first_name'),
                                                                            width: 100,
                                                                            //fieldLabel: _('name'),
                                                                            enableKeyEvents: true,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_mname',
                                                                            emptyText: _('middle_name'),
                                                                            width: 20,
                                                                            enableKeyEvents: true,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_lname',
                                                                            emptyText: _('last_name'),
                                                                            width: 180,
                                                                            enableKeyEvents: true,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_phone',
                                                                            emptyText: '000-000-0000',
                                                                            width: 90,
                                                                            margin: '5 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'gaiaehr.combo',
                                                                            name: 'guardians_phone_type',
                                                                            emptyText: _('phone_type'),
                                                                            width: 113,
                                                                            list: 136,
                                                                            loadStore: true,
                                                                            editable: false,
                                                                            margin: '5 5 0 0'
                                                                        }
                                                                    ]
                                                                },
                                                                {
                                                                    xtype: 'fieldcontainer',
                                                                    layout: 'hbox',
                                                                    width: 660,
                                                                    items: [
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_address',
                                                                            emptyText: _('street'),
                                                                            width: 170,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_address_cont',
                                                                            emptyText: '(' + _('optional') + ')',
                                                                            width: 170,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_city',
                                                                            emptyText: _('city'),
                                                                            width: 90,
                                                                            margin: '2 5 5 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_state',
                                                                            emptyText: _('state'),
                                                                            width: 30,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_zip',
                                                                            emptyText: _('zip'),
                                                                            width: 80,
                                                                            margin: '2 5 0 0'
                                                                        },
                                                                        {
                                                                            xtype: 'textfield',
                                                                            name: 'guardians_country',
                                                                            emptyText: _('country'),
                                                                            labelWidth: 50,
                                                                            width: 90,
                                                                            margin: '2 5 0 0'
                                                                        }
                                                                    ]
                                                                }

                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                },
								{
									xtype: 'panel',
									title: _('aditional_info')+'.',
									layout: 'column',
									enableKeyEvents: true,
									checkboxToggle: false,
									collapsed: false,
									border: false,
									bodyBorder: false,
									bodyPadding: 10,
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: 'vbox',
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    title: _('emer_contact'),
                                                    collapsible: false,
                                                    checkboxToggle: false,
                                                    collapsed: false,
                                                    margin: '5 2 2 0',
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            width: 660,
                                                            defaults: {
                                                                labelAlign: 'top',
                                                                labelWidth: 50
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'activefacilitiescombo',
                                                                    fieldLabel: _('primary_facility'),
                                                                    width: 350,
                                                                    name: 'primary_facility',
                                                                    displayField: 'option_name',
                                                                    valueField: 'option_value',
                                                                    queryMode: 'local',
                                                                    forceSelection: true
                                                                },
                                                                {
                                                                    xtype: 'activeproviderscombo',
                                                                    fieldLabel: _('primary_provider'),
                                                                    width: 350,
                                                                    name: 'primary_provider',
                                                                    forceSelection: true
                                                                },
                                                                {
                                                                    xtype: 'mitos.pharmaciescombo',
                                                                    fieldLabel: _('pharmacy'),
                                                                    margin: '0 5 5 0',
                                                                    name: 'pharmacy',
                                                                    forceSelection: true,
                                                                    emptyText: 'Select'
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    collapsible: false,
                                                    checkboxToggle: false,
                                                    collapsed: false,
                                                    margin: '5 2 2 0',
                                                    style: {
                                                        'background-color': 'AliceBlue',
                                                        'border-radius': '5px'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            width: 660,
                                                            defaults: {
                                                                labelWidth: 50,
                                                                hideLabel: false,
                                                                labelAlign: 'right'
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'mitos.datetime',
                                                                    name: 'DOB',
                                                                    emptyText: _('dob'),
                                                                    width: 250,
                                                                    fieldLabel: _('dob'),
                                                                    labelWidth: 20,
                                                                    enableKeyEvents: true,
                                                                    allowBlank: false,
                                                                    margin: '2 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'checkbox',
                                                                    name: 'birth_multiple',
                                                                    //fieldLabel: _('multiple_birth'),
                                                                    boxLabel: _('multiple_birth'),
                                                                    margin: '2 2 2 10'
                                                                },
                                                                {
                                                                    xtype: 'numberfield',
                                                                    name: 'birth_order',
                                                                    width: 50,
                                                                    fieldLabel: _('order'),
                                                                    labelWidth: 35,
                                                                    hideLabel: false,
                                                                    value: 1,
                                                                    maxValue: 15,
                                                                    minValue: 1,
                                                                    margin: '2 2 2 0'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    name: 'birth_place',
                                                                    width: 80,
                                                                    fieldLabel: _('birth_place'),
                                                                    hideLabel: false,
                                                                    margin: '2 2 2 10'
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                },


                                                {
                                                    xtype: 'textfield',
                                                    name: 'alias',
                                                    fieldLabel: _('alias_name'),
                                                    hideLabel: false
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: _('social_security'),
                                                    emptyText: _('social_security'),
                                                    name: 'SS',
                                                    labelWidth: 90,
                                                    width: 190
                                                },
                                                {
                                                    xtype: 'gaiaehr.combo',
                                                    fieldLabel: _('citizenship'),
                                                    hideLabel: false,
                                                    name: 'citizenship',
                                                    list: 104,
                                                    loadStore: true,
                                                    editable: false
                                                },
                                                {
                                                    xtype: 'gaiaehr.combo',
                                                    fieldLabel: _('hipaa_notice'),
                                                    margin: '0 5 5 0',
                                                    name: 'hipaa_notice',
                                                    list: 1,
                                                    loadStore: true,
                                                    editable: false
                                                },
                                                {
                                                    xtype: 'gaiaehr.combo',
                                                    fieldLabel: _('veteran'),
                                                    width: 300,
                                                    boxLabel: 'Yes',
                                                    name: 'is_veteran',
                                                    loadStore: true,
                                                    editable: false
                                                },
                                                {
                                                    xtype: 'fieldcontainer',
                                                    fieldLabel: _('multiple_birth'),
                                                    hideLabel: false,
                                                    layout: 'hbox',
                                                    width: 350,
                                                    items: [
                                                    ]
                                                },
                                                {
                                                    xtype: 'gaiaehr.combo',
                                                    name: 'organ_donor_code',
                                                    fieldLabel: _('organ_donor'),
                                                    list: 137,
                                                    width: 500,
                                                    loadStore: true,
                                                    editable: false
                                                },
                                                {
                                                    xtype: 'gaiaehr.combo',
                                                    fieldLabel: _('deceased'),
                                                    hideLabel: false,
                                                    width: 350,
                                                    boxLabel: 'Yes',
                                                    name: 'deceased',
                                                    list: 103,
                                                    loadStore: true,
                                                    editable: false
                                                },
                                                {
                                                    xtype: 'mitos.datetime',
                                                    fieldLabel: _('death_date'),
                                                    hideLabel: false,
                                                    width: 350,
                                                    margin: '0 5 5 0',
                                                    name: 'death_date'
                                                }

                                            ]
										},
										{
											xtype: 'container',
											items: [
                                                {
                                                    xtype: 'fieldcontainer',
                                                    fieldLabel: _('drivers_info_line'),
                                                    labelWidth: 149,
                                                    hideLabel: false,
                                                    layout: 'hbox',
                                                    width: '80%',
                                                    defaults: {
                                                        margin: '2 5 0 0'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            emptyText: _('driver_license'),
                                                            labelWidth: 149,
                                                            enableKeyEvents: true,
                                                            width: 175,
                                                            name: 'drivers_license'
                                                        },
                                                        {
                                                            xtype: 'gaiaehr.combo',
                                                            width: 175,
                                                            name: 'drivers_license_state',
                                                            list: 20,
                                                            loadStore: true,
                                                            editable: false
                                                        },
                                                        {
                                                            xtype: 'datefield',
                                                            width: 140,
                                                            name: 'drivers_license_exp',
                                                            format: 'Y-m-d'
                                                        }
                                                    ]
                                                }
											]
										}
									]
								},
                                {
                                    xtype: 'panel',
                                    title: _('communication'),
                                    hideLabel: false,
                                    enableKeyEvents: true,
                                    checkboxToggle: false,
                                    collapsed: false,
                                    border: false,
                                    bodyBorder: false,
                                    bodyPadding: 10,
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: 'hbox',
                                            items:[
                                                {
                                                    xtype: 'container',
                                                    layout: 'vbox',
                                                    items:[
                                                        {
                                                            xtype: 'container',
                                                            layout: 'hbox',
                                                            margin: '0 0 0 10',
                                                            items: [
                                                                {
                                                                    xtype: 'checkbox',
                                                                    width: 150,
                                                                    margin: '0 5 0 0',
                                                                    boxLabel: _('allow_voice_msg'),
                                                                    name: 'allow_voice_msg'
                                                                },
                                                                {
                                                                    xtype: 'checkbox',
                                                                    width: 150,
                                                                    margin: '0 5 0 0',
                                                                    boxLabel: _('allow_mail_msg'),
                                                                    name: 'allow_mail_msg'
                                                                },
                                                                {
                                                                    xtype: 'checkbox',
                                                                    width: 240,
                                                                    margin: '0 5 0 0',
                                                                    boxLabel: _('allow_immunization_registry_use'),
                                                                    name: 'allow_immunization_registry'
                                                                },
                                                                {
                                                                    xtype: 'checkbox',
                                                                    margin: '0 5 0 0',
                                                                    boxLabel: _('allow_health_information_exchange'),
                                                                    name: 'allow_health_info_exchange'
                                                                }
                                                            ]
                                                        },
                                                        {
                                                            xtype: 'container',
                                                            layout: 'hbox',
                                                            margin: '5 0 0 10',
                                                            items: [
                                                                {
                                                                    xtype: 'checkbox',
                                                                    width: 240,
                                                                    margin: '0 5 0 0',
                                                                    boxLabel: _('allow_immunization_info_sharing'),
                                                                    name: 'allow_immunization_info_sharing'
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'container',
                                            layout: 'hbox',
                                            margin: '0 0 10 10',
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    title: _('allow_patient_web_portal'),
                                                    checkboxName: 'allow_patient_web_portal',
                                                    checkboxToggle: true,
                                                    width: 320,
                                                    margin: '0 5 0 0',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            fieldLabel: _('web_portal_username'),
                                                            labelWidth: 149,
                                                            name: 'portal_username'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            fieldLabel: _('web_portal_password'),
                                                            labelWidth: 149,
                                                            name: 'portal_password',
                                                            inputType: 'password'
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    title: _('allow_pat_guardian_access_portal'),
                                                    checkboxName: 'allow_guardian_web_portal',
                                                    checkboxToggle: true,
                                                    collapsible: false,
                                                    width: 320,
                                                    margin: '0 5 0 0',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            fieldLabel: _('web_portal_username'),
                                                            labelWidth: 149,
                                                            name: 'guardian_portal_username'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            fieldLabel: _('web_portal_password'),
                                                            labelWidth: 149,
                                                            name: 'guardian_portal_password',
                                                            inputType: 'password'
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    title: _('allow_pat_emerg_access_web_portal'),
                                                    checkboxName: 'allow_emergency_contact_web_portal',
                                                    checkboxToggle: true,
                                                    width: 320,
                                                    margin: '0 5 0 0',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            fieldLabel: _('web_portal_username'),
                                                            labelWidth: 149,
                                                            name: 'emergency_contact_portal_username'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            fieldLabel: _('web_portal_password'),
                                                            labelWidth: 149,
                                                            name: 'emergency_contact_portal_password',
                                                            inputType: 'password'
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
							]
						}
					]
				})
			]
		};

		configs.bbar = [
			{
				xtype: 'button',
				action: 'readOnly',
				text: _('possible_duplicates'),
				minWidth: 75,
				itemId: 'PatientPossibleDuplicatesBtn'
			},
			'-',
			'->',
			'-',
			{
				xtype: 'button',
				action: 'readOnly',
				text: _('save'),
				itemId: 'PatientDemographicSaveBtn',
				minWidth: 75,
				scope: me,
				handler: me.formSave
			},
			'-',
			{
				xtype: 'button',
				text: _('cancel'),
				action: 'readOnly',
				itemId: 'PatientDemographicCancelBtn',
				minWidth: 75,
				scope: me,
				handler: me.formCancel
			}
		];

		configs.listeners = {
			scope: me,
			beforerender: me.beforePanelRender
		};

		Ext.apply(me, configs);

		me.callParent(arguments);

	},

	beforePanelRender: function(){
		var me = this,
			whoPanel,
			form = me.demoForm.getForm(),
			fname = form.findField('fname'),
			mname = form.findField('mname'),
			lname = form.findField('lname'),
			sex = form.findField('sex'),
			dob = form.findField('DOB'),
			crtl;

		if(fname) fname.vtype = 'nonspecialcharacters';
		if(mname) mname.vtype = 'nonspecialcharacters';
		if(lname) lname.vtype = 'nonspecialcharacters';

		if(dob) dob.setMaxValue(new Date());

		if(me.newPatient){
			crtl = App.app.getController('patient.Patient');
			fname.on('blur', crtl.checkForPossibleDuplicates, crtl);
			lname.on('blur', crtl.checkForPossibleDuplicates, crtl);
			sex.on('blur', crtl.checkForPossibleDuplicates, crtl);
			dob.dateField.on('blur', crtl.checkForPossibleDuplicates, crtl);
		}else{
			whoPanel = me.demoForm.query('[action=DemographicWhoFieldSet]')[0];
			whoPanel.insert(0,
				me.patientImages = Ext.create('Ext.panel.Panel', {
					action: 'patientImage',
					layout: 'vbox',
					style: 'float:right;',
					bodyPadding: 10,
					height: 300,
					width:180,
					items: [
                        {
                            xtype: 'image',
                            itemId: 'image',
                            imageAlign: 'center',
                            width: 150,
                            height: 120,
                            margin: '0 5 10 5',
                            src: me.defaultPatientImage
                        },
                        {
                            xtype: 'textareafield',
                            name: 'image',
                            hidden: true
                        },
                        {
                            xtype: 'image',
                            itemId: 'qrcode',
                            imageAlign: 'center',
                            width: 150,
                            height: 120,
                            margin: '0 5 10 5',
                            src: me.defaultQRCodeImage
                        }
                        ],
                    bbar: [
                        '-',
                            {
                                text: _('take_picture'),
                                action: 'onWebCam'
                                //handler: me.getPhotoIdWindow
                            },
                        '-',
                        '->',
                        '-',
                            {
                                text: _('print_qrcode'),
                                scope: me,
                                handler: function () {
                                window.printQRCode(app.patient.pid);
                            }
                        },
                        '-'
                    ]
				})
			);
		}
	},

	onAddNewContact: function(btn){
		var grid = btn.up('grid'),
			store = grid.store,
			record;

		record = {
			created_date: new Date(),
			pid: app.patient.pid,
			uid: app.user.id
		};

		grid.plugins[0].cancelEdit();
		store.insert(0, record);
		grid.plugins[0].startEdit(0, 0);
	},

	getPatientImages: function(record){
		var me = this;
		if(me.patientImages){
			me.patientImages.getComponent('image').setSrc(
				(record.data.image !== '' ? record.data.image : me.defaultPatientImage)
			);
		}
		if(me.patientImages){
			me.patientImages.getComponent('qrcode').setSrc(
				(record.data.qrcode !== '' ? record.data.qrcode : me.defaultQRCodeImage)
			);
		}
	},

	getPatientContacts: function(pid){
		var me = this;

		me.patientContacsStore.clearFilter(true);
		me.patientContacsStore.load({
			params: {
				pid: pid
			},
			filters: [
				{
					property: 'pid',
					value: pid
				}
			]
		});
	},

	/**
	 * verify the patient required info and add a yellow background if empty
	 */
	verifyPatientRequiredInfo: function(){
		var me = this,
			field;
		me.patientAlertsStore.load({
			scope: me,
			params: {pid: me.pid},
			callback: function(records, operation, success){
				for(var i = 0; i < records.length; i++){
					field = me.demoForm.getForm().findField(records[i].data.name);
					if(records[i].data.val){
						if(field) field.removeCls('x-field-yellow');
					}else{
						if(field) field.addCls('x-field-yellow');
					}
				}
			}
		});
	},

	/**
	 * allow to edit the field if the filed has no data
	 * @param fields
	 */
	readOnlyFields: function(fields){
        // for(var i = 0; i < fields.items.length; i++){
        //    var f = fields.items[i], v = f.getValue(), n = f.name;
        //    if(n == 'SS' || n == 'DOB' || n == 'sex'){
        //        if(v == null || v == ''){
        //            f.setReadOnly(false);
        //        }else{
        //            f.setReadOnly(true);
        //        }
        //    }
        // }
	},

    getValidInsurances: function(){
        var me = this,
            forms = Ext.ComponentQuery.query('#PatientInsurancesPanel')[0].items.items,
            records = [],
            form,
            rec;

        for(var i = 0; i < forms.length; i++){
            form = forms[i].getForm();
            if(!form.isValid()){
                me.insTabPanel.setActiveTab(forms[i]);
                return false;
            }
            rec = form.getRecord();
            app.fireEvent('beforepatientinsuranceset', form, rec);
            rec.set(form.getValues());
            app.fireEvent('afterpatientinsuranceset', form, rec);
            records.push(rec);
        }
        return records;
    },

	formSave: function(){
		var me = this,
			form = me.demoForm.getForm(),
			record = form.getRecord(),
			values = form.getValues();
			insRecs = me.getValidInsurances();

		if(form.isValid() && insRecs !== false){
			record.set(values);

			// fire global event
			app.fireEvent('beforedemographicssave', record, me);

			record.save({
				scope: me,
				callback: function(record){

					app.setPatient(record.data.pid, null, null, function(){

						var insStore = record.insurance();

						for(var i = 0; i < insRecs.length; i++){
							if(insRecs[i].data.id === 0){
								insStore.add(insRecs[i]);
							}
						}

						insStore.sync();

						if(me.newPatient){
							app.openPatientSummary();
						}else{
							me.getPatientImages(record);
							me.verifyPatientRequiredInfo();
							me.readOnlyFields(form.getFields());
						}
					});

					// fire global event
					app.fireEvent('afterdemographicssave', record, me);
					me.msg(_('sweet'), _('record_saved'));
				}
			});
		}else{
			me.msg(_('oops'), _('missing_required_data'), true);
		}
	},

	formCancel: function(btn){
		var me = this,
            form = me.demoForm.getForm(),
            record = form.getRecord();
		form.loadRecord(record);
	},

	loadNew: function(){
		var patient = Ext.create('App.model.patient.Patient', {
			'create_uid': app.user.id,
			'update_uid': app.user.id,
			'create_date': new Date(),
			'update_date': new Date(),
			'DOB': '0000-00-00 00:00:00'
		});
		this.demoForm.getForm().loadRecord(patient);
	},

	loadPatient: function(pid){
		var me = this,
			form = me.demoForm.getForm();

		me.pid = pid;

		form.reset();

		me.getPatientContacts(pid);

		app.patient.record.insurance().load({
			filters: [
				{
					property: 'pid',
					value: app.patient.record.data.pid
				}
			],
			callback: function(records){

				form.loadRecord(app.patient.record);
				me.setReadOnly(app.patient.readOnly);
				me.setButtonsDisabled(me.query('button[action="readOnly"]'));
				me.verifyPatientRequiredInfo();
				me.insTabPanel = Ext.ComponentQuery.query('#PatientInsurancesPanel')[0];

				// set the insurance panel
				me.insTabPanel.removeAll(true);
				for(var i = 0; i < records.length; i++){
					me.insTabPanel.add(
						Ext.widget('patientinsuranceform', {
							closable: false,
							insurance: records[i]
						})
					);
				}
				if(me.insTabPanel.items.length !== 0) me.insTabPanel.setActiveTab(0);
			}
		});
	}
});
