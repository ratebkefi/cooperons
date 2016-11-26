define([
    './repositories/autoEntrepreneurRepository',
    './repositories/quarterlyTaxationRepository',
    './services/settingContractService',
    './services/settingSettlementsService'
], function (autoEntrepreneurRepository, quarterlyTaxationRepository, settingContractService, settingSettlementsService) {
    'use strict';

    var servicesModuleName = "autoEntrepreneur.services";

    angular.module(servicesModuleName, [])
        .factory('autoEntrepreneurRepository', autoEntrepreneurRepository)
        .factory('quarterlyTaxationRepository', quarterlyTaxationRepository)
        .factory('settingContractService', settingContractService)
        .factory('settingSettlementsService', settingSettlementsService);

    return servicesModuleName;
});