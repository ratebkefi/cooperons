/**
 * @ngdoc controller
 * @name admin.controllers:programController
 * @description
 * This file defines the home controller for admin space
 */
define([], function () {
    'use strict';

    programController.$inject = ['$rootScope', '$scope', 'programService', 'mandataireService'];

    function programController($rootScope, $scope, programService, mandataireService) {

        /**
         * @ngdoc method
         * @name loadPrograms
         * @methodOf admin.controllers:adminController
         * @description
         * Get all programs
         */
        $scope.loadPrograms = function () {
            programService.getPrograms().then(function (response) {
                $scope.allprograms = response.programs;
                $scope.haveWaitingSettlements = response.haveWaitingSettlements;
            });
        };

        /**
         * @ngdoc method
         * @name renewalProgram
         * @methodOf admin.controllers:adminController
         * @description
         * Renewal Program
         * 
         * @param {number} programId Id of program
         */
        $scope.renewalProgram = function (programId) {
            var patch = [{op: 'renewal', path: '/'}];

            programService.patchProgram(programId, patch).then(function () {
                $scope.loadPrograms();
                $rootScope.$broadcast('refreshSettlements');
            });
        };

        /**
         * @ngdoc method
         * @name updateMinDeposit
         * @methodOf admin.controllers:adminController
         * @description
         * Update minimum deposit
         *
         * @param {number} mandataireId Id of mandataire
         * @param {number} minDeposit A min value of Deposit
         */
        $scope.updateMinDeposit = function (mandataireId, minDeposit) {
            if (!isNaN(minDeposit)) {
                var patch = [{op: 'replace', path: '/mindeposit', value: minDeposit}];
                mandataireService.patchMandataire(mandataireId, patch).then(function () {
                    $scope.loadPrograms();
                });
            } else {
                Notification.primary({
                    title: '<i class="fa fa-warning" style="color: white;">  Attention!</i>',
                    message: 'Montant non reconnu'
                });
            }
        };

        $scope.loadPrograms();

        $scope.$on('refreshPrograms', function(event) {
            $scope.loadPrograms();
        });
    }

    return programController;
});
