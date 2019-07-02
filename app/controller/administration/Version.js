Ext.define('App.controller.administration.Version', {
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

        me.v_major = null;
        me.v_minor = null;
        me.v_patch = null;
        me.v_notes_url = null;
        me.version = null;

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

    	var  me = this;

        Version.getLatestUpdate(function (lastUpdate) {

            me.v_major = lastUpdate.v_major;
            me.v_minor = lastUpdate.v_minor;
            me.v_patch = lastUpdate.v_patch;
            me.v_notes_url = lastUpdate.v_notes_url;

            me.version = me.v_major.toString() + '.' + me.v_minor.toString() + '.' + me.v_patch.toString();

            if (me.v_major !== null && me.v_minor !== null && me.v_patch !== null && me.v_notes_url !== null){
                Version.getUpdateAcknowledge(me.version, app.user.id, function (acknowledge) {
                    say(acknowledge);
                    if (!acknowledge) {
                        me.showUpdateNotesWindow();
                        me.getUpdateNotesWindowIframe().setSrc(me.v_notes_url);
                    }
                })
            }
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

        Version.setUpdateAcknowledge(me.version,app.user.id,function (response) {
            me.getUpdateNotesWindow().close();
        })

    }

    // getLastUpdate: function () {
    //     var me = this;
    //
    //     Version.getLatestUpdate(function (lastUpdate) {
    //         me.version = lastUpdate.version;
    //         me.url = lastUpdate.url;
    //         if(me.version !== false || me.version != null || me.url.length !== 0 || me.url !== null){}
    //         Version.getUpdateAcknowledge(lastUpdate.id, app.user.id, function (acknowledge) {
    //             me.lastUpdateId = lastUpdate.id;
    //             if (!acknowledge) {
    //                 me.showUpdateNotesWindow();
    //                 me.getUpdateNotesWindowIframe().setSrc(me.url);
    //             }
    //         })
    //     });
    // }
});
