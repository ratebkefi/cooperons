/**
 * This file defines the routes of member module
 */
define([],
    function () {
        'use strict';

        var routesModuleName = 'contract.routes';
        var modulePath = 'app/contract';

        angular.module(routesModuleName, []).config(/*ngInject*/function ($stateProvider) {
            $stateProvider
                .state('contract.administration', {
                    params: {id: null},
                    url: '/:id/administration',
                    templateUrl: modulePath + '/views/administration/administration.html',
                    controller: 'contract:AdministrationController',
                    controllerAs: 'adminCtr'
                })
                .state('contract.edit', {
                    params: {id: null},
                    url: '/:id/edit',
                    templateUrl: modulePath + '/views/contract/contract.html',
                    controller: 'contract:ContractController',
                    controllerAs: 'contractCtr'

                })
                .state('contract.legal', {
                    params: {id: null},
                    url: '/:id/legal',
                    templateUrl: modulePath + '/views/legal/legal.html',
                    controller: 'contract:ContractLegalController',
                    controllerAs: 'legalCtr'
                });
        });

        return routesModuleName;
    }
);


