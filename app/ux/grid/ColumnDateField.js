Ext.define('App.ux.grid.ColumnDateField', {
	extend: 'Ext.form.field.Date',
	xtype: 'columndatefield',
	// triggerCls: 'x-form-clear-trigger',
	operator: '=',
	prefix: '',
	suffix: '',
	enableKeyEvents: true,
	initComponent:function(){

		var me = this;

		if(me.autoSearch !== true){
			me.trigger2Cls = 'x-form-search-trigger';
		}

		me.setFilterBuffer = Ext.Function.createBuffered(me.setFilter, 500, me);

		me.callParent(arguments);

		// me.on('render', function() {
		// 	var mee = this;
		// 	mee.ownerCt.on('resize', function () {
		// 		mee.setWidth(this.getEl().getWidth());
		// 	});
		// });
		//
		// me.on('change', function() {
		// 	if(me.autoSearch){
		// 		me.setFilterBuffer(me.up().dataIndex, me.getValue());
		// 	}
		// });
		//
		// me.on('keyup', function(f, e) {
		// 	if(e.getKey() === e.ENTER) {
		// 		me.setFilterBuffer(me.up().dataIndex, me.getValue());
		// 	}
		// });

	},

	// onTriggerClick: function() {
	// 	this.setValue('');
	// 	this.setFilter(this.up().dataIndex, undefined);
	// },

	onTrigger2Click: function() {
		this.setFilter(this.up().dataIndex, this.getValue());
	},

	setFilter: function(filterId, value){
		var store = this.up('grid').getStore();
		if(value){
			store.removeFilter(filterId, false);
			var filter = {
				id: filterId,
				property: filterId,
				operator: this.operator,
				value: this.prefix + value + this.suffix
			};
			if(this.anyMatch) filter.anyMatch = this.anyMatch;
			if(this.caseSensitive) filter.caseSensitive = this.caseSensitive;
			if(this.exactMatch) filter.exactMatch = this.exactMatch;
			if(this.operator) filter.operator = this.operator;
			store.addFilter(filter)
		} else {
			store.filters.removeAtKey(filterId);
			store.reload()
		}
	}
});