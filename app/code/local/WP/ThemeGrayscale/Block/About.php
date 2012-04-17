<?php

class WP_ThemeGrayscale_Block_About
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $default = <<<HTML
<div style="background-color:#EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:20px;">
    <p>
        <b style="font-size:12px;">WebAndPeople</b>, a family of niche sites, provides small businesses with everything they need to start selling online.
    </p>
    <p>
        <strong>PREMIUM and FREE MAGENTO TEMPALTES and EXTENSIONS</strong><br />
        <a href="http://web-experiment.info" target="_blank">Web-Experiment.info</a> offers a wide choice of nice-looking and easily editable free and premium Magento Themes. At Web-Experiment, you can find free downloads or buy premium tempaltes for the extremely popular Magento eCommerce platform.<br />
        <strong>MAGENTO HOSTING</strong></strong><br />
        <a href="http://magenting.com" target="_blank">Magenting.com</a>, a new and improved hosting solution, is allowing you to easily create, promote, and manage your online store with Magento. Magenting users will receive a valuable set of tools and features, including automatic Magento eCommerce installation, automatic Magento template installation and a free or paid professional Magento hosting account.<br />
        <strong>WEB DEVELOPMENT</strong><br />
        <a href="http://webandpeople.com" target="_blank">WebAndPeople.com</a> is a team of professional Web developers and designers who are some of the best in the industry. WebAndPeople provides Web application development, custom Magento theme designs, and Website design services.<br />
        <br />
    </p>
    <p>
        Our themes and extensions on <a href="http://www.magentocommerce.com/magento-connect/developer/WebAndPeople" target="_blank">MagentoConnect</a><br />
        Should you have any questions <a href="http://webandpeople.com/contact.html" target="_blank">Contact Us</a> or email at <a href="mailto:support@web-experiment.info">support@web-experiment.info</a>
        <br />
    </p>
</div>
HTML;
        $default = json_encode($default);
        $html = '<div id="wp_aboutus_content"></div>

<script type="text/javascript"> //<![CDATA[

    /* JSON-P implementation for Prototype.js somewhat by Dan Dean (http://www.dandean.com)
     *
     * *HEAVILY* based on Tobie Langel\'s version: http://gist.github.com/145466.
     * Might as well just call this an iteration.
     *
     * This version introduces:
     * - Support for predefined callbacks (Necessary for OAuth signed requests, by @rboyce)
     * - Partial integration with Ajax.Responders (Thanks to @sr3d for the kick in this direction)
     * - Compatibility with Prototype 1.7 (Thanks to @soung3 for the bug report)
     * - Will not break if page lacks a <head> element
     *
     * See examples in README for usage
     *
     * VERSION 1.1.2
     *
     * new Ajax.JSONRequest(url, options);
     * - url (String): JSON-P endpoint url.
     * - options (Object): Configuration options for the request.
     */
    Ajax.JSONRequest = Class.create(Ajax.Base, (function() {
        var id = 0, head = document.getElementsByTagName(\'head\')[0] || document.body;
        return {
            initialize: function($super, url, options) {
                $super(options);
                this.options.url = url;
                this.options.callbackParamName = this.options.callbackParamName || \'callback\';
                this.options.timeout = this.options.timeout || 10; // Default timeout: 10 seconds
                this.options.invokeImmediately = (!Object.isUndefined(this.options.invokeImmediately)) ? this.options.invokeImmediately : true ;

                if (!Object.isUndefined(this.options.parameters) && Object.isString(this.options.parameters)) {
                    this.options.parameters = this.options.parameters.toQueryParams();
                }

                if (this.options.invokeImmediately) {
                    this.request();
                }
            },

            /**
             *    Ajax.JSONRequest#_cleanup() -> undefined
             *    Cleans up after the request
             **/
            _cleanup: function() {
                if (this.timeout) {
                    clearTimeout(this.timeout);
                    this.timeout = null;
                }
                if (this.transport && Object.isElement(this.transport)) {
                    this.transport.remove();
                    this.transport = null;
                }
            },

            /**
             *    Ajax.JSONRequest#request() -> undefined
             *    Invokes the JSON-P request lifecycle
             **/
            request: function() {

                // Define local vars
                var response = new Ajax.JSONResponse(this);
                var key = this.options.callbackParamName,
                    name = \'_prototypeJSONPCallback_\' + (id++),
                    complete = function() {
                        if (Object.isFunction(this.options.onComplete)) {
                            this.options.onComplete.call(this, response);
                        }
                        Ajax.Responders.dispatch(\'onComplete\', this, response);
                    }.bind(this);

                // If the callback parameter is already defined, use that
                if (this.options.parameters[key] !== undefined) {
                    name = this.options.parameters[key];
                }
                // Otherwise, add callback as a parameter
                else {
                    this.options.parameters[key] = name;
                }

                // Build request URL
                this.options.parameters[key] = name;
                var url = this.options.url + ((this.options.url.include(\'?\') ? \'&\' : \'?\') + Object.toQueryString(this.options.parameters));

                // Define callback function
                window[name] = function(json) {
                    this._cleanup(); // Garbage collection
                    window[name] = undefined;

                    response.status = 200;
                    response.statusText = "OK";
                    response.setResponseContent(json);

                    if (Object.isFunction(this.options.onSuccess)) {
                        this.options.onSuccess.call(this, response);
                    }
                    Ajax.Responders.dispatch(\'onSuccess\', this, response);

                    complete();

                }.bind(this);

                this.transport = new Element(\'script\', { type: \'text/javascript\', src: url });

                if (Object.isFunction(this.options.onCreate)) {
                    this.options.onCreate.call(this, response);
                }
                Ajax.Responders.dispatch(\'onCreate\', this);

                head.appendChild(this.transport);

                this.timeout = setTimeout(function() {
                    this._cleanup();
                    window[name] = Prototype.emptyFunction;
                    if (Object.isFunction(this.options.onFailure)) {
                        response.status = 504;
                        response.statusText = "Gateway Timeout";
                        this.options.onFailure.call(this, response);
                    }
                    complete();
                }.bind(this), this.options.timeout * 1000);
            },
            toString: function() { return "[object Ajax.JSONRequest]"; }
        };
    })());

    Ajax.JSONResponse = Class.create({
        initialize: function(request) {
            this.request = request;
        },
        request: undefined,
        status: 0,
        statusText: \'\',
        responseJSON: undefined,
        responseText: undefined,
        setResponseContent: function(json) {
            this.responseJSON = json;
            this.responseText = Object.toJSON(json);
        },
        getTransport: function() {
            if (this.request) return this.request.transport;
        },
        toString: function() { return "[object Ajax.JSONResponse]"; }
    });

    window.onload = function(){
        var html = ' . $default . ';

        new Ajax.JSONRequest(\'http://web-experiment.info/about-us.php\', {
            callbackParamName: "jsoncallback",
            onComplete: function(response) {
                if (response.responseJSON && response.responseJSON.html) {
                    html = response.responseJSON.html;
                }
                Element.replace(\'wp_aboutus_content\', html);
            }
        });
    };
//]]></script>';
        return $html;
    }
}
