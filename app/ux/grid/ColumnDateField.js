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

		me.on('render', function() {
			var mee = this;
			mee.ownerCt.on('resize', function () {
				mee.setWidth(this.getEl().getWidth());
			});
		});

		me.on('select', function() {
			me.setFilterBuffer(me.up('headercontainer').dataIndex, me.getSubmitValue());
		});

		me.on('keyup', function(f, e) {
			if(e.getKey() === e.ENTER) {
				me.setFilterBuffer(me.up('headercontainer').dataIndex, me.getSubmitValue());
			}
		});

	},

	onTrigger2Click: function() {
		this.setFilter(this.up('headercontainer').dataIndex, this.getSubmitValue());
	},

	setFilter: function(filterId, value){
		var store = this.up('grid').getStore(),
			parentContainer = this.up('container');

		if(parentContainer){

			say('setFilter');
			say(parentContainer);

			var datefields = parentContainer.query('datefield');

			say(datefields);

			if(datefields.length === 2) {
				value = Ext.String.format('{0}~{1}', datefields[0].getSubmitValue(), datefields[1].getSubmitValue());

				say(value);

			}
		}


		if(value){
			store.filters.removeAtKey(filterId);
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