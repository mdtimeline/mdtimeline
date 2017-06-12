Ext.define('App.ux..grid.ColumnSearchField', {
	extend: 'Ext.form.field.Trigger',
	xtype: 'columnsearchfield',
	triggerCls: 'x-form-clear-trigger',
	operator: '=',
	prefix: '',
	suffix: '',
	initComponent:function(){

		say(this.autoSearch);

		if(this.autoSearch !== true){
			this.trigger2Cls = 'x-form-search-trigger';
		}

		this.setFilterBuffer = Ext.Function.createBuffered(this.setFilter, 500, this);

		this.callParent();
	},

	onTriggerClick: function() {
		this.setValue('');
		this.setFilter(this.up().dataIndex, undefined);
	},

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
	},
	listeners: {
		render: function(){
			var me = this;
			me.ownerCt.on('resize', function(){
				me.setWidth(this.getEl().getWidth());
			})
		},
		change: function() {
			if(this.autoSearch){
				this.setFilterBuffer(this.up().dataIndex, this.getValue());
			}
		}
	}
});