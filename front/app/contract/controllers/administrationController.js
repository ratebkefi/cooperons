/**
 * @ngdoc controller
 * @name contract.controllers:AdministrationController
 * @description
 * This file defines the administration controller for contract module
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function AdministrationController($state, contractAdministrationService) {

        var contractId = $state.params.id;

        var vm = this;

        vm.contract = null;
        vm.mandataire = null;
        vm.authorizedParty = null;

        activate();

        /**
         * @ngdoc method
         * @name activate
         * @methodOf party.controllers:partyController
         * @description
         * Initialize data
         */
        function activate() {
            return contractAdministrationService.loadContractDetails(contractId).then(function (data) {
                vm.contract = data.contract;
                vm.mandataire = data.mandataire;
            });
        }
    }

    return AdministrationController;
});