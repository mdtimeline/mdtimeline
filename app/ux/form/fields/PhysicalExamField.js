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
Ext.define('App.ux.form.fields.PhysicalExamField', {
	extend: 'Ext.form.FieldContainer',
	xtype: 'physicalexamfield',

	labelAlign: 'top',
	isFormField: true,
	submitValue: true,
	name: undefined,

	getName: function (){
		return this.name;
	},
	validate: function (){
		return true;
	},
	isValid: function (){
		return true;
	},
	isDirty: function (){
		return false;
	},
	initComponent:function(){

		var me = this;

			// 'itemId',
			// 'action',
			// 'name',
			// 'width',
			// 'fieldLabel',
			// 'boxLabel',
			// 'inputValue',
			// 'fieldLabel',
			// 'hideLabel',
			// 'labelWidth',
			// 'margin',
			// 'code'

		me.items = [
			{
				xtype: 'checkbox',
				boxLabel: 'Normal ' + me.boxLabel,
				action: 'normal',
				inputValue: true,
				uncheckedValue: false,
				submitValue: false
			},
			{
				xtype: 'container',
				layout: 'hbox',
				items: [
					{
						xtype: 'checkbox',
						boxLabel: 'ABN: ',
						action: 'abnormal',
						inputValue: true,
						uncheckedValue: false,
						submitValue: false
					},
					{
						xtype: 'textfield',
						margin: '0 0 0 5',
						width: 200,
						action: 'abnormalnote',
						submitValue: false
					}
				]
			}
		]

		me.callParent();

		me.normalCk = me.down('checkbox[action=normal]');
		me.abnormalCk = me.down('checkbox[action=abnormal]');
		me.abnormalNote = me.down('textfield[action=abnormalnote]');

		me.normalCk.on('change', function (newValue){

			if(me.normalCk.ignoreChange === true){
				return;
			}

			me.abnormalCk.ignoreChange = true
			me.abnormalCk.setValue(!newValue);
			me.abnormalCk.ignoreChange = false
		});

		me.abnormalCk.on('change', function (newValue){

			if(me.abnormalCk.ignoreChange === true){
				return;
			}

			me.normalCk.ignoreChange = true
			me.normalCk.setValue(!newValue);
			me.normalCk.ignoreChange = false
		});

	},

	getSubmitData: function (){
		return this.getValue();
	},

	getValue: function (){
		var v = {}

		v[this.name] = {
			label: this.fieldLabel,
			normal: this.normalCk.getValue(),
			abnormal: this.abnormalCk.getValue(),
			abnormal_note: this.abnormalNote.getValue(),
		}
		return v;

	},

	setValue: function (value){
		var me = this;

		say(value);

		me.normalCk.ignoreChange = true;
		me.abnormalCk.ignoreChange = true;

		me.normalCk.setValue(value.normal);
		me.abnormalCk.setValue(value.abnormal);
		me.abnormalNote.setValue(value.abnormal_note);

		me.normalCk.ignoreChange = false;
		me.abnormalCk.ignoreChange = false;

	}

});
