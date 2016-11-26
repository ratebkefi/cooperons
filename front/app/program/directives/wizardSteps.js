/**
 * @ngdoc directive
 * @name program.directives:wizardSteps
 * 
 * @restrict 'E' 
 *
 * @description
 * Switching between steps
 *  
 * @example
   <example>
     <file name="exemple.html">
        <div>
            <wizard-steps></wizard-steps> 
        </div>
     </file>
   </example>
 */
define([], function () {
    'use strict';
    wizardSteps.$inject = ['$state', 'operationService'];
   
    function wizardSteps($state, operationService) {
        return {
            restrict: 'E',
            templateUrl: 'app/program/views/directives/wizardSteps.html',
            scope: {
                program: '=',
                step: '='
            },
            controller: function ($scope) {
                /**
                * @ngdoc method
                * @name init
                * @methodOf  program.directives:wizardSteps
                * @description
                * Initialize scope
                * 
                */
                $scope.init = function () {
                    $scope.classes = [];
                    for (var i = 1; i <= 4; i++) {
                        i < $scope.step ? $scope.classes[i] = 'visited' : null;
                        i == $scope.step ? $scope.classes[i] = 'active' : null;
                        i > $scope.step ? $scope.classes[i] = '' : null;
                    }

                    if ($scope.program) {
                        $scope.loadProgramOperations($scope.program);
                    }
                }; 
                
                /**
                * @ngdoc method
                * @name loadProgramOperations
                * @methodOf  program.directives:wizardSteps
                * @description
                * Load program operations
                * 
                * @param {object} program A program data
                * 
                * @return {object} Program operations data
                */
                $scope.loadProgramOperations = function (program) {
                    operationService.getProgramOperations(program.id).then(function (data) {
                        $scope.program.operations = data.operations;
                    });
                };
 
                /**
                * @ngdoc method
                * @name goToStep
                * @methodOf  program.directives:wizardSteps
                * @description
                * Navigate between steps
                * 
                * @param {string} step A step data
                * 
                * @return {object} Next step data
                */
                $scope.goToStep = function (step) {
                    if ($scope.program) {
                        switch (step) {
                            case 1:
                                $state.go('program.presentation', {id: $scope.program.id});
                                break;
                            case 2:
                                if ($scope.program.operations.length) {
                                    $state.go('program.commissioning', {id: $scope.program.id});
                                }
                                break;
                            case 3:
                                if ($scope.program.status === 'preprod') {
                                    $state.go('program.generalTerms', {programId: $scope.program.id});
                                }
                                break;
                            case 4:
                                if ($scope.program.status === 'preprod') {
                                    $state.go('program.payment', {programId: $scope.program.id});
                                }
                                break;
                        }
                    }

                }; 
                
                $scope.init();
            }
        };
    }

    return wizardSteps;
});
