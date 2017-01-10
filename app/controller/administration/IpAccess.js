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

Ext.define('App.controller.administration.IpAccess', {
    extend: 'Ext.app.Controller',

    refs: [
        {
            ref:'IpAccessPanel',
            selector:'ipaccesspanel'
        },
        {
            ref: 'IpAccessRulesGrid',
            selector: 'ipaccesspanel #IpAccessRulesGrid'
        },
        {
            ref: 'IpAccessLogGrid',
            selector: 'ipaccesspanel #IpAccessLogGrid'
        },
        {
            ref: 'IpAccessPanelContextMenu',
            selector: '#IpAccessPanelContextMenu'
        }
    ],

    init: function() {
        var me = this;

        me.control({
            'ipaccesspanel':{
                activate: me.onIpAccessPanelActive
            },
            'ipaccesspanel #addIpRule':{
                click: me.onAddIpRuleClick
            },
            'ipaccesspanel #IpAccessLogGrid':{
	            beforeitemcontextmenu: me.onIpAccessLogGridBeforeContextMenu
            },
            '#IpAccessPanelAddToWhiteListMenu':{
	            click: me.onIpAccessPanelAddToWhiteListMenuClick
            }
        });

        me.clockCtrl = me.getController('Clock');

    },

    onAddIpRuleClick: function(btn){
        var me = this,
            rulesGrid = me.getIpAccessRulesGrid();

        rulesGrid.editingPlugin.cancelEdit();
        rulesGrid.getStore().add({
            create_date: this.clockCtrl.getTime(),
            update_date: this.clockCtrl.getTime(),
            active: 1
        });
        rulesGrid.editingPlugin.startEdit(0, 0);
    },

    onIpAccessPanelActive: function(){
        var me = this,
            rulesGrid = me.getIpAccessRulesGrid(),
            logGrid = me.getIpAccessLogGrid();

        rulesGrid.getStore().load();
        logGrid.getStore().load();
    },

	onIpAccessPanelAddToWhiteListMenuClick: function() {

    	var net_log_record = this.getIpAccessLogGrid().getSelectionModel().getLastSelected(),
    	    ip_rules_store = this.getIpAccessRulesGrid().getStore(),
    	    ip_rules_editingPlugin = this.getIpAccessRulesGrid().editingPlugin;

		ip_rules_editingPlugin.cancelEdit();

		var records = ip_rules_store.add({
			ip: net_log_record.get('ip'),
			rule: 'WHT',
			create_date: this.clockCtrl.getTime(),
			create_uid: app.user.id,
			active: 1
		});

		ip_rules_editingPlugin.startEdit(records[0], 0);
	},

	onIpAccessLogGridBeforeContextMenu: function (grid, record, item, index, e) {
    	e.preventDefault();
    	this.showNetWorkLogContextMenu(e);
	},

	showNetWorkLogContextMenu: function(e) {

		if(!this.getIpAccessPanelContextMenu()){
			Ext.widget('menu', {
				margin: '0 0 10 0',
				itemId: 'IpAccessPanelContextMenu',
				items: [
					{
						text: _('add_to_network_rules'),
						itemId: 'IpAccessPanelAddToWhiteListMenu',
						icon: 'resources/images/icons/add.png'
					}
				]
			});
		}

		return this.getIpAccessPanelContextMenu().showAt(e.getXY());
	}

});
