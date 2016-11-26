/**
 * @ngdoc controller
 * @name program.controllers:affairController
 * @description
 * This file defines the program's affair controller
 */
define([], function () {
    'use strict';

    affairController.$inject = ['$rootScope', '$scope', '$state', 'programService', 'easySettingService', 'ngDialog', 'affairService'];

    function affairController($rootScope, $scope, $state, programService, easySettingService, ngDialog, affairService) {
        /**
         * Init global parameters
         */
        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var programId = $state.params.programId;
        var affairId = $state.params.affairId;
        $scope.amount = {
            pattern: /^[0-9]{1,10}([,\.][0-9]{1,2})?$/,
            value: 0
        };

        /**
         * @ngdoc method
         * @name loadProgramsById
         * @methodOf program.controllers:affairController
         * @description
         * Get program by id
         */
        $scope.loadProgramsById = function () {
            programService.getProgram(programId).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
            });
        };

        /**
         * @ngdoc method
         * @name loadEasySetting
         * @methodOf program.controllers:affairController
         * @description
         * Load settings of easy program
         */
        $scope.loadEasySetting = function () {
            easySettingService.getProgramEasySetting(programId).then(function (data) {
                $scope.easySetting = data.easySetting;
            });
        };

        /**
         * @ngdoc method
         * @name loadParticipate
         * @methodOf program.controllers:affairController
         * @description
         * Load participate
         */
        $scope.loadParticipate = function () {
            affairService.getAffairParticipate(programId, affairId).then(function (response) {
                $scope.participate = response.participate;
            });
        };

        /**
         * @ngdoc method
         * @name loadAffair
         * @methodOf program.controllers:affairController
         * @description
         * Get program's affairs
         */
        $scope.loadAffair = function () {
            affairService.getProgramAffair(programId, affairId).then(function (response) {
                $scope.affair = response.affair;
                $scope.amount.value = $scope.affair.remains;
                $scope.loadUpline();
            });
        };

        /**
         * @ngdoc method
         * @name loadUpline
         * @methodOf program.controllers:affairController
         * @description
         * Load upline
         */
        $scope.loadUpline = function () {
            affairService.getAffairUpline(programId, affairId).then(function (response) {
                $scope.upline = response.upline;
                $scope.totalPoints = 0;
                $scope.total = 0;

                angular.forEach($scope.upline, function (upline) {
                    if (upline.participatesTo.id === $scope.participate.id) {
                        $scope.totalPoints = $scope.totalPoints + upline.multiPoints;
                    }

                    $scope.total = $scope.total + upline.calc.amountHt;
                    $scope.totalPoints = $scope.totalPoints + upline.points;
                });
            });
        };

        /**
         * @ngdoc method
         * @name loadComissioning
         * @methodOf program.controllers:affairController
         * @description
         * Get program's comissioning
         */
        $scope.loadComissioning = function () {
            $scope.totalCommission = 0;
            affairService.getAffairComissioning(programId, affairId).then(function (response) {
                $scope.commissions = response.commissions;
                angular.forEach($scope.commissions, function (commission) {
                    $scope.totalCommission = $scope.totalCommission + commission.base;
                });

            });
        };

        /**
         * @ngdoc method
         * @name submitAffair
         * @methodOf program.controllers:affairController
         * @description
         * Negotiate affair
         *
         * @param {object} form Affair form to be validated
         */
        $scope.submitAffair = function (form) {
            if (form.$valid) {
                if ($scope.affair.amount == 0 || $scope.amount.value == 0 || $scope.affair.status == 'payable' && $scope.amount.value > $scope.affair.remains) {
                    form.amount.$error.pattern = true;
                    return false;
                }

                if ($scope.affair.status === 'negotiation' && $scope.program.status !== 'preprod') {
                    ngDialog.openConfirm({template: 'affair.close', scope: $scope})
                        .then(function () {
                            $scope.processAffair();
                        });
                } else {
                    $scope.processAffair();
                }
            }
        };

        /**
         * @ngdoc method
         * @name checkAmountConditions
         * @methodOf program.controllers:affairController
         * @description
         * check condition  on change input to display errors message
         *
         * @param {boolean} condition Condition data
         */
        $scope.checkAmountConditions = function (form) {
            if ($scope.affair.amount == 0 || $scope.amount.value == 0 || ($scope.affair.status == 'payable'
                && $scope.amount.value > $scope.affair.remains)) {
                form.amount.$error.pattern = true;
                return false;
            }
            return true;
        };

        /**
         * @ngdoc method
         * @name processAffair
         * @methodOf program.controllers:affairController
         * @description
         * A process Affair
         */
        $scope.processAffair = function () {
            var patch = [{op: 'process', path: '/', amount: $scope.amount.value}];
            affairService.patchProgramAffair(programId, $scope.affair.id, patch).then(function () {
                $scope.loadAffair();
                $scope.loadComissioning();
                $scope.$broadcast('refreshInfoMandataire');
            });
        };

        /**
         * @ngdoc method
         * @name cancelAffair
         * @methodOf program.controllers:affairController
         * @description
         * Cancel affair
         *
         * @param {object} form Affair form to be validated
         */
        $scope.cancelAffair = function (form) {
            if (form.$valid) {
                var patch = [{op: 'cancel', path: '/', message: $scope.affair.cancelMsg}];
                affairService.patchProgramAffair($scope.program.id, $scope.affair.id, patch).then(function () {
                    $scope.loadAffair();
                    $scope.loadComissioning();
                });
            }
        };

        /**
         * Load data
         */
        $scope.loadProgramsById();
        $scope.loadParticipate();
        $scope.loadEasySetting();
        $scope.loadAffair();
        $scope.loadComissioning();
    }

    return affairController;
});
