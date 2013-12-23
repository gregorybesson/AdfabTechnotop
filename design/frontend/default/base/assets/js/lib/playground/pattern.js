/**
 * this is the main application file, which one that init project bind event, etc...
 * Copyright (C) 2013 - Adfab - nicolas labb�
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

//pl.ready(function ()
//{
    'use strict';
    
	/**
	 * @namespace  reference to Adfab.Playground.Promise object
	 */
     
    /**
     * Create a new instance of Promise pattern
     * 
     * @name promise
     */
    var promise = function ()
    {
        'use strict';
    };
    
    /**
     * Create a new instance of Promise pattern
     * 
     * @name PG.Promise
     * @class
	 * @constructor
	 * 
	 * @param {null}
	 * @return {Object} this
     * 
     * @this {object} PG.Promise
	 * 
	 * @example
	 * var p = new PG.Promise();
	 * // call success callback
	 * p.resolve();
	 * // call error callback
	 * p.reject();
	 * 
     * @since version 1.0.0
	 */
    promise.prototype = {
    	
		/**
	     * @ignore
	     */
	    pending: {
	        resolve: function ()
			{
	            'use strict';
	        },
	        reject: function ()
	        {
	            'use strict';
	        }
	    },
	    
	    /** function called when a promise is done
	     * 
	     * @function
	     * @name PG.Promise.then
	     * 
	     * @param {Function} success callback
	     * @param {Function} failure callback
	     * 
	     * @this {object} PG.Promise
	     * 
	     * @example
	     * PG.Promise.then( {Function} success, {Function} error)
	     * 
	     * @since version 1.0.0
	     */
        then: function (success, failure)
        {
            'use strict';
            
            this.pending = { resolve: success, reject: failure };
            return this;
        },
        
	    /**
	     * never call this method, use then (success, failure)
	     * @function
	     * 
	     * @name PG.Promise.resolve
	     * 
	     * @param {object} value
	     * @return {null} no return
	     * 
	     * @this {object} PG.Promise
	     * 
	     * @ignore
	     * 
	     * @since version 1.0.0
	     */
        resolve: function (value)
        {
            'use strict';
            
            this.pending.resolve(value);
        },
        
	    /**
	     * never call this method, use then (success, failure)
	     * @function
	     * 
	     * @name PG.Promise.reject
	     * 
	     * @param {object} value
	     * @return {null} no return
	     * 
	     * @this {object} PG.Promise
	     * 
	     * @ignore
	     * 
	     * @since version 1.0.0
	     */
        reject: function (error)
        {
            'use strict';
            
            this.pending.reject(error);
        }
    };
    
    // put the promise into Adfab.Playground.Promise
    try {
        addToNamespace('Promise', promise);
    }catch(e) {
       throw new Error( "Cannot extends 'Promise' to 'Adfab.playground.Promise'" );
    }
//});