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
Ext.define('App.ux.form.fields.Canvas', {
	extend: 'Ext.form.FieldContainer',
	mixins: {
		field: 'Ext.form.field.Field'
	},
	xtype: 'cnavasfield',
	layout: 'absolute',

	emptyText: '',
	readOnly: false,
//	combineErrors: true,
	msgTarget: 'under',
	width: 600,
	height: 380,

	// inputValue: '1',
	// uncheckedValue: '0',

	controller: this,
	mouse: {x: 0, y: 0},
	last_mouse: {x: 0, y: 0},
	ppts: [],
	paint: false,
	color: '#000000',

	clickX: [],
	clickY: [],
	clickDrag: [],
	paint: false,
	offsetTop: 0,
	offsetLeft: 0,

	iCanvas: null,
	iContext: null,
	dCanvas: null,
	dCanvas: null,

	image: null,
	drawing: null,

	initComponent: function(){
		var me = this;

		me.items = [
			{
				xtype: 'box',
				x: 0,
				y: 0,
				action:'image',
				autoEl: {
					tag: 'canvas'
				}
			},
			{
				xtype: 'box',
				x: 0,
				y: 0,
				action:'drawing',
				autoEl: {
					tag: 'canvas'
				}
			},
			{
				xtype: 'button',
				x: 5,
				y: 5,
				iconCls: 'fas fa-palette',
				listeners: {
					scope: me,
					click: me.onPaletteBtnClick
				}
			},
			{
				xtype: 'button',
				x: 30,
				y: 5,
				iconCls: 'fas fa-undo',
				listeners: {
					scope: me,
					click: me.onUndoBtnClick
				}
			}
		];

		// me.on('resize', me.setFieldResize, me);
		me.on('afterrender', me.initFormCanvas, me);

		me.callParent();

	},

	initFormCanvas: function() {

		var me = this;

		me.mouse = {x: 0, y: 0};
		me.last_mouse = {x: 0, y: 0};
		me.ppts = [];
		me.paint = false;
		me.color = '#000000';

		me.iCanvas = me.getComponent(0).el;
		me.iContext = me.iCanvas.dom.getContext("2d");

		me.dCanvas = me.getComponent(1).el;
		me.dContext = me.dCanvas.dom.getContext("2d");

		me.dContext.lineWidth = 5;
		me.dContext.lineJoin = 'round';
		me.dContext.lineCap = 'round';
		me.dContext.strokeStyle = me.color;
		me.dContext.fillStyle = me.color;

		me.setFormCanvasEvents();

		me.setCanvasBoxes();

		if (Ext.isString(me.image)) {
			me.loadImage(me.image);
		}

		if (me.drawing) {
			me.loadDrawing(me.drawing);
		}

	},

	loadImage: function(src){

		var me = this;

		say('loadImage');
		say(src);

		me.image = new Image();
		me.image.width = me.getWidth();
		me.image.height = me.getHeight();

		me.image.onload = function(){
			me.setCanvasBoxes();
			me.iContext.clearRect(0, 0, me.image.width, me.image.height);
			me.iContext.drawImage(me.image, 0, 0, me.image.width, me.image.height);
			me.iContext.save();
		};
		me.image.src = src;
	},

	loadDrawing: function(src){
		var me = this;

		say('loadDrawing');
		say(src);

		me.drawing = null;
		me.drawing = new Image();
		me.drawing.onload = function(){
			me.dContext.clearRect(0, 0, me.dContext.canvas.width, me.dContext.canvas.height);
			me.dContext.drawImage(me.drawing, 0, 0, me.dContext.canvas.width, me.dContext.canvas.height);
			me.dContext.save();
		};
		me.drawing.src = src;
	},

	setCanvasBoxes: function(){

		var me = this,
			w = me.getWidth(),
			h = me.getHeight();

		me.dCanvas.setWidth(w);
		me.dCanvas.setHeight(h);
		me.dContext.canvas.width = w;
		me.dContext.canvas.height = h;

		me.iCanvas.setWidth(w);
		me.iCanvas.setHeight(h);
		me.iContext.canvas.width = w;
		me.iContext.canvas.height = h;
	},

	setFieldResize: function (){
		this.setCanvasBoxes();
	},

	setFormCanvasEvents: function(){
		this.dCanvas.on('mouseup', this.onMouseUp, this);
		this.dCanvas.on('mouseleave', this.onMouseLeave, this);
		this.dCanvas.on('mousemove', this.onMouseMove, this);
		this.dCanvas.on('mousedown', this.onMouseDown, this);
	},

	stopFormCanvasEvents: function(){
		this.dCanvas.un('mouseup', this.onMouseUp, this);
		this.dCanvas.un('mouseleave', this.onMouseLeave, this);
		this.dCanvas.un('mousemove', this.onMouseMove, this);
		this.dCanvas.un('mousedown', this.onMouseDown, this);
	},

	onMouseUp: function(){
		this.paint = false;
		this.ppts = [];
	},

	onMouseLeave: function(){
		this.paint = false;
	},

	onMouseMove: function(e){
		if(this.paint){
			this.mouse.x = e.getX() - this.offsetLeft;
			this.mouse.y = e.getY() - this.offsetTop;
			this.doPaint();
		}
	},

	onMouseDown: function(e){
		this.setFormCanvasOffSets(this);
		this.paint = true;
		this.mouse.x = e.getX() - this.offsetLeft;
		this.mouse.y = e.getY() - this.offsetTop;
		this.ppts.push({x: this.mouse.x, y: this.mouse.y});
		this.doPaint();
	},

	setFormCanvasOffSets: function(){
		this.offsetLeft = this.dCanvas.getX();
		this.offsetTop = this.dCanvas.getY();
	},

	doPaint: function(){
		var me = this,
			b, i, c, d;

		me.ppts.push({x: me.mouse.x, y: me.mouse.y});

		if(me.ppts.length < 3){
			b = me.ppts[0];
			me.dContext.beginPath();
			me.dContext.arc(b.x, b.y, me.dContext.lineWidth / 2, 0, Math.PI * 2, !0);
			me.dContext.fill();
			me.dContext.closePath();
			return;
		}

		// Tmp canvas is always cleared up before drawing.
		me.dContext.clearRect(0, 0, me.dCanvas.width, me.dCanvas.height);

		me.dContext.beginPath();
		me.dContext.moveTo(me.ppts[0].x, me.ppts[0].y);

		for(i = 1; i < me.ppts.length - 2; i++){
			c = (me.ppts[i].x + me.ppts[i + 1].x) / 2;
			d = (me.ppts[i].y + me.ppts[i + 1].y) / 2;
			me.dContext.quadraticCurveTo(me.ppts[i].x, me.ppts[i].y, c, d);
		}

		// For the last 2 points
		me.dContext.quadraticCurveTo(
			me.ppts[i].x,
			me.ppts[i].y,
			me.ppts[i + 1].x,
			me.ppts[i + 1].y
		);
		me.dContext.stroke();
	},

	onUndoBtnClick: function(btn){
		this.loadDrawing(this.initialConfig.drawing);
		this.dContext.clearRect(0, 0, this.dContext.canvas.width, this.dContext.canvas.height);
	},

	onPaletteBtnClick: function(btn){
		var me = this,
			picker = Ext.widget('window', {
				items: [
					{
						xtype: 'colorpicker',
						value: me.color,
						listeners: {
							scope: me,
							select: me.onColorSelect
						}
					}
				]
			});

		picker.showAt(btn.getXY());
	},

	onColorSelect: function (field, color){
		this.color = '#' + color;
		this.dContext.strokeStyle = this.color;
		this.dContext.fillStyle = this.color;
		field.up('window').close();
	},

	getValue: function(){
		var val = {};
		val[this.name] = {
			image: this.iCanvas.dom.toDataURL(),
			drawing: this.dCanvas.dom.toDataURL(),
		};
		return val;
	},

	getSubmitValue: function(){
		return this.getValue();
	},

	setValue: function(value){

		Ext.Function.defer(function (){
			if(value.image){
				this.loadImage(value.image);
			}
			if(value.drawing){
				this.loadDrawing(value.drawing);
			}
		}, 250, this);

	},

	// Bug? A field-mixin submits the data from getValue, not getSubmitValue
	getSubmitData: function(){
		return this.getValue();
	},

	setReadOnly: function(value){
		// this.chekboxField.setReadOnly(value);
		// this.textField1.setReadOnly(value);
		// if(this.textField2) this.textField2.setReadOnly(value);
	},

	isValid: function(){
		return true;
	}
});
