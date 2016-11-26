/**
 * @ngdoc controller
 * @name public.controllers:activateAccountController
 * @description
 * This file defines the activate account controller
 */
define([], function () {
    'use strict';

    activateAccountController.$inject = ['$rootScope', '$scope', '$state', 'userService', 'Notification', '$window'];

    function activateAccountController($rootScope, $scope, $state, userService, Notification, $window) {

        var token = $state.params.token;
        $scope.cgv = false;

        /**
         * @ngdoc method
         * @name activateAccount
         * @methodOf public.controllers:activateAccountController
         * @description
         * Resend mail of activation account
         *
         * @param {string} token User token
         *
         */
        $scope.activateAccount = function (token) {
            var patch = [{"op": "activate", "path": "/", "token": token}];
            userService.publicPatchUser(patch).then(function (respense) {
                Notification.success("Activation du compte effectuée avec succès");
                $window.localStorage.setItem('jwt_token', respense.data.jwtAuth.token);
                $window.localStorage.setItem('roles', respense.data.jwtAuth.roles);
                $state.go("member.home");
            });
        };

        $scope.activateAccount(token);
    }

    return activateAccountController;
});
