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

Ext.define('App.controller.administration.CronJob', {
    extend: 'Ext.app.Controller',

    refs: [
        {
            ref:'CronJobPanel',
            selector:'cronjobpanel'
        },
        {
            ref: 'CronJobGrid',
            selector: 'cronjobpanel #CronJobGrid'
        }
    ],

    init: function() {
        var me = this;

        me.control({
            'cronjobpanel':{
                activate: me.onCronJobPanelActive
            },
            'cronjobpanel #refresh':{
                click: me.onCronJobPanelActive
            }
        });

    },

    onCronJobPanelActive: function(){
        var me = this,
            CronJobGrid = me.getCronJobGrid();
        CronJobGrid.getStore().load();
    }

});
