/**
 * @ngdoc controller
 * @name program.controllers:generalTermsController
 * @description
 * This file defines the program's general terms controller
 */
define([], function () {
    'use strict';
    generalTermsController.$inject = ['$rootScope', '$scope', '$state', '$sce', 'programService', 'operationService', 'Notification'];

    function generalTermsController($rootScope, $scope, $state, $sce, programService, operationService, Notification) {
        /**
         * Init global parameters
         */
        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var programId = $state.params.programId;
        $scope.checkboxModel = {
            cgv: false
        };
        $scope.description = {
            simpledesc: '',
            multidesc: ''
        };

        /**
         * @ngdoc method
         * @name loadProgram
         * @methodOf program.controllers:generalTermsController
         * @description
         * Retrieve program
         */
        $scope.loadProgram = function () {
            programService.getProgram(programId).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramOperations
         * @methodOf program.controllers:generalTermsController
         * @description
         * Retrieve all operations by program
         */
        $scope.loadProgramOperations = function () {
            operationService.getProgramOperations(programId).then(function (response) {
                $scope.operations = response.operations;
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramClauses
         * @methodOf program.controllers:generalTermsController
         * @description
         * Retrieve legal clauses of program
         */
        $scope.loadProgramClauses = function () {
            programService.getProgramClausesAsHtml(programId).then(function (data) {
                $scope.clauses = data.clauses;
                angular.forEach(data.clauses, function (clause, key) {
                    clause.content = $sce.trustAsHtml(clause.content);
                    clause.key = key;
                    clause.show = key == 2 || key == 3;
                });
            });
        };

        /**
         * @ngdoc method
         * @name editEasySetting
         * @methodOf program.controllers:generalTermsController
         * @description
         * Go to general terms
         */
        $scope.editEasySetting = function () {
            $state.go('program.generalTerms', {program: $scope.program, programId: programId});
        };

        /**
         * @ngdoc method
         * @name editProgrmaCgv
         * @methodOf program.controllers:generalTermsController
         * @description
         * Modify cgv of program
         */
        $scope.editProgrmaCgv = function () {
            var patch = [{op: 'describe', path: '/', value: $scope.program.description}];
            angular.forEach($scope.operations, function (operation) {
                patch.push({
                    op: 'describe',
                    path: '/operation',
                    operation: operation.id,
                    value: operation.description
                });
            });

            programService.patchProgram(programId, patch).then(function () {
                $state.go('program.cgv', {programId: programId});
            });
        };

        /**
         * @ngdoc method
         * @name editProgram
         * @methodOf program.controllers:generalTermsController
         * @description
         * Save general terms's data
         */
        $scope.editProgram = function () {
            if ($scope.checkboxModel.cgv === true) {
                if ($scope.program.description) {
                    var patch = [{op: 'describe', path: '/', value: $scope.program.description}];
                    angular.forEach($scope.operations, function (operation) {
                        patch.push({
                            op: 'describe',
                            path: '/operation',
                            operation: operation.id,
                            value: operation.description
                        });
                    });

                    programService.patchProgram(programId, patch).then(function () {
                        $state.go('program.payment', {program: $scope.program, programId: programId});
                    });
                } else {
                    Notification.primary({
                        title: '<i class="fa fa-exclamation-circle" style="color: white;">  Attention !</i>',
                        message: 'Vous devez remplir la description de votre programme.'
                    });
                }
            } else {
                Notification.primary({
                    title: '<i class="fa fa-exclamation-circle" style="color: white;">  Attention !</i>',
                    message: 'Vous devez certifier être autorisé à représenter SAS EDATIS pour la publication des Conditions Générales du Programme de Stimulation.'
                });
            }
        };

        /**
         * Load data
         */
        $scope.loadProgram();
        $scope.loadProgramOperations();
        $scope.loadProgramClauses();
    }

    return generalTermsController;
});
