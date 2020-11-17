Ext.define('App.controller.Labels', {
    extend: 'Ext.app.Controller',
    requires: [
    ],
    refs: [],

    browserHelperCtl: undefined,

    init: function () {
        var me = this;

        me.control({
            '#PatientSummaryImagesPanel': {
                beforerender: me.onPatientSummaryImagesPanelBeforeRender
            }
        });
    },

    onPatientSummaryImagesPanelBeforeRender: function (panel) {

        var me = this;

        panel.addDocked({
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
                // {
                //     xtype: 'button',
                //     text: '2 5/16 x 4',
                //     icon: 'modules/worklist/resources/images/barcode.png',
                //     action: '2.3125-4-5',
                //     flex: 1,
                //     scope: me,
                //     handler: me.onLabelBtnHandler
                // },
                // '-',
                {
                    xtype: 'button',
                    text: 'Postal Label 1 1/8 x 3 1/2',
                    icon: 'modules/worklist/resources/images/barcode.png',
                    action: '1.1250-3.5-1',
                    flex: 1,
                    scope: me,
                    handler: me.onLabelBtnHandler
                }
            ]
        },0);
    },

    onLabelBtnHandler: function (btn) {

        var size = btn.action.split('-'),
            patient_record = btn.up('form').getForm().getRecord(),
            label_data = this.getDemographicsLabelsData(patient_record);

        if(label_data === false){
            app.msg(_('oops'), _('no_order_selected'), true);
            return
        }

        Labels.CreateLabel('patient', label_data, size[0], size[1], 300, function (result) {

            var images = [],
                width = 0,
                height = 0;

            if(width === 0)	width += result.width/3;
            height += result.height/3;
            images.push({
                xtype:'image',
                margin: 2,
                base64data: result.base64data,
                src: ('data:image/jpg;base64,' + result.base64data),
                width: result.width/3,
                height: result.height/3
            });

                height = height + 4;

            width = width + 10;
            height =  height < 250 ? (height + 62) : 350;

            Ext.create('Ext.window.Window', {
                title: _('patient') + ' ' + _('postal_address'),
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                items: images,
                autoScroll: true,
                width: width,
                height: height,
                buttons: [
                    {
                        xtype: 'printerscombo',
                        itemId: 'WorkListOrderPrintersCombo',
                    },
                    '->',
                    {
                        text: _('print'),
                        itemId: 'WorkListOrderPrintBtn'
                    }
                ]
            }).show();

        });
    },

    getDemographicsLabelsData:  function(patient_record){

        if(!patient_record){
            return false;
        }

        var patient_age_yrs = app.calculateAge(app.getDate(), patient_record.get('DOB'));

        say(patient_record);

        return {
            '[PATIENT_NAME]': Ext.String.format('{0}, {1} {2}', patient_record.get('lname'), patient_record.get('fname'), patient_record.get('mname')).trim(),
            '[RECORD_NUMBER]': patient_record.get('pubpid'),
            '[PATIENT_AGE]': (patient_age_yrs + ' yrs'),
            '[PATIENT_SEX]': patient_record.get('sex'),
            '[PATIENT_POSTAL_ADDRESS1]': patient_record.get('postal_address'),
            '[PATIENT_POSTAL_ADDRESS2]': patient_record.get('postal_address_cont'),
            '[PATIENT_POSTAL_CITY]': patient_record.get('postal_city'),
            '[PATIENT_POSTAL_STATE]': patient_record.get('postal_state'),
            '[PATIENT_POSTAL_ZIP]': patient_record.get('postal_zip')
        };

    },

});
