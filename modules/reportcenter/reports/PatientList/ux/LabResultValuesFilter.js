/**
 * mdTimeLine (Electronic Health Records)
 * LabResultValuesFilter.js
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
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
Ext.define('Modules.reportcenter.reports.PatientList.ux.LabResultValuesFilter', {
	extend: 'Ext.form.FieldContainer',
	alias: 'widget.labresultvalues',
	fieldLabel: _('lab_results'),
	labelAlign: 'top',
	layout: 'anchor',

	initComponent: function () {
		var me = this;

		/**
		 * Model & Store for the Laboratory Results combo
		 */
		Ext.define('ReportLabResultValueFilterModel', {
			extend: 'Ext.data.Model',
			fields: [
				{
					name: 'code',
					type: 'string'
				},
				{
					name: 'code_text',
					type: 'string'
				}
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'LabResultsValuesFilter.getDistinctResults'
				},
				reader: {
					root: 'rows',
					totalProperty: 'totals'
				}
			},
			idProperty: 'code'
		});

		me.items = [
			{
				xtype: 'combo',
				store: Ext.create('Ext.data.Store', {
					model: 'ReportLabResultValueFilterModel',
					autoLoad: false
				}),
				anchor: '100%',
				emptyText: _('select_lab_result'),
				itemId: 'LabResultValuesFilterLabField',
				displayField: 'code_text',
				valueField: 'code',
                editable: false,
				submitValue: false
			},
			{
				xtype: 'combo',
				store: Ext.create('Ext.data.Store', {
					fields: ['name', 'value'],
					data: [
						{
							"name": "More than",
							"value": ">="
						},
						{
							"name": "Less than",
							"value": "<="
						},
						{
							"name": "Equal to",
							"value": "="
						}
					]
				}),
				anchor: '100%',
                editable: false,
				emptyText: _('select_comparison'),
				itemId: 'LabResultValuesFilterOperatorField',
				displayField: 'name',
				valueField: 'value',
				submitValue: false
			},
			{
				xtype: 'textfield',
				anchor: '100%',
				itemId: 'LabResultValuesFilterValueField',
				emptyText: _('lab_enter_value'),
				submitValue: false
			},
			{
				xtype: 'container',
				layout: 'hbox',
				anchor: '100%',
				height: 30,
				items: [
					{
						xtype: 'button',
						text: 'Reset Labs',
						flex: 1,
						listeners: {
							click: me.reset,
							scope: me
						}
					},
					{
						xtype: 'button',
						text: 'Add Lab',
						flex: 1,
						listeners: {
							click: me.addFieldValue,
							scope: me
						}
					}
				]
			},
			{
				xtype: 'fieldcontainer',
				itemId: 'LabResultValuesFilterFieldsContainer',
				anchor: '100%'
			}
		];

		me.callParent();

	},

	addFieldValue: function () {
		var me = this,
			lab_code = me.getComponent('LabResultValuesFilterLabField').getValue(),
			lab_name = me.getComponent('LabResultValuesFilterLabField').findRecordByValue(lab_code).get('code_text'),
			operator = me.getComponent('LabResultValuesFilterOperatorField').getValue(),
			value = me.getComponent('LabResultValuesFilterValueField').getValue(),
			fieldContainer = me.getComponent('LabResultValuesFilterFieldsContainer');

		if(lab_code == '' || operator == '' || value == ''){
			app.msg(_('oops'),'Laboratory Result, Operator, and Value are required', true);
			return;
		}

        fieldContainer.add({
            xtype: 'checkboxfield',
            boxLabel: Ext.String.format('{0} {1} {2}', lab_name, operator, value),
            name: me.name,
            checked: true,
            inputValue: {
                lab_code: lab_code,
                lab_name: lab_name,
                operator: operator,
                value: value
            },
            listeners: {
                change: function (field, value) {
                    if (!value) {
                        Ext.Function.defer(function () {
                            field.destroy();
                        }, 200);
                    }
                }
            }
        });

	},

	reset: function () {
		this.getComponent('LabResultValuesFilterLabField').reset();
		this.getComponent('LabResultValuesFilterOperatorField').reset();
		this.getComponent('LabResultValuesFilterValueField').reset();
	}

});
