/**
 * This file defines the routes of member module
 *
 * @returns {String}
 */
define([],
    function () {
        'use strict';

        var routesModuleName = 'corporate.routes';
        var modulePath = './app/corporate';

        angular.module(routesModuleName, []).config(/*ngInject*/function ($stateProvider) {
            $stateProvider
                .state('corporate.edit', {
                    params: {idCorporate: null},
                    url: '/edit/:idCorporate',
                    templateUrl: modulePath + '/views/settingCorporate/settingCorporate.html',
                    controller: 'corporate:SettingCorporateController',
                    controllerAs: 'settingCorporateCtr'
                });
        });
        return routesModuleName;
    }
);


