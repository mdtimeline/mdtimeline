/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.administration.DocumentsTemplatesHeaderFooter', {
	extend: 'Ext.data.Model',
	table: {
		name: 'documents_templates_header_footer'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'template_id',
			type: 'int',
			index: true
		},
		{
			name: 'data_type',
			type: 'string',
			len: 10
		},
		{
			name: 'text',
			type: 'string',
			len: 40
		},
		{
			name: 'text_align',
			type: 'string',
			len: 40
		},
		{
			name: 'font',
			type: 'string',
			len: 40
		},
		{
			name: 'font_size',
			type: 'int',
			len: 2
		},
		{
			name: 'font_color',
			type: 'string',
			len: 40
		},
		{
			name: 'font_color',
			type: 'string',
			len: 40
		},
		{
			name: 'col',
			type: 'string',
			len: 40
		},
		{
			name: 'border',
			type: 'bool'
		},
		{
			name: 'x',
			type: 'int',
			len: 4
		},
		{
			name: 'y',
			type: 'int',
			len: 4
		},
		{
			name: 'w',
			type: 'int',
			len: 4
		},
		{
			name: 'h',
			type: 'int',
			len: 4
		}
	]
});