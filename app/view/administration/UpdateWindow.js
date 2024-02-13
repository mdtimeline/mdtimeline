/**
 * mdTimeLine (Billing Module)
 * Copyright (C) 2018 mdTimeLine.
 *
 */

Ext.define('App.view.administration.UpdateWindow', {
	extend: 'Ext.window.Window',
	requires: [
        'Ext.grid.Panel',
        'Ext.dd.DropTarget',
        'App.ux.grid.RowFormEditing',
        'Ext.grid.plugin.RowExpander'
    ],

	title: _('update'),
	itemId: 'UpdateWindow',
	width: 1200,
    height: 500,
	flex: 1,
    modal: true,
	layout: 'fit',
    bodyMargin: 10,
    initComponent: function() {
        var me = this;

        me.updateGridStore = Ext.create('App.store.administration.UpdateScript');

        // me.updateGridStore =  Ext.create('App.store.administration.AuditLogs',{
        //     remoteFilter: true
        // });

        me.items = [
            {
                xtype: 'textarea',
                name: 'gitterresult',
                grow: true,
                anchor: '100%',
                itemId: 'GitterResult',
                readOnly: true,
                hidden: false
            },
            {
                xtype: 'grid',
                itemId: 'AdminUpdateScriptGrid',
                title: 'Update Scripts',
                flex: 1,
                hidden: true,
                store: me.updateGridStore,
                selType: 'checkboxmodel',
                columns: [
                    {
                        text: 'Module',
                        dataIndex: 'module'
                        //flex: 1,
                    },
                    {
                        text: 'Version',
                        dataIndex: 'version'
                        //flex: 1,
                    },
                    {
                        xtype: 'datecolumn',
                        text: 'Timestamp',
                        dataIndex: 'timestamp',
                        width: 150,
                        format: 'Y-m-d H:i:s'
                    },
                    {
                        text: 'Script',
                        dataIndex: 'script',
                        flex: 1,
                    }
                ],
                bbar: {
                    xtype: 'pagingtoolbar',
                    pageSize: 25,
                    store: me.updateGridStore,
                    plugins: Ext.create('Ext.ux.SlidingPager'),
                    listeners: {
                        afterrender : function() {
                            this.child('#refresh').hide();
                        }
                    }
                }
            }
        ];

        me.callParent(arguments);
    },


    buttons: [
        {
            xtype: 'button',
            iconCls: 'icoClose',
            text: _('close'),
            itemId: 'UpdateWindowButtonCloseBtn'
        },
        '-',
        {
            xtype: 'button',
            iconCls: 'icoAdd',
            text: _('execute'),
            itemId: 'UpdateWindowButtonExecuteScriptBtn',
            hidden: true
        },
    ]
});