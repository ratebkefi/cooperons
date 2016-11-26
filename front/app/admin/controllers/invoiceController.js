/**
 * @ngdoc controller
 * @name admin.controllers:invoiceController
 * @description
 * This file defines the home controller for admin space
 */
define([], function () {
    'use strict';

    invoiceController.$inject = ['$scope', 'invoiceService', 'mandataireService', 'Notification'];

    function invoiceController($scope, invoiceService, mandataireService, Notification) {

        /**
        * @ngdoc method
        * @name loadPendingInvoices
        * @methodOf admin.controllers:invoiceController
        * @description
        * Get all pending invoices
        */
        $scope.loadPendingInvoices = function () {
            invoiceService.getInvoices().then(function (response) {
                $scope.allInvoices = response.invoices;
            });
        };

        /**
        * @ngdoc method
        * @name confirmInvoice
        * @methodOf admin.controllers:invoiceController
        * @description
        * Confirm invoice
        *
        * @param {number} mandataireId Id of mandataire
        */
        $scope.confirmInvoice = function (mandataireId) {
            var patch = [{op: 'confirm', path: '/invoice'}];
            mandataireService.patchMandataire(mandataireId, patch).then(function () {
                Notification.success({
                    title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                    message: 'Confirmation effectuée avec succès'
                });
                $scope.loadPendingInvoices();
            });
        };

        $scope.loadPendingInvoices();

    }

    return invoiceController;
});
