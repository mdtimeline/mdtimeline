Ext.define('App.controller.DocumentViewer', {
    extend: 'Ext.app.Controller',
    requires: [
        'App.view.patient.windows.ArchiveDocument'
    ],
    refs: [
        {
            ref: 'DocumentViewerWindow',
            selector: 'documentviewerwindow'
        },
        {
            ref: 'DocumentViewerWindow',
            selector: 'documentviewerwindow > form'
        },
        {
            ref: 'ArchiveDocumentBtn',
            selector: 'documentviewerwindow #archiveDocumentBtn'
        },
        {
            ref: '#documentViewerAddToPrintJobBtn',
            selector: 'documentViewerAddToPrintJobBtn'
        },
        {
            ref: 'ArchiveWindow',
            selector: 'patientarchivedocumentwindow'
        },
        {
            ref: 'ArchiveForm',
            selector: 'patientarchivedocumentwindow > form'
        },
        {
            selector: '#ArchiveDocumentOptionalFieldSet',
            ref: 'ArchiveDocumentOptionalFieldSet'
        }
    ],

    init: function () {
        var me = this;

        me.control({
            'documentviewerwindow': {
                close: me.onViewerDocumentsWinClose
            },
            'documentviewerwindow #archiveDocumentBtn': {
                click: me.onArchiveDocumentBtnClick
            },
            '#documentViewerAddToPrintJobBtn': {
                click: me.onDocumentViewerAddToPrinterJobBtn
            },
            'patientarchivedocumentwindow #archiveBtn': {
                click: me.onArchiveBtnClick
            }
        });
    },

    onArchiveBtnClick: function (btn) {
        var win = btn.up('window'),
            form = win.down('form').getForm(),
            values = form.getValues(),
            docTypeField = form.findField('docTypeCode'),
            // printerCmb = form.findField('printer'),
            // printerRecord = printerCmb.findRecordByValue(printerCmb.getValue()),
            priority = form.findField('priority').getValue(),
            // printNow = form.findField('printNow').getValue(),
            printNow = false,
            number_of_copies = form.findField('number_of_copies').getValue();

        if (form.isValid()) {
            values.pid = app.patient.pid;
            values.eid = app.patient.eid;
            values.uid = app.user.id;

            var docTypeRecord = docTypeField.findRecordByValue(values.docTypeCode);
            values.docType = docTypeRecord.get('option_name');

            // scanner archive logic
            if (Ext.getClassName(win.documentWindow) === 'App.view.scanner.Window') {

                var controller = app.getController('Scanner');
                controller.doArchive(values, function (success) {
                    if (success) win.close();
                });

            } else {
                DocumentHandler.transferTempDocument(values, function (response) {
                    if (response.success) {
                        if (window.dual) {
                            window.dual.msg(_('sweet'), 'document_transferred');
                        } else {
                            window.app.msg(_('sweet'), 'document_transferred');
                        }

                        if (win.doAddPrintJobCallback) {
                            win.doAddPrintJobCallback(response.record,null,0,priority,number_of_copies);
                        }
                        win.documentWindow.close();
                        win.close();
                    } else {
                        if (window.dual) {
                            window.dual.msg(_('oops'), 'document_transfer_failed', true);
                        } else {
                            window.app.msg(_('oops'), 'document_transfer_failed', true);
                        }
                    }
                });
            }
        }
    },

    onArchiveDocumentBtnClick: function (btn) {
        var win = btn.up('window'),
            values = {
                id: win.documentId,
                docType: win.documentType,
                title: win.documentType + ' ' + _('order')
            };
        var archive_window = Ext.widget('patientarchivedocumentwindow', {
            documentWindow: win,
            doAddPrintJobCallback: undefined
        });

        var form = archive_window.down('form').getForm();
        form.setValues(values);

        //optional fieldset not visible
        archive_window.down('fieldset').setVisible(false);
    },

    onDocumentViewerAddToPrinterJobBtn: function (btn) {
        var win = btn.up('window'),
            values = {
                id: win.documentId,
                docType: win.documentType,
                title: win.documentType + ' ' + _('order')
            };
        var archive_window = Ext.widget('patientarchivedocumentwindow', {
            documentWindow: win,
            doAddPrintJobCallback: this.doAddPrintJob
        });

        archive_window.down('form').getForm().setValues(values);
    },

    doAddPrintJob: function (document, printer_record, print_now, priority, number_of_copies) {
        app.getController('PrintJob').addPrintJob(document.id,printer_record, print_now, priority, number_of_copies);
    },

    onViewerDocumentsWinClose: function (win) {
        DocumentHandler.destroyTempDocument({id: win.documentId});
    },

    doDocumentView: function (id, type, site, closable) {

        var windows = Ext.ComponentQuery.query('documentviewerwindow'),
            src = 'dataProvider/DocumentViewer.php?site=' + (site || app.user.site) + '&id=' + id + '&token=' + app.user.token,
            win;

        if (typeof type != 'undefined') src += '&temp=' + type;

        src += '&_dc=' + Ext.Date.now();

        win = Ext.create('App.view.patient.windows.DocumentViewer', {
            documentType: type,
            documentId: id,
            closable: (closable !== undefined ? closable : true),
            items: [
                {
                    xtype: 'miframe',
                    autoMask: false,
                    src: src
                }
            ]
        });

        if (windows.length > 0) {
            var last = windows[(windows.length - 1)];
            for (var i = 0; i < windows.length; i++) {
                windows[i].toFront();
            }
            win.showAt((last.x + 25), (last.y + 5));

        } else {
            win.show();
        }

        return win;
    }


});