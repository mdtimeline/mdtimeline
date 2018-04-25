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

Ext.define('App.view.patient.encounter.ICDs', {
	extend: 'Ext.form.FieldSet',
	alias: 'widget.icdsfieldset',
	title: _('dx_codes'),
	padding: '10 15',
	layout: {
		type: 'vbox',
		align: 'stretch'
	},
	requires: [ 'App.ux.LiveICDXSearch' ],
	autoFormSync: true,
	dxGroup: {},
	initComponent: function(){
		var me = this;

		me.items = [
			{
				xtype:'container',
				layout:'hbox',
				items:[
					{
						xtype:'combobox',
						store: Ext.create('Ext.data.Store', {
							fields: [
								{ name:'option', type: 'auto' },
								{ name:'value', type: 'int' }
							],
							data : [
								{ option:'DX:1', value: 1 },
								{ option:'DX:2', value: 2 },
								{ option:'DX:3', value: 3 },
								{ option:'DX:4', value: 4 },
								{ option:'DX:5', value: 5 },
								{ option:'DX:6', value: 6 },
								{ option:'DX:7', value: 7 },
								{ option:'DX:8', value: 8 },
								{ option:'DX:9', value: 9 }
							]
						}),
						width: 55,
						itemId: this.id + '-group-cmb',
						queryMode: 'local',
						displayField: 'option',
						valueField: 'value',
						value: 1,
						margin: '0 3 0 0',
						forceSelection: true,
						editable: false,
						hide: true
					},
					{
						xtype:'combobox',
						store: Ext.create('Ext.data.Store', {
							fields: [
								{ name:'code', type: 'string' },
								{ name:'code_text', type: 'string' }
							],
							data : [
								{ code:'A', code_text: 'Admitting' },
								{ code:'F', code_text: 'Final' },
								{ code:'W', code_text: 'Working' }
							]
						}),
						width: 100,
						itemId: this.id + '-dx-type-cmb',
						queryMode: 'local',
						displayField: 'code_text',
						valueField: 'code',
						margin: '0 3 0 0',
						forceSelection: true,
						allowBlank: false,
						submitValue: false,
						editable: false,
						value: 'F'
					},
					{
						xtype: 'liveicdxsearch',
						itemId: 'liveicdxsearch',
						emptyText: me.emptyText,
						name: 'dxCodes',
						flex: 1,
						listeners: {
							scope: me,
							select: me.onLiveIcdSelect,
							blur: function(field){
								field.reset();
							}
						}
					}
				]
			}
		];

		me.callParent(arguments);
	},

	getGroupContainer: function(group){

		var me = this;

		if(!this.dxGroup[group]){
			this.dxGroup[group] = Ext.widget('container',{
				layout: {
					type: 'table',
					columns: 6
				},
				itemId: this.id + '-group-' + group,
				margin: '5 0 0 0',
				items:[
					{ xtype:'container', itemId: this.id + '-dx-order-1', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-2', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-3', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-4', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-5', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-6', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-7', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-8', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-9', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-10', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-11', action: 'pointer' },
					{ xtype:'container', itemId: this.id + '-dx-order-12', action: 'pointer' }
				],
				listeners:{
					afterrender:function(dxsContainer){

						var dxContainers = dxsContainer.items.items;

						for(var k=0; k < dxContainers.length; k++){

							new Ext.dd.DropTarget(dxContainers[k].el, {
								// must be same as for tree
								ddGroup: 'group-' + group + '-dx',
								dropPos: false,
								dxsContainer: dxsContainer,
								dxContainer: dxContainers[k],

								notifyOver: function(dd, e, data){

//									say('notifyOver');

									var dx = data.panel,
										dxContainer = this.dxContainer,
										proxy = dd.proxy;

									if(dxContainer.items.items.length == 0){
										// is over empty dx container
										return false;
									}else if(dxContainer.items.items[0] == dx){
										return false;
									}else{

										var dragDxContainerIndex = dxsContainer.items.items.indexOf(dx.ownerCt),
											dropDxContainerIndex = dxsContainer.items.items.indexOf(dxContainer);

										this.dropBefore = dragDxContainerIndex > dropDxContainerIndex;
										this.dropPos = dxsContainer.items.items.indexOf(dxContainer);
									}
									return true;
								},

								notifyDrop: function(dd, e, data){

									dd.panelProxy.hide();
									dd.proxy.hide();
									Ext.suspendLayouts();
									if(this.lastPos !== false){

										var parentDragContainer = data.panel.ownerCt,
											parentDropContainer = this.dxsContainer.items.items[this.dropPos],
											parentDropDx = this.dxsContainer.items.items[this.dropPos].items.items[0],
											parentDragDx = data.panel;


										parentDragContainer.remove(parentDragDx, false);
										parentDropContainer.remove(parentDropDx, false);

										parentDropContainer.add(parentDragDx);
										parentDragContainer.add(parentDropDx);

									}

									Ext.resumeLayouts(true);
									delete this.dropPos;

									me.onReOrder(dxsContainer);

									return true;
								}
							});
						}
					}
				}
			});
		}

		this.add(this.dxGroup[group]);

		return this.dxGroup[group];
	},

	onLiveIcdSelect: function(field, record){
		var me = this,
			soap = me.up('form').getForm().getRecord(),
			group_cmb = me.getDxGroupCombo(),
			group = group_cmb.getValue(),
			type_cmb = me.getDxTypeCombo(),
			type = type_cmb.getValue(),
			order = me.getNextOrder(group),
			dxRecords;

		if(!group_cmb.isValid() && !type_cmb.isValid()) return;

		var record_data = {
			pid: soap.data.pid,
			eid: soap.data.eid,
			uid: app.user.id,
			code: record[0].data.code,
			code_text: record[0].data.code_text,
			code_type: record[0].data.code_type,
			dx_group: group,
			dx_type: type,
			dx_order: order
		};

		if(me.fireEvent('beforerecordadd', me, record_data) === false) return;

		dxRecords = this.store.add(record_data);

		me.fireEvent('recordadd', me, dxRecords[0]);

		me.addIcd(dxRecords[0], group, order);
		field.reset();
	},

	doAddIcd: function (data) {
		var me = this,
			soap = me.up('form').getForm().getRecord(),
			group_cmb = me.getDxGroupCombo(),
			group = group_cmb.getValue(),
			type_cmb = me.getDxTypeCombo(),
			type = type_cmb.getValue(),
			order = me.getNextOrder(group),
			dxRecords;

		if(!group_cmb.isValid() && !type_cmb.isValid()) return;

		if(me.store.find('code', data.code) !== -1) return;

		var record_data = {
			pid: soap.data.pid,
			eid: soap.data.eid,
			uid: app.user.id,
			code: data.code,
			code_text: data.code_text,
			code_type: data.code_type,
			dx_group: group,
			dx_type: type,
			dx_order: order
		};

		dxRecords = me.store.add(record_data);

		me.addIcd(dxRecords[0], group, order);
	},

	removeIcds: function(){

		Ext.Object.each(this.dxGroup, function(key, group){
			Ext.destroy(group);
		});

		this.dxGroup = {};
	},

	loadIcds: function(store){

		var me = this,
			dxs = store.data.items;

		me.store = store;
		me.removeIcds();
		me.loading = true;

		for(var i = 0; i < dxs.length; i++){
			me.addIcd(dxs[i], dxs[i].data.dx_group, dxs[i].data.dx_order);
		}
		me.loading = false;
		me.getIcdLiveSearch().reset();
	},

	addIcd: function(record, group, order){
		var me = this;

		me.getDxCell(group, order).add({
			xtype: 'panel',
			closable: true,
			title: (record.get('code') + ' (' + record.get('dx_type')+ ')'),
			dxRecord: record,
			width: 120,
			margin: '0 5 0 0',
			name: this.name,
			editable: false,
			action: 'Dx',
			draggable: {
				moveOnDrag: false,
				ddGroup: 'group-' + group + '-dx'
			},
			listeners: {
				render: function(cmp){
					cmp.el.dom.setAttribute('data-qtip' , ('<b>' + record.get('code') + '</b>: ' +record.get('code_text')));
				},
				close: function(cmp){
					me.store.remove(cmp.dxRecord);
				}
			}
		});
	},

	getDxCell: function(group, order){
		return this.getGroupContainer(group).getComponent(this.id + '-dx-order-' + order);
	},

	getIcdLiveSearch: function(){
		return this.query('liveicdxsearch')[0];
	},

	getDxGroupCombo: function(){
		return this.query('#' + this.id + '-group-cmb')[0];
	},

	getDxTypeCombo: function(){
		return this.query('#' + this.id + '-dx-type-cmb')[0];
	},

	getNextOrder: function(group){
		var pointers = this.getGroupContainer(group).query('container[action=pointer]'),
			i, len = pointers.length;

		for(i=0; i < len; i++){
			if(pointers[i].items.items.length == 0) return (i + 1);
		}
		return false;
	},

	onReOrder: function(group){
		var orders = group.query('container[action=pointer]'),
			len;

		len = orders.length;
		for(var i=0; i < len; i++){
			if(orders[i].items.items.length > 0 && orders[i].items.items[0].action == 'Dx'){
				orders[i].items.items[0].dxRecord.set({dx_order: (i+1)});
			}
		}
	},

	sync: function(){
		this.store.sync();
	}
});