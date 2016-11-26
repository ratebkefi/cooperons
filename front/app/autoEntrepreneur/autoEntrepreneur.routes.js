/**
 * This file defines the routes of autoEntrepreneur module
 */
define([],
    function () {
        'use strict';

        var routesModuleName = 'autoEntrepreneur.routes';
        var modulePath = './app/autoEntrepreneur';

        angular.module(routesModuleName, []).config(/*ngInject*/function ($stateProvider) {
            $stateProvider
                .state('autoEntrepreneur.settingContract', {
                    params: {id: null},
                    url: '/contract/:id/edit',
                    templateUrl: modulePath + '/views/settingContract/settingContract.html',
                    controller: 'autoEntrepreneur:settingContractController',
                    contractAs: 'settingContractCtr'
                })
                .state('autoEntrepreneur.settingSettlements', {
                    params: {id: null},
                    url: '/contract/:id/settlements/new',
                    templateUrl: modulePath + '/views/settingSettlements/settingSettlements.html',
                    controller: 'autoEntrepreneur:settingSettlementsController',
                    controllerAs: 'settingSettlementsCtr'

                });
        });

        return routesModuleName;
    }
);


