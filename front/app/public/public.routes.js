/**
 * This file defines the routes of public module
 *
 * @returns {String}
 */
define([],
    function () {
        'use strict';
        var routesModuleName = "public.routes";
        angular.module(routesModuleName, ['ui.router'])
            .config(["$stateProvider", function ($stateProvider) {
                var modulePath = "./app/public";
                $stateProvider
                    .state("public.invitationParrainage", {
                        params: {token: null},
                        url: '/invitation/:token',
                        templateUrl: modulePath + '/views/invitation/invitation.html',
                        controller: 'public:invitationController'
                    })
                    .state("public.createUser", {
                        params: {token: null},
                        url: '/user/create/:token',
                        templateUrl: modulePath + '/views/createUser/createUser.html',
                        controller: 'public:createUserController'
                    })
                    .state("public.connectMember", {
                        params: {token: null},
                        url: '/member/connect/:token',
                        templateUrl: modulePath + '/views/connectMember/connectMember.html',
                        controller: 'public:connectMemberController'
                    })
                    .state("public.activateAccount", {
                        params: {token: null},
                        url: '/account/activate/:token',
                        controller: 'public:activateAccountController'
                    });
            }]);
        return routesModuleName;
    }
);


