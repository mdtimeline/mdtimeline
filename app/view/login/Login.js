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

Ext.define('App.view.login.Login', {
	extend: 'Ext.Viewport',
	requires: [
		'App.ux.combo.Languages',
		'App.ux.combo.ActiveFacilities'
	],

	initComponent: function(){
		var me = this;

		me.currSite = null;
		me.siteLang = window['lang']['lang_code'];

		// setting to show site field
		me.showSite = false;
		me.siteError = window.site === false || window.site === '';
		me.logged = false;

		me.enableReCaptcha = false;

		me.theme = Ext.state.Manager.get('mdtimeline_theme', 'light');

		/**
		 * The Copyright Notice Window
		 */
		me.winCopyright = Ext.create('widget.window', {
			id: 'winCopyright',
			title: 'MD Timeline Copyright Notice',
			bodyStyle: 'background-color: #ffffff; padding: 5px;',
			autoLoad: 'gpl-licence-en.html',
			closeAction: 'hide',
			width: 900,
			height: 600,
			modal: false,
			resizable: true,
			draggable: true,
			closable: true
		});

		/**
		 * The Logon Window
		 */
		me.winLogon = Ext.create('widget.window', {
			closeAction: 'hide',
			plain: true,
			modal: false,
			resizable: false,
			draggable: false,
			closable: false,
			autoShow: true,
			frame: false,
			border: false,
			cls: 'login-window',
			shadow: false,
			items: [
				{
					xtype: 'form',
					defaultType: 'textfield',
					frame: false,
					border: false,
					baseParams: {
						auth: 'true'
					},
					layout: {
						type: 'hbox',
						align: 'top'
					},
					fieldDefaults: {
						msgTarget: 'side',
						labelAlign: 'top'
					},
					items: [
						{
							xtype: 'image',
							width: 190,
							height: 190,
							padding: 0,
							margin: 0,
							src: (me.theme == 'dark' ? 'resources/images/logo_190_190_dark.png' : 'resources/images/logo_190_190_light.png')
						},
						{
							xtype: 'fieldcontainer',
							margin: '0 10 0 35',
							width: 305,
							layout: 'anchor',
							items:[
								{
									xtype: 'textfield',
									fieldLabel: _('username'),
									blankText: 'Enter your username',
									name: 'authUser',
									minLengthText: 'Username must be at least 3 characters long.',
									minLength: 2,
									maxLength: 12,
									allowBlank: false,
									validationEvent: false,
									anchor: '97%',
									msgTarget: 'side',
									labelAlign: 'top',
									listeners: {
										scope: me,
										specialkey: me.onEnter
									}
								},
								{
									xtype: 'textfield',
									blankText: 'Enter your password',
									inputType: 'password',
									name: 'authPass',
									fieldLabel: _('password'),
									minLengthText: 'Password must be at least 4 characters long.',
									validationEvent: false,
									allowBlank: false,
									minLength: 4,
									maxLength: 12,
									anchor: '97%',
									msgTarget: 'side',
									labelAlign: 'top',
									listeners: {
										scope: me,
										specialkey: me.onEnter,
										afterrender:function(cmp){
											if(!eval(g('save_password'))){
												cmp.inputEl.set({
													autocomplete:'new-password'
												});
											}
										}
									}
								},
								{
									xtype: 'activefacilitiescombo',
									name: 'facility',
									fieldLabel: _('facility'),
									allowBlank: false,
									editable: false,
									hidden: true,
									storeAutoLoad: false,
									anchor: '97%',
									msgTarget: 'side',
									labelAlign: 'top',
									listeners: {
										scope: me,
										specialkey: me.onEnter,
										beforerender: me.onFacilityCmbBeforeRender
									}
								},
								{
									xtype: 'languagescombo',
									name: 'lang',
									fieldLabel: _('language'),
									allowBlank: false,
									editable: false,
									anchor: '97%',
									msgTarget: 'side',
									labelAlign: 'top',
									listeners: {
										scope: me,
										specialkey: me.onEnter,
										select: me.onLangSelect
									}
								}
							]
						}

					]

				}
			],
			listeners: {
				scope: me,
				afterrender: me.afterAppRender
			},
			buttons: [
				{
					xtype: 'button',
					itemId: 'themeSwitcherBtn',
					text: me.theme == 'light' ? _('go_dark') : _('go_light'),
					cls: 'login-theme-switch-btn',
					margin: '0 0 0 5',
					handler: me.onThemeSwitch,
					scope: me
				},
				'->',
				{
					text: _('reset'),
					name: 'btn_reset',
					scope: me,
					handler: me.onFormReset
				},
				'-',
				{
					text: _('login'),
					name: 'btn_login',
					scope: me,
					margin: '0 5 0 0',
					handler: me.loginSubmit
				}
			]
		});

		me.listeners = {
			resize: me.onAppResize
		};

		me.callParent(arguments);

		var fieldcontainer = this.winLogon.down('form').down('fieldcontainer');

		if(me.showSite){
			fieldcontainer.add({
				xtype: 'combobox',
				name: 'site',
				itemId: 'site',
				displayField: 'site',
				valueField: 'site',
				queryMode: 'local',
				fieldLabel: 'Site',
				store: me.storeSites = Ext.create('App.store.login.Sites'),
				allowBlank: false,
				editable: false,
				labelWidth: 100,
				anchor: '97%',
				msgTarget: 'side',
				labelAlign: 'top',
				listeners: {
					scope: me,
					specialkey: me.onEnter,
					select: me.onSiteSelect
				}
			});

		}else{

			fieldcontainer.add({
				xtype: 'hiddenfield',
				name: 'site',
				itemId: 'site',
				value: window.site
			});
		}

		if(!me.siteError){

			Ext.Function.defer(function () {
				me.siteLogo = Ext.create('Ext.Img', {
					src: 'sites/' + window.site + '/logo-' + me.theme +'.png',
					renderTo: me.el,
					floating: true,
					defaultAlign: 'b-t',
					width: 320,
					height: 120,
					shadow: false,
					border: false
				});

				me.siteLogo.alignTo(me.el, 't-t', [0,25]);

			}, 300);
		}else{
			me.msg('Oops!', 'Sorry no site configuration file found.<br>Please contact Support Desk.', true);
		}
	},

	onThemeSwitch: function (btn) {
		var theme = this.theme == 'dark' ? 'light' : 'dark';
		Ext.state.Manager.set('mdtimeline_theme', theme);
		window.location.reload();
	},

	/**
	 * when keyboard ENTER key press
	 * @param field
	 * @param e
	 */
	onEnter: function(field, e){
		if(e.getKey() == e.ENTER){
			this.loginSubmit();
		}
	},

	onFacilityCmbBeforeRender: function(cmb){
		var me = this;
		cmb.getStore().on('load', me.onFacilityLoad, me);
		cmb.getStore().load();
	},
	/**
	 * Form Submit/Logon function
	 */
	loginSubmit: function(){

		var me = this,
			formPanel = me.winLogon.down('form'),
			form = formPanel.getForm(),
			params = form.getValues();
			//checkInMode = formPanel.query('checkbox')[0].getValue();

		if(me.enableReCaptcha && params['g-recaptcha-response'] == ''){
			me.msg('Oops!', 'reCaptcha Validation Required.', true);
			return;
		}


		if(form.isValid()){
			me.winLogon.el.mask('Sending credentials...');
			authProcedures.login(params, function(provider, response){
				if(response.result.success){
					window.location.reload();
				}else{
					me.msg('Oops!', response.result.message, true);
					me.onFormReset();
					me.winLogon.el.unmask();
				}
			});
		}else{
			me.msg('Oops!', 'Username And Password are required.', true);
		}
	},
	/**
	 * gets the site combobox value and store it in currSite
	 * @param combo
	 * @param value
	 */
	onSiteSelect: function(combo, value){
		this.currSite = combo.getValue();
	},

	onLangSelect: function(combo, value){
		this.siteLang = combo.getValue();
	},

	onFacilityLoad: function(store, records){
		var cmb = this.winLogon.down('form').getForm().findField('facility');

		store.insert(0, {
			option_name: 'Default',
			option_value: '0'
		});

		cmb.setVisible(records.length > 1);
		cmb.select(0);
	},

	/**
	 * form rest function
	 */
	onFormReset: function(){
		var me = this,
			form = me.winLogon.down('form').getForm();

		form.setValues({
			site: window.site,
			authUser: '',
			authPass: '',
			lang: me.siteLang
		});

		form.findField('authUser').focus();
	},
	/**
	 * After form is render load store
	 */
	afterAppRender: function(win){

		var me = this,
			formPanel = win.down('form'),
			form = formPanel.getForm(),
			langCmb = form.findField('lang'),
			themeSwitcherBtn = win.query('#themeSwitcherBtn')[0],
			fieldContainer = formPanel.down('fieldcontainer');

		themeSwitcherBtn.action = Ext.state.Manager.get('mdtimeline_theme', g('application_theme'));

		if(themeSwitcherBtn.action == 'dark'){
			themeSwitcherBtn.setText(_('go_light'));
		}else{
			themeSwitcherBtn.setText(_('go_dark'));
		}


		if(!me.siteError){
			if(me.showSite){
				me.storeSites.load({
					scope: me,
					callback: function(records, operation, success){
						if(success === true){
							/**
							 * Lets add a delay to make sure the page is fully render.
							 * This is to compensate for slow browser.
							 */
							Ext.Function.defer(function(){
								me.currSite = records[0].data.site;
								if(me.showSite){
									form.findField('site').setValue(me.currSite);
								}
							}, 500, this);
						}
						else{
							me.msg('Oops! Something went wrong...', 'No site found.', true);
						}
					}
				});
			}

			langCmb.store.load({
				callback: function(){
					langCmb.setValue(me.siteLang);

				}
			});

			Ext.Function.defer(function(){
				//me.onAppResize();
				form.findField('authUser').inputEl.focus();
			}, 200);

		}

		if(me.enableReCaptcha){

			fieldContainer.insert(2, [
				{
					xtype: 'container',
					id: 'g-recaptcha-container',
					anchor: '97%',
					height: 80,
					margin: '10 0 0 0',
					listeners: {
						scope: me,
						afterrender: function (comp) {

							Ext.Function.defer(function(){

								grecaptcha.render(comp.el.dom.lastChild.lastChild, {
									'sitekey': g('recaptcha_public_key'),
									'callback': function (response) {
										say('callback');

										form.findField('g-recaptcha-response').setValue(response);
									},
									'expired-callback': function (response) {
										say('expired-callback');

										form.findField('g-recaptcha-response').setValue('');
									},
									'lang' : 'en',
									'theme': me.theme
								});

								me.winLogon.doLayout();

							}, 500);

						}
					}
				},
				{
					xtype: 'hiddenfield',
					name: 'g-recaptcha-response',
					value: ''
				}
			]);
		}

		win.doLayout();

	},
	/**
	 *  animated msg alert
	 * @param title
	 * @param format
	 * @param error
	 * @param persistent
	 */
	msg: function(title, format, error, persistent){
		var msgBgCls = (error === true) ? 'msg-red' : 'msg-green';
		this.msgCt = Ext.get('msg-div');
		this.msgCt.alignTo(document, 't-t');
		var s = Ext.String.format.apply(String, Array.prototype.slice.call(arguments, 1)),
			m = Ext.core.DomHelper.append(this.msgCt, {
				html: '<div class="flyMsg ' + msgBgCls + '"><h3>' + (title || '') + '</h3><p>' + s + '</p></div>'
			}, true);
		if(persistent === true) return m; // if persistent return the message element without the fade animation
		m.addCls('fadeded');
		Ext.create('Ext.fx.Animator', {
			target: m,
			duration: error ? 7000 : 2000,
			keyframes: {
				0: { opacity: 0 },
				20: { opacity: 1 },
				80: { opacity: 1 },
				100: { opacity: 0, height: 0 }
			},
			listeners: {
				afteranimate: function(){
					m.destroy();
				}
			}
		});
		return true;
	},

	onAppResize: function(){
		var body = Ext.getBody();
		this.winLogon.alignTo(body, 'c-c');

		if(this.siteLogo) {
			this.siteLogo.alignTo(body, 't-t', [0, 25]);
		}
	}
});
