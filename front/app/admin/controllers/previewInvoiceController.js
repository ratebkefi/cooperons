/**
 * @ngdoc controller
 * @name admin.controllers:previewInvoiceController
 * @description
 * This file defines the preview invoice controller for admin space
 */
define([], function () {
    'use strict';

    previewInvoiceController.$inject = ['$scope', '$rootScope', '$state', 'mandataireService'];

    function previewInvoiceController($scope, $rootScope, $state, mandataireService) {
        /**
         * Init global parameters
         */
        var idMandataire = $state.params.idMandataire;
        $rootScope.space = null;

        /**
         * @ngdoc method
         * @name loadInvoice
         * @methodOf admin.controllers:previewInvoiceController
         * @description
         * Load mandataire by idMandataire
         *
         * @param {number} idMandataire Id of mandataire
         */
        $scope.loadInvoice = function (idMandataire) {
            mandataireService.getMandataireInvoice(idMandataire).then(function (data) {
                $scope.invoice = data.invoice;
                $scope.ownerIsCooperons = data.ownerIsCooperons;
                $scope.summaryTva = data.summaryTva;
                $scope.operations = data.operations;
                $scope.endBalance = data.endBalance;
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataire
         * @methodOf admin.controllers:previewInvoiceController
         * @description
         * Load mandataire by id
         *
         * @param {number} idMandataire Id of mandataire
         */
        $scope.loadMandataire = function (idMandataire) {
            mandataireService.getMandataire(idMandataire).then(function (data) {
                $scope.mandataire = data.mandataire;
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataireClient
         * @methodOf admin.controllers:previewInvoiceController
         * @description
         * Retrieve member's mandataire client
         *
         * @param {number} idMandataire Id of mandataire
         */
        $scope.loadMandataireClient = function (idMandataire) {
            mandataireService.getMandataireClient(idMandataire).then(function (data) {
                $scope.client = data.client;
            });
        };

        /**
         * @ngdoc method
         * @name loadMandataireOwner
         * @methodOf admin.controllers:previewInvoiceController
         * @description
         * Retrieve member's mandataire owner
         *
         * @param {number} idMandataire Id of mandataire
         */
        $scope.loadMandataireOwner = function (idMandataire) {
            mandataireService.getMandataireOwner(idMandataire).then(function (data) {
                $scope.owner = data.owner;
            });
        };

        /**
         * Load all data
         */
        $scope.loadInvoice(idMandataire);
        $scope.loadMandataire(idMandataire);
        $scope.loadMandataireClient(idMandataire);
        $scope.loadMandataireOwner(idMandataire);
    }

    return previewInvoiceController;
});