/**
 * @ngdoc controller
 * @name public.controllers:createUserController
 * @description
 * This file defines the create user controller
 */
define([], function () {
    'use strict';

    createUserController.$inject = ['$rootScope', '$scope', '$state', 'config', 'common', 'userService'];

    function createUserController($rootScope, $scope, $state, config, common, userService) {

        var token = $state.params.token;
        $scope.token = token;
        $scope.cgv = false;
        $scope.errorMessages = {};


        /**
         * Object user
         */
        $scope.user = {email: null, plainPassword: {first: null, second: null}, lastName: null, firstName: null};

        /**
         * Object contact
         */
        $scope.contact = {phone: null, address: null, secondAddress: null, city: null, postalCode: null};

        /**
         * @ngdoc method
         * @name loadToken
         * @methodOf public.controllers:createUserController
         * @description
         * Get token data
         *
         * @return {void}
         */
        $scope.loadToken = function () {
            userService.getToken(token).then(function (data) {
                var invitation = null;

                $scope.program = null;
                if (data.hasProgram) {
                    $scope.program = data.object.program;
                    $scope.isProgramPlus = ($scope.program.id == config.idProgramPlus);
                }

                if (data.isParticipatesTo) {
                    invitation = data.object.member;
                    $scope.authentication.displayCheckboxCGV = true;
                } else if (data.isInvitation) {
                    invitation = data.object;
                } else {
                    common.dispatch();
                }

                if (invitation) {
                    $scope.user.firstName = invitation.firstName;
                    $scope.user.lastName = invitation.lastName;
                    $scope.user.email = invitation.email;
                    $scope.lockFirstName = invitation.firstName && invitation.firstName !== '';
                    $scope.lockLastName = invitation.lastName && invitation.lastName !== '';
                    $scope.lockEmail = invitation.email && invitation.email !== '';
                }

            })
                .catch(function () {
                    common.dispatch();
                });
        };

        /**
         * @ngdoc method
         * @name createUser
         * @methodOf public.controllers:createUserController
         * @description
         * Create user
         *
         * @param {object} form User form to be validated
         */
        $scope.createUser = function (form) {
            if (form.$valid) {
                var patch = [{"op": "check", "path": "/email", "value": $scope.user.email}];
                userService.publicPatchUser(patch).then(function (response) {
                    if (response.data.checkMail.isAllocated) {
                        $scope.errorMessages.email = "Utilisateur existant";
                        return false;
                    } else {
                        var data = {user: $scope.user, contact: $scope.contact};
                        userService.postUser(token, data).then(function (response) {
                            common.saveAuthenticationToken(response.token, response.roles, true);
                        });
                    }
                });
            }
        };

        /**
         * @ngdoc method
         * @name confirmPassword
         * @methodOf public.controllers:createUserController
         * @description
         * Create user
         *
         * @param {object} form User form to be validated
         */
        $scope.confirmPassword = function (form) {
            form.passwordConfirmation.$error.required = $scope.user.plainPassword.first && !$scope.user.plainPassword.second;
            form.passwordConfirmation.$error.pwCheck = $scope.user.plainPassword.first && $scope.user.plainPassword.second
                && ($scope.user.plainPassword.first !== $scope.user.plainPassword.second);

            if (form.passwordConfirmation.$error.required || form.passwordConfirmation.$error.pwCheck) {
                form.$valid = false;
            }
        };

        $scope.loadToken();
    }

    return createUserController;
});
