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
            '#CronJobPanelKillProcessBtn':{
                click: me.onCronJobPanelKillProcessBtnClick
            }
        });

    },


    doGridRefresh: function (){
        this.getCronJobGrid().getStore().load();
    },

    onCronJobPanelActive: function(){
        this.doGridRefresh();
    },

    onCronJobPanelKillProcessBtnClick: function (btn){

        var me = this,
            grid = btn.up('grid'),
            selection = grid.getSelectionModel().getSelection();

        if(selection.length === 0){
            return;
        }

        if(!selection[0].get('running')){
            app.msg(_('oops'), 'Process is not running', true);
            return;
        }

        if(selection[0].get('pid') === ''){
            app.msg(_('oops'), 'Process PID missing', true);
            return;
        }

        var pid = selection[0].get('pid');

        Ext.Msg.show({
            title: 'Wait!',
            msg: Ext.String.format('This action will kill process <b>{0}</b>, would you like to continue?', pid),
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            fn: function (answer){
                if(answer === 'yes'){
                    CronJob.killCronjob(selection[0].data, function (success){
                        if(success){
                            app.msg(_('sweet'), Ext.String.format('Process {0} Killed', pid), true);
                            me.doGridRefresh();
                        }else{
                            app.msg(_('oops'), 'Something went wrong killing the process', true);
                        }
                    });
                }
            }
        });

    }

});
