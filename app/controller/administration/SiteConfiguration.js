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

Ext.define('App.controller.administration.SiteConfiguration', {
    extend: 'Ext.app.Controller',

    refs: [
        {
            ref: 'ServerConfiguration',
            selector: '#ServerConfiguration'
        },
        {
            ref: 'ServerConfigurationIFrame',
            selector: '#ServerConfigurationIFrame'
        },
    ],

    init: function() {
        var me = this;

        me.control({
            '#ServerConfiguration':{
                activate: me.onServerConfigurationActive
            },
            '#ServerConfigurationIFrame':{
                afterrender: me.onServerConfigurationIFrameRender
            },
            '#ServerConfigurationResetBtn':{
                click: me.onServerConfigurationResetBtnClick
            },
            '#ServerConfigurationSaveBtn':{
                click: me.onServerConfigurationSaveBtnClick
            }
        });
    },

    onServerConfigurationIFrameRender: function(miframe){
        window.SiteConfigurationEditorGetValueCallback = function(value) {
            SiteConfiguration.setSiteConfiguration(value, function (response){
                if(!response.success){
                    app.msg(_('error'), response.error, true);
                }else{
                    app.msg(_('sweet'), 'Site Configuration Saved');
                }
            });
        }
    },

    onServerConfigurationActive: function(){
        this.getServerConfiguration();
    },

    getServerConfiguration: function (){
        var iframe = this.getServerConfigurationIFrame();

        SiteConfiguration.getSiteConfiguration(function (content){
            iframe.frameElement.dom.contentWindow.EditorSetValue(content);
        });

    },

    onServerConfigurationResetBtnClick: function (){
        this.getServerConfiguration();
    },

    onServerConfigurationSaveBtnClick: function (){
        var iframe = this.getServerConfigurationIFrame();

        iframe.frameElement.dom.contentWindow.EditorGetValue();
    }



});
