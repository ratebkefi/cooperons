/**
 * @ngdoc controller
 * @name admin.controllers:mandataireController
 * @description
 * This file defines the home controller for admin space
 */
define([], function () {
    'use strict';

    mandataireController.$inject = ['$rootScope', '$scope', 'mandataireService', 'paymentService', 'settlementService',
        'quarterlyTaxationRepository', 'autoEntrepreneurRepository', 'FileSaver', 'ngDialog', 'Notification', '$filter'];

    function mandataireController($rootScope, $scope, mandataireService, paymentService, settlementService,
                                  quarterlyTaxationRepository, autoEntrepreneurRepository, FileSaver, ngDialog, Notification, $filter) {

        /**
         * @ngdoc method
         * @name loadWaitingPayments
         * @methodOf admin.controllers:mandataireController
         * @description
         * Get waiting payments list
         */
        $scope.loadWaitingPayments = function () {
            var filter = {status: 'standby', paymentMode: 'virement'};
            paymentService.getPayments(filter).then(function (response) {
                $scope.waitingPayments = response.payments;
                angular.forEach($scope.waitingPayments, function (payment) {
                    payment.selected = false;
                    payment.showFrais = false;
                    $scope.showPaymentsActions = false;
                });
                $scope.loadNetBalancePayments();
            });
        };

        /**
         * @ngdoc method
         * @name loadWaitingSettlements
         * @methodOf admin.controllers:mandataireController
         * @description
         * Get waiting settlements list
         */
        $scope.loadWaitingSettlements = function () {
            settlementService.getSettlements({status: 'waiting'}).then(function (response) {
                $scope.waitingSettlements = response.settlements;
            });
        };

        /**
         * @ngdoc method
         * @name loadNetBalancePayments
         * @methodOf admin.controllers:mandataireController
         * @description
         * Get netbalance payments list
         */
        $scope.loadNetBalancePayments = function () {
            paymentService.getNetbalances().then(function (response) {
                $scope.netbalances = response.netbalances;
            });
        };


        /**
         * @ngdoc method
         * @name loadQuarterlyTaxations
         * @methodOf admin.controllers:mandataireController
         * @description
         * Get all pending quarterly taxations
         */
        $scope.loadQuarterlyTaxations = function () {
            quarterlyTaxationRepository.getQuarterlyTaxations().then(function (response) {
                $scope.allQuarterlyTaxations = response.quarterlyTaxations;
            });
        };

        /**
         * @ngdoc method
         * @name deleteSettlement
         * @methodOf admin.controllers:mandataireController
         * @description
         * Delete settlement
         *
         * @param {object} settlement A settlement to remove
         */
        $scope.deleteSettlement = function (settlement) {
            settlementService.deleteSettlement(settlement.id).then(function () {
                $scope.loadWaitingSettlements();
                $rootScope.$broadcast('refreshPrograms');
            });
        };

        /**
         * @ngdoc method
         * @name confirmPayment
         * @methodOf admin.controllers:mandataireController
         * @description
         * Submit order virement
         *
         * @param {number} mandataireId Id of mandataire
         * @param {number} amount A amount value
         */
        $scope.confirmPayment = function (mandataireId, amount) {
            mandataireService.postMandatairePayment(mandataireId, -amount).then(function () {
                Notification.success({
                    title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                    message: 'Paiement effectuée avec succès'
                });
                $scope.loadNetBalancePayments();
                $scope.loadWaitingPayments();
                $scope.loadWaitingSettlements();
            });
        };

        /**
         * @ngdoc method
         * @name selectAllPayments
         * @methodOf admin.controllers:mandataireController
         * @description
         * Select/Deselect all payments
         *
         * @param {object} selected A selected payment
         */
        $scope.selectAllPayments = function (selected) {
            angular.forEach($scope.waitingPayments, function (payment) {
                payment.selected = selected;
            });

            $scope.isPaymentsSelected();
        };

        /**
         * @ngdoc method
         * @name selectPayment
         * @methodOf admin.controllers:mandataireController
         * @description
         * Select/Deselect payment
         *
         * @param {object} payment A selected payment
         */
        $scope.selectPayment = function (payment) {
            payment.selected = !payment.selected;
            //used to show input for cesu virment mode and not disappear after second select
            payment.showFrais = true;
            if (typeof(payment.frais) === 'undefined')payment.frais = 0;
            if (typeof(payment.frais) !== 'undefined') {
                payment.frais = $filter('displayPrice')(payment.frais, '', '.');
            }
            $scope.isPaymentsSelected();
        };

        /**
         * @ngdoc method
         * @name isPaymentsSelected
         * @methodOf admin.controllers:mandataireController
         * @description
         * Check existing of selected payment
         */
        $scope.isPaymentsSelected = function () {
            var selected = false;

            angular.forEach($scope.waitingPayments, function (payment) {
                if (payment.selected) {
                    selected = true;
                }
            });

            $scope.showPaymentsActions = selected;
        };

        /**
         * @ngdoc method
         * @name getSelectedPaymentsIds
         * @methodOf admin.controllers:mandataireController
         * @description
         * Get ids of selected payments
         *
         * @returns {Array} ids A selected payments ids
         */
        $scope.getSelectedPaymentsIds = function () {
            var ids = [];

            angular.forEach($scope.waitingPayments, function (payment) {
                if (payment.selected) {
                    ids.push(parseInt(payment.id));
                }
            });

            return ids;
        };

        /**
         * @ngdoc method
         * @name validatePayments
         * @methodOf admin.controllers:mandataireController
         * @description
         * Validate selected payments
         */
        $scope.validatePayments = function () {
            ngDialog.openConfirm({template: 'payments.validate', scope: $scope})
                .then(function () {
                    var ids = $scope.getSelectedPaymentsIds();
                    var patch = [];
                    angular.forEach(ids, function (id) {
                        patch.push({op: 'confirm', path: '/', payment: id});
                    });
                    paymentService.patchPayments(patch)
                        .then(function () {
                            $scope.loadWaitingPayments();
                            $scope.loadNetBalancePayments();
                            $scope.loadWaitingPayments();
                            $scope.loadWaitingSettlements();
                            $rootScope.$broadcast('refreshPrograms');
                        });
                });
        };

        /**
         * @ngdoc method
         * @name cancelPayments
         * @methodOf admin.controllers:mandataireController
         * @description
         * Cancel selected payments
         */
        $scope.cancelPayments = function () {
            ngDialog.openConfirm({template: 'payments.cancel', scope: $scope})
                .then(function () {
                    var ids = $scope.getSelectedPaymentsIds();
                    var patch = [];

                    angular.forEach(ids, function (id) {
                        patch.push({op: 'cancel', path: '/', payment: id});
                    });

                    paymentService.patchPayments(patch).then(function () {
                        $scope.loadWaitingPayments();
                    });
                });
        };

        /**
         * @ngdoc method
         * @name exportPayments
         * @methodOf admin.controllers:mandataireController
         * @description
         * Export selected payments in XLS file
         */
        $scope.exportPayments = function () {
            var ids = $scope.getSelectedPaymentsIds();
            paymentService.exportPayment(ids).then(function (data) {
                var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
                FileSaver.saveAs(blob, 'payments.xls');
            });
        };


        /**
         * @ngdoc method
         * @name rescheduleQuarterlyTaxation
         * @methodOf admin.controllers:mandataireController
         * @description
         * Reschedule quarterly taxation to next quarter ...
         *
         * @param {number} idAutoentrepreneur Id of AutoEntrepreneur
         */
        $scope.rescheduleQuarterlyTaxation = function (idAutoentrepreneur) {
            var patch = [{op: 'reschedule', path: '/'}];
            autoEntrepreneurRepository.patchAutoEntrepreneur(idAutoentrepreneur, patch).then(function () {
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white;">  Succès</i>',
                        message: "Report des charges sociales effectué avec succès."
                    });
                    $scope.loadQuarterlyTaxations();
                }
            );
        };

        /**
         * @ngdoc method
         * @name confirmQuarterlyTaxation
         * @methodOf admin.controllers:mandataireController
         * @description
         * Confirm quarterly taxation
         *
         * @param {number} idAutoentrepreneur Id of AutoEntrepreneur
         * @param {number} amount of quarterly taxation
         */
        $scope.confirmQuarterlyTaxation = function (idAutoentrepreneur, amount) {
            var patch = [{op: 'quarterly', path: '/', amount: amount}];
            autoEntrepreneurRepository.patchAutoEntrepreneur(idAutoentrepreneur, patch).then(function () {
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white;">  Succès</i>',
                        message: "Paiement des charges sociales effectué avec succès."
                    });
                    $scope.loadQuarterlyTaxations();
                    $scope.loadNetBalancePayments();

                }
            );
        };

        $scope.$on('refreshSettlements', function (event) {
            $scope.loadWaitingSettlements();
        });

        /**
         * Load all data
         */
        $scope.loadNetBalancePayments();
        $scope.loadWaitingSettlements();
        $scope.loadWaitingPayments();
        $scope.loadQuarterlyTaxations();
    }

    return mandataireController;
});
