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
Ext.define('App.ux.LivePatientSearch', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.patienlivetsearch',
	hideLabel: true,
	displayField: 'fullname',
	valueField: 'pid',
	emptyText: _('search_for_a_patient') + '...',
	maxLength: 40,
    queryMode: 'remote',
    allowBlank: true,
	typeAhead: false,
    forceSelection: false,
    allowOnlyWhitespace: true,
	hideTrigger: true,
    validateBlank: true,
    submitValue: true,
	minChars: 1,
	queryDelay: 500,
	resetEnabled: false,
	initComponent: function(){
		var me = this;

		Ext.define('patientLiveSearchModel', {
			extend: 'Ext.data.Model',
			fields: [
				{
					name: 'pid',
					type: 'int'
				},
				{
					name: 'pubpid',
					type: 'string'
				},
				{
					name: 'fname',
					type: 'string'
				},
				{
					name: 'mname',
					type: 'string'
				},
				{
					name: 'lname',
					type: 'string'
				},
				{
					name: 'fullname',
					type: 'string',
					convert: function(v, record){
						return record.data.fname + ' ' + record.data.mname + ' ' + record.data.lname
					}
				},
				{
					name: 'DOB',
					type: 'date',
					dateFormat: 'Y-m-d H:i:s'
				},
				{
					name: 'sex',
					type: 'string'
				},
				{
					name: 'SS',
					type: 'string'
				}
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'Patient.patientLiveSearch'
				},
                writer:{
                    writeAllFields: true
                },
				reader: {
					totalProperty: 'totals',
					root: 'rows'
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model: 'patientLiveSearchModel',
			pageSize: 10,
			autoLoad: false
		});

		Ext.apply(me, {
			store: me.store,
			listConfig: {
				loadingText: _('searching') + '...',
				getInnerTpl: function(){
					var pid = (eval(g('display_pubpid')) ? 'pubpid' : 'pid');
					return '<div class="search-item"><h3><span>{fullname}</span> {[Ext.Date.format(values.DOB, g("date_display_format"))]}</h3>' +
						'Record #{' + pid + '}</div>';
				}
			},
			pageSize: 10
		});

		if(this.resetEnabled){
			me.hideTrigger = false;
			me.triggerTip = _('click_to_clear_selection');
			me.spObj = '';
			me.spForm ='';
			me.spExtraParam = '';
			me.qtip = _('clearable_combo_box');
			me.trigger1Class = 'x-form-select-trigger';
			me.trigger2Class = 'x-form-clear-trigger';
		}


		me.callParent();
	},

	onRender: function(ct, position){
		var id = this.getId();
		var trigger2;
		this.callParent(arguments);

		if(!this.resetEnabled){
			return;
		}

		this.triggerConfig = {
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
					cls: "x-form-trigger " + this.trigger2Class
				}
			]
		};
		this.triggerEl.replaceWith(this.triggerConfig);
		this.triggerEl.on('mouseup', function(e){
			if(e.target.name == "trigger2" + id){
				this.reset();
				this.fireEvent('reset', this);
			}
		}, this);
		trigger2 = Ext.get("trigger2" + id);
		trigger2.addClsOnOver('x-form-trigger-over');
	}
});
