Ext.define('App.controller.Print', {
    extend: 'Ext.app.Controller',
    requires: [
        'App.ux.combo.Printers'
    ],
    refs: [],

    printers: [],

    browserHelperCtl: undefined,

    init: function () {
        var me = this;

        me.control({
            'viewport': {
                browserhelperopen: me.onBrowserHelperOpen
            },
            'printerscombo': {
                render: me.onPrintersComboBeforeRender
            }
        });

        me.loadRemotePrinters();

    },

    doPrint: function (printer_record, base64data) {
        var me = this;

        if (printer_record.get('local')) {
            if (!me.browserHelperCtl) {
                app.msg(_('oops'), _('no_browser_helper_found'), true);
                return;
            }

            me.browserHelperCtl.send('{"action":"print", "printer": "' + printer_record.get('id') + '", "payload": "' + base64data + '"}', function (response) {
                say(response);
            });
        } else {
            Printer.doPrint(printer_record.get('id'), base64data);
        }
    },

    loadRemotePrinters: function () {
        var me = this;

        Printer.getPrinters(function (printers){
            me.printers = Ext.Array.merge(me.printers, printers);
        });
    },

    onBrowserHelperOpen: function (ctl) {
        var me = this;

        me.browserHelperCtl = ctl;

        say('onBrowserHelperOpen');

        me.browserHelperCtl.send('{"action":"printer/list"}', function (printers) {
            //Add local property to each printer
            for (var i = 0; i < printers.length; i++) {
                printers[i].local = true;
            }

            me.printers = Ext.Array.merge(me.printers, printers);
        });

    },

    onPrintersComboBeforeRender: function (cmb) {
        cmb.store.loadData(this.printers);

        //register combobox to change when facility changes
        app.on('appfacilitychanged', function () {
            cmb.showPrintersByFacility();
            if (cmb.store.getCount() > 0) {
                cmb.setValue(cmb.store.first());
            } else {
                cmb.setValue(null);
            }
        });

        //filter printers by facility and local
        cmb.showPrintersByFacility();

        if (cmb.store.find('id', cmb.getValue()) === cmb.getValue()) {
            // fix to stateful not selecting
            cmb.setValue(cmb.getValue());
        } else {
            cmb.store.getCount() > 0 ? cmb.setValue(cmb.store.first()) : cmb.setValue(null);
        }

    },

    promptPrint: function () {

    }


});
