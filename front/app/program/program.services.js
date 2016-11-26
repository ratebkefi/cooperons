/**
 * This file defines the list services of programs
 *
 * @param {service} programService
 * @param {service} giftOrderService
 * @param {service} easySetting
 * @returns {service}
 */
define([
    "./repositories/programRepository",
    "./services/programService",
    "./services/giftOrderService",
    "./services/easySettingService",
    "./services/mailInvitationService",
    "./services/operationService",
    "./services/affairService",
    "./services/participateService",
    "./services/invitationService"
], function (programRepository, programService, giftOrderService, easySettingService, mailInvitationService,
             operationService, affairService, participateService, invitationService) {
    'use strict';
    var servicesModuleName = "program.services";

    angular.module(servicesModuleName, [])
        /* View handlers */
        .factory('programRepository', programRepository)
        .factory('programService', programService)
        .factory('giftOrderService', giftOrderService)
        .factory('easySettingService', easySettingService)
        .factory('mailInvitationService', mailInvitationService)
        .factory('operationService', operationService)
        .factory('affairService', affairService)
        .factory('participateService', participateService)
        .factory('invitationService', invitationService);;

    return servicesModuleName;
});