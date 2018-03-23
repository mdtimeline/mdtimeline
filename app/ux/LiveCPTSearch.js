/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.ux.LiveCPTSearch', {
	extend: 'Ext.form.field.ComboBox',
	xtype: 'livecptsearch',
	hideLabel: true,
	triggerTip: _('click_to_clear_selection'),
	spObj: '',
	spForm: '',
	spExtraParam: '',
	displayField: 'code_text_medium',
	valueField: 'code',
	qtip: _('clearable_combo_box'),
	trigger1Class: 'x-form-select-trigger',
	trigger2Class: 'x-form-clear-trigger',
    hideTrigger: true,
	initComponent: function(){
		var me = this;

		Ext.define('liveCPTSearchModel', {
			extend: 'Ext.data.Model',
			fields: [
				{ name: 'id' },
				{ name: 'eid' },
				{ name: 'code', type: 'strig' },
				{ name: 'code_text', type: 'string' },
				{ name: 'code_text_medium', type: 'string' },
				{ name: 'code_type', type: 'string', defaultValue: 'CPT' },
				{ name: 'place_of_service', type: 'string' },
				{ name: 'emergency', type: 'string' },
				{ name: 'charge', type: 'string' },
				{ name: 'days_of_units', type: 'string' },
				{ name: 'essdt_plan', type: 'string' },
				{ name: 'modifiers', type: 'string' }
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'Services.liveCodeSearch'
				},
				reader: {
					totalProperty: 'totals',
					root: 'rows'
				},
				extraParams: {
					code_type: 'cpt'
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model: 'liveCPTSearchModel',
			pageSize: 25,
			autoLoad: false
		});

		Ext.apply(this, {
			store: me.store,
			emptyText: _('search') + '...',
			typeAhead: false,
			minChars: 1,
			anchor: '100%',
			listConfig: {
				loadingText: _('searching') + '...',
				getInnerTpl: function(){
					return '<div class="search-item">{code}: {code_text_medium}</div>';
				}
			},
			pageSize: 25
		});

		me.callParent();
	},

	onRender: function(ct, position){

	    var me = this;

        me.callParent(arguments);
		var id = me.getId();
        me.triggerConfig = {
			tag: 'div',
			cls: 'x-form-twin-triggers',
			style: 'display:block;',
			cn: [
				{
					tag: "img",
					style: Ext.isIE ? 'margin-left:0;height:21px' : '',
					src: Ext.BLANK_IMAGE_URL,
					id: "trigger2" + id,
					name: "trigger2" + id,
					cls: "x-form-trigger " + me.trigger2Class
				}
			]
		};
        me.triggerEl.replaceWith(me.triggerConfig);
        me.triggerEl.on('mouseup', function(e){
			if(e.target.name == "trigger2" + id){
                me.clearValue();
                // me.reset();
                me.oldValue = null;
				if(me.spObj !== '' && me.spExtraParam !== ''){
					Ext.getCmp(me.spObj).store.setExtraParam(me.spExtraParam, '');
					Ext.getCmp(me.spObj).store.load()
				}
				if(me.spForm !== ''){
					Ext.getCmp(me.spForm).getForm().reset();
				}
			}
		});
		var trigger2 = Ext.get("trigger2" + id);
		trigger2.addClsOnOver('x-form-trigger-over');
	}
});