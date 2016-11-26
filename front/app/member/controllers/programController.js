/**
 * @ngdoc controller
 * @name member.controllers:programController
 * @description
 * This file defines the member's program controller
 */
define([], function () {
    'use strict';
    programController.$inject = ['$scope', '$state','config', 'common', 'programService', 'memberRepository', '$mdDialog','Notification'];

    function programController($scope, $state, config, common, programService, memberRepository, $mdDialog, Notification) {

        common.setMenuTab('programmes');

        /**
         * @ngdoc method
         * @name loadPrograms
         * @methodOf member.controllers:programController
         * @description
         * Load all programs
         */
        $scope.loadPrograms = function () {
            $scope.programs = [];
            $scope.loadMemberPrograms();
            $scope.loadParticipatePrograms();
            $scope.$broadcast('refreshInfoMandataire');
        };

        /**
         * @ngdoc method
         * @name editProgram
         * @methodOf member.controllers:programController
         * @description
         * Edit program
         *
         * @param {object} program A program data
         */
        $scope.editProgram = function (program) {
            var scopeDialog = $scope.$new();
            scopeDialog.program = program;

            if (program.status === "prod") {
                $mdDialog.showConfirm({template: 'program.prod.edit', data: {program: program}})
                    .then(function () {
                        programService.editProgram(program.id).then(function (response) {
                            $state.go('program.presentation', {id: response.newProgram.id});
                        });
                    });
            } else if (program.status === "cancel") {
                $mdDialog.showConfirm({template: 'program.reactivate', scope: scopeDialog})
                    .then(function () {
                        var patch = [{op: 'reactivate', path: '/'}];
                        programService.patchProgram(program.id, patch).then(function () {
                            $state.go('program.presentation', {id: program.id});
                        });
                    });
            } else {
                $state.go('program.presentation', {id: program.id});
            }
        };

        /**
         * @ngdoc method
         * @name deleteProgram
         * @methodOf member.controllers:programController
         * @description
         * Delete program
         *
         * @param {object} program A program data
         */
        $scope.deleteProgram = function (program) {
            if (program.status === 'prod' || program.status === 'preprod') {
                $scope.program = program;
                ngDialog.openConfirm({template: 'program.prod.delete', scope: $scope})
                    .then(function () {
                        programService.deleteProgram(program.id).then(function (response) {
                            if (response.status === 'success') {
                                Notification.success("Résiliation du programme effectuée avec succès");
                                $scope.loadPrograms();
                            }
                        });
                    });
            } else {
                programService.deleteProgram(program.id).then(function (response) {
                    if (response.status === 'success') {
                        if (!program.oldProgram) {
                            Notification.success("Suppression du programme effectuée avec succès");
                        }

                        $scope.loadPrograms();
                    }
                });
            }
        };

        /**
         * @ngdoc method
         * @name newProgram
         * @methodOf member.controllers:programController
         * @description
         * Creating new easy program
         *
         * @param {boolean} isEasy Define whether it is a easy program
         */
        $scope.newProgram = function (isEasy) {
            if (!$scope.allCollaborators.length) {
                ngDialog.openConfirm({template: 'program.new.disable'});
            } else {
                $state.go('program.presentation', {id: 0, isEasy: isEasy ? 1 : null});
            }
        };

        /**
         * @ngdoc method
         * @name loadMemberPrograms
         * @methodOf member.controllers:programController
         * @description
         * Retrieve details of member's programs
         */
        $scope.loadMemberPrograms = function () {
            memberRepository.getMemberContracts().then(function (data) {
                $scope.contracts = data.contracts;
                angular.forEach($scope.contracts, function (contract) {
                    var program = $scope.getPendingProgram(contract.program);

                    if (program) {
                        program.isProgramPlus = (program.id == config.idProgramPlus);
                        $scope.pushProgram(program, true);
                    }
                });
            }).catch(function(){
                toastr.remove();
                toastr.warning('Ceci est un exemple de désactivation de la notification par défaut en cas d\'echec d\'une requête' , 'Attention !');

            });
        };

        /**
         * @ngdoc method
         * @name loadParticipatePrograms
         * @methodOf member.controllers:programController
         * @description
         * Load member participates
         */
        $scope.loadParticipatePrograms = function () {
            memberRepository.getMemberParticipates().then(function (data) {
                $scope.participates = data.participates;
                angular.forEach($scope.participates, function (participate) {
                    var program = $scope.getPendingProgram(participate.program);
                    program.hasMailInvitation = participate.hasMailInvitation;
                    $scope.pushProgram(program, null, true, participate.token);
                });
            });
        };

        /**
         * @ngdoc method
         * @name getPendingProgram
         * @methodOf member.controllers:programController
         * @description
         * Get the new program if it has a copy, otherwise return the program
         *
         * @param {object} program A program data
         * @return {object} program A program data
         */
        $scope.getPendingProgram = function (program) {
            if (program && program.oldProgram) {
                return program;
            } else if (program && program.newProgram) {
                var newProgram = program.newProgram;
                program.newProgram = null;
                newProgram.oldProgram = program;
                return newProgram;
            } else {
                return program;
            }
        };

        /**
         * @ngdoc method
         * @name loadMemberCollaborators
         * @methodOf member.controllers:programController
         * @description
         * Load member collaborator for corporate
         */
        $scope.loadMemberCollaborators = function () {
            memberRepository.getMemberCollaborators().then(function (data) {
                $scope.allCollaborators = data.collaborators;
            });
        };

        /**
         * @ngdoc method
         * @name pushProgram
         * @methodOf member.controllers:programController
         * @description
         * Combine participate's programs and collaboratoris program
         *
         * @param {object} program A program data
         * @return {boolean} isCollaborator Define whether it is a collaborator
         * @return {boolean} isParticipate Define whether it is a participate
         * @return {string} token A token of participate
         */
        $scope.pushProgram = function (program, isCollaborator, isParticipate, token) {
            var exist = false;
            var selectedProgram = program;

            angular.forEach($scope.programs, function (programIterator) {
                if (programIterator.id === program.id && !exist) {
                    selectedProgram = programIterator;
                    exist = true;
                }
            });

            if (!exist) {
                program.statusLabel = programService.getProgramStatusLabel(selectedProgram);
                program.actions = {};
                $scope.programs.push(program);
                selectedProgram = program;

            }

            isCollaborator ? selectedProgram.isCollaborator = true : null;
            isParticipate ? selectedProgram.isParticipate = true : null;
            token ? selectedProgram.token = token : null;

            if ((program.hasMailInvitation && selectedProgram.isParticipate) || selectedProgram.isProgramPlus) {
                selectedProgram.actions.sponsor = true;
            }

            if (selectedProgram.isCollaborator) {
                selectedProgram.actions.edit = true;

                if (selectedProgram.status != 'standby' && selectedProgram.status != 'cancel') {
                    selectedProgram.actions.administration = true;
                    selectedProgram.actions.configuration = true;
                }

                selectedProgram.actions.delete = selectedProgram.status != "cancel" && selectedProgram.status != 'preprod';
            }
        };

        /**
         * Load data
         */
        $scope.loadPrograms();
        $scope.loadMemberCollaborators();
    }

    return programController;
});
