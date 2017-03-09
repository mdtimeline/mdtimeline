Ext.define('App.ux.LiveProviderMultipleSearch', {
    extend: 'App.ux.form.fields.BoxSelect',
    xtype: 'liveprovidermultiple',
    validateOnChange: false,
    validateOnBlur: false,
    allowBlank: true,
    editable: true,
    typeAhead: false,
    autoSelect: false,
    //triggerOnClick: false,
    createNewOnEnter: true,
    createNewOnBlur: true,
    emptyText: _('select_provider')+'...',
    queryMode: 'remote',
    labelTpl: '{option_name}',
    forceSelection: false,
    displayField: 'option_name',
    valueField: 'id',
    pageSize: 25,
    enableReset: true,
    initComponent: function(){
        var me = this,
            model = 'ActiveProvidersMultipleModel' + me.id;

        if(me.enableReset){
	        me.trigger2Cls = 'x-form-clear-trigger';
	        me.onTrigger2Click = function() {
		        me.reset();
	        }
        }

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'id',
                    type: 'int'
                },
                {
                    name: 'title',
                    type: 'string'
                },
                {
                    name: 'fname',
                    type: 'string'
                },
                {
                    name: 'mname',
                    type: 'string'
                },
                {
                    name: 'lname',
                    type: 'string'
                },
                {
                    name: 'fullname',
                    type: 'string',
                    convert: function(v, record){
                        return record.data.title + ' ' + record.data.lname + ', ' + record.data.fname + ' ' + record.data.mname;
                    }
                },
                {
                    name: 'option_name',
                    type: 'string',
                    convert: function(v, record){
                        return record.data.title + ' ' + record.data.lname + ', ' + record.data.fname + ' ' + record.data.mname;
                    }
                },
                {
                    name: 'option_value',
                    type: 'int',
                    convert: function(v, record){
                        return record.data.id;
                    }
                }
            ],
            proxy: {
                idProperty: 'ConceptId',
                type: 'direct',
                api: {
                    read: 'User.getActiveProviders'
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'ActiveProvidersMultipleModel' + me.id,
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
