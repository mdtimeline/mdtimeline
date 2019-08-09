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

Ext.define('App.view.patient.windows.Charts', {
	extend: 'Ext.window.Window',
	requires: [],
	title: _('vector_charts'),
	modal: true,
	width: 1000,
	height: 700,
	layout: 'fit',
	itemId: 'PatientChartsWindow',
    items: [
        {
            xtype: 'miframe',
	        itemId: 'PatientChartsIframe',
        }
    ],
	tbar: [
		{
			text: 'Weight vs Age (0-5)',
			itemId: 'PatientChartWeightVsAgeToFiveBtn',
		},
		'-',
		{
			text: 'Weight vs Age (2-20)',
			itemId: 'PatientChartWeightVsAgeToTwentyBtn',
		},
		'-',
		{
			text: 'Head Circumference vs Age (0-5)',
			itemId: 'PatientChartHeadCircumferenceVsAgeToFiveBtn',
		},
		'-',
		{
			text: 'Length vs Age (0-5)',
			itemId: 'PatientChartLengthVsAgeToFiveBtn',
		},
		'-',
		{
			text: 'BMI (2-20)',
			itemId: 'PatientChartBMIBtn',
		}
	]
});
