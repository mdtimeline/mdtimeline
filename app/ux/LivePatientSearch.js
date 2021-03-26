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
	newPatientEnabled: false,
	newPatientCallback: undefined,
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
					name: 'email',
					type: 'string'
				},
				{
					name: 'phone_mobile',
					type: 'string'
				},
				{
					name: 'phone_mobile',
					type: 'string'
				},
				{
					name: 'phone_home',
					type: 'string'
				},
				{
					name: 'fullname',
					type: 'string',
					convert: function(v, record){
						return record.data.lname + ', ' + record.data.fname + ' ' + record.data.mname
					}
				},
				{
					name: 'hl7_fullname',
					type: 'string',
					convert: function(v, record){
						var name = [ record.data.lname ];
						if(record.data.fname){
							name.push(record.data.fname);
						}
						if(record.data.mname){
							name.push(record.data.mname);
						}
						return name.join('^');
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
			pageSize: 15,
			autoLoad: false
		});

		Ext.apply(me, {
			store: me.store,
			listConfig: {
				loadingText: _('searching') + '...',
				minWidth: 450,
				maxHeight: 600,
				getInnerTpl: function(){
					var pid = (eval(g('display_pubpid')) ? 'pubpid' : 'pid');
					return '<div class="search-item">' +
						'<div style="float: right; font-size: 10px;">#{' + pid + '}</div>' +
						'<div style="font-weight: bold;">{fullname}&nbsp;&nbsp;-&nbsp;&nbsp;{[Ext.Date.format(values.DOB, g("date_display_format"))]} ({[app.getAge(values.DOB)[\'years\']]}Y)</div> ' +
						'</div>';
				}
			},
			pageSize: 15
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

		me.on('render', function (){
			me.getPicker().down('toolbar').insert(0,{
				xtype: 'button',
				text: _('new_patient'),
				cls: 'btnGreenBackground',
				itemId: 'SearchNewPatientBtn',
				newPatientCallback: function (patient){
					if(me.newPatientCallback){
						me.newPatientCallback(me, patient);
					}
				},
				handler: function (){
					me.reset();
					me.picker.hide();
				}
			});

			//me.inputEl.dom.setAttribute('autocomplete', Ext.isChrome ? 'none' : 'false');
		});
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
