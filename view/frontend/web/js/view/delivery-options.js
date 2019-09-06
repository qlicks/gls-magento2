/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define([
    'jquery',
    'uiComponent',
    'ko',
    'TIG_GLS/js/helper/address-finder',
    'Magento_Checkout/js/model/quote'
], function (
    $,
    Component,
    ko,
    AddressFinder,
    quote
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'TIG_GLS/delivery/options',
            postcode: null,
            country: null,
            availableServices: ko.observableArray([]),
            parcelShops: ko.observableArray([])
        },

        initObservable: function () {
            this.selectedMethod = ko.computed(function () {
                var method = quote.shippingMethod();
                var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                return selectedMethod;
            }, this);
            
            
            this._super().observe([
                'postcode',
                'country',
                'availableServices',
                'parcelShops'
            ]);

            AddressFinder.subscribe(function (address, oldAddress) {
                if (!address || JSON.stringify(address) == JSON.stringify(oldAddress)) {
                    return;
                }

                if (address.country !== 'NL') {
                    return;
                }
                this.getAvailableServices();
                this.getParcelShops(address.postcode);
            }.bind(this));

            return this;
        },
    
        /**
         * Retrieve Delivery Options from GLS.
         *
         * This is done through a controller, because we will start using an API
         * in the near future.
         */
        getAvailableServices: function () {
            $.ajax({
                method : 'GET',
                url    : '/gls/deliveryoptions/services',
                type   : 'jsonp'
            }).done(function (data) {
                this.availableServices(data);
            }.bind(this));
        },
    
        /**
         * Retrieve Parcel Shops from GLS.
         *
         * @param postcode
         */
        getParcelShops: function (postcode) {
            $.ajax({
                method : 'GET',
                url    : '/gls/deliveryoptions/parcelshops',
                type   : 'jsonp',
                data   : {
                    postcode: postcode
                }
            }).done(function (data) {
                this.parcelShops(data);
            }.bind(this));
        }
    });
});