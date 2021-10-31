Ext.define('App.ux.grid.SlidingPagerAndPageSize', {
	requires: [
		'Ext.slider.Single',
		'Ext.slider.Tip'
	],

	/**
	 * Creates new SlidingPager.
	 * @param {Object} config Configuration options
	 */
	constructor : function(config) {
		if (config) {
			Ext.apply(this, config);
		}
	},

	init : function(pbar){
		var me = this,
			idx = pbar.items.indexOf(pbar.child("#inputItem")),
			slider, page_size;

		Ext.each(pbar.items.getRange(idx - 2, idx + 2), function(c){
			c.hide();
		});

		slider = Ext.create('Ext.slider.Single', {
			width: 114,
			minValue: 1,
			maxValue: 1,
			hideLabel: true,
			tipText: function(thumb) {
				return Ext.String.format('Page <b>{0}</b> of <b>{1}</b>', thumb.value, thumb.slider.maxValue);
			},
			listeners: {
				changecomplete: function(s, v){
					pbar.store.loadPage(v);
				}
			}
		});

		page_size = Ext.create('Ext.form.field.ComboBox', {
			width: 120,
			value: pbar.pageSize,
			editable: false,
			fieldLabel: 'Page Size',
			labelWidth: 55,
			labelAlign: 'right',
			store: [
				10,25,50,100,500,1000,2000,4000
			],
			listeners: {
				select: function(c){
					pbar.pageSize = c.getValue();
					pbar.store.pageSize = c.getValue();
					pbar.store.loadPage(1);
				}
			}
		});

		pbar.insert(idx + 1, slider);
		pbar.insert(idx + 6, page_size);

		pbar.on({
			change: function(pb, data){
				slider.setMaxValue(data ? data.pageCount : 1);
				slider.setValue(data ? data.currentPage : 1);
			}
		});
	}

});