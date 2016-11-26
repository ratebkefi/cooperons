/*global define, angular, list of services*/
define([
    './repositories/collaboratorRepository',
    './repositories/habilitationRepository',
    './repositories/partyRepository',
    './services/partyService',
    './services/collaboratorService',
    './services/habilitationService'
], function (collaboratorRepository, habilitationRepository, partyRepository, partyService, collaboratorService, habilitationService) {
    'use strict';

    var servicesModuleName = "party.services";

    angular.module(servicesModuleName, [])
        .factory('collaboratorRepository', collaboratorRepository)
        .factory('habilitationRepository', habilitationRepository)
        .factory('partyRepository', partyRepository)
        .factory('partyService', partyService)
        .factory('collaboratorService', collaboratorService)
        .factory('habilitationService', habilitationService);

    return servicesModuleName;
});