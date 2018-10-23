/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-10-25 14:22:10
 */

Ext.define('App.model.reports.AppointmentRequest',{
    extend: 'Ext.data.Model',
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'pid',
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
            name: 'sex',
            type: 'string'
        },
        {
            name: 'DOB',
            type: 'date',
            dateFormat: 'Y-m-d H:i:s',
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
            comment: 'marital status'
        },
        {
            name: 'pubpid',
            type: 'string',
            index: true,
            comment: 'external reference id'
        },
        {
            name: 'race',
            type: 'string',
            comment: 'race'
        },
        {
            name: 'secondary_race',
            type: 'string',
            comment: 'secondary race'
        },
        {
            name: 'ethnicity',
            type: 'string',
            comment: 'ethnicity'
        },
        {
            name: 'language',
            type: 'string',
            comment: 'language'
        },
        {
            name: 'primary_provider',
            type: 'int'
        },
        {
            name: 'administrative_status',
            type: 'string',
            comment: 'active | inactive | merged'
        },
	    {
		    name: 'phone_publicity',
		    type: 'string'
	    },
	    {
		    name: 'phone_home',
		    type: 'string'
	    },
	    {
		    name: 'phone_mobile',
		    type: 'string'
	    },
	    {
		    name: 'procedure1',
		    type: 'string'
	    },
	    {
		    name: 'procedure2',
		    type: 'string'
	    },
	    {
		    name: 'procedure3',
		    type: 'string'
	    },
	    {
		    name: 'create_date',
		    ype: 'date',
		    dateFormat: 'Y-m-d H:i:s'
	    },
	    {
		    name: 'create_uid',
		    ype: 'int'
	    }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'AppointmentRequest.getAppointmentRequestReport'
        },
	    reader:{
		    root: 'data'
	    }
    },
	getAge: function (birthDate) {
		var today = new Date();
		var age = today.getFullYear() - birthDate.getFullYear();
		var m = today.getMonth() - birthDate.getMonth();
		if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
			age--;
		}
		return age;
	}
});
