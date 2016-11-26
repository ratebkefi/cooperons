/**
 * @ngdoc controller
 * @name program.controllers:presentationController
 * @description
 * This file defines the presentation controller
 */
define([], function () {
    'use strict';
    presentationController.$inject = ['$rootScope', '$scope', '$state', 'programService'];

    function presentationController($rootScope, $scope, $state, programService) {

        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var id = $state.params.id;
        var isEasy = $state.params.isEasy;

        /**
         * @ngdoc method
         * @name init
         * @methodOf program.controllers:presentationController
         * @description
         * Initialize scope
         *
         */
        $scope.init = function () {
            if (id == 0) {
                $rootScope.isEasyProgram = isEasy ? 1 : 0;
                $scope.newProgram = {
                    id: 0,
                    isEasy: isEasy,
                    label: null,
                    collaborator: null,
                    status: null,
                    image: null,
                    newImage: null
                };
                $scope.loadCollaborators();
            } else {
                $scope.loadProgram(id);
            }
        };


        /**
         * @ngdoc method
         * @name loadProgram
         * @methodOf program.controllers:presentationController
         * @description
         * Get program
         *
         */
        $scope.loadProgram = function (id) {
            programService.getProgram(id).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
                $scope.loadProgramCollaborator($scope.program);

            });
        };

        /**
         * @ngdoc method
         * @name loadProgramCollaborator
         * @methodOf program.controllers:presentationController
         * @description
         * Load program collaborator
         *
         * @return {object} collaborator A collaborator data
         */
        $scope.loadProgramCollaborator = function (program) {
            programService.getProgramCollaborator(program.id).then(function (data) {
                $scope.collaborator = data.collaborator;
                $scope.newProgram = {
                    id: program.id,
                    isEasy: program.isEasy,
                    label: program.label,
                    status: 'edit',
                    collaborator: $scope.collaborator.id,
                    image: program.image
                };
            });
        };

        /**
         * @ngdoc method
         * @name postProgram
         * @methodOf program.controllers:presentationController
         * @description
         * Create a new program: step1
         *
         * @param {object} form Program form to be validated
         */
        $scope.postProgram = function (form) {

            if (form.$valid && $scope.newProgram.image) {

                if ($scope.newProgram.id === 0) {
                    programService.postProgram($scope.newProgram).then(function (response) {
                        $state.go("program.commissioning", {id: response.data.program.id});
                    }).catch(function (fallback) {
                        if (fallback.data.data && fallback.data.data.errors) {
                            var errors = fallback.data.data.errors;
                            var labelMessage = errors['label'][0];
                            $scope.errors = {label: labelMessage};
                        }
                    });
                } else if ($scope.newProgram.status === 'edit') {
                    if ($scope.newProgram.image.name) {
                        programService.postProgram($scope.newProgram).then(function (response) {
                            if (response.status === 'success') {
                                $state.go("program.commissioning", {id: $scope.program.id});
                            }
                        });
                    } else {
                        $state.go("program.commissioning", {id: $scope.program.id});
                    }
                }
            }
        };

        /**
         * @ngdoc method
         * @name loadCollaborators
         * @methodOf program.controllers:presentationController
         * @description
         * Get collaborators of program
         *
         * @returns {object} collaborators Collaborators data
         */
        $scope.loadCollaborators = function () {
            programService.newProgram().then(function (data) {
                $scope.collaborators = data.collaborators || "";
                var $formData = [];
                angular.forEach($scope.collaborators, function (value, key) {
                    if ($scope.newProgram && $scope.newProgram.id == 0 && !$scope.newProgram.collaborator) {
                        $scope.newProgram.collaborator = key;
                    }
                    var value = value || 'Not Available';
                    var parts = {};
                    parts['value'] = key;
                    parts['label'] = value;
                    $formData.push(parts);
                });
                $scope.allCollaborators = $formData;
            });
        };

        $scope.init();
    }

    return presentationController;
});
