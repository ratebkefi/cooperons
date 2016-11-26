/**
 * This file defines the list controllers of autoEntrepreneur module
 */
define([
    './controllers/settingContractController',
    './controllers/settingSettlementsController'
], function (settingContractController, settingSettlementsController) {
    'use strict';

    var controllersModuleName = 'autoEntrepreneurController.controllers';

    angular.module(controllersModuleName, [])
        .controller('autoEntrepreneur:settingContractController', settingContractController)
        .controller('autoEntrepreneur:settingSettlementsController', settingSettlementsController);

    return controllersModuleName;
});