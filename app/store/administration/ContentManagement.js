/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:35 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.store.administration.ContentManagement', {
    extend: 'Ext.data.Store',
    requires: ['App.model.administration.ContentManagement'],
    model: 'App.model.administration.ContentManagement'
});