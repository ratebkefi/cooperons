/*global define, angular, list of services*/
define([
    "./repositories/mandataireRepository",
    "./services/mandataireService",
    "./services/paymentService",
    "./services/settlementService",
    "./services/invoiceService",
], function (mandataireRepository, mandataireService, paymentService, settlementService, invoiceService) {
    'use strict';
    var servicesModuleName = "mandataire.services";
    angular.module(servicesModuleName, [])
        /* Models */

        /* View handlers */
        .factory('mandataireRepository', mandataireRepository)
        .factory('mandataireService', mandataireService)
        .factory('paymentService', paymentService)
        .factory('settlementService', settlementService)
        .factory('invoiceService', invoiceService);

    return servicesModuleName;
});