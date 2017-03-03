Ext.define('App.ux.LiveSnomedProblemMultipleSearch', {
    extend: 'App.ux.form.fields.BoxSelect',
    xtype: 'livesnomedproblemmultiple',
    validateOnChange: false,
    validateOnBlur: false,
    allowBlank: true,
    editable: true,
    typeAhead: false,
    autoSelect: false,
    //triggerOnClick: false,
    createNewOnEnter: true,
    createNewOnBlur: true,
    emptyText: _('problem_search') + '...',
    queryMode: 'remote',
    labelTpl: '{FullySpecifiedName} &#60;{ConceptId}&#62;',
    forceSelection: false,
    displayField: 'FullySpecifiedName',
    valueField: 'ConceptId',
    pageSize: 25,
    listConfig: {
        tpl: [
            '<tpl for=".">',
            '<div role="option" class="x-boundlist-item">({CodeType}) {FullySpecifiedName}  &#60;{ConceptId}&#62;</div>',
            '</tpl>'
        ]
    },
    initComponent: function(){
        var me = this,
            model = 'SnomedProblemsMultipleSearchModel' + me.id;

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'ConceptId',
                    type: 'string'
                },
                {
                    name: 'FullySpecifiedName',
                    type: 'string'
                },
                {
                    name: 'CodeType',
                    type: 'string'
                },
                {
                    name: 'OCCURRENCE',
                    type: 'int'
                }
            ],
            proxy: {
                idProperty: 'ConceptId',
                type: 'direct',
                api: {
                    read: 'SnomedCodes.liveProblemCodeSearch'
                },
                reader: {
                    totalProperty: 'totals',
                    root: 'data'
                }
            }
        });

        me.store = Ext.create('Ext.data.Store', {
            model: 'SnomedProblemsMultipleSearchModel' + me.id,
            pageSize: 25,
            autoLoad: false
        });

        Ext.apply(this, {
            store: me.store,
            listConfig: {
                loadingText: _('searching') + '...',
                getInnerTpl: function(){
                    return '<div class="search-item"><h3>{FullySpecifiedName}<span style="font-weight: normal"> ({ConceptId}) </span></h3></div>';
                }
            },
            pageSize: 25
        });

        me.callParent();
    }
});
