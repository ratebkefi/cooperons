/**
 * @ngdoc controller
 * @name mandataire.controllers:paymentController
 * @description
 * This file defines the payment controller
 */
define([], function () {
    'use strict';

    paymentController.$inject = ['$rootScope', '$scope', '$window', 'paymentService', 'mandataireService'];

    function paymentController($rootScope, $scope, $window, paymentService, mandataireService) {

        /**
         * @ngdoc method
         * @name loadNewPayment
         * @methodOf mandataire.controllers:paymentController
         * @description
         * Load data for submitting virement
         *
         * @param {object} mandataire A mandataire data
         * @param {number} amount A amount value
         */
        $scope.loadNewPayment = function (mandataire, amount) {
            mandataireService.newMandatairePayment(mandataire.id, amount).then(function (data) {
                $scope.server = data.server;
            });
        };

        /**
         * @ngdoc method
         * @name postVirement
         * @methodOf mandataire.controllers:paymentController
         * @description
         * Post payment
         *
         * @param {string} mode A mode of payment
         */
        $scope.postVirement = function (mode) {
            if ($scope.$parent.checkBeforePayment()) {
                if (mode == 'VIREMENT') {
                    mandataireService.postMandatairePayment($scope.mandataire.id, $scope.amount).then(function () {
                        $scope.$parent.afterPayment();
                    });
                } else if ($scope.server && $scope.server.url) {
                    var redirectUrl = $scope.server.url + '?';
                    angular.forEach($scope.server.params, function (value, key) {
                            redirectUrl += key + '=' + $window.encodeURIComponent(value) + '&';
                        }
                    );

                    $window.location = redirectUrl;
                }
            }
        };

        /**
         * @ngdoc method
         * @name deletePayment
         * @methodOf mandataire.controllers:paymentController
         * @description
         * Delete payment
         */
        $scope.deletePayment = function () {
            paymentService.deletePayment($scope.payment.id).then(function () {
                $scope.$parent.afterPayment();
            });
        };

        $scope.loadNewPayment($scope.mandataire, $scope.amount);
    }

    return paymentController;
});