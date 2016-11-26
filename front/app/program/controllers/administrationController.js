/**
 * @ngdoc controller
 * @name program.controllers:administrationController
 * @description
 * This file defines the program's administration controller
 */
define([], function () {
    'use strict';

    administrationController.$inject = ['$rootScope', '$scope', '$state', 'programService', 'Notification', 'participateService'];

    function administrationController($rootScope, $scope, $state, programService, Notification, participateService) {
        /**
         * Init global parameters
         */
        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var programId = $state.params.id;
        $scope.participate = {};
        $scope.filleuls = [];
        $scope.searchFilleul = {label: ''};

        /**
         * @ngdoc method
         * @name init
         * @methodOf program.controllers:administrationController
         * @description
         * Initialize scope
         *
         * @param {number} programId Id of program
         */
        $scope.init = function (programId) {
            $scope.loadProgram(programId);
            $scope.loadProgramCollaborator(programId);
            $scope.loadProgramMandataire(programId);
            $scope.loadProgramParticipates(programId);
        };

        /**
         * @ngdoc method
         * @name loadProgram
         * @methodOf program.controllers:administrationController
         * @description
         * Get program
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgram = function (programId) {
            programService.getProgram(programId).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
                if ($scope.program.status == 'prod') {
                    $scope.loadProgramHistoryPoint($scope.program.id);
                }
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramCollaborator
         * @methodOf program.controllers:administrationController
         * @description
         * Get program's collaborator
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgramCollaborator = function (programId) {
            programService.getProgramCollaborator(programId).then(function (data) {
                $scope.collaborator = data.collaborator;
                $scope.corporate = data.collaborator.corporate;
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramHistoryPoint
         * @methodOf program.controllers:administrationController
         * @description
         * Get program's history point
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgramHistoryPoint = function (programId) {
            $scope.settlementTotal = 0;
            programService.getProgramPointsHistory(programId).then(function (data) {
                $scope.historyPoints = data.historyPoints;
                $scope.settlementTotal = [];
                $scope.settlementTotal['total'] = 0;

                angular.forEach($scope.historyPoints, function (points) {
                    $scope.settlementTotal[points.settlement.id] = 0;

                    angular.forEach(points.historyPoints, function (historyPoints) {
                        $scope.settlementTotal[points.settlement.id] += historyPoints.points;
                    });

                    $scope.settlementTotal['total'] += $scope.settlementTotal[points.settlement.id];
                });
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramMandataire
         * @methodOf program.controllers:administrationController
         * @description
         * Get program's mandataire
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgramMandataire = function (programId) {
            programService.getProgramMandataire(programId).then(function (data) {
                $scope.mandataire = data.mandataire;
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramParticipates
         * @methodOf program.controllers:administrationController
         * @description
         * Get program's participates
         *
         * @param {number} programId Id of program
         * @param {string} search A search value
         */
        $scope.loadProgramParticipates = function (programId, search) {
            participateService.getProgramParticipates(programId, search).then(function (data) {
                $scope.filleuls = $scope.filleuls.concat(data.participates);
                if (!search) {
                    $scope.participates = data.participates;
                }
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramInvitations
         * @methodOf program.controllers:administrationController
         * @description
         * Get program's invitations
         *
         * @param {number} programId Id of program
         * @param {string} search A search value
         */
        $scope.loadProgramInvitations = function (programId, search) {
            programService.getProgramInvitations(programId, search).then(function (data) {
                $scope.filleuls = $scope.filleuls.concat(data.invitations);
            });
        };

        /**
         * @ngdoc method
         * @name postParticipate
         * @methodOf program.controllers:administrationController
         * @description
         * Create a new participate
         *
         * @param {object} form Participate form to be validated
         */
        $scope.postParticipate = function (form) {
            if (form.$valid) {
                participateService.postProgramParticipate($scope.program.id, $scope.participate).then(function () {
                    $scope.filleuls = [];
                    $scope.loadProgramParticipates($scope.program.id);
                    $scope.participate = {};
                    form.$submitted = false;
                    Notification.success("Envoi d'invitation avec succès");
                });
            }
        };

        /**
         * @ngdoc method
         * @name resendInvitation
         * @methodOf program.controllers:administrationController
         * @description
         * Resend invitation to participate
         *
         * @param {number} programId Id of program
         * @param {number} participatesId Id of participates
         */
        $scope.resendInvitation = function (programId, participatesId) {
            var patch = [{op: 'resend', path: '/welcomeEmail'}];
            participateService.patchProgramParticipate(programId, participatesId, patch).then(function () {
                Notification.success("Envoi d'invitation avec succès");
            });
        };

        /**
         * @ngdoc method
         * @name confirmInvitation
         * @methodOf program.controllers:administrationController
         * @description
         * Confirm invitation
         *
         * @param {number} programId Id of program
         * @param {number} invitationId Id of invitation
         */
        $scope.confirmInvitation = function (programId, invitationId) {
            var patch = [{op: 'confirm', path: '/invitation', invitation: invitationId}];
            programService.patchProgram(programId, patch).then(function (data) {
                Notification.success("Confiramation d'invitation avec succès");
                var newParticipateId = data.confirmedInvitation.participate.id;
                $state.go('program.sponsorship', {programId: programId, participatesId: newParticipateId});
            });
        };

        /**
         * @ngdoc method
         * @name searchFilleuls
         * @methodOf program.controllers:administrationController
         * @description
         * Search filleuls
         */
        $scope.searchFilleuls = function () {
            if ($scope.searchFilleul.label && $scope.searchFilleul.label.length >= 2) {
                $scope.filleuls = [];
                $scope.loadProgramParticipates($scope.program.id, $scope.searchFilleul.label);
                $scope.loadProgramInvitations($scope.program.id, $scope.searchFilleul.label);
            } else if (!$scope.searchFilleul.label) {
                $scope.filleuls = $scope.participates;
            }
        };

        /**
         * Load data
         */
        $scope.init(programId);
    }

    return administrationController;
});
