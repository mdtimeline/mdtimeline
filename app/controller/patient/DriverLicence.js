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

        });
    },

    parseDriverLic: function (driver_lic_data){

        var me = this,
            lines = driver_lic_data.split("\n"),
            data = {};

        lines.forEach(function (line){

            var field  = line.substr(0,3),
                value = line.substr(3, 100);

            if(me.fieldMap[field]){
                data[me.fieldMap[field]] = value;
            }
        });

        if(data.drivers_license_exp){
            data.drivers_license_exp = data.drivers_license_exp.replace(/(\d{2})(\d{2})(\d{4})/, '$1-$2-$3')
        }
        if(data.DOB){
            data.DOB = data.DOB.replace(/(\d{2})(\d{2})(\d{4})/, '$1-$2-$3') + ' 00:00:00'
        }

        say(data);
        say(lines);

    }
});
