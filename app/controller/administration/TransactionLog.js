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

Ext.define('App.controller.administration.TransactionLog', {
    extend: 'Ext.app.Controller',

    refs: [
        {
            ref:'TransactionLogPanel',
            selector:'#TransactionLogPanel'
        },
        {
            ref:'TransactionLogFilterFormPanel',
            selector:'#TransactionLogFilterFormPanel'
        },
        {
            ref:'TransactionLogDataGrid',
            selector:'#TransactionLogDataGrid'
        },
        {
            ref:'TransactionLogFilterPanel',
            selector:'#TransactionLogFilterPanel'
        },

	    {
		    ref:'TransactionLogWindow',
		    selector:'#TransactionLogWindow'
	    },
	    {
		    ref:'TransactionLogDetailLogGrid',
		    selector:'#TransactionLogDetailLogGrid'
	    }
    ],

    init: function() {
        var me = this;

        me.control({
            '#TransactionLogFilterSearchBtn':{
                click: me.onTransactionLogFilterSearchBtnClick
            },
            '#TransactionLogDataGrid':{
                itemdblclick: me.onTransactionLogDataGridItemDblClick
            }
        });

        me.clockCtrl = me.getController('Clock');

    },

	onTransactionLogFilterSearchBtnClick: function() {

		var form = this.getTransactionLogFilterFormPanel().getForm(),
			store = this.getTransactionLogDataGrid().getStore(),
			values = form.getValues();

		if (!form.isValid()) return;

		store.getProxy().extraParams = { filters: values };
		store.load();
	},

	onTransactionLogDataGridItemDblClick: function (grid, record) {
    	this.doTransactionLogDetailByTableAndPk(record.get('table_name'), record.get('pk'))
	},

	doTransactionLogDetailByTableAndPk: function (table, pk) {
		var me = this;

		TransactionLog.getTransactionLogDetailByTableAndPk(table, pk, function (response) {

			if(response.total == 0) {
				app.msg(_('info'), _('no_record_fund'));
				return;
			}

			me.showTransactionLogWindow();
			me.setTransactionLogDetailGrid(response);

		});
	},

	setTransactionLogDetailGrid: function (response) {


		say('setTransactionLogDetailGrid');
		say(response);

		var me = this,
			model_name = 'App.model.' + Ext.String.capitalize(response.table) + '_TransactionLogDetailModel',
			fields = [
				'_id',
				'_event_time',
				'_event_type',
				'_event_uid',
				'_event_user_title',
				'_event_user_fname',
				'_event_user_mname',
				'_event_user_lname',
				{
					name: '_event_user',
					convert: function (v,rec) {
						return rec.get('_event_user_lname') + ', ' +
							rec.get('_event_user_fname') + ' ' +
							rec.get('_event_user_mname');
					}
				}
			],
			columns = [
				{
					text: _('event_info'),
					locked: true,
					columns: [
						{
							text: 'event_time',
							dataIndex: '_event_time',
							width: 120
						},
						{
							text: 'event_type',
							dataIndex: '_event_type',
							width: 100
						},
						{
							text: 'event_user',
							dataIndex: '_event_user',
							width: 150
						}
					]
				},
				{
					text: _('record'),
					columns: [	]
				}
			];

		fields = Ext.Array.merge(fields, response.columns);

		response.columns.forEach(function (col) {
			columns[1].columns = Ext.Array.push(columns[1].columns, {
				text: col,
				dataIndex: col,
				width: 200
			});
		});

		if(!eval(model_name)){
			Ext.define(model_name, {
				extend: 'Ext.data.Model',
				fields: fields,
				idProperty: '_id'
			});
		}

		var store = Ext.create('Ext.data.Store', {
			model: model_name,
			data: { data: response.data },
			autoLoad: true,
			proxy: {
				type: 'memory',
				reader: {
					type: 'json',
					root: 'data'
				}
			}
		});

		me.getTransactionLogDetailLogGrid().reconfigure(store, columns);
	},

	showTransactionLogWindow: function () {
		if(!this.getTransactionLogWindow()){
			Ext.create('Ext.window.Window', {
				layout: 'fit',
				title: _('transactions'),
				itemId: 'TransactionLogWindow',
				maximizable: true,
				closeAction: 'hide',
				items: [
					{
						xtype:'grid',
						height: 400,
						width: 1000,
						enableLocking: true,
						itemId: 'TransactionLogDetailLogGrid',
						columns: [
							{
								text: '',
								flex: 1
							}
						]
					}
				]
			});
		}
		return this.getTransactionLogWindow().show();
	}
});
