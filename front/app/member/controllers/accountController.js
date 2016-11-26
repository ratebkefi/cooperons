/**
 * @ngdoc controller
 * @name member.controllers:accountController
 * @description
 * This file defines the member's account controller
 */
define([], function () {
    'use strict';

    accountController.$inject = ['$scope', '$state', 'common', 'userService', 'Notification'];

    function accountController($scope, $state, common, userService, Notification) {

        common.setMenuTab('account');

        $scope.account = {
            email: '',
            plainPassword: '',
            lastName: '',
            firstName: ''
        };

        /**
         * @ngdoc method
         * @name loadUser
         * @methodOf member.controllers:accountController
         * @description
         * Retrieve details of user
         */
        $scope.loadUser = function () {
            userService.getUser().then(function (data) {
                $scope.user = data.user;

                // Init account user
                $scope.account.email = data.user.email;
                $scope.account.lastName = data.user.lastName;
                $scope.account.firstName = data.user.firstName;

                /**
                 * Object contact
                 */
                $scope.contact = {
                    phone: data.user.contact.phone,
                    address: data.user.contact.address,
                    secondAddress: data.user.contact.secondAddress,
                    city: data.user.contact.city,
                    postalCode: data.user.contact.postalCode
                };
            });
        };


        /**
         * @ngdoc method
         * @name confirmUpdateUserAccount
         * @methodOf member.controllers:accountController
         * @description
         * confirm update member's account
         *
         * @param {object} form Member's account form to be validated
         * @return {boolean} Return false value if form is not valid
         */
        $scope.confirmUpdateUserAccount = function (form) {
            if (form.$valid) {
                if ($scope.user.email !== $scope.account.email) {
                    var patch = [{"op": "check", "path": "/email", "value": $scope.account.email}];
                    userService.publicPatchUser(patch).then(function (response) {
                        if (response.data.checkMail.isAllocated) {
                            $scope.errorMessages = {email: "email existant"};
                            form.email.$error = true;
                            return false
                        } else {
                            $scope.updateUserAccount();
                        }
                    });
                } else {
                    $scope.updateUserAccount();
                }
            } else {
                return false;
            }
        };


        /**
         * @ngdoc method
         * @name updateAccount
         * @methodOf member.controllers:accountController
         * @description
         * Update member's account
         *
         */
        $scope.updateUserAccount = function () {
            userService.putUser({user: $scope.account, contact: $scope.contact}).then(function (response) {
                Notification.success("Modification effectuée avec succès");
                var data = response.data.jwtAuth;
                if (data) {
                    common.saveAuthenticationToken(data.token, data.roles, false);
                }
                $state.go('member.home');
            });
        };

        /**
         * @ngdoc method
         * @name userResendEmail
         * @methodOf member.controllers:accountController
         * @description
         * Resend mail of activation account
         */
        $scope.userResendEmail = function () {
            var patch = [{"op": "resend", "path": "/email"}];
            userService.patchUser(patch).then(function () {
                Notification.success("Envoi d'email avec succès");
                $state.go('member.home');
            });
        };

        /**
         * Load data
         */
        $scope.loadUser();
    }

    return accountController;
});