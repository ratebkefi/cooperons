/**
 * @ngdoc controller
 * @name program.controllers:commisionigController
 * @description
 * This file defines the program's commissioning controller
 */
define([], function () {
    'use strict';

    commisionigController.$inject = ['$rootScope', '$scope', '$state', 'programService', 'Notification', 'operationService', 'easySettingService'];

    function commisionigController($rootScope, $scope, $state, programService, Notification, operationService, easySettingService) {
        /**
         * Init global parameters
         */
        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var idProgram = $state.params.id;
        $scope.commission = {};
        $scope.newOperation = {
            label: null,
            defaultAmount: null,
            multi: 'multipoints'
        };

        /**
         * @ngdoc method
         * @name loadProgram
         * @methodOf program.controllers:commisionigController
         * @description
         * Retrieve programs by id
         */
        $scope.loadProgram = function () {
            programService.getProgram(idProgram).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
                if ($scope.program.isEasy) {
                    $scope.loadEasySetting();
                }
            });
        };

        /**
         * @ngdoc method
         * @name updateCommissionning
         * @methodOf program.controllers:commisionigController
         * @description
         * Update commissionning
         */
        $scope.updateCommissionning = function () {
            $scope.commission.simpleRate = $scope.sliderSimpleRate.value;
            $scope.commission.multiRate = $scope.sliderMultiRate.value;
            $scope.commission.comm1 = $scope.sliderSimpleRate.value * 1 + $scope.sliderMultiRate.value * 1;
            $scope.commission.comm2 = ($scope.sliderMultiRate.value * 2) / 3;
            $scope.commission.comm3 = ((($scope.sliderMultiRate.value * 2) / 3) * 2) / 3;
            $scope.commission.commSomme = Math.floor(($scope.sliderSimpleRate.value * 1) + ($scope.sliderMultiRate.value * 3));
            $scope.commission.commEasy = Math.floor(($scope.sliderSimpleRate.value * 1) + ($scope.sliderMultiRate.value * 3)) * 0.25;
            $scope.commission.commTotal = Math.floor(($scope.sliderSimpleRate.value * 1) + ($scope.sliderMultiRate.value * 3))
                + Math.floor(($scope.sliderSimpleRate.value * 1) + ($scope.sliderMultiRate.value * 3)) * 0.25;
        };

        /**
         * @ngdoc method
         * @name loadOperations
         * @methodOf program.controllers:commisionigController
         * @description
         * Retrieve all operations by programId
         */
        $scope.loadOperations = function () {
            operationService.getProgramOperations(idProgram).then(function (data) {
                $scope.operations = data.operations;
            });
        };


        /**
         * @ngdoc method
         * @name postOperations
         * @methodOf program.controllers:commisionigController
         * @description
         * Create new operation
         */
        $scope.postOperations = function () {
            if ($scope.newOperation.label) {
                var newOperation = angular.copy($scope.newOperation);
                newOperation.multi = $scope.newOperation.multi === 'multipoints';

                operationService.postProgramOperation(idProgram, newOperation).then(function () {
                    Notification.success(" Succès <br> Création de l'opportunité effectuée avec succès");
                    $scope.loadOperations();
                    $scope.newOperation = {
                        label: null,
                        defaultAmount: null,
                        multi: 'multipoints'
                    };
                });
            } else {
                Notification.primary(" Attention <br> Vous devez entrer le nom de l'opération.");
            }
        };

        /**
         * @ngdoc method
         * @name editOperation
         * @methodOf program.controllers:commisionigController
         * @description
         * Edit an operation
         *
         * @param {object} operation A operation data
         */
        $scope.editOperation = function (operation) {
            if (operation.label) {
                operationService.putProgramOperation(idProgram, operation).then(function (response) {
                    if (response.status === 'success') {
                        Notification.success(" Succès <br> Modification de l'opportunité effectuée avec succès");
                        $scope.loadOperations();
                    }
                });
            } else {
                Notification.primary(" Attention <br> Vous devez entrer le nom de l'opération.");
            }
        };

        /**
         * @ngdoc method
         * @name deleteOperation
         * @methodOf program.controllers:commisionigController
         * @description
         * Delete an operation
         *
         * @param {object} operation A operation data
         */
        $scope.deleteOperation = function (operation) {
            operationService.deleteProgramOperation(idProgram, operation.id).then(function () {
                Notification.success(" Succès <br> Suppression de l'opportunité effectuée avec succès");
                $scope.loadOperations();
            });
        };

        /**
         * @ngdoc method
         * @name loadEasySetting
         * @methodOf program.controllers:commisionigController
         * @description
         * Get program esay setting
         */
        $scope.loadEasySetting = function () {
            easySettingService.getProgramEasySetting(idProgram).then(function (data) {
                //Minimal simple slider config
                $scope.sliderSimpleRate = {
                    value: data.easySetting.simpleRate,
                    maxValue: 20
                };

                //Minimal multi slider config
                $scope.sliderMultiRate = {
                    value: data.easySetting.multiRate,
                    maxValue: 20
                };

                $scope.updateCommissionning();
            });
        };

        /**
         * @ngdoc method
         * @name editEasySetting
         * @methodOf program.controllers:commisionigController
         * @description
         * Update Genaral terms of program
         */
        $scope.editEasySetting = function () {
            if ($scope.program.isEasy) {
                var patch = [
                    {op: 'replace', path: '/simplerate', value: $scope.sliderSimpleRate.value},
                    {op: 'replace', path: '/multirate', value: $scope.sliderMultiRate.value}
                ];

                easySettingService.patchProgramEasySetting(idProgram, patch).then(function () {
                    $state.go('program.generalTerms', {programId: idProgram});
                });
            } else {
                if ($scope.operations.length > 0) {
                    $state.go('program.generalTerms', {programId: idProgram});
                } else {
                    Notification.error({
                        title: '<i class="fa fa-exclamation-circle" style="color: white;">  Attention !</i>',
                        message: 'Votre programme doit comporter au moins une opportunité'
                    });
                }
            }
        };

        /**
         * @ngdoc method
         * @name checkAmount
         * @methodOf program.controllers:commisionigController
         * @description
         * Check a amount
         */
        $scope.checkAmount = function () {
            if (isNaN($scope.newOperation.defaultAmount) || $scope.newOperation.defaultAmount <= 0) {
                $scope.newOperation.defaultAmount = null;
            }
        };

        /**
         * Load data
         */
        $scope.loadProgram();
        $scope.loadOperations();
    }

    return commisionigController;
});
