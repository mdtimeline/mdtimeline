/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.administration.ContentManagement', {
    extend: 'Ext.data.Model',
    table: {
        name: 'content_management'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'content_type',
            type: 'string',
            len: 45,
            index: true
        },
        {
            name: 'content_lang',
            type: 'string',
            len: 3,
            index: true
        },
        {
            name: 'content_body',
            type: 'string',
            dataType: 'mediumtext'
        },
        {
            name: 'content_version',
            type: 'string',
            len:10,
            index: true
        }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'ContentManagement.getContentManagements',
            create: 'ContentManagement.addContentManagement',
            update: 'ContentManagement.updateContentManagement',
            destroy: 'ContentManagement.destroyContentManagement'
        },
        reader: {
            root: 'data'
        },
        writer: {
            writeAllFields: true
        },
        remoteGroup: false
    }
});
