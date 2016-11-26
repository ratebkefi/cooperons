/**
 * @ngdoc controller
 * @name program.controllers:paymentController
 * @description
 * This file defines the payment controller
 */
define([], function () {
    'use strict';

    paymentController.$inject = ['$rootScope', '$scope', '$state', 'programService', 'mandataireService', 'Notification',
        'operationService', '$sce'];

    function paymentController($rootScope, $scope, $state, programService, mandataireService, Notification,
                               operationService, $sce) {

        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var programId = $state.params.programId;
        $scope.checkboxModel = {cgv: false};

        /**
         * @ngdoc method
         * @name init
         * @methodOf program.controllers:paymentController
         * @description
         * Init scope
         *
         */
        $scope.init = function () {
            $scope.loadProgram(programId);
            $scope.loadCollaborator(programId);
            $scope.loadProgramMandataire(programId);
            $scope.loadProgramOperations(programId);
            $scope.loadSubscriptionOrder(programId);
        };

        /**
         * @ngdoc method
         * @name loadProgram
         * @methodOf program.controllers:paymentController
         * @description
         * Get program
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgram = function (programId) {
            programService.getProgram(programId).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
            });
        };

        /**
         * @ngdoc method
         * @name loadCollaborator
         * @methodOf program.controllers:paymentController
         * @description
         * Load program collaborator
         *
         * @param {number} programId Id of program
         */
        $scope.loadCollaborator = function (programId) {
            programService.getProgramCollaborator(programId).then(function (data) {
                $scope.collaborator = data.collaborator;
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramOperations
         * @methodOf program.controllers:paymentController
         * @description
         * Get program's oprations
         *
         * @param {number} programId Id of program
         *
         * @return {object} operations Operations data
         */
        $scope.loadProgramOperations = function (programId) {
            operationService.getProgramOperationsAsHtml(programId).then(function (data) {
                $scope.htmlOperations = $sce.trustAsHtml(data);
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramMandataire
         * @methodOf program.controllers:paymentController
         * @description
         * Get program's oprations
         *
         * @param {number} programId Id of program
         *
         * @return {object} mandataire A mandataire data
         */
        $scope.loadProgramMandataire = function (programId) {
            programService.getProgramMandataire(programId).then(function (data) {
                $scope.mandataire = data.mandataire;
                if ($scope.mandataire) {
                    $scope.loadWaitingPayment($scope.mandataire);
                }
            });
        };

        /**
         * @ngdoc method
         * @name loadWaitingPayment
         * @methodOf program.controllers:paymentController
         * @description
         * Get waiting payment for mandataire
         *
         * @param {object} mandataire Manadataire data
         *
         * @return {object} payment A payment data
         */
        $scope.loadWaitingPayment = function (mandataire) {
            mandataireService.getMandatairePayments(mandataire.id, 'standby').then(function (data) {
                $scope.payment = data.payments[0] || null;
            });
        };

        /**
         * @ngdoc method
         * @name loadSubscriptionOrder
         * @methodOf program.controllers:paymentController
         * @description
         * Get program's orders
         *
         * @param {number} programId Id of program
         *
         * @return {object} subscriptionOrder A subscriptionOrder data
         */
        $scope.loadSubscriptionOrder = function (programId) {
            programService.getProgramSubscriptionOrder(programId).then(function (response) {
                $scope.subscriptionOrder = response.order;
                $scope.amount = $scope.subscriptionOrder.total;
            });
        };

        /**
         * @ngdoc method
         * @name checkBeforePayment
         * @methodOf program.controllers:paymentController
         * @description
         * Check that CGV is accepted before payment
         *
         * @return {boealn} cgv Cgv data
         */
        $scope.checkBeforePayment = function () {
            if (!$scope.checkboxModel.cgv === true) {
                Notification.primary({
                    title: '<i class="fa fa-exclamation-circle" style="color: white;">  Attention !</i>',
                    message: 'Vous devez accepter les conditions générales de vente.'
                });
            }
            return $scope.checkboxModel.cgv;
        };

        /**
         * @ngdoc method
         * @name afterPayment
         * @methodOf program.controllers:paymentController
         * @description
         * Reload data after payment
         *
         */
        $scope.afterPayment = function () {
            Notification.success({
                title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                message: 'Paiement effectué avec succès'
            });
            $state.go('member.programs');
        };

        /**
         * Load data
         */
        $scope.init();
    }

    return paymentController;
});
