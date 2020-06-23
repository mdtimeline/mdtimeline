/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-10-25 14:22:10
 */

Ext.define('App.model.patient.Patient',{
    extend: 'Ext.data.Model',
    requires: [
        'App.model.patient.Insurance',
        'App.model.patient.Allergies',
        'App.model.patient.Medications',
        'App.model.patient.PatientActiveProblem'
    ],
    table: {
        name: 'patient'
    },
    fields: [
        {
            name: 'pid',
            type: 'int',
            comment: 'patient ID'
        },
	    {
		    name: 'pubpid',
		    type: 'string',
		    index: true,
		    comment: 'external reference id',
		    len: 40
	    },
	    {
		    name: 'interface_number',
		    type: 'string',
		    index: true,
		    comment: 'interface reference number',
		    len: 40
	    },
        {
            name: 'title',
            type: 'string',
            comment: 'Title Mr. Sr.',
            len: 10
        },
        {
            name: 'fname',
            type: 'string',
            comment: 'first name',
            index: true,
            len: 35
        },
        {
            name: 'mname',
            type: 'string',
            comment: 'middle name',
            index: true,
            len: 25
        },
        {
            name: 'lname',
            type: 'string',
            comment: 'last name',
            index: true,
            len: 60
        },
        {
            name: 'suffix',
            type: 'string',
            len: 35
        },
        {
            name: 'sex',
            type: 'string',
            comment: 'sex',
            index: true,
            len: 10
        },
        {
            name: 'orientation',
            type: 'string',
            comment: 'sex orientation',
            index: true,
            len: 15
        },
        {
            name: 'identity',
            type: 'string',
            comment: 'sex identity',
            index: true,
            len: 15
        },
        {
            name: 'DOB',
            type: 'date',
            comment: 'day of birth',
            dateFormat: 'Y-m-d H:i:s',
            index: true,
            defaultValue: '0000-00-00 00:00:00'
        },
        {
            name: 'DOBFormatted',
            type: 'string',
            persist: false,
            convert: function (v, record) {
                return Ext.Date.format(record.get('DOB'), g('date_display_format'));
            }
        },
        {
            name: 'marital_status',
            type: 'string',
            comment: 'marital status',
            len: 40
        },
        {
            name: 'SS',
            type: 'string',
            index: true,
            comment: 'social security',
            len: 40
        },
	    {
		    name: 'drivers_license',
		    type: 'string',
		    index: true,
		    comment: 'driver licence #',
		    len: 40
	    },
	    {
		    name: 'drivers_license_state',
		    type: 'string',
		    len: 40
	    },
	    {
		    name: 'drivers_license_exp',
		    type: 'date',
		    dataType: 'date',
		    dateFormat: 'Y-m-d'
	    },
	    {
		    name: 'provider',
		    type: 'string',
            len: 40
	    },
	    {
		    name: 'pharmacy',
		    type: 'string',
            len: 40
	    },
	    {
		    name: 'hipaa_notice',
		    type: 'string',
		    comment: 'HIPAA notice status',
		    len: 40
	    },
        {
            name: 'record_number',
            type: 'string',
            persist: false
        },
        {
            name: 'fulladdress',
            type: 'string',
            persist: false,
            convert: false
        },
        {
            name: 'phones',
            type: 'string',
            store: false
        },
        {
            name: 'race',
            type: 'string',
            comment: 'race',
            len: 40
        },
        {
            name: 'secondary_race',
            type: 'string',
            comment: 'secondary race',
            len: 40
        },
        {
            name: 'tertiary_race',
            type: 'string',
            comment: 'tertiary race',
            len: 40
        },
        {
            name: 'ethnicity',
            type: 'string',
            comment: 'ethnicity',
            len: 40
        },
        {
            name: 'secondary_ethnicity',
            type: 'string',
            len: 40
        },
        {
            name: 'language',
            type: 'string',
            comment: 'language',
            len: 10
        },
        {
            name: 'birth_fname',
            type: 'string',
            len: 35
        },
        {
            name: 'birth_mname',
            type: 'string',
            len: 35
        },
        {
            name: 'birth_lname',
            type: 'string',
            len: 60
        },
        {
            name: 'allow_leave_msg',
            type: 'bool'
        },
        {
            name: 'allow_voice_msg',
            type: 'bool'
        },
        {
            name: 'allow_mail_msg',
            type: 'bool'
        },
        {
            name: 'allow_sms',
            type: 'bool'
        },
        {
            name: 'allow_email',
            type: 'bool'
        },
        {
            name: 'allow_immunization_registry',
            type: 'bool'
        },
        {
            name: 'allow_immunization_info_sharing',
            type: 'bool'
        },
        {
            name: 'allow_health_info_exchange',
            type: 'bool'
        },
        {
            name: 'allow_patient_web_portal',
            type: 'bool'
        },
        {
            name: 'allow_guardian_web_portal',
            type: 'bool'
        },
        {
            name: 'allow_guardian_web_portal_cda',
            type: 'bool'
        },
        {
            name: 'allow_emergency_contact_web_portal',
            type: 'bool'
        },
        {
            name: 'allow_emergency_contact_web_portal_cda',
            type: 'bool'
        },
        {
            name: 'organ_donor_code',
            type: 'string',
	        len: 10,
	        defaultValue: 'U'
        },
        {
            name: 'occupation',
            type: 'string',
            comment: 'patient occupation',
            len: 40
        },
        {
            name: 'employer_name',
            type: 'string',
            len: 60
        },
        {
            name: 'employer_address',
            type: 'string',
            len: 55
        },
        {
            name: 'employer_city',
            type: 'string',
            len: 30
        },
        {
            name: 'employer_state',
            type: 'string',
            len: 2
        },
        {
            name: 'employer_country',
            type: 'string',
            len: 3
        },
        {
            name: 'employer_postal_code',
            type: 'string',
            len: 15
        },
        {
            name: 'rating',
            type: 'int',
            comment: 'patient stars rating'
        },
        {
            name: 'image',
            type: 'string',
            dataType: 'mediumtext',
            comment: 'patient image base64 string'
        },
        {
            name: 'qrcode',
            type: 'string',
            dataType: 'mediumtext',
            comment: 'patient QRCode base64 string'
        },
	    {
		    name: 'pubaccount',
		    type: 'string',
		    index: true,
		    comment: 'external reference account',
		    len: 40
	    },
        {
            name: 'birth_place',
            type: 'string',
            len: 150
        },
        {
            name: 'birth_multiple',
            type: 'bool',
            useNull: true
        },
        {
            name: 'birth_order',
            type: 'int',
            useNull: true,
            len: 2
        },
        {
            name: 'is_veteran',
            type: 'string',
            len: 1
        },
        {
            name: 'deceased',
            type: 'string',
            len: 1
        },
        {
            name: 'death_date',
            type: 'date',
            dateFormat: 'Y-m-d H:i:s'
        },
        {
            name: 'death_cause',
            type: 'string',
            len: 180
        },
        {
            name: 'alias',
            type: 'string',
            len: 80
        },
        {
            name: 'citizenship',
            type: 'string',
            len: 80
        },
        {
            name: 'primary_facility',
            type: 'int'
        },
        {
            name: 'first_visit_date',
            type: 'date',
            dataType: 'date',
            dateFormat: 'Y-m-d'
        },
        {
            name: 'administrative_status',
            type: 'string',
            comment: 'active | inactive | merged',
            len: 15
        },
        {
            name: 'create_uid',
            type: 'int',
            comment: 'create user ID'
        },
        {
            name: 'update_uid',
            type: 'int',
            comment: 'update user ID'
        },
        {
            name: 'create_date',
            type: 'date',
            comment: 'create date',
            dateFormat: 'Y-m-d H:i:s'
        },
        {
            name: 'update_date',
            type: 'date',
            comment: 'last update date',
            dateFormat: 'Y-m-d H:i:s'
        },
        {
            name: 'portal_password',
            type: 'string',
            dataType: 'blob',
            encrypt: true
        },
        {
            name: 'portal_username',
            type: 'string',
	        len: 40
        },
	    {
		    name: 'phone_publicity',
		    type: 'string',
		    len: 10
	    },
	    {
		    name: 'phone_publicity_date',
		    type: 'date',
            dataType: 'date'
	    },
        {
            name: 'phone_home',
            type: 'string',
	        len: 25
        },
        {
            name: 'phone_mobile',
            type: 'string',
	        len: 25
        },
        {
            name: 'phone_work',
            type: 'string',
	        len: 25
        },
        {
            name: 'phone_work_ext',
            type: 'string',
	        len: 25
        },
	    {
		    name: 'phone_fax',
		    type: 'string',
		    len: 25
	    },
        {
            name: 'email',
            type: 'string',
	        len: 80
        },
        {
            name: 'postal_address',
            type: 'string',
	        len: 55
        },
        {
            name: 'postal_address_cont',
            type: 'string',
	        len: 55
        },
        {
            name: 'postal_city',
            type: 'string',
	        len: 30
        },
        {
            name: 'postal_state',
            type: 'string',
	        len: 2
        },
        {
            name: 'postal_zip',
            type: 'string',
	        len: 15
        },
        {
            name: 'postal_country',
            type: 'string',
	        len: 3
        },
        {
            name: 'physical_address',
            type: 'string',
	        len: 55
        },
        {
            name: 'physical_address_cont',
            type: 'string',
	        len: 55
        },
        {
            name: 'physical_city',
            type: 'string',
	        len: 30
        },
        {
            name: 'physical_state',
            type: 'string',
	        len: 2
        },
        {
            name: 'physical_zip',
            type: 'string',
	        len: 15
        },
        {
            name: 'physical_country',
            type: 'string',
	        len: 3
        },
	    {
		    name: 'mother_pid',
		    type: 'int',
            index: true
	    },
	    {
		    name: 'mother_fname',
		    type: 'string',
		    len: 35
	    },
	    {
		    name: 'mother_mname',
		    type: 'string',
		    len: 25
	    },
	    {
		    name: 'mother_lname',
		    type: 'string',
		    len: 60
	    },
        {
            name: 'father_pid',
            type: 'int',
            index: true
        },
	    {
		    name: 'father_fname',
		    type: 'string',
		    len: 35
	    },
	    {
		    name: 'father_mname',
		    type: 'string',
		    len: 25
	    },
	    {
		    name: 'father_lname',
		    type: 'string',
		    len: 60
	    },
        {
            name: 'spouse_pid',
            type: 'int',
            index: true
        },
        {
            name: 'spouse_fname',
            type: 'string',
            len: 35
        },
        {
            name: 'spouse_mname',
            type: 'string',
            len: 25
        },
        {
            name: 'spouse_lname',
            type: 'string',
            len: 60
        },
        {
            name: 'guardians_pid',
            type: 'int',
            index: true
        },
	    {
		    name: 'guardians_relation',
		    type: 'string',
		    len: 20
	    },
        {
            name: 'guardians_fname',
            type: 'string',
	        len: 35
        },
        {
            name: 'guardians_mname',
            type: 'string',
	        len: 25
        },
        {
            name: 'guardians_lname',
            type: 'string',
	        len: 60
        },
        {
            name: 'guardians_phone',
            type: 'string',
	        len: 25
        },
        {
            name: 'guardians_phone_type',
            type: 'string',
	        len: 10
        },
        {
            name: 'guardians_address',
            type: 'string',
            len: 55
        },
        {
            name: 'guardians_address_cont',
            type: 'string',
            len: 55
        },
        {
            name: 'guardians_city',
            type: 'string',
            len: 30
        },
        {
            name: 'guardians_state',
            type: 'string',
            len: 2
        },
        {
            name: 'guardians_country',
            type: 'string',
            len: 3
        },
        {
            name: 'guardians_zip',
            type: 'string',
            len: 15
        },
        {
            name: 'guardian_portal_password',
            type: 'string',
            dataType: 'blob',
            encrypt: true
        },
        {
            name: 'guardian_portal_username',
            type: 'string',
            len: 40
        },
        {
            name: 'emergency_contact_relation',
            type: 'string',
	        len: 20
        },
        {
            name: 'emergency_contact_fname',
            type: 'string',
	        len: 80
        },
        {
            name: 'emergency_contact_mname',
            type: 'string',
	        len: 25
        },
        {
            name: 'emergency_contact_lname',
            type: 'string',
	        len: 60
        },
        {
            name: 'emergency_contact_phone',
            type: 'string',
	        len: 25
        },
        {
            name: 'emergency_contact_phone_type',
            type: 'string',
            len: 10
        },
	    {
		    name: 'emergency_contact_address',
		    type: 'string',
		    len: 35
	    },
	    {
		    name: 'emergency_contact_address_cont',
		    type: 'string',
		    len: 55
	    },
	    {
		    name: 'emergency_contact_city',
		    type: 'string',
		    len: 55
	    },
	    {
		    name: 'emergency_contact_state',
		    type: 'string',
		    len: 2
	    },
	    {
		    name: 'emergency_contact_country',
		    type: 'string',
		    len: 3
	    },
	    {
		    name: 'emergency_contact_zip',
		    type: 'string',
		    len: 15
	    },
        {
            name: 'emergency_contact_portal_password',
            type: 'string',
            dataType: 'blob',
            encrypt: true
        },
        {
            name: 'emergency_contact_portal_username',
            type: 'string',
            len: 40
        },
	    {
		    name: 'religion',
		    type: 'string',
		    len: 20
	    },
	    {
		    name: 'authorized_01_relation',
		    type: 'string',
		    len: 20
	    },
	    {
		    name: 'authorized_01_fname',
		    type: 'string',
		    len: 35
	    },
	    {
		    name: 'authorized_01_mname',
		    type: 'string',
		    len: 25
	    },
	    {
		    name: 'authorized_01_lname',
		    type: 'string',
		    len: 60
	    },
	    {
		    name: 'authorized_01_phone',
		    type: 'string',
		    len: 25
	    },
	    {
		    name: 'authorized_01_phone_type',
		    type: 'string',
		    len: 10
	    },
	    {
		    name: 'authorized_02_relation',
		    type: 'string',
		    len: 20
	    },
	    {
		    name: 'authorized_02_fname',
		    type: 'string',
		    len: 35
	    },
	    {
		    name: 'authorized_02_mname',
		    type: 'string',
		    len: 25
	    },
	    {
		    name: 'authorized_02_lname',
		    type: 'string',
		    len: 60
	    },
	    {
		    name: 'authorized_02_phone',
		    type: 'string',
		    len: 25
	    },
	    {
		    name: 'authorized_02_phone_type',
		    type: 'string',
		    len: 10
	    },
	    {
		    name: 'phone_mobile_supplier',
		    type: 'string',
		    len: 25
	    },
	    {
		    name: 'pbm_payer_id',
		    type: 'string',
		    len: 60
	    },
	    {
		    name: 'pbm_payer_name',
		    type: 'string',
		    len: 45
	    },
	    {
		    name: 'pbm_card_fname',
		    type: 'string',
		    len: 45
	    },
	    {
		    name: 'pbm_card_lname',
		    type: 'string',
		    len: 45
	    },
        {
            name: 'pbm_member_id',
            type: 'string',
            len: 45
        },
        {
            name: 'pbm_group',
            type: 'string',
            len: 45
        },
	    {
		    name: 'pbm_bin',
		    type: 'string',
		    len: 45
	    },
	    {
		    name: 'pbm_pcn',
		    type: 'string',
		    len: 45
	    },
	    {
		    name: 'pbm_consent',
		    type: 'string',
		    len: 45
	    },
        {
            name: 'immunization_registry_status',
            type: 'string',
            len: 10
        },
        {
            name: 'immunization_registry_status_date',
            type: 'date',
            dateFormat: 'Y-m-d'
        },
        {
            name: 'protection_indicator',
            type: 'bool'
        },
        {
            name: 'protection_indicator_date',
            type: 'date',
            dataType: 'date'
        },
	    {
		    name: 'authy_id',
		    type: 'string',
		    len: 45,
            index: true
	    },
	    {
		    name: 'last_visit_id',
		    type: 'string',
		    len: 45,
            index: true
	    },
	    {
		    name: 'name',
		    type: 'string',
		    store: false
	    },
	    {
		    name: 'fullname',
		    type: 'string',
            convert: function (v, rec) {
                return rec.get('lname') + ', ' + rec.get('fname') + ' ' + rec.get('mname');
            },
		    store: false
	    }
    ],
    idProperty: 'pid',
    proxy: {
        type: 'direct',
        api: {
            read: 'Patient.getPatients',
            create: 'Patient.savePatient',
            update: 'Patient.savePatient'
        },
        writer: {
            writeAllFields: true
        }
    },
    hasMany: [
        {
            model: 'App.model.patient.Insurance',
            name: 'insurance',
            primaryKey: 'pid',
            foreignKey: 'pid'
        },
        {
            model: 'App.model.patient.Allergies',
            name: 'allergies',
            primaryKey: 'pid',
            foreignKey: 'pid'
        },
        {
            model: 'App.model.patient.Medications',
            name: 'medications',
            primaryKey: 'pid',
            foreignKey: 'pid'
        },
        {
            model: 'App.model.patient.PatientActiveProblem',
            name: 'activeproblems',
            primaryKey: 'pid',
            foreignKey: 'pid'
        },
        {
            model: 'App.model.patient.PatientContacts',
            name: 'contacts',
            primaryKey: 'pid',
            foreignKey: 'pid'
        }
    ],
    indexes: [
        {
            name: 'live_search_index',
            choice: 'INDEX',
            type: 'BTREE',
            columns: [
                'pid',
                'pubpid',
                'fname',
                'mname',
                'lname',
                'SS'
            ]
        }
    ]
});
