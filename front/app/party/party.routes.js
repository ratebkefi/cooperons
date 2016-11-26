/**
 * This file defines the routes of party module
 */
define([],
    function () {
        'use strict';

        var routesModuleName = 'party.routes';
        var modulePath = 'app/party';

        angular.module(routesModuleName, []).config(/*ngInject*/function ($stateProvider) {
            $stateProvider
                .state('party.home', {
                    url: '',
                    templateUrl: modulePath + '/views/party/party.html',
                    controller: 'party:PartyController',
                    controllerAs: 'partyCtr'
                })
                .state('party.habilitations', {
                    url: '/habilitations',
                    templateUrl: modulePath + '/views/habilitation/habilitation.html',
                    controller: 'party:HabilitationController',
                    controllerAs: 'habilitationCtr'
                });

        });

        return routesModuleName;
    }
);


