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
	width: 500,
    height: 500,
	flex: 1,
    modal: true,
	layout: 'fit',
    bodyMargin: 10,
	items: [
        {
            xtype: 'textarea',
            name: 'gitterresult',
            grow: true,
            anchor: '100%',
            itemId: 'GitterResult',
            readOnly: true
        }
    ],


    buttons: [
        {
            xtype: 'button',
            iconCls: 'icoClose',
            text: _('close'),
            itemId: 'UpdateWindowButtonCloseBtn'
        },
        '-'

    ]
});