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

Ext.define('App.model.patient.ImplantableDevice', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_implantable_device'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'pid',
			type: 'int',
			index: true
		},
		{
			name: 'eid',
			type: 'int',
			index: true
		},
		{
			name: 'di',
			type: 'string',
			index: true,
			len: 180
		},
		{
			name: 'udi',
			type: 'string',
			index: true,
			len: 600
		},
		{
			name: 'description',
			type: 'string',
			len: 160
		},
		{
			name: 'description_code',
			type: 'string',
			len: 20
		},
		{
			name: 'description_code_type',
			type: 'string',
			len: 20
		},
		{
			name: 'lot_number',
			type: 'string',
			len: 20
		},
		{
			name: 'serial_number',
			type: 'string',
			len: 20
		},
		{
			name: 'exp_date',
			type: 'date',
			dataType: 'date',
			dateFormat: 'Y-m-d'
		},
		{
			name: 'mfg_date',
			type: 'date',
			dataType: 'date',
			dateFormat: 'Y-m-d'
		},
		{
			name: 'donation_id',
			type: 'string',
			len: 160
		},
		{
			name: 'brand_name',
			type: 'string',
			len: 80
		},
		{
			name: 'version_model',
			type: 'string',
			len: 20
		},
		{
			name: 'company_name',
			type: 'string',
			len: 80
		},
		{
			name: 'mri_safety_info',
			type: 'string',
			len: 600
		},
		{
			name: 'required_lnr',
			type: 'bool'
		},
		{
			name: 'status',
			type: 'string',
			len: 20
		},
		{
			name: 'status_code',
			type: 'string',
			len: 20
		},
		{
			name: 'status_code_type',
			type: 'string',
			len: 20
		},
		{
			name: 'note',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'implanted_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'removed_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'create_uid',
			type: 'int'
		},
		{
			name: 'update_uid',
			type: 'int'
		},
		{
			name: 'create_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'update_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'ImplantableDevice.getPatientImplantableDevices',
			create: 'ImplantableDevice.addPatientImplantableDevice',
			update: 'ImplantableDevice.updatePatientImplantableDevice'
		}
	}
});