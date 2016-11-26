/**
 * This file defines the routes of user module
 *
 * @returns {String}
 */
define([],
    function () {
        'use strict';

        var routesModuleName = "user.routes";

        angular.module(routesModuleName, ['ui.router'])
            .config(["$stateProvider", function ($stateProvider) {

                var modulePath = "./app/user";

                $stateProvider
                    .state("user.login", {
                        url: '/login',
                        templateUrl: modulePath + '/views/security/login.html',
                        controller: 'user:securityController'
                    })
                    .state("user.forgetPassword", {
                        url: '/password/forget',
                        templateUrl: modulePath + '/views/security/forgetPassword.html',
                        controller: 'user:securityController'
                    })
                    .state("user.resetPassword", {
                        params: {token: null},
                        url: '/password/reset/:token',
                        templateUrl: modulePath + '/views/security/resetPassword.html',
                        controller: 'user:securityController'
                    });
            }]);

        return routesModuleName;
    }
);


