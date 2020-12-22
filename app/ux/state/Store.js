/*******************************************************************************
 * Copyright (c) 2012 by Jan Philipp, Herrmann & Lenz Solutions GmbH
 *
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 ******************************************************************************/

/**
 * Default state store.
 *
 * This implementation relies on a simple remote HTTP based storage.
 */
Ext.define('App.ux.state.Store', {

    extend  : 'Ext.data.Store',
    requires: [ 'App.ux.state.Model' ],

    model: 'App.ux.state.Model',

    pageSize: -1,

    proxy: {
        type  : 'direct',
        api: {
            read: 'AppState.get',
            create: 'AppState.set',
            update: 'AppState.set',
            destroy: 'AppState.unset',
        },
        reader: {
            type: 'json',
            root: 'data',
        },
        writer: {
            type: 'json'
        }
    }

});