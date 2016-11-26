/*global define, angular, list of services*/
define([
    './repositories/contractRepository',
    './repositories/serviceTypeRepository',
    './repositories/legalDocumentRepository',
    './services/contractService',
    './services/contractListService',
    './services/contractLegalService',
    './services/contractAdministrationService'
], function (contractRepository, serviceTypeRepository, legalDocumentRepository, contractService, contractListService,
             contractLegalService, contractAdministrationService) {
    'use strict';

    var servicesModuleName = 'contract.services';

    angular.module(servicesModuleName, [])
        .factory('contractRepository', contractRepository)
        .factory('serviceTypeRepository', serviceTypeRepository)
        .factory('legalDocumentRepository', legalDocumentRepository)
        .factory('contractService', contractService)
        .factory('contractListService', contractListService)
        .factory('contractLegalService', contractLegalService)
        .factory('contractAdministrationService', contractAdministrationService);

    return servicesModuleName;
});