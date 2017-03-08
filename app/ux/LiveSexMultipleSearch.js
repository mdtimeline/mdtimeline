Ext.define('App.ux.LiveSexMultipleSearch', {
    extend: 'App.ux.form.fields.BoxSelect',
    xtype: 'sexmultiple',
    validateOnChange: false,
    validateOnBlur: false,
    allowBlank: true,
    editable: true,
    typeAhead: false,
    autoSelect: false,
    createNewOnEnter: true,
    createNewOnBlur: true,
    emptyText: _('sex_search') + '...',
    queryMode: 'remote',
    forceSelection: false,
    displayField: 'option_name',
    valueField: 'option_value',
    pageSize: 25,
    initComponent: function(){
        var me = this,
            model = 'SexModelMultipleModel' + me.id;

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'option_name', type: 'string' },
                {name: 'option_value', type: 'string' }
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'CombosData.getOptionsByListId'
                },
                extraParams: {
                    list_id: 19
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'SexModelMultipleModel' + me.id,
            pageSize: 25,
            autoLoad: false
        });

        Ext.apply(this, {
            store: me.store,
            listConfig: {
                loadingText: _('searching') + '...'
            },
            pageSize: 25
        });

        me.callParent();
    }
});
