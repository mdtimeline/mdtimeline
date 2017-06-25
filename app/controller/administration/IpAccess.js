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
        },
        {
            ref: 'IpAccessRulesIpField',
            selector: '#IpAccessRulesIpField'
        },
        {
            ref: 'IpAccessRulesIpFormField',
            selector: '#IpAccessRulesIpFormField'
        },
        {
            ref: 'IpAccessRulesIpToField',
            selector: '#IpAccessRulesIpToField'
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
            },
            '#IpAccessRulesIpField':{
	            keyup: me.onIpAccessRulesIpFieldKeyUp
            }
        });

        me.clockCtrl = me.getController('Clock');

    },

    onAddIpRuleClick: function(btn){
        var me = this,
            rulesGrid = me.getIpAccessRulesGrid();

        rulesGrid.editingPlugin.cancelEdit();
        var rercords = rulesGrid.getStore().add({
            create_date: this.clockCtrl.getTime(),
            update_date: this.clockCtrl.getTime(),
            active: 1
        });
        rulesGrid.editingPlugin.startEdit(rercords[0], 0);
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
    	    ip_rules_editingPlugin = this.getIpAccessRulesGrid().editingPlugin,
		    ip = net_log_record.get('ip') + '/32',
		    range = this.cidrToRange(ip, true);

		ip_rules_editingPlugin.cancelEdit();

		var records = ip_rules_store.add({
			ip: ip,
			ip_from: range[0],
			ip_to: range[1],
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
	},

	onIpAccessRulesIpFieldKeyUp: function (field) {

    	if(!field.isValid()) return;

    	var value = field.getValue(),
	        range = this.cidrToRange(value, true),
	        record = field.up('form').getForm().getRecord();

		record.set({
			ip_from: range[0],
			ip_to: range[1]
		});
	},

	long2ip: function (ip) {
		//  discuss at: http://locutus.io/php/long2ip/
		// original by: Waldo Malqui Silva (http://waldo.malqui.info)
		//   example 1: long2ip( 3221234342 )
		//   returns 1: '192.0.34.166'

		if (!isFinite(ip)) {
			return false
		}

		return [ip >>> 24, ip >>> 16 & 0xFF, ip >>> 8 & 0xFF, ip & 0xFF].join('.')
	},

	ip2long: function(ip) {
		//  discuss at: http://locutus.io/php/ip2long/
		// original by: Waldo Malqui Silva (http://waldo.malqui.info)
		// improved by: Victor
		//  revised by: fearphage (http://http/my.opera.com/fearphage/)
		//  revised by: Theriault (https://github.com/Theriault)
		//    estarget: es2015
		//   example 1: ip2long('192.0.34.166')
		//   returns 1: 3221234342
		//   example 2: ip2long('0.0xABCDEF')
		//   returns 2: 11259375
		//   example 3: ip2long('255.255.255.256')
		//   returns 3: false

		var i = 0;
		// PHP allows decimal, octal, and hexadecimal IP components.
		// PHP allows between 1 (e.g. 127) to 4 (e.g 127.0.0.1) components.

		const pattern = new RegExp([
			'^([1-9]\\d*|0[0-7]*|0x[\\da-f]+)',
			'(?:\\.([1-9]\\d*|0[0-7]*|0x[\\da-f]+))?',
			'(?:\\.([1-9]\\d*|0[0-7]*|0x[\\da-f]+))?',
			'(?:\\.([1-9]\\d*|0[0-7]*|0x[\\da-f]+))?$'
		].join(''), 'i');

		ip = ip.match(pattern); // Verify ip format.
		if (!ip) {
			// Invalid format.
			return false
		}
		// Reuse ip variable for component counter.
		ip[0] = 0;
		for (i = 1; i < 5; i += 1) {
			ip[0] += !!((ip[i] || '').length);
			ip[i] = parseInt(ip[i]) || 0
		}
		// Continue to use ip for overflow values.
		// PHP does not allow any component to overflow.
		ip.push(256, 256, 256, 256);
		// Recalculate overflow of last component supplied to make up for missing components.
		ip[4 + ip[0]] *= Math.pow(256, 4 - ip[0]);
		if (ip[1] >= ip[5] ||
			ip[2] >= ip[6] ||
			ip[3] >= ip[7] ||
			ip[4] >= ip[8]) {
			return false
		}

		return ip[1] * (ip[0] === 1 || 16777216) +
			ip[2] * (ip[0] <= 2 || 65536) +
			ip[3] * (ip[0] <= 3 || 256) +
			ip[4] * 1
	},

	cidrToRange: function(cidr, long) {
		var range = [2];
		cidr = cidr.split('/');
		var start = this.ip2long(cidr[0]);
		var stop = Math.pow(2, 32 - cidr[1]) + start - 1;
		range[0] = long ? start : this.long2ip(start);
		range[1] = long ? stop : this.long2ip(stop);
		return range;
	}

});
