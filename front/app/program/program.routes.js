/**
 * This file defines the routes of member module
 *
 * @returns {String}
 */
define([],
    function () {
        'use strict';
        var routesModuleName = "program.routes";
        angular.module(routesModuleName, ['ui.router'])
            .config(["$stateProvider", function ($stateProvider) {
                var modulePath = "./app/program";
                $stateProvider
                    .state("program.presentation", {
                        params: {id: null, isEasy: null},
                        url: '/:id/edit/step1/:isEasy',
                        templateUrl: modulePath + '/views/presentation/presentation.html',
                        controller: 'program:presentationController'
                    })
                    .state("program.commissioning", {
                        params: {id: null},
                        url: '/:id/edit/step2',
                        templateUrl: modulePath + '/views/commissioning/commissioning.html',
                        controller: 'program:commissioningController'
                    })
                    .state("program.generalTerms", {
                        params: {programId: null},
                        url: '/:programId/edit/step3',
                        templateUrl: modulePath + '/views/generalTerms/generalTerms.html',
                        controller: 'program:generalTermsController'
                    })
                    .state("program.cgv", {
                        params: {programId: null},
                        url: '/:programId/edit/step3/1',
                        templateUrl: modulePath + '/views/generalTerms/cgv.html',
                        controller: 'program:generalTermsController'
                    })
                    .state("program.payment", {
                        params: {programId: null},
                        url: '/:programId/edit/step4',
                        templateUrl: modulePath + '/views/payment/payment.html',
                        controller: 'program:paymentController'
                    })
                    .state("program.configuration", {
                        params: {id: null},
                        url: '/:id/configuration',
                        templateUrl: modulePath + '/views/configuration/configuration.html',
                        controller: 'program:configurationController'
                    })
                    .state("program.journal", {
                        params: {id: null},
                        url: '/:id/journal',
                        templateUrl: modulePath + '/views/journal/journal.html',
                        controller: 'program:journalController'
                    })
                    .state("program.administration", {
                        params: {id: null},
                        url: '/:id/administration',
                        templateUrl: modulePath + '/views/administration/administration.html',
                        controller: 'program:administrationController'
                    })
                    .state("program.sponsorship", {
                        params: {programId: null, participatesId: null},
                        url: '/:programId/filleuls/:participatesId',
                        templateUrl: modulePath + '/views/sponsorship/sponsorship.html',
                        controller: 'program:sponsorshipController'
                    })
                    .state("program.affair", {
                        params: {programId: null, affairId: null},
                        url: '/:programId/affair/:affairId/view',
                        templateUrl: modulePath + '/views/affair/affair.html',
                        controller: 'program:affairController'
                    });
            }]);
        return routesModuleName;
    }
);


