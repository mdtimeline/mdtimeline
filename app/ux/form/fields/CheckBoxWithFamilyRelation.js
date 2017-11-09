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
Ext.define('App.ux.form.fields.CheckBoxWithFamilyRelation', {
	extend: 'App.ux.form.fields.CheckBoxWithText',
	xtype: 'checkboxwithfamilyhistory',

	initComponent:function(){

		var me = this;
		
		me.textField1 = {
			xtype: 'fieldcontainer',
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			getSubmitValue: function () {
				var values = [];

				this.items.each(function (item) {

					var relation_cmb  = item.getComponent(0),
						relation_store = relation_cmb.store,
						relation_record = relation_store.getById(relation_cmb.getSubmitValue()),
						note  = item.getComponent(1).getValue();

					if(!relation_record) return;

					values.push(Ext.String.format(
						'{0}:{1}:{2}:{3}',
						relation_record.get('code_type'),
						relation_record.get('code'),
						relation_record.get('option_name'),
						note
					));

				});

				return values.join(',');

			},
			getValue: function () {
				return this.getSubmitValue();
			},
			setValue: function () {

			},
			isValid: function () {

			}
		};

		me.inputValue = me.code || '1';
		me.callParent();
		me.addRelationField();

	},

	addRelationField: function () {
		var me = this;

		me.textField1.add({
			xtype: 'fieldcontainer',
			action: 'fieldscontainer',
			layout: {
				type: 'hbox',
				align: 'stretch'
			},
			items: [
				{
					xtype: 'gaiaehr.combo',
					fieldLabel: _('relation'),
					labelAlign: 'right',
					action: 'relationcmb',
					labelWidth: 100,
					list: 109,
					allowBlank: false,
					loadStore: true,
					editable: false,
					resetable: true,
					value: '',
					isEmpty: true,
					submitValue: false,
					listeners: {
						select: me.onRelationComoSelect,
						fieldreset: me.onRelationComoReset,
						scope: me
					}
				},
				{
					xtype: 'textfield',
					fieldLabel: _('note'),
					labelAlign: 'right',
					labelWidth: 80,
					flex: 1
				}
			]
		});
	},

	onRelationComoReset: function (cmb) {

		say('onRelationComoReset');
		if(this.textField1.items.length === 1) return;
		this.textField1.remove(cmb.up('fieldcontainer'))
	},

	onRelationComoSelect: function (cmb, selection) {
		if(selection.length > 0){
			cmb.isEmpty = false;
		}

		var fieldcontainer = cmb.up('fieldcontainer'),
			emptyfields = this.textField1.query('[action=relationcmb][isEmpty]');

		if(selection.length > 0 && emptyfields.length === 0){
			this.addRelationField();
		}
	},

	getValue: function(){
		var value = '',
			ckValue = this.chekboxField.getSubmitValue(),
			txtValue;

		if(ckValue != '0'){
			ckValue += ':' + this.chekboxField.boxLabel;
			txtValue = this.textField1.getSubmitValue();
		} else {
			txtValue = '0';
		}

		if(ckValue) {
			value = ckValue + '~' + txtValue;

			if(this.textField2){
				value += '~' + this.textField2.getSubmitValue() || '';
			}
		}


		say('getValue');
		say(value);


		return value;
	},

	setValue: function(value){
		// if(value && value.split){
		// 	var val = value.split('~');
		// 	this.chekboxField.setValue(val[0] || 0);
		//
		// 	if(val[1] != '0' && val[1].split){
		// 		var relation = val[1].split(':');
		// 		this.textField1.select(relation[1] || relation[0] || '');
		// 	} else {
		// 		this.textField1.setValue('');
		// 	}
		//
		// 	if(this.textField2 && val[2]){
		// 		this.textField2.setValue(val[2]) || '';
		// 	}
		//
		// 	return;
		// }
		// this.chekboxField.setValue(0);
		// this.textField1.setValue('');
		// if(this.textField2) this.textField2.setValue('');

	}
});
