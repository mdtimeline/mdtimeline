/**
 * mdTimeLine (Billing Module)
 * Copyright (C) 2018 mdTimeLine.
 *
 */

Ext.define('App.view.administration.UserTransactionWindow', {
	extend: 'Ext.window.Window',
	requires: [
        'Ext.grid.Panel',
        'Ext.dd.DropTarget',
        'App.ux.grid.RowFormEditing',
        'Ext.grid.plugin.RowExpander',
        'Modules.billing.ux.LiveBillingPaymentAdjustmentConceptSearch'
    ],

	title: _('users'),
	itemId: 'UserTransactionWindow',
	width: 500,
	flex: 1,
    modal: true,
	layout: {
		type: 'hbox',
		align: 'stretch'
	},

	items: [
        {
            xtype: 'form',
            itemId: 'UserTransactionWindowFields',
            frame: true,
            flex: 1,
            bodyPadding: 5,
            layout: {
                type:'vbox',
                align: 'stretch'
            },

            defaults: {
                margin: '10 10 10 10',
                anchor: '100%',
                labelAlign: 'top',
                labelWidth: 30
            },

            items: [


                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    defaults: {
                        margin: '10 10 10 10',
                        anchor: '100%',
                        labelAlign: 'right',
                        labelWidth: 50
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'create_date',
                            fieldLabel: _('create'),
                            format: 'm-d-Y H:i:s',
                            width: 200,
                            itemId: 'UserTransactionWindowCreateDateField',
                            readOnly: true,
                            timeConfig: {
                                increment: 60
                            }
                        },
                        {
                            xtype: 'textfield',
                            name: 'user_username',
                            fieldLabel: _('user'),
                            itemId: 'UserTransactionWindowCreateUserNameField',
                            hideTrigger: false,
                            hideLabel: false,
                            readOnly: true,
                            flex: 1
                        }
                    ]
                },



                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    defaults: {
                        margin: '0 10 10 10',
                        anchor: '100%',
                        labelAlign: 'right',
                        labelWidth: 50
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'update_date',
                            fieldLabel: _('update'),
                            format: 'm-d-Y H:i:s',
                            width: 200,
                            itemId: 'UserTransactionWindowUpdateDateField',
                            readOnly: true,
                            timeConfig: {
                                increment: 60
                            }
                        }
                    ]
                }

                // {
                //     xtype: 'container',
                //     layout: {
                //         type: 'hbox',
                //         align: 'stretch'
                //     },
                //     defaults: {
                //         margin: '0 10 10 10',
                //         anchor: '100%',
                //         labelAlign: 'right',
                //         labelWidth: 50
                //     },
                //     items: [
                //         {
                //             xtype: 'textfield',
                //             name: 'approve_username',
                //             fieldLabel: _('approve'),
                //             itemId: 'UserLogWindowApproveUserNameField',
                //             hideTrigger: false,
                //             hideLabel: false,
                //             disabled: true,
                //             flex: 1
                //         }
                //     ]
                // }







            ]

        }
    ],


    buttons: [
        {
            xtype: 'button',
            iconCls: 'icoClose',
            text: _('close'),
            itemId: 'UserTransactionWindowButtonCloseBtn'
        },
        '-'

    ]
});