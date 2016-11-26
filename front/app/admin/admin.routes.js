/* global define, angular, routes*/
define([],
    function () {
        'use strict';
        var routesModuleName = "admin.routes";
        angular.module(routesModuleName, ['ui.router'])
            .config(["$stateProvider", function ($stateProvider) {
                var modulePath = "app/admin";
                $stateProvider
                    .state("admin.home", {
                        url: '',
                        templateUrl: modulePath + '/views/home/home.html',
                        controller: 'admin:homeController'
                    })
                    .state("admin.user", {
                        params: {
                            searchValue: null
                        },
                        url: '/user/search/:searchValue',
                        templateUrl: modulePath + '/views/user/user.html',
                        controller: 'admin:userController'
                    })
                    .state("admin.autoEntrepreneur", {
                        params: {userId: null},
                        url: '/users/:userId/autoentrepreneur/edit',
                        templateUrl: modulePath + '/views/autoEntrepreneur/autoEntrepreneur.html',
                        controller: 'admin:autoEntrepreneurController'
                    })
                    .state("admin.previewInvoice", {
                        params: {idMandataire: null},
                        url: '/mandataire/:idMandataire/invoice/preview',
                        templateUrl: modulePath + '/views/previewInvoice/previewInvoice.html',
                        controller: 'admin:previewInvoiceController'
                    });

            }]);
        return routesModuleName;
    }
);