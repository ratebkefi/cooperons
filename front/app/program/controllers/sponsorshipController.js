/**
 * @ngdoc controller
 * @name program.controllers:sponsorshipController
 * @description
 * This file defines the sponsorship controller
 */
define([], function () {
    'use strict';

    sponsorshipController.$inject = ['$rootScope', '$scope', '$state', 'programService', 'Notification',
        'operationService', 'mandataireService', 'ngDialog', 'affairService', 'participateService'];

    function sponsorshipController($rootScope, $scope, $state, programService, Notification, operationService,
                                   mandataireService, ngDialog, affairService, participateService) {

        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var programId = $state.params.programId;
        var participateId = $state.params.participatesId;

        $scope.searchedFilleuls = {value: '', filleuls: null};
        $scope.affair = {label: ''};
        $scope.formErrors = {};
        $scope.selected = {};
        $scope.filter = {};

        /**
         * @ngdoc method
         * @name init
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Init scope
         *
         */
        $scope.init = function () {
            $scope.loadProgram();
            $scope.loadParticipate();
            $scope.loadProgramOperations();
            $scope.loadProgramMandataire();
            $scope.loadProgramAffairs();
            $scope.loadAccountpointshistory();
            $scope.loadParticipateFilleuls();
        };

        /**
         * @ngdoc method
         * @name loadProgram
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get program
         *
         * @return {object} program A program data
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
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get program's oprations
         *
         * @return {object} operations Operations data
         */
        $scope.loadProgramOperations = function () {
            operationService.getProgramOperations(programId).then(function (data) {
                $scope.operations = data.operations;
                $scope.selected.operation = data.operations[0];
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramMandataire
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get program's mandataire
         *
         * @return {object} mandataire A mandataire data
         */
        $scope.loadProgramMandataire = function () {
            programService.getProgramMandataire(programId).then(function (data) {
                $scope.mandataire = data.mandataire;
                $scope.loadWaitingSettlements();
            });
        };

        /**
         * @ngdoc method
         * @name loadWaitingSettlements
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get waiting settlements list
         *
         * @return {object} waitingSettlements WaitingSettlements data
         */
        $scope.loadWaitingSettlements = function () {
            mandataireService.getMandataireSettlements($scope.mandataire.id, 'waiting').then(function (data) {
                $scope.waitingSettlements = data.settlements;
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramAffairs
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get program's affair
         *
         * @return {object} affairs Affairs data
         */
        $scope.loadProgramAffairs = function () {
            affairService.getProgramAffairs(programId, {participateId: participateId}).then(function (response) {
                $scope.affairs = response.affairs;
            });
        };

        /**
         * @ngdoc method
         * @name loadParticipate
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get participate
         *
         * @return {object} participate A participate data
         */
        $scope.loadParticipate = function () {
            participateService.getProgramParticipate(programId, participateId).then(function (response) {
                $scope.participate = response.participate;
            });
        };

        /**
         * @ngdoc method
         * @name loadAccountpointshistory
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get program's account points history
         *
         * @return {object} accountPointsHistory An accountPointsHistory data
         */
        $scope.loadAccountpointshistory = function () {
            participateService.getParticipatePointsHistory(programId, participateId).then(function (response) {
                $scope.accountPointsHistory = response.accountPointsHistory;
            });
        };

        /**
         * @ngdoc method
         * @name createAffair
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Create new affair
         *
         * @param {object} form Affair confirmation form to be validated
         * @param {number} programId ID of program
         * @param {number} participateId Id of participate
         */
        $scope.createAffair = function (form, programId, participateId) {
            if (form.$valid) {
                var data = {'label': $scope.affair.label, 'participateId': participateId};
                affairService.postProgramAffair(programId, data).then(function () {
                    $scope.affair.label = '';
                    $scope.formErrors.label = null;
                    form.$submitted = false;
                    $scope.loadProgramAffairs();
                }).catch(function (fallback) {
                    $scope.formErrors.label = fallback.data.data.errors.label[0];
                });
            }
        };

        /**
         * @ngdoc method
         * @name loadParticipateFilleuls
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Get member filleuls
         *
         * @return {object} participateFilleuls Participate filleuls data
         */
        $scope.loadParticipateFilleuls = function () {
            participateService.getParticipateFilleuls(programId, participateId, $scope.filter).then(function (data) {
                $scope.listeDates = data.listeDates;
                $scope.listePrograms = data.listePrograms;
                $scope.filleuls = data.filleuls;
                $scope.nbre_filleuls = data.nbre_filleuls;
                $scope.total_multipoints = data.total_multipoints;
                $scope.total_multipoints_filleuls = data.total_multipoints_filleuls;

                if (!$scope.allYears) {
                    $scope.allYears = [];
                    $scope.allYears[0] = {id: 0, value: 'Année'};
                    var i = 0;
                    angular.forEach(data.listeDates, function (key, value) {
                        $scope.allYears[++i] = {id: value, value: value};
                    });
                    $scope.selected.year = $scope.allYears[0];
                }

                if (!$scope.allPrograms) {
                    $scope.allPrograms = [];
                    $scope.allPrograms[0] = {id: 0, value: 'Programme'};
                    i = 0;

                    angular.forEach(data.listePrograms, function (key, value) {
                        $scope.allPrograms[++i] = {id: value, value: key};

                    });

                    $scope.selected.program = $scope.allPrograms[0];
                }
            });
        };

        /**
         * @ngdoc method
         * @name filterParticipateFilleuls
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Filter filleul by year/program
         *
         */
        $scope.filterParticipateFilleuls = function () {
            $scope.filter = {};

            if ($scope.selected) {

                if ($scope.selected.year.id !== 0) {
                    $scope.filter.year = $scope.selected.year.value;
                }

                if ($scope.selected.program.id !== 0) {
                    $scope.filter.programId = $scope.selected.program.id;
                }

                $scope.loadParticipateFilleuls();
            }
        };

        /**
         * @ngdoc method
         * @name validateOperation
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Validate operation
         *
         */
        $scope.validateOperation = function () {
            if ($scope.selected.operation.defaultAmount) {
                var params = {
                    operationId: $scope.selected.operation.id,
                    amount: $scope.selected.operation.defaultAmount
                };
                participateService.getParticipateUpline(programId, participateId, params).then(function (response) {
                    $scope.upline = response.upline;
                });
                ngDialog.openConfirm({template: 'operation.validate', scope: $scope})
                    .then(function () {
                        var patch = [{
                            op: 'add',
                            path: '/points',
                            value: $scope.selected.operation.defaultAmount,
                            operation: $scope.selected.operation.id
                        }];
                        participateService.patchProgramParticipate(programId, participateId, patch).then(function () {
                            $scope.loadParticipate();
                            $scope.loadWaitingSettlements();
                            $scope.loadAccountpointshistory();
                            $scope.loadProgramOperations();
                            $scope.$broadcast('refreshInfoMandataire');
                            Notification.success("L'opération a été validée");
                        });
                    });
            } else {
                Notification.primary("Attention <br>Montant nul");
            }
        };

        /**
         * @ngdoc method
         * @name searchFilleuls
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Search participate filleuls
         *
         * @param {string} search A search key
         */
        $scope.searchFilleuls = function (search) {
            if (search && search.length >= 2) {
                participateService.getParticipateFilleuls($scope.program.id, $scope.participate.id, {search: search})
                    .then(function (response) {
                        $scope.searchedFilleuls.value = search;
                        $scope.searchedFilleuls.filleuls = response.filleuls;
                    });
            }
        };

        /**
         * @ngdoc method
         * @name confirmSponsorship
         * @methodOf program.controllers:sponsorshipController
         * @description
         * Confirm sponsorship
         *
         * @param {object} affiliate An affiliate data
         */
        $scope.confirmSponsorship = function (affiliate) {
            $scope.selectedAffiliate = affiliate;
            participateService.getParticipateUpline($scope.program.id, $scope.participate.id, {affiliateId: affiliate.id})
                .then(function (response) {
                    $scope.affiliateUplines = response.upline;
                });
            ngDialog.openConfirm({template: 'sponsorship.confirm', scope: $scope})
                .then(function () {
                    var patch = [{op: 'confirm', path: '/sponsorship', affiliate: affiliate.id}];
                    participateService.patchProgramParticipate($scope.program.id, $scope.participate.id, patch).then(function () {
                        $scope.searchFilleuls($scope.searchedFilleuls.value);
                        $scope.loadParticipate();
                        $scope.loadAccountpointshistory();
                        $scope.loadParticipateFilleuls();
                        $scope.loadWaitingSettlements();

                    });
                });
        };

        /**
         * Load data
         */
        $scope.init();
    }

    return sponsorshipController;
})
;
