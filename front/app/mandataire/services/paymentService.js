/**
 * @ngdoc service
 * @name mandataire.services:paymentService
 * @description
 * This file defines the paymentService
 */
define([], function () {
    'use strict';
    paymentService.$inject = ["$q", 'Restangular'];
 
    function paymentService($q, Restangular) {
        return {

            basePayments: Restangular.all('payments'),
 
            /**
            * @ngdoc method
            * @name getPayments
            * @methodOf mandataire.services:paymentService
            * @description
            * Get payments
            *  
            * @param {string} filter Filter key
            * 
            * @returns {object} Payments data
            */
            getPayments: function (filter) {
                return this.basePayments.getList(filter)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name exportPayment
            * @methodOf mandataire.services:paymentService
            * @description
            * Download payments in xls format
            *  
            * @param {array} ids Payments ids table
            * 
            * @returns {object} Payments data
            */
            exportPayment: function (ids) {
                return this.basePayments.one('export').withHttpConfig({responseType: 'arraybuffer'}).get({ids: JSON.stringify(ids)})
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name deletePayment
            * @methodOf mandataire.services:paymentService
            * @description
            * Delete payment
            *  
            * @param {array} paymentId Id of payment
            *  
            */
            deletePayment: function (paymentId) {
                return Restangular.one('payments', paymentId).remove()
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name patchPayments
            * @methodOf mandataire.services:paymentService
            * @description
            * Partial updating payments
            *  
            * @param {object} patch Data to patching
            * 
            * @returns {object} Payments data
            */
            patchPayments: function (patch) {
                return this.basePayments.patch(patch)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getNetbalances
            * @methodOf mandataire.services:paymentService
            * @description
            * Get net balances
            *   
            * @returns {object} Netbalances data
            */
            getNetbalances: function () {
                return Restangular.all('payments').all('netbalances').getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return paymentService;
});