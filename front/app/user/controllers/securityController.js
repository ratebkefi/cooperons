/**
 * @ngdoc controller
 * @name user.controllers:securityController
 * @description
 * This file defines the security user controller
 */
define([], function () {
    'use strict';

    securityController.$inject = ['$scope', '$rootScope', '$state', 'common', 'securityService', 'userService', 'Notification'];


    function securityController($scope, $rootScope, $state, common, securityService, userService, Notification) {

        /**
         * Init login parameters
         *
         * @type {object}
         */
        $scope.user = {username: '', password: ''};

        /**
         * Init authentication status
         *
         * @type {object}
         */
        $scope.authentication = {failed: false, message: ''};

        /**
         * Disable/Enable submit button
         *
         * @type {boolean}
         */
        $scope.buttonDisabled = false;

        /**
         * Init parameters for forget password form
         *
         * @type {object}
         */
        $scope.forgetPassword = {email: '', message: '', error: ''};

        /**
         * Init parameters for reset password form
         *
         * @type {object}
         */
        $scope.resetPassword = {firstPassword: '', secondPassword: ''};

        /**
         * If resetting password page then check confirmation token
         */
        if ($state.current.name == 'user.resetPassword') {
            $scope.token = $state.params.token;
            var patch = [{op: 'check', path: '/confirmationToken', value: $scope.token}];
            userService.publicPatchUser(patch).then(function (response) {
                if (response.data.checkConfirmationToken.isValid) {
                } else {
                    Notification.error({
                        title: '<i class="fa fa-exclamation-circle" style="color: white;">  Erreur !</i>',
                        message: 'Jeton de confirmation non valide'
                    });
                    common.dispatch();
                }
            }).catch(function () {
                common.dispatch();
            });
        }

        /**
         * @ngdoc method
         * @name login
         * @methodOf user.controllers:securityController
         * @description
         * Authenticate user
         *
         */
        $scope.login = function () {
            common.disableNextLoading();
            $scope.buttonDisabled = true;
            securityService.login($scope.user).then(function (response) {
                $scope.authentication.failed = false;
                common.saveAuthenticationToken(response.token, response.data.roles, true);
            }).catch(function () {
                $scope.buttonDisabled = false;
                $scope.authentication.failed = true;
                $scope.authentication.message = "Données d'authentification non valides";
            });
        };

        /**
         * @ngdoc method
         * @name confirmForgetPassword
         * @methodOf user.controllers:securityController
         * @description
         * Send mail for resetting password
         *
         * @param {object} form Email confirmation form to be validated
         */
        $scope.confirmForgetPassword = function (form) {
            if (form.$valid) {
                common.disableNextLoading();
                $scope.buttonDisabled = true;
                var patch = [{op: 'check', path: '/email', value: $scope.forgetPassword.email}];
                userService.publicPatchUser(patch).then(function (response) {
                    if (response.data.checkMail.isAllocated) {
                        common.disableNextLoading();
                        patch = [{op: 'forget', path: '/password', email: $scope.forgetPassword.email}];
                        userService.publicPatchUser(patch).then(function () {
                            $scope.forgetPassword.message = 'Email de modification du mot de passe envoyé';
                            $scope.forgetPassword.error = '';
                            $scope.buttonDisabled = false;
                        });

                    } else {
                        $scope.forgetPassword.error = 'Adresse email inexistante';
                        $scope.forgetPassword.message = '';
                        $scope.buttonDisabled = false;
                    }

                });
            }
        };

        /**
         * @ngdoc method
         * @name confirmResetPassword
         * @methodOf user.controllers:securityController
         * @description
         * Reset password
         *
         * @param {object} form Rest password form to be validated
         */
        $scope.confirmResetPassword = function (form) {
            if (form.$valid) {
                common.disableNextLoading();
                $scope.buttonDisabled = true;
                var patch = [{
                    op: 'reset',
                    path: '/password',
                    value: $scope.resetPassword.firstPassword,
                    token: $scope.token
                }];
                userService.publicPatchUser(patch).then(function (response) {
                    $scope.buttonDisabled = false;
                    var data = response.data.jwtAuth;
                    common.saveAuthenticationToken(data.token, data.roles, false);
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                        message: 'Le mot de passe a été modifié'
                    });
                    common.dispatch();
                });
            }
        };

        /**
         * @ngdoc method
         * @name confirmPassword
         * @methodOf user.controllers:securityController
         * @description
         * Create user
         *
         * @param {object} form User form to be validated
         */
        $scope.confirmPassword = function (form) {
            form.passwordConfirmation.$error.required = $scope.resetPassword.firstPassword && !$scope.resetPassword.secondPassword;
            form.passwordConfirmation.$error.pwCheck = $scope.resetPassword.firstPassword && $scope.resetPassword.secondPassword
                && ( $scope.resetPassword.firstPassword !== $scope.resetPassword.secondPassword);

            if (form.passwordConfirmation.$error.required || form.passwordConfirmation.$error.pwCheck) {
                form.$valid = false;
            }

        };
    }

    return securityController;
});
