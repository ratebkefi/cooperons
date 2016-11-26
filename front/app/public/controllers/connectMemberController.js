/**
 * @ngdoc controller
 * @name public.controllers:connectMemberController
 * @description
 * This file defines the connect member controller
 */
define([], function () {
        'use strict';

        connectMemberController.$inject = ['$rootScope', '$scope', '$state', 'common', 'securityService', 'userService',
            'Notification'];

        function connectMemberController($rootScope, $scope, $state, common, securityService, userService, Notification) {

            $scope.authentication = {};
            $scope.token = $state.params.token;

            /**
             * @ngdoc method
             * @name loadToken
             * @methodOf public.controllers:connectMemberController
             * @description
             * Get token data
             *
             */
            $scope.loadToken = function () {
                userService.getToken($scope.token).then(function (data) {
                    var invitation = null;

                    if (data.isPreProd) {
                        $state.go('public.createUser');
                    } else {
                        $scope.authentication.displayCheckboxCGV = false;

                        $scope.program = null;
                        if (data.hasProgram) {
                            $scope.program = data.object.program;
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
                            $scope.firstName = invitation.firstName;
                            $scope.lastName = invitation.lastName;
                            $scope.authentication.email = invitation.email;
                        }
                    }
                }).catch(function () {
                    common.dispatch();
                });
            };

            $scope.loadToken();

            /**
             * @ngdoc method
             * @name connectMember
             * @methodOf public.controllers:connectMemberController
             * @description
             * Confirm invitation and connect member
             *
             * @param {object} form Invitation form to be validated
             */
            $scope.connectMember = function (form) {
                if (form.$valid) {
                    if ($scope.authentication.displayCheckboxCGV && !$scope.authentication.checkboxCGV) {
                        Notification.primary({
                            title: '<i class="fa fa-check-circle" style="color: white"> Alert</i>',
                            message: 'Vous devez accepter les Conditions Générales du Programme de Stimulation «'
                            + $scope.program.label + '»'
                        });
                    } else {
                        var auth = {username: $scope.authentication.email, password: $scope.authentication.password};
                        securityService.login(auth).then(function (response) {
                            $scope.authentication.failed = false;
                            common.saveAuthenticationToken(response.token, response.data.roles);
                            if ($scope.token) {
                                var patch = [{op: 'connect', path: '/token', token: $scope.token}];
                                userService.patchUser(patch).then(function () {
                                    common.dispatch();
                                });
                            }
                        }).catch(function () {
                            $scope.authentication.failed = true;
                            $scope.authentication.message = "Données d'authentification non valides";
                        });
                    }
                }
            };

        }

        return connectMemberController;
    }
)
;
