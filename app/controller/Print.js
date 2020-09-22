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

        me.printJobCtl = this.getController('PrintJob');

        me.loadRemotePrinters();

    },

    doPrint: function (printer_record, document_base64, job_id) {
        var me = this;

        if (printer_record.get('local')) {
            if (!me.browserHelperCtl) {
                app.msg(_('oops'), _('no_browser_helper_found'), true);
                return;
            }

            job_id = job_id || "";

            me.browserHelperCtl.send('{"action":"print", "printer": "' + printer_record.get('id') + '", "payload": "' + document_base64 + '", "job_id": ' + job_id + '}', function (response) {
                if (!response.success) {
                    app.msg(_('oops'), 'Document could not be printed', true);
                }
            });
        } else {
            Printer.doPrint(printer_record.get('id'), document_base64, job_id);
        }
    },

    addPrintJob: function (document_id, printer_record) {
        var me = this;
        me.printJobCtl.addPrintJob(document_id, printer_record);
    },

    loadRemotePrinters: function () {
        var me = this;

        Printer.getPrinters(function (printers) {
            me.printers = Ext.Array.merge(me.printers, printers);
        });
    },

    onBrowserHelperOpen: function (ctl) {
        var me = this;

        me.browserHelperCtl = ctl;

        me.browserHelperCtl.send('{"action":"printer/list"}', function (printers) {
            //Add local property to each printer
            for (var i = 0; i < printers.length; i++) {
                printers[i].local = true;
            }

            me.printers = Ext.Array.merge(me.printers, printers);
        });

    },

    onPrintersComboBeforeRender: function (cmb) {
        say(this.printers);
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

    getPrinters: function () {
        var me = this;

        return me.printers;
    },

    promptPrint: function () {

    }


});
