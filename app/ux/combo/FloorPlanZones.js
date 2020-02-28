Ext.define('App.ux.combo.FloorPlanZones', {
	extend: 'App.ux.combo.ComboResettable',
	xtype: 'floorplanazonescombo',
	editable: false,
	queryMode: 'local',
	displayField: 'title',
	valueField: 'id',
	emptyText: _('all'),
	initComponent: function () {
		this.store = Ext.create('App.store.administration.FloorPlanZones',{
			autoLoad: false,
			remoteFilter: false
		});
		this.callParent();
	}

});