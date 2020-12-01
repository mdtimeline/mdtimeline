Ext.define('App.ux.combo.ActiveSpecialties', {
	extend: 'Ext.form.ComboBox',
	xtype: 'activespecialtiescombo',
	displayField: 'title',
	valueField: 'id',
	editable: false,
	emptyText: _('select'),
	queryMode: 'local',
	includeAllOption: false,
	initComponent: function(){
		var me = this;

		this.store = Ext.create('App.store.administration.Specialties',{
			pageSize: 500,
			autoLoad: true,
			listeners: {
				load: function (){
					if(me.includeAllOption){
						me.store.insert(0,{
							id: -1,
							title: 'ALL',
							combo_text: 'ALL',
							active: true
						});
						me.setValue(-1);
					}
				}
			}
		});

		this.store.clearFilter(true);
		this.store.filter([
			{
				property: 'active',
				value: true
			}
		]);
		this.callParent();
	}

});