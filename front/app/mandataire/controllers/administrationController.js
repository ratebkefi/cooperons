/**
 * @ngdoc controller
 * @name mandataire.controllers:administrationController
 * @description
 * This file defines the mandataire's administration controller
 */
define([], function () {
    'use strict';

    administrationController.$inject = ['$state', '$rootScope', '$scope', 'Notification', 'paymentService',
        'settlementService', 'mandataireService', 'invoiceService', 'FileSaver'];

    function administrationController($state, $rootScope, $scope, Notification, paymentService, settlementService,
                                      mandataireService, invoiceService, FileSaver) {

        /**
         * @ngdoc method
         * @name loadMandataireOperations
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Get mandataire account
         *
         * @param {number} mandataireId Id of mandataire
         */
        $scope.loadMandataireOperations = function (mandataireId) {
            mandataireService.getMandataireOperations(mandataireId).then(function (data) {
                $scope.operations = data.operations;
                var lastInvoiceId = null;
                angular.forEach($scope.operations, function (operation) {
                    operation.lastInvoiceId = lastInvoiceId;
                    lastInvoiceId = operation.invoiceId ? operation.invoiceId : null;
                });
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataireSettlements
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Get waiting settlements list
         *
         * @param {number} mandataireId Id of mandataire
         */
        $scope.loadMandataireSettlements = function (mandataireId) {
            mandataireService.getMandataireSettlements(mandataireId, 'waiting').then(function (data) {
                $scope.waitingSettlements = data.settlements;
            });
        };

        /**
         * @ngdoc method
         * @name loadWaitingPayment
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Get waiting payment list
         *
         * @param {number} mandataireId Id of mandataire
         */
        $scope.loadWaitingPayment = function (mandataireId) {
            mandataireService.getMandatairePayments(mandataireId, 'standby').then(function (data) {
                $scope.waitingPayment = data.payments[0] || null;
            });
        };

        /**
         * @ngdoc method
         * @name loadRecords
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Get records
         *
         * @param {number} mandataireId Id of mandataire
         */
        $scope.loadRecords = function (mandataireId) {
            mandataireService.getMandataireRecords(mandataireId).then(function (data) {
                $scope.records = data.records;
                $scope.accounts = data.accounts;
                $scope.provision = data.provision;
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataireClient
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Retrieve mandataire client
         *
         * @param {number} mandataireId Id of mandataire
         */
        $scope.loadMandataireClient = function (mandataireId) {
            mandataireService.getMandataireClient(mandataireId).then(function (data) {
                $scope.client = data.client;
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataireOwner
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Retrieve mandataire owner
         *
         * @param {number} mandataireId Id of mandataire
         */
        $scope.loadMandataireOwner = function (mandataireId) {
            mandataireService.getMandataireOwner(mandataireId).then(function (data) {
                $scope.owner = data.owner;
            });
        };

        /**
         * @ngdoc method
         * @name loadAuthorizedParty
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Retrieve authorized party
         *
         * @param {number} mandataireId Id of mandataire
         */
        $scope.loadAuthorizedParty = function (mandataireId) {
            mandataireService.getMandataireAuthorizedParty(mandataireId).then(function (data) {
                $scope.authorizedParty = data.party;
                if ($scope.$parent.contract) {
                    $rootScope.menuTab = $scope.authorizedParty.type;
                }
            });
        };

        /**
         * @ngdoc method
         * @name downloadInvoice
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Download invoice in pdf format
         *
         * @param {number} InvoiceId Id of invoice
         */
        $scope.downloadInvoice = function (InvoiceId) {
            invoiceService.downloadInvoice(InvoiceId).then(function (data) {
                var blob = new Blob([data], {type: "application/pdf"});
                FileSaver.saveAs(blob, 'invoice_' + InvoiceId + '.pdf');
            });
        };

        /**
         * @ngdoc method
         * @name deletePayment
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Delete payment
         *
         * @param {number} idPayment Id of mandataire payment
         */
        $scope.deletePayment = function (idPayment) {
            paymentService.deletePayment(idPayment)
                .then(function () {
                    if ($scope.$parent.program) {
                        $state.go('member.programs');
                    } else if ($scope.$parent.contract) {
                        $state.go('party.home');
                    } else {
                        $state.go('member.home');
                    }
                });
        };

        /**
         * @ngdoc method
         * @name deleteSettlement
         * @methodOf mandataire.controllers:administrationController
         * @description
         * Delete settlement
         *
         * @param {number} settlementId Id of settlement
         */
        $scope.deleteSettlement = function (settlementId) {
            settlementService.deleteSettlement(settlementId).then(function () {
                $scope.loadMandataireSettlements($scope.$parent.mandataire.id);
                Notification.success({
                    title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                    message: 'Prestation supprimée'
                });
            });
        };

        /**
         * Load data
         */
        $scope.loadMandataireClient($scope.$parent.mandataire.id);
        $scope.loadMandataireOwner($scope.$parent.mandataire.id);
        $scope.loadAuthorizedParty($scope.$parent.mandataire.id);
        $scope.loadMandataireOperations($scope.$parent.mandataire.id);
        $scope.loadMandataireSettlements($scope.$parent.mandataire.id);
        $scope.loadWaitingPayment($scope.$parent.mandataire.id);
        $scope.loadRecords($scope.$parent.mandataire.id);
    }

    return administrationController;
});