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

Ext.define('App.model.administration.CronJob', {
    extend: 'Ext.data.Model',
    table: {
        name: 'cronjob',
        comment: 'Holds all the available scripts that runs on the background.'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'name',
            type: 'string',
            dataType: 'varchar',
            len: 45
        },
        {
            name: 'filename',
            type: 'string',
            dataType: 'varchar',
            len: 45
        },
        {
            name: 'minute',
            dataType: 'varchar',
            len: 3
        },
        {
            name: 'hour',
            dataType: 'varchar',
            len: 3
        },
        {
            name: 'month_day',
            dataType: 'varchar',
            len: 3
        },
        {
            name: 'month',
            dataType: 'varchar',
            len: 3
        },
        {
            name: 'week_day',
            dataType: 'varchar',
            len: 3
        },
        {
            name: 'pid',
            dataType: 'varchar',
            len: 10
        },
        {
            name: 'running',
            type: 'bool'
        },
        {
            name: 'last_run_date',
            type: 'date'
        },
        {
            name: 'elapsed',
            type: 'string',
            store: false
        },
        {
            name: 'timeout',
            type: 'int'
        },
        {
            name: 'params',
            type: 'string',
            len: 50
        },
        {
            name: 'active',
            type: 'bool'
        }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'CronJob.getCronJob',
            update: 'CronJob.updateCronJob'
        },
        reader: {
            root: 'data'
        }
    }
});
