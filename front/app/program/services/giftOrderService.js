/**
 * @ngdoc service
 * @name program.services:giftOrderService
 * @description
 * This file defines the giftOrderService
 */
define([], function () {
    'use strict';
    giftOrderService.$inject = ["$q", 'Restangular'];
 
    function giftOrderService($q, Restangular) {
        return {

            baseGiftorders: Restangular.all('giftorders'),
 
            /**
            * @ngdoc method
            * @name getGiftOrders
            * @methodOf program.services:giftOrderService
            * @description
            * Get gift orders history
            *  
            * @returns {object} Gift orders list
            */
            getGiftOrders: function () {
                return this.baseGiftorders.getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name postGiftOrder
            * @methodOf program.services:giftOrderService
            * @description
            * Create gift order
            *  
            * @returns {object} Gift orders
            */
            postGiftOrder: function () {
                return this.baseGiftorders.post()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name patch
            * @methodOf program.services:giftOrderService
            * @description
            * Confirm gift order
            *  
            * @param {number} id Id of giftOrder
            * @param {object} patch Data to paching
            * 
            * @returns {object} Confirmation status
            */
            patch: function (id, patch) {
                return Restangular.one('giftorders', id).patch(patch)
                    .then(function () { 
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name downloadGiftOrder
            * @methodOf program.services:giftOrderService
            * @description
            * Download gift order in xls format
            *  
            * @param {number} idGiftOrder Id of giftOrder 
            * 
            * @returns {object} Gift Order
            */
            downloadGiftOrder: function (idGiftOrder) {
                return Restangular.one('giftorders', idGiftOrder).withHttpConfig({responseType: 'blob'}).get({_format: 'xls'})
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return giftOrderService;
});