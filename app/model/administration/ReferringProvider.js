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
Ext.define('App.model.administration.ReferringProvider', {
    extend: 'Ext.data.Model',
    table: {
        name: 'referring_providers'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'title',
            type: 'string',
            len: 10
        },
        {
            name: 'fname',
            type: 'string',
            len: 35
        },
        {
            name: 'mname',
            type: 'string',
            len: 25
        },
        {
            name: 'lname',
            type: 'string',
            len: 60
        },
        {
            name: 'upin',
            type: 'string',
            len: 15,
            comment: 'Carrier Claim Referring Physician UPIN Number'
        },
        {
            name: 'lic',
            type: 'string',
            len: 15
        },
        {
            name: 'npi',
            type: 'string',
            len: 15
        },
        {
            name: 'ssn',
            type: 'string',
	        len: 15
        },
        {
            name: 'taxonomy',
            type: 'string',
            len: 15,
            comment: 'taxonomy',
            defaultValue: '207Q00000X'
        },
        {
            name: 'accept_mc',
            type: 'bool',
            comment: 'Accepts Medicare'
        },
        {
            name: 'notes',
            type: 'string',
            len: 600
        },
        {
            name: 'email',
            type: 'string',
            len: 180
        },
        {
            name: 'direct_address',
            type: 'string',
            len: 180
        },
        {
            name: 'phone_number',
            type: 'string',
            len: 25
        },
        {
            name: 'fax_number',
            type: 'string',
            len: 25
        },
        {
            name: 'cel_number',
            type: 'string',
            len: 25
        },
        {
            name: 'allow_mail_notifications',
            type: 'bool'
        },
        {
            name: 'code',
            type: 'string',
            len: 50
        },
        {
            name: 'username',
            type: 'string',
            len: 40,
            useNull: true,
            index: true
        },
        {
            name: 'password',
            type: 'string',
            len: 300,
            encrypt: true
        },
        {
            name: 'authorized',
            type: 'bool',
            index: true
        },
        {
            name: 'authy_id',
            type: 'string',
            len: 45,
            index: true
        },
        {
            name: 'active',
            type: 'bool',
            index: true
        },
        {
            name: 'create_uid',
            type: 'int'

        },
        {
            name: 'update_uid',
            type: 'int'
        },
        {
            name: 'create_date',
            type: 'date',
            comment: 'create date',
            dateFormat: 'Y-m-d H:i:s'
        },
        {
            name: 'update_date',
            type: 'date',
            comment: 'last update date',
            dateFormat: 'Y-m-d H:i:s'
        }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'ReferringProviders.getReferringProviders',
            create: 'ReferringProviders.addReferringProvider',
            update: 'ReferringProviders.updateReferringProvider'
        },
        reader: {
            root: 'data'
        }
    },
    hasMany: [
        {
            model: 'App.model.administration.ReferringProviderFacility',
            name: 'facilities',
            foreignKey: 'referring_provider_id'
        }
    ]
});
