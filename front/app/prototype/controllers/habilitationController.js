/**
 * @ngdoc controller
 * @name prototype.controllers:habilitationController
 * @description
 * This file defines the habilitation controller for prototype module
 */
define([], function () {
    "use strict";

    habilitationController.$inject = ['$rootScope', '$scope', 'habilitationRepository'];

    function habilitationController($rootScope, $scope, habilitationRepository) {

        // Initialize global parameters
        $rootScope.space = 'member';
        $rootScope.menuTab = 'party';

        $scope.newHabilitation = {file: {}};


        /**
         * @ngdoc method
         * @name loadHabilitations
         * @methodOf prototype.controllers:habilitationController
         * @description Retrieve list of habilitations
         */
        var loadHabilitations = function () {
                habilitationRepository.getHabilitations()
                    .then(function (data) {
                        $scope.habilitations = data.habilitations;
                        angular.forEach($scope.habilitations, function (habilitation) {
                            if (habilitation.active) {
                                $scope.selectedHabilitation = habilitation;
                            }
                        });
                        $scope.selectHabilitation();
                    });
        };

        /**
         * @ngdoc method
         * @name loadHabilitationFile
         * @methodOf prototype.controllers:habilitationController
         * @description Load PDF file of habilitation
         */
        var loadHabilitationFile = function () {
                habilitationRepository.getHabilitationFile($scope.selectedHabilitation.id)
                    .then(function (data) {
                        $scope.selectedHabilitation.file = data.file;
                    });
        };

        /**
         * @ngdoc method
         * @name loadHabilitationLegalDocuments
         * @methodOf prototype.controllers:habilitationController
         * @description Retrieve list of legal documents related with habilitation
         */
        var loadHabilitationLegalDocuments = function () {
                habilitationRepository.getHabilitationLegalDocuments($scope.selectedHabilitation.id)
                    .then(function (data) {
                        $scope.selectedHabilitation.legalDocuments = data.legalDocuments;
                    })
        };

        /**
         * @ngdoc method
         * @name selectHabilitation
         * @methodOf prototype.controllers:habilitationController
         * @description Load data related with selected habilitation
         */
        $scope.selectHabilitation = function () {
            loadHabilitationFile();
            loadHabilitationLegalDocuments();
        };


        /**
         * @ngdoc method
         * @name addHabilitation
         * @methodOf prototype.controllers:habilitationController
         * @description create new habilitation
         * @param form Form to be validated
         */
        $scope.addHabilitation = function (form) {
            if (form.$valid) {
                if ($scope.selectedHabilitation.file || $scope.selectedHabilitation.file
                    && $scope.selectedHabilitation.file.type !== 'application/pdf') {
                    return false;
                }
                    habilitationRepository.postHabilitation($scope.newHabilitation)
                        .then(function () {
                            loadHabilitations();
                        });
            }
        };

        loadHabilitations();

    }

    return habilitationController;
});
