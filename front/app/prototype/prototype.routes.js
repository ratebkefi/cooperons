/**
 * @ngdoc module
 * @name prototype:prototype.routes
 * @description This file define angular routes for prototype module
 */

define([],
    function () {
        "use strict";

        var routesModuleName = "prototype.routes";
        var modulePath = "./app/prototype";

        angular.module(routesModuleName, ['ui.router'])
            .config(["$stateProvider", function ($stateProvider) {
                $stateProvider
                    .state("prototype.contrat", {
                        url: '/contrat/:contratId',
                        params: {
                            contratId: null,
                            legalDocumentId: null
                        },
                        templateUrl: modulePath + '/views/contrat/contrat.html',
                        controller: 'prototype:contratController'
                    })
                    .state("prototype.habilitations", {
                        url: '/habilitations',
                        templateUrl: modulePath + '/views/habilitation/habilitation.html',
                        controller: 'prototype:habilitationController'

                    })

            }]);

        return routesModuleName;
    }
);