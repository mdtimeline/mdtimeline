/**
 * mdTimeLine (Billing Module)
 * Copyright (C) 2018 mdTimeLine.
 *
 */

Ext.define('App.controller.administration.UsersTransaction', {
    extend: 'Ext.app.Controller',
    requires: [

    ],

    refs: [

        //USER TRANSACTIONS WINDOW FORM
        {
            ref: 'UserTransactionWindow',
            selector: '#UserTransactionWindow'
        },
        {
            ref: 'UserTransactionWindowFields',
            selector: '#UserTransactionWindowFields'
        },
        {
            ref: 'UserTransactionWindowCreateDateField',
            selector: '#UserTransactionWindowCreateDateField'
        },
        {
            ref: 'UserTransactionWindowCreateUserNameField',
            selector: '#UserTransactionWindowCreateUserNameField'
        },
        {
            ref: 'UserTransactionWindowUpdateDateField',
            selector: '#UserTransactionWindowUpdateDateField'
        },
        {
            ref: 'UserTransactionWindowButtonCloseBtn',
            selector: '#UserTransactionWindowButtonCloseBtn'
        }

    ],

    init: function(){

        var me = this;

        me.control({

            //WINDOW FORM BUTTONS
            '#UserTransactionWindow': {
                beforerender: me.onUserTransactionWindowBeforeRender
            },
            '#UserTransactionWindowButtonCloseBtn': {
                click: me.onUserTransactionWindowButtonCloseBtnClick
            }

        });
    },

    /**
     * USER TRANSACTIONS WINDOW - FUNCTIONS
     */

    onUserTransactionWindowBeforeRender: function(win){

        var me = this,
            create_date = win.create_date,
            user_username = win.user_username,
            update_date = win.update_date;

        me.getUserTransactionWindowCreateDateField().setValue(create_date);
        me.getUserTransactionWindowCreateUserNameField().setValue(user_username);
        me.getUserTransactionWindowUpdateDateField().setValue(update_date);
    },

    onUserTransactionWindowButtonCloseBtnClick: function(btn){
        var win = btn.up('window');

        win.close();
    }

});