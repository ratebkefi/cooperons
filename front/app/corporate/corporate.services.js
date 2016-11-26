define([
    './repositories/collegeRepository',
    './repositories/corporateRepository',
    './repositories/attestationRepository',
    './services/collegeService',
    './services/collegeInvitationService',
    './services/settingCorporateService'
], function (collegeRepository, corporateRepository, attestationRepository, collegeService, collegeInvitationService,
             settingCorporateService) {
    'use strict';

    var servicesModuleName = 'corporate.services';

    angular.module(servicesModuleName, [])
        .factory('collegeRepository', collegeRepository)
        .factory('corporateRepository', corporateRepository)
        .factory('attestationRepository', attestationRepository)
        .factory('collegeService', collegeService)
        .factory('collegeInvitationService', collegeInvitationService)
        .factory('settingCorporateService', settingCorporateService);

    return servicesModuleName;
});