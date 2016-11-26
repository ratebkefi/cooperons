/**
 * @ngdoc controller
 * @name admin.controllers:attestationController
 * @description
 * This file defines the home controller for admin space
 */
define([], function () {
    'use strict';

    attestationController.$inject = ['$scope', 'attestationRepository'];

    function attestationController($scope, attestationRepository) {

        /**
        * @ngdoc method
        * @name loadAttestations
        * @methodOf admin.controllers:attestationController
        * @description
        * Get annual attestations list
        */
        $scope.loadAttestations = function () {
            attestationRepository.getAttestations().then(function (data) {
                $scope.yearlyAttestations = data.attestations;
                $scope.year = data.year;
                $scope.totalAmount = 0;
                $scope.totalCotisation = 0;

                angular.forEach($scope.yearlyAttestations, function (value) {
                    $scope.totalAmount += parseFloat(value.amount);
                    $scope.totalCotisation += parseFloat(value.cotisation);
                });
            });
        };

        /**
        * @ngdoc method
        * @name validateAttestations
        * @methodOf admin.controllers:attestationController
        * @description
        * Validate annual attestations
        */
        $scope.validateAttestations = function () {
            var patch = [{op: 'validate', path: '/'}];
            attestationService.patchAttestations(patch).then(function () {
                $scope.loadAttestations();
            });
        };

        $scope.loadAttestations();
    }

    return attestationController;
});
