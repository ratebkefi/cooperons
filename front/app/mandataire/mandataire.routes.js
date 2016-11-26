/**
 * This file defines the routes of mandataire module
 *
 * @returns {String}
 */
define([],
    function () {
        'use strict';
        var routesModuleName = "mandataire.routes";
        angular.module(routesModuleName, ['ui.router'])
            .config(["$stateProvider", function ($stateProvider) {
                var modulePath = "./app/mandataire";
                $stateProvider
                    .state("mandataire.provision", {
                        params: {id: null},
                        url: '/:id/provision',
                        templateUrl: modulePath + '/views/provision/provision.html',
                        controller: 'mandataire:provisionController'
                    })
                    .state("mandataire.partyAdministration", {
                        params: {id: null},
                        url: '/party/:id/administration',
                        templateUrl: modulePath + '/views/partyAdministration/partyAdministration.html',
                        controller: 'mandataire:partyAdministrationController'
                    });
            }]);
        return routesModuleName;
    }
);


