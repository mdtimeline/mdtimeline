/**
 * @class App.ux.grid.exporter.Button
 * @extends Ext.Button
 * @author Nige White, with modifications from Ed Spencer, with modifications from iwiznia with modifications from yogesh
 * Internally, this is just a link.
 * Pass it either an Ext.Component subclass with a 'store' property, or componentQuery of that component or just a store or nothing and it will try to grab the first parent of this button that is a grid or tree panel:
 * new App.ux.grid.exporter.ExporterButton({component: someGrid});
 * new App.ux.grid.exporter.ExporterButton({store: someStore});
 * new App.ux.grid.exporter.ExporterButton({component: '#itemIdSomeGrid'});
 * @cfg {Ext.Component} component The component the store is bound to
 * @cfg {Ext.data.Store} store The store to export (alternatively, pass a component with a getStore method)
 */
Ext.define("App.ux.grid.exporter.ExporterButton", {
    extend: "Ext.Button",
    requires: ['App.ux.grid.exporter.Exporter', 'App.ux.grid.exporter.FileSaver'],
    alias: "widget.exporterbutton",
    
    /**
     * @cfg {String} text
     * The button text to be used as innerHTML (html tags are accepted).
     */
    text: 'Download',

    /**
     * @cfg {String} format
     * The Exported File formatter 
     */
    format: 'csv',
    
    /**
     * @cfg {Boolean} preventDefault
     * False to allow default action when the {@link #clickEvent} is processed.
     */
    preventDefault: false,
    
    /**
     * @cfg {Number} saveDelay
     * Increased buffer to avoid clickEvent fired many times within a short period.
     */
    saveDelay: 300,
    
    //iconCls: 'save',
    
    /**
     * @cfg {Boolean} remote
     * To remotely download file only if browser doesn't support locally 
     * otherwise it will try to open in new window
     */
    remote: false,
    /**
     * @cfg {String} title
     * To set name to eported file, extension will be appended based on format  
     */
    title: 'export',
    
    constructor: function(config) {
        var me = this;
        
        App.ux.grid.exporter.ExporterButton.superclass.constructor.call(me, config);
        
        me.on("afterrender", function() { //wait for the button to be rendered, so we can look up to grab the component
            if (me.component) {
                me.component = !Ext.isString(me.component) ? me.component : Ext.ComponentQuery.query(me.component)[0];
            }
            me.setComponent(me.store || me.component || me.up("gridpanel") || me.up("treepanel"), config);
        });
    },
    
    onClick: function(e) {

	    var me = this;

	    var blobURL = "",
		    format = me.format,
		    title = me.title,
		    remote = me.remote,
		    dt = new Date(),
		    link = me.el.dom,
		    res, filename;

	    me.fireEvent('start', me);
	    res = App.ux.grid.exporter.Exporter.exportAny(me.component, format, { title : title });
	    filename = title + "_" + Ext.Date.format(dt, "Y-m-d h:i:s") + "." + res.ext;
	    App.ux.grid.exporter.FileSaver.saveAs(res.data, res.mimeType, res.charset, filename, link, remote, me.onComplete, me);
	    me.callParent(arguments);
    },

    setComponent: function(component, config) {
        var me = this;
        me.component = component;
        me.store = !component.is ? component : component.getStore(); // only components or stores, if it doesn't respond to is method, it's a store        
    },

    onComplete: function() {
        this.fireEvent('complete', this);
    }
});
