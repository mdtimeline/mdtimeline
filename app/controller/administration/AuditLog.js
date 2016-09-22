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

Ext.define('App.controller.administration.AuditLog', {
	extend: 'Ext.app.Controller',
	requires: [],

	refs: [
		{
			selector: '#AuditLogWindow',
			ref: 'AuditLogWindow'
		},
		{
			selector: '#AuditLogWindowGrid',
			ref: 'AuditLogWindowGrid'
		}
	],

	/**
	 *
	 */
	init: function(){
		var me = this;

		me.control({
			'#AuditLogWindowGrid': {
				close: me.onAuditLogWindowGridClose
			}
		});
	},

	onAuditLogWindowGridClose: function(){
		this.getAuditLogWindowGrid().getStore().removeAll();
	},

	/**
	 *
	 * @param pid               {int}       Example: 1111
	 * @param uid               {int}       Example: 2222
	 * @param foreign_id        {int}       Example: 3333
	 * @param foreign_table     {string}    Example: worklist_reports
	 * @param event             {string}    Example: create
	 * @param event_description {string}    Example: Report Created
	 */
	addLog: function(pid, uid, foreign_id, foreign_table, event, event_description){

		WorkListAuditLog.addLog({
			pid: pid,
			uid: uid,
			foreign_id: foreign_id,
			foreign_table: foreign_table,
			event: event,
			event_description: event_description
		});
	},

	showLogByRecord: function(record){
		var me = this,
			win = me.showLogWindow(),
			store = me.getAuditLogWindowGrid().getStore();

		store.clearFilter(true);

		store.getProxy().extraParams = { };

		store.filter([
			{
				property: 'foreign_id',
				value: record.get('id')
			},
			{
				property: 'foreign_table',
				value: record.table.name
			}
		]);
	},

	showLogByPidEvent: function(pid, event){
		var me = this,
			win = me.showLogWindow(),
			store = me.getAuditLogWindowGrid().getStore();

		store.clearFilter(true);

		store.getProxy().extraParams = { };

		store.filter([
			{
				property: 'pid',
				value: pid
			},
			{
				property: 'event',
				value: event
			}
		]);
	},

	showLogWindow: function(){
		if(!this.getAuditLogWindow()){
			Ext.create('App.view.administration.AuditLogWindow');
		}
		return this.getAuditLogWindow().show();
	}

});