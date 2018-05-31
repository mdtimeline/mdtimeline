/**
 * Created by JetBrains PhpStorm.
 * User: Ernesto J. Rodriguez (Certun)
 * File:
 * Date: 10/29/11
 * Time: 4:45 PM
 */
Ext.define('App.ux.combo.LayoutForms', {
	extend: 'Ext.form.ComboBox',
	xtype: 'layoutformscombo',
	editable: false,
	valueField: 'id',
	displayField: 'name',
	emptyText: _('select'),
	initComponent: function(){
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			autoLoad: true,
			model : 'App.model.administration.FormsList'
		});

		me.callParent();
	}
});
