/**
 * @ngdoc controller
 * @name admin.controllers:autoentrepreneurController
 * @description
 * This file defines the autoentrepreneur controller for admin space
 */
define([], function () {
    'use strict';

    autoentrepreneurController.$inject = ['$rootScope', '$scope', '$state', 'autoEntrepreneurRepository', 'userService',
        'Notification'];

    function autoentrepreneurController($rootScope, $scope, $state, autoEntrepreneurRepository, userService, Notification) {
        /**
         * Init global parameters
         */
        var userId = $state.params.userId;

        $scope.autoEntrepreneur = {
            userId: null,
            SIRET: null,
            externalLastName: null,
            externalFirstName: null,
            externalPassword: null,
            externalOldPassword: null,
            externalEmail: null,
            typeActivation: 'waitForQuarter'
        };


        function generatePassword() {
            var length = 8,
                charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }

        /**
         * @ngdoc method
         * @name loadMember
         * @methodOf admin.controllers:autoentrepreneurController
         * @description
         * Retrieve member entity
         */
        $scope.loadMember = function () {
            userService.getUserMember(userId).then(function (data) {
                if (data.member.isAutoEntrepreneur) {
                    $scope.loadAutoEntrepreneur(userId);
                } else {
                    $scope.autoEntrepreneur.externalEmail = data.member.firstName.toLowerCase() + '.' + data.member.lastName.toLowerCase() + '@cooperons.com';
                    $scope.autoEntrepreneur.externalPassword = generatePassword();
                }
            });
        };

        /**
         * @ngdoc method
         * @name loadAutoEntrepreneur
         * @methodOf admin.controllers:autoentrepreneurController
         * @description
         * Retrieve AutoEntrepreneur entity
         *
         * @param {number} userId Id of user
         */
        $scope.loadAutoEntrepreneur = function (userId) {
            userService.getUserAutoEntrepreneur(userId).then(function (data) {
                $scope.autoEntrepreneur = data.autoEntrepreneur;
            });
        };

        /**
         * @ngdoc method
         * @name postAutoEntrepreneurExternal
         * @methodOf admin.controllers:autoentrepreneurController
         * @description
         * Create autoEntrepreneur account (with external access to net-entreprises.com website)
         *
         * @param {object} form AutoEntrepreneur form to be validated
         * @param {number} idAutoentrepreneur Id of AutoEntrepreneur
         * @returns {void|boolean} Return false value if form is not valid
         */
        $scope.postAutoEntrepreneurExternal = function (form, idAutoentrepreneur) {
            if (form.$valid) {
                if ($scope.autoEntrepreneur.id == null) {
                    $scope.autoEntrepreneur.userId = userId;
                    autoEntrepreneurRepository.postAutoEntrepreneur($scope.autoEntrepreneur).then(function () {
                        Notification.success("Creation du compte effectuée avec succès");
                        $state.go($state.current, {userId: userId}, {reload: true});
                    });
                } else {
                    var patch = [{
                        op: 'external', path: '/', externalEmail: $scope.autoEntrepreneur.externalEmail,
                        externalPassword: $scope.autoEntrepreneur.externalPassword
                    }];
                    autoEntrepreneurRepository.patchAutoEntrepreneur(idAutoentrepreneur, patch).then(function () {
                        Notification.success({
                            title: '<i class="fa fa-check-circle" style="color: white;">  Succès</i>',
                            message: "Accès Net-Entrepreneur modifié avec succès."
                        });
                    })
                }
            } else {
                return false;
            }
        };

        /**
         * @ngdoc method
         * @name updateBank
         * @methodOf admin.controllers:autoentrepreneurController
         * @description
         * Update autoEntrepreneur bank account
         *
         * @param {object} form AutoEntrepreneur form to be validated
         * @param {number} idAutoentrepreneur Id of AutoEntrepreneur
         * @returns {void|boolean} Return false value if form is not valid
         */
        $scope.updateBank = function (form, idAutoentrepreneur) {
            var patch = [{op: 'bank', path: '/', BIC: $scope.autoEntrepreneur.BIC, IBAN: $scope.autoEntrepreneur.IBAN}];
            if (form.$valid) {
                autoEntrepreneurRepository.patchAutoEntrepreneur(idAutoentrepreneur, patch).then(function (response) {
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white;">  Succès</i>',
                        message: "Compte bancaire modifié avec succès."
                    });
                });
            }
        };

        /**
         * @ngdoc method
         * @name activate
         * @methodOf admin.controllers:autoentrepreneurController
         * @description
         * Activate autoEntrepreneur after first quarter & declaration/payment of quarterly charges ...
         *
         * @param {number} idAutoentrepreneur Id of AutoEntrepreneur
         * @returns {void|boolean} Return false value if form is not valid
         */
        $scope.activateAutoEntrepreneur = function (idAutoentrepreneur) {
            var patch = [{op: 'activate', path: '/'}];
            autoEntrepreneurRepository.patchAutoEntrepreneur(idAutoentrepreneur, patch).then(
                function (response) {
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white;">  Succès</i>',
                        message: "Activation du compte effectuée avec succès."
                    });
                    $state.go($state.current, {userId: userId}, {reload: true});
                }
            );
        };

        /**
         * Load user member
         */
        $scope.loadMember();
    }

    return autoentrepreneurController;
});
