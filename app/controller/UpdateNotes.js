Ext.define('App.controller.UpdateNotes', {
    extend: 'Ext.app.Controller',
    requires: [
        'App.ux.ManagedIframe'
    ],
    refs: [
        {
            ref: 'UpdateNotesWindow',
            selector: '#UpdateNotesWindow'
        },
        {
            ref: 'UpdateNotesWindowDontShowAgainBtn',
            selector: '#UpdateNotesWindowDontShowAgainBtn'
        },
        {
            ref: 'UpdateNotesWindowIframe',
            selector: '#UpdateNotesWindow miframe'
        }

    ],

    init: function () {
        var me = this;

        me.lastUpdateId = null;
        me.version = null;
        me.url = null;

        me.control({
            'viewport': {
                render: me.onViewportRender
            },
            '#UpdateNotesWindowDontShowAgainBtn': {
                click: me.onUpdateNotesWindowDontShowAgainBtn
            }
        });
    },

    onViewportRender: function(){
        UpdateNotes.getLatestUpdate(function (lastUpdate) {
            me.version = lastUpdate.version;
            me.url = lastUpdate.url;
            UpdateNotes.getUpdateAcknowledge(lastUpdate.id, app.user.id, function (acknowledge) {
                me.lastUpdateId = lastUpdate.id;
                if (!acknowledge) {
                    me.showUpdateNotesWindow();
                    me.getUpdateNotesWindowIframe().setSrc(me.url);
                }
            })
        });
    },

    showUpdateNotesWindow: function () {
        var me = this;

        if (!me.getUpdateNotesWindow()) {
            Ext.create('App.view.update_notes.UpdateNotesWindow',{
                title: 'Version ' + me.version
            });
        }
        return me.getUpdateNotesWindow().show();
    },

    onUpdateNotesWindowDontShowAgainBtn: function (btn) {
        var me = this;

        UpdateNotes.setUpdateAcknowledge(this.lastUpdateId,app.user.id,function (response) {
            me.getUpdateNotesWindow().close();
        })

    }

    // getLastUpdate: function () {
    //     var me = this;
    //
    //     UpdateNotes.getLatestUpdate(function (lastUpdate) {
    //         me.version = lastUpdate.version;
    //         me.url = lastUpdate.url;
    //         if(me.version !== false || me.version != null || me.url.length !== 0 || me.url !== null){}
    //         UpdateNotes.getUpdateAcknowledge(lastUpdate.id, app.user.id, function (acknowledge) {
    //             me.lastUpdateId = lastUpdate.id;
    //             if (!acknowledge) {
    //                 me.showUpdateNotesWindow();
    //                 me.getUpdateNotesWindowIframe().setSrc(me.url);
    //             }
    //         })
    //     });
    // }
});
