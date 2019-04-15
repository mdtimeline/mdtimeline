Ext.define('App.ux.combo.Ethnicity', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.ethnicitycombo',
	store: Ext.create('Ext.data.Store', {
		fields: [
			{name: 'code', type: 'string'},
			{name: 'code_type', type: 'string'},
			{name: 'code_description', type: 'string'},
			{
				name: 'indent_index',
				type: 'int',
				convert: function (v) {
					var str = '';
					while(v > 0){ str += '----'; v--; }
					return str.trim();
				}
			}
		],
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url: 'resources/code_sets/HL7v3-Ethnicity.json',
			reader: {
				type: 'json'
			}
		},
		listeners: {
			load: function () {
				this.insert(0, [{code: 'ASKU', code_type: 'CDA', code_description: 'Declined to specify' }]);

			}
		}
	}),
	tpl: Ext.create('Ext.XTemplate',
		'<tpl for=".">',
		'<div class="x-boundlist-item">{indent_index} <b>{code_description}</b> [{code}]</div>',
		'</tpl>'
	),
	typeAhead: true,
	typeAheadDelay: 50,
	queryMode: 'local',
	displayField: 'code_description',
	valueField: 'code',
	emptyText: _('ethnicity'),
	initComponent: function () {
		var me = this;

		me.callParent(arguments);
	}
});
