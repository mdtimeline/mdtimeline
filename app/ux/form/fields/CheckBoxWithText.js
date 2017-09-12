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
 * @class Ext.ux.form.field.DateTime
 * @extends Ext.form.FieldContainer
 * @author atian25 (http://www.sencha.com/forum/member.php?51682-atian25)
 * @author ontho (http://www.sencha.com/forum/member.php?285806-ontho)
 * @author jakob.ketterl (http://www.sencha.com/forum/member.php?25102-jakob.ketterl)
 *
 */
Ext.define('App.ux.form.fields.CheckBoxWithText', {
	extend: 'Ext.form.FieldContainer',
	mixins: {
		field: 'Ext.form.field.Field'
	},
	xtype: 'checkboxwithtext',
	layout: 'hbox',
	boxLabel: 'boxLabel',
	emptyText: '',
	readOnly: false,
//	combineErrors: true,
	msgTarget: 'under',
	width: 400,

	inputValue: '1',
	uncheckedValue: '0',

	textField1: undefined,
	textField2: undefined,

	initComponent: function(){
		var me = this;

		me.items = me.items || [];

		me.items = [
			{
				xtype:'checkbox',
				boxLabel: me.boxLabel,
				submitValue: false,
				inputValue: me.inputValue,
				width: 130,
				margin: '0 10 0 0'
			}
		];

		me.textField1 = me.textField1 || {
			xtype:'textField1'
		};

		Ext.apply(me.textField1 , {
			submitValue: false,
			flex: 1,
			hidden: true,
			emptyText: me.emptyText
		});

		me.items.push(me.textField1);


		if(me.textField2){

			Ext.apply(me.textField2 , {
				submitValue: false,
				flex: 1,
				hidden: true,
				emptyText: me.emptyText
			});

			me.items.push(me.textField2);
		}

		if(me.layout == 'vbox') me.height = 44;

		me.callParent();

		me.chekboxField = me.items.items[0];
		me.textField1 = me.items.items[1];
		me.chekboxField.on('change', me.settextField1, me);


		if(me.items.items[2]){
			me.textField2 = me.items.items[2];
			me.chekboxField.on('change', me.settextField2, me);
		}

		me.initField();
	},

	settextField1: function(checkbox, value){
		if(!this.textField1) return;
		if(value == 0 || value == 'off' || value == false){
			this.textField1.reset();
			this.textField1.hide();
		}else{
			this.textField1.show();
		}
	},

	settextField2: function(checkbox, value){

		if(!this.textField2) return;
		if(value == 0 || value == 'off' || value == false){
			this.textField2.reset();
			this.textField2.hide();
		}else{
			this.textField2.show();
		}
	},

	getValue: function(){
		var value = '',
			ckValue = this.chekboxField.getSubmitValue(),
			txtValue = this.textField1.getSubmitValue() || '';

		if(ckValue) {
			value = ckValue + '~' + txtValue;

			if(this.textField2){
				value += ('~' + (this.textField2.getSubmitValue() || ''));
			}
		}

		return value;
	},

	getSubmitValue: function(){
		return this.getValue();
	},

	setValue: function(value){
		if(value && value.split){
			var val = value.split('~');
			this.chekboxField.setValue(val[0] || 0);
			this.textField1.setValue(val[1] || '');

			if(this.textField2){
				this.textField1.setValue(val[2] || '');
			}

			return;
		}
		this.chekboxField.setValue(0);
		this.textField1.setValue('');
		if(this.textField2) this.textField2.setValue('');
	},

	// Bug? A field-mixin submits the data from getValue, not getSubmitValue
	getSubmitData: function(){
		var me = this,
			data = null;
		if(!me.disabled && me.submitValue && !me.isFileUpload()){
			data = {};
			data[me.getName()] = '' + me.getSubmitValue();
		}
		return data;
	},

	setReadOnly: function(value){
		this.chekboxField.setReadOnly(value);
		this.textField1.setReadOnly(value);
		if(this.textField2) this.textField2.setReadOnly(value);
	},

	isValid: function(){
		return this.chekboxField.isValid() && this.textField1.isValid();
	}
});
