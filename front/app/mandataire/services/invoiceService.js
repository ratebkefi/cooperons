/**
 * @ngdoc service
 * @name mandataire.services:invoiceService
 * @description
 * This file defines the invoiceService
 */
define([], function () {
    'use strict';
    invoiceService.$inject = ["$q", 'Restangular'];
 
    function invoiceService($q, Restangular) {
        return {

            basePayments: Restangular.all('invoices'),
 
            /**
            * @ngdoc method
            * @name getInvoices
            * @methodOf mandataire.services:invoiceService
            * @description
            * Get invoices
            *   
            * @returns {object} Invoices data
            */
            getInvoices: function () {
                return Restangular.all('invoices').getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name downloadInvoice
            * @methodOf mandataire.services:invoiceService
            * @description
            * Download invoice in pdf format
            *  
            * @param {number} id Id of invoice 
            * 
            * @returns {object} Invoice pdf data
            */
            downloadInvoice: function (id) {
                return Restangular.one('invoices', id).withHttpConfig({responseType: 'blob'}).get({_format: 'pdf'}).
                    then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return invoiceService;
});