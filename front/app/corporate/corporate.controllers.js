/**
 * This file defines the list controllers of corporate module
 */
define([
    './controllers/settingCorporateController',
    './controllers/collegeController',
    './controllers/collegeInvitationController'
], function (SettingCorporateController, CollegeController, CollegeInvitationController) {
    'use strict';

    var controllersModuleName = 'corporate.controllers';

    angular.module(controllersModuleName, [])
            .controller('corporate:SettingCorporateController', SettingCorporateController)
            .controller('corporate:CollegeController', CollegeController)
            .controller('corporate:CollegeInvitationController', CollegeInvitationController);

    return controllersModuleName;
});