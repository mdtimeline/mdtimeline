/**
 * Created by JetBrains PhpStorm.
 * User: Ernesto J. Rodriguez (Certun)
 * File:
 * Date: 10/29/11
 * Time: 4:45 PM
 */
Ext.define('App.ux.combo.Users', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.userscombo',
	acl: null,
	includeAllOption: false,
	editable: false,
	queryMode: 'local',
	valueField: 'id',
	displayField: 'name',
	emptyText: _('select'),

	initComponent: function(){
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			autoLoad: true,
			fields: [
				{name: 'id', type: 'int'},
				{name: 'name', type: 'string'}
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'CombosData.getUsers'
				},
				extraParams: {
					acl: me.acl,
					includeAllOption: me.includeAllOption
				}
			}
		});

		me.callParent();
	}
})
;