/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, LLC.
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

Ext.define('App.view.administration.DataPortability', {
    extend: 'App.ux.RenderPanel',
    requires: [
        'App.ux.form.fields.BoxSelect'
    ],
	pageTitle: _('patients_export'),
	pageBody: [

		{
			xtype: 'panel',
			itemId: 'DataPortabilityPanel',
			layout: 'fit',
			items: [
				{
					xtype: 'miframe',
					itemId: 'DataPortabilityPanelIFrame'
				}
			],
			tbar: [
                {
                    xtype:'form',
                    itemId: 'ExportFilterForm',
                    items:[
                        {
                            xtype: 'label',
                            text: _('service_date')
                        },
                        {
                            xtype: 'datefield',
                            name: 'service_from',
                            text: _('from'),
                            itemId: 'ServiceFromDate'
                        },
                        {
                            xtype: 'datefield',
                            name: 'service_to',
                            text: _('to'),
                            itemId: 'ServiceToDate'
                        }
                    ]
                },
                '-',
				{
                    xtype: 'button',
					text: _('export'),
					itemId: 'DataPortabilityExportBtn'
				},
				{
                    xtype: 'button',
					text: _('import'),
					itemId: 'DataPortabilityImportBtn'
				}
			]
		}
	]
});
