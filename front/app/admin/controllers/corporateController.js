/**
 * @ngdoc controller
 * @name admin.controllers:corporateController
 * @description
 * This file defines the home controller for admin space
 */
define([], function () {
    'use strict';

    corporateController.$inject = ['$scope', 'corporateRepository'];

    function corporateController($scope, corporateRepository) {

        /**
        * @ngdoc method
        * @name loadCorporates
        * @methodOf admin.controllers:corporateController
        * @description
        * Get corporates
        */
        $scope.loadCorporates = function () {
            corporateRepository.getCorporates().then(function (data) {
                $scope.corporates = data.corporates;
                angular.forEach($scope.corporates, function (corporate) {
                    corporateRepository.getCorporateDelegate(corporate.id).then(function (data) {
                        corporate.delegate = data.delegate;
                    });
                });
            });
        };

        /**
        * @ngdoc method
        * @name validateCorporate
        * @methodOf admin.controllers:corporateController
        * @description
        * Validate corporate
        *
        * @param {object} corporate A corporate entity
        */
        $scope.validateCorporate = function (corporate) {
            var patch = [{op: 'validate', path: '/'}];
            corporateRepository.patchCorporate(corporate.siren, patch).then(function () {
                $scope.loadCorporates();
            });
        };

        /**
        * @ngdoc method
        * @name cancelCorporate
        * @methodOf admin.controllers:corporateController
        * @description
        * Cancel corporate
        *
        * @param {object} corporate A corporate entity
        */
        $scope.cancelCorporate = function (corporate) {
            var patch = [{op: 'cancel', path: '/'}];
            corporateRepository.patchCorporate(corporate.siren, patch).then(function () {
                $scope.loadCorporates();
            });
        };

        $scope.loadCorporates();
    }

    return corporateController;
});
