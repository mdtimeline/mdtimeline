/**
 GaiaEHR (Electronic Health Records)
 Copyright (C) 2013 Certun, LLC.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.model.administration.Version', {
    extend: 'Ext.data.Model',
    table: {
        name: 'version'
    },
    fields: [
        {
            name: 'v_module',
            type: 'string',
            len: 80
        },
        {
            name: 'v_major',
            type: 'int'
        },
        {
            name: 'v_minor',
            type: 'int'
        },
        {
            name: 'v_patch',
            type: 'int'
        },
        {
            name: 'v_timestamp',
            type: 'date'
        },
        {
            name: 'v_notes_url',
            type: 'string',
            len: 600
        }
    ]
});