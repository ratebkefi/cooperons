/**
 * This file defines the routes of member module
 *
 * @returns {String}
 */
define([],
    function () {
        "use strict";
        var routesModuleName = "member.routes";
        angular.module(routesModuleName, ['ui.router'])
            .config(["$stateProvider", function ($stateProvider) {
                var modulePath = "./app/member";
                $stateProvider
                    .state("member.home", {
                        url: '/home',
                        templateUrl: modulePath + '/views/home/home.html',
                        controller: 'member:homeController',
                        controllerAs: 'mainCtr'
                    })
                    .state("member.programs", {
                        url: "/programs",
                        templateUrl: modulePath + '/views/program/program.html',
                        controller: 'member:programController'
                    })
                    .state("member.account", {
                        url: "/account",
                        templateUrl: modulePath + '/views/account/account.html',
                        controller: 'member:accountController'
                    })
                    .state("member.filleuls", {
                        url: "/filleuls",
                        templateUrl: modulePath + '/views/filleul/filleul.html',
                        controller: 'member:filleulController'
                    })
                    .state("member.points", {
                        url: "/points",
                        templateUrl: modulePath + '/views/point/point.html',
                        controller: 'member:pointController'
                    });
            }]);
        return routesModuleName;
    }
);


