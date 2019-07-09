/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, LLC.
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

Ext.define('App.model.administration.Label', {
    extend: 'Ext.data.Model',
    table: {
        name: 'labels'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'label_size',
            type: 'string',
            len: 45
        },
        {
            name: 'type',
            type: 'string',
            len: 45
        },
        {
            name: 'height',
            type: 'string',
            len: 5
        },
        {
            name: 'width',
            type: 'string',
            len: 5
        },
        {
            name: 'x',
            type: 'string',
            len: 10
        },
        {
            name: 'y',
            type: 'string',
            len: 10
        },
        {
            name: 'angle',
            type: 'int',
            len: 3
        },
        {
            name: 'text',
            type: 'string',
            len: 180
        },
        {
            name: 'font_size',
            type: 'string',
            len: 3
        },
        {
            name: 'show_text',
            type: 'bool'
        }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'Labels.getLabels',
            create: 'Labels.addLabel',
            update: 'Labels.updateLabel',
            destroy: 'Labels.destroyLabel'
        }
    }
});
