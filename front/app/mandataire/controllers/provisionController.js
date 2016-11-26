/**
 * @ngdoc controller
 * @name mandataire.controllers:provisionController
 * @description
 * This file defines the mandataire provision controller
 */

define([], function () {
    'use strict';

    provisionController.$inject = ['$rootScope', '$scope', '$state', 'mandataireService', 'contractRepository',
        'programService', 'Notification', '$sce', '$filter'];

    function provisionController($rootScope, $scope, $state, mandataireService, contractRepository, programService,
                                 Notification, $sce, $filter) {

        /**
         * Init global parameters
         */
        var mandataireId = $state.params.id;
        $scope.amountData = {};

        /**
         * @ngdoc method
         * @name init
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Init data
         *
         * @param {mandataireId} mandataireId Id of mandataire
         */
        $scope.init = function (mandataireId) {
            $scope.loadMandataire(mandataireId);
            $scope.loadAuthorizedParty(mandataireId);
            $scope.loadMandataireSettlements(mandataireId);
            $scope.loadWaitingPayment(mandataireId);
            $scope.loadMandataireContract(mandataireId);
        };

        /**
         * @ngdoc method
         * @name loadMandataire
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Get program's mandataire
         *
         * @param {mandataireId} mandataireId Id of mandataire
         */
        $scope.loadMandataire = function (mandataireId) {
            mandataireService.getMandataire(mandataireId).then(function (data) {
                $scope.mandataire = data.mandataire;
                $scope.calculateAmounts();
            });
        };

        /**
         * @ngdoc method
         * @name loadAuthorizedParty
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Retrieve authorized party
         *
         * @param {mandataireId} mandataireId Id of mandataire
         */
        $scope.loadAuthorizedParty = function (mandataireId) {
            mandataireService.getMandataireAuthorizedParty(mandataireId).then(function (data) {
                $scope.authorizedParty = data.party;
                $rootScope.menuTab = $scope.authorizedParty.type;
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataireSettlements
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Get waiting settlements list
         *
         * @param {mandataireId} mandataireId Id of mandataire
         */
        $scope.loadMandataireSettlements = function (mandataireId) {
            mandataireService.getMandataireSettlements(mandataireId, 'waiting').then(function (data) {
                $scope.waitingSettlements = data.settlements;
                $scope.calculateAmounts();
            });
        };

        /**
         * @ngdoc method
         * @name calculateAmounts
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Calculate amounts after getting mandataire and waitingSettlements
         */
        $scope.calculateAmounts = function () {
            if ($scope.mandataire && $scope.waitingSettlements) {
                var amountTtc = 0;
                var amountTva = 0;
                var amountHt = 0;
                var depot = 0;

                angular.forEach($scope.waitingSettlements, function (settlement) {
                    amountHt += settlement.amountHt;
                    amountTva += settlement.amount - settlement.amountHt;
                    amountTtc += settlement.amount;
                });
                var amount = depot + amountTtc;
                amount = ($scope.mandataire.depot > (amount + $scope.mandataire.minDepot)) ? 0 : $scope.mandataire.minDepot * 2;
                depot += amount;

                $scope.amountHt = amountHt;
                $scope.amountTtc = amountTva;
                $scope.amountTtc = amountTtc;
                $scope.depot = depot;
                $scope.amountData.total = amountTtc + depot;
                $scope.total = amountTtc + depot;
                $scope.amountData.provision = $filter('displayPrice')($scope.amountData.total, '', '.');
                // Used by paymentController
                $scope.amount = $scope.total;
            }
        };

        /**
         * @ngdoc method
         * @name loadWaitingPayment
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Get waiting payments for mandataire
         *
         * @param {mandataireId} mandataireId Id of mandataire
         */
        $scope.loadWaitingPayment = function (mandataireId) {
            mandataireService.getMandatairePayments(mandataireId, 'standby').then(function (data) {
                $scope.payment = data.payments[0] || null;
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataireContract
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Get mandataire contract
         *
         * @param {mandataireId} mandataireId Id of mandataire
         */
        $scope.loadMandataireContract = function (mandataireId) {
            mandataireService.getMandataireContract(mandataireId).then(function (data) {
                $scope.contract = data.contract;
                if ($scope.contract) {
                    if ($scope.contract.program) {
                        $scope.loadProgramProvisionFooter($scope.contract.program.id);
                    } else {
                        $scope.loadContractRecruitment($scope.contract.id);
                    }
                }
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramProvisionFooter
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Get provision footer for program in html format
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgramProvisionFooter = function (programId) {
            programService.getProgramsProvisionFooterAsHtml(programId).then(function (data) {
                $scope.programProvisionFooter = $sce.trustAsHtml(data);
            });
        };

        /**
         * @ngdoc method
         * @name loadContractRecruitment
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Get recruitment contract
         *
         * @param {number} contractId Id of contract
         */
        $scope.loadContractRecruitment = function (contractId) {
            contractRepository.getContractRecruitment(contractId).then(function (data) {
                $scope.contract.recruitment = data.recruitment;
            });
        };

        /**
         * @ngdoc method
         * @name checkBeforePayment
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Check that CGV is accepted before payment
         *
         * @return {boolean} A status of checking
         */
        $scope.checkBeforePayment = function () {
            $scope.amount = $scope.amountData.provision;
            if (isNaN($scope.amountData.provision) || $scope.amountData.provision == 0) {
                Notification.primary({
                    title: '<i class="fa fa-warning" style="color: white;">  Attention!</i>',
                    message: 'Veuillez saisir un montant correcte (nombre positif)'
                });
                return false;
            } else if ($scope.amountData.provision >= $scope.total) {
                return true;
            } else {
                Notification.primary({
                    title: '<i class="fa fa-warning" style="color: white;">  Attention!</i>',
                    message: 'Le montant à payer  doit être au minimum ' + $scope.total + '€'
                });
                return false;
            }
        };

        /**
         * @ngdoc method
         * @name afterPayment
         * @methodOf mandataire.controllers:provisionController
         * @description
         * Reload data after payment
         */
        $scope.afterPayment = function () {
            $scope.loadMandataireSettlements($scope.mandataire.id);
            $scope.loadWaitingPayment($scope.mandataire.id);
        };


        /**
         * Load data
         */
        $scope.init(mandataireId);
    }

    return provisionController;
});
