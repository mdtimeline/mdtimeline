/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

Ext.define('App.ux.LiveReferringPhysicianSearch', {
	extend: 'Ext.form.ComboBox',
	xtype: 'referringphysicianlivetsearch',
	hideLabel: true,
	displayField: 'fullname',
	valueField: 'id',
	emptyText: _('search_for_a_physician') + '...',
    queryMode: 'remote',
    allowBlank: true,
	typeAhead: false,
    forceSelection: false,
    allowOnlyWhitespace: true,
    validateBlank: true,
    submitValue: true,
	minChars: 0,
	queryDelay: 200,
	enableAddTrigger: true,
	trigger1Cls: 'x-form-add-trigger',
	trigger2Cls: 'x-form-clear-trigger',
	hideTrigger1: false,
	onTrigger1Click: function () {
		if(a('allow_add_referring_physician')){
			app.fireEvent('referringproviderddbtnclick', this, this.findRecordByValue(this.getValue()));
		}else{
			app.msg(_('oops'), 'Not Authorized', true)
		}
	},
	onTrigger2Click: function () {
		this.reset();
		this.oldValue = null;
		this.fireEvent('fieldreset', this);
	},

	initComponent: function(){
		var me = this;

		Ext.define('referringPhysicianLiveSearchModel', {
			extend: 'Ext.data.Model',
			fields: [
				{
					name: 'id',
					type: 'int'
				},
				{
					name: 'title',
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
					name: 'organization_name',
					type: 'string'
				},
				{
					name: 'fullname',
					type: 'string',
					convert: function(v, record){
						if(record.data.lname){
							return record.data.fname + ' ' + record.data.mname + ' ' + record.data.lname
						}else{
							return record.data.organization_name
						}
					}
				},
				{
					name: 'upin',
					type: 'string'
				},
				{
					name: 'lic',
					type: 'string'
				},
				{
					name: 'npi',
					type: 'string'
				},
                {
                    name: 'ssn',
                    type: 'string'
                },
                {
                    name: 'taxonomy',
                    type: 'string'
                },
                {
                    name: 'email',
                    type: 'string'
                },
                {
                    name: 'direct_address',
                    type: 'string'
                },
                {
                    name: 'phone_number',
                    type: 'string'
                },
                {
                    name: 'fax_number',
                    type: 'string'
                },
                {
                    name: 'cel_number',
                    type: 'string'
                }
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'ReferringProviders.referringPhysicianLiveSearch'
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
			model: 'referringPhysicianLiveSearchModel',
			pageSize: 10,
			autoLoad: false
		});

		Ext.apply(me, {
			store: me.store,
			listConfig: {
				loadingText: _('searching') + '...',
				getInnerTpl: function(){
					return '<div class="search-item"><h3><span>{fullname}</span></h3><b>NPI:</b> {npi} <b>LIC.:</b> {lic}</div>';
				}
			},
			pageSize: 10
		});

		me.callParent();

		me.on('change', me.onReferringValueChange, me);
	},

	onReferringValueChange: function (field, value) {
		if(!Ext.isNumber(value) || value == '0') return;
		var me = this;

		App.model.administration.ReferringProvider.load(value, {
			success: function(referring) {
				if(me.store.getById(referring.get('id')) !== null) return;
				me.store.add(referring.data);
				me.setValue(value);
			}
		});
	}
});
