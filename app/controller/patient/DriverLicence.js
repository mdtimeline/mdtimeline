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

Ext.define('App.controller.patient.DriverLicence', {
    extend: 'Ext.app.Controller',
    requires: [],
    refs: [
        {
            ref: 'HeaderPatientLiveSearchField',
            selector: '#HeaderPatientLiveSearchField'
        }
    ],

    fieldMap: {
        'DBA':'drivers_license_exp', // License Expiration Date
        'DAB':'lname', // Family Name
        'DBO':'lname', // Family Name
        'DCS':'lname', // Family Name
        'DAC':'fname', // First Name
        'DBP':'fname', // First Name
        'DCT':'fname', // First Name
        'DAD':'mname', // Middle Name or Initial
        'DBQ':'mname', // Middle Name or Initial
        'DBB':'DOB', // Date of Birth
        'DBL':'DOB', // Date of Birth
        'DBC':'sex', // Sex
        'DAG':'postal_address', // Mailing Street Address1
        'DAH':'postal_address_cont', // Mailing Street Address2
        'DAI':'postal_city', // Mailing City
        'DAJ':'postal_state', // Mailing Jurisdiction Code
        'DAK':'postal_code', // Mailing Postal Code
        'DAQ':'drivers_license', // License or ID Number
        'DCG':'postal_country', // Country territory of issuance
    },

    init: function () {
        var me = this;

        me.control({
            'viewport': {
                'barcodelicensescanned': me.onBarcodeLicenseScanned
            },
            '#DriverLicenceSearchBtn': {
                click: me.onDriverLicenceSearchBtnClick
            },
            '#DriverLicenceCreateBtn': {
                click: me.onDriverLicenceCreateBtnClick
            },
            '#DriverLicenceCancelBtn': {
                click: me.onDriverLicenceCancelBtnClick
            }
        });

    },

    onDriverLicenceSearchBtnClick: function (btn){
        var win = btn.up('window'),
            form = win.down('form').getForm(),
            values = form.getValues(),
            search_field = this.getHeaderPatientLiveSearchField(),
            query = values.lname + ', ' + values.fname;

        say(search_field);
        search_field.setValue(query);
        search_field.doQuery(query)
    },

    onDriverLicenceCreateBtnClick: function (btn){
        var me = this,
            win = btn.up('window'),
            form = win.down('form').getForm(),
            demographics_params = form.getValues(),
            patientCtl = app.getController('patient.Patient');

        patientCtl.lookForPossibleDuplicates(demographics_params, null, function (duplicared_win, response) {

            if(response === true){
                win.el.mask(_('please_wait'));
                // continue clicked
                Patient.createNewPatient(demographics_params, function (response) {
                    app.setPatient(response.pid, null, null, function(){
                        win.el.unmask();
                        win.close();
                        if(win.newPatientCallback){
                            win.newPatientCallback(response);
                        }
                    }, true);
                });

            }else if(response.isModel === true){
                win.el.mask(_('please_wait'));
                // duplicated record clicked
                app.setPatient(response.get('pid'), null, null, function(){
                    duplicared_win.close();
                    win.el.unmask();
                    win.close();
                    if(win.newPatientCallback){
                        win.newPatientCallback(response);
                    }
                }, true);
            }
        });

    },

    onDriverLicenceCancelBtnClick: function (btn){
        btn.up('window').close();
    },

    onBarcodeLicenseScanned: function (driver_lic_data){

        var me = this,
            data = me.parseDriverLic(driver_lic_data);

        if(!data.drivers_license || data.drivers_license === ''){
            app.msg(_('oops'), 'Unable to parse driver license');
            return;
        }

        say(data);

        Patient.getPatients({
            filter: [
                {
                    property: 'drivers_license',
                    value: data.drivers_license
                }
            ]
        }, function (patients){
            if(patients.length === 0){
                me.patientNotFoundHandler(data);
            }else if(patients.length === 1){
                me.patientFoundHandler(data, patients[0]);
            }else{
                me.showMultiplePatientsFound(data, patients);
            }
        });

    },

    patientFoundHandler: function (data, patient){
        app.setPatient(patient.pid, null, app.user.site, function (){
            app.msg(_('sweet'), 'Patient License Found');
            app.openPatientSummary();
        }, true);
    },

    patientNotFoundHandler: function (data){

        Ext.create('Ext.window.Window', {
            title: 'No License Found For:',
            width: 450,
            layout: 'fit',
            bodyPadding: 5,
            items: {  // Let's put an empty grid in just to illustrate fit layout
                xtype: 'form',
                bodyPadding: 5,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'textfield',
                                name: 'fname',
                                fieldLabel: _('fname'),
                                value: data.fname,
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 2
                            },
                            {
                                xtype: 'textfield',
                                name: 'mname',
                                fieldLabel: _('mname'),
                                value: data.mname,
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 1
                            },
                            {
                                xtype: 'textfield',
                                name: 'lname',
                                fieldLabel: _('lname'),
                                value: data.lname,
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 3
                            },
                            {
                                xtype: 'textfield',
                                name: 'sex',
                                fieldLabel: _('sex'),
                                value: data.sex,
                                labelAlign: 'top',
                                flex: 2
                            },
                        ]
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'textfield',
                                name: 'drivers_license',
                                fieldLabel: _('license_no'),
                                value: data.drivers_license,
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 1
                            },
                            {
                                xtype: 'datefield',
                                name: 'drivers_license_exp',
                                fieldLabel: _('exp_date'),
                                value: new Date(data.drivers_license_exp),
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 1
                            },
                        ]
                    },

                    {
                        xtype: 'textfield',
                        name: 'postal_address',
                        fieldLabel: _('address'),
                        value: data.postal_address,
                        labelAlign: 'top'
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'textfield',
                                name: 'postal_city',
                                fieldLabel: _('city'),
                                value: data.postal_city,
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 2
                            },
                            {
                                xtype: 'textfield',
                                name: 'postal_state',
                                fieldLabel: _('state'),
                                value: data.postal_state,
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 1
                            },
                            {
                                xtype: 'textfield',
                                name: 'postal_code',
                                fieldLabel: _('postal_code'),
                                value: data.postal_code,
                                labelAlign: 'top',
                                margin: '0 5 0 0',
                                flex: 1
                            },
                            {
                                xtype: 'textfield',
                                name: 'postal_country',
                                fieldLabel: _('country'),
                                value: data.postal_country,
                                labelAlign: 'top',
                                flex: 1
                            }
                        ]
                    }
                ],
                buttons: [
                    {
                        text: _('search'),
                        itemId: 'DriverLicenceSearchBtn'
                    },
                    {
                        text: _('create'),
                        itemId: 'DriverLicenceCreateBtn'
                    },
                    {
                        text: _('cancel'),
                        itemId: 'DriverLicenceCancelBtn'
                    }
                ]
            }
        }).show();

    },

    showMultiplePatientsFound: function (data, patients){
        var search_field = this.getHeaderPatientLiveSearchField();
        search_field.setValue(data.drivers_license);
        search_field.doQuery(data.drivers_license);
        app.msg(_('oops'), 'Multiple Patient Licenses Found', true);
    },

    parseDriverLic: function (driver_lic_data){

        var me = this,
            lines = driver_lic_data.split("\n"),
            data = {};

        lines.forEach(function (line){

            var field  = line.substr(0,3),
                value = line.substr(3, 100);

            if(me.fieldMap[field]){
                data[me.fieldMap[field]] = value.trim();
            }
        });

        if(data.sex){
            data.sex = data.sex === '1' ? 'M' : 'F';
        }

        if(data.drivers_license_exp){
            data.drivers_license_exp = data.drivers_license_exp.replace(/(\d{2})(\d{2})(\d{4})/, '$1-$2-$3')
        }
        if(data.DOB){
            data.DOB = data.DOB.replace(/(\d{2})(\d{2})(\d{4})/, '$1-$2-$3') + ' 00:00:00'
        }

        return data;
    }
});
