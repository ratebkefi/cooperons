/**
 * @ngdoc controller
 * @name contract.controllers:ContractLegalController
 * @description
 * This file defines the legal contract controller
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function ContractLegalController($state, $mdDialog, common, contractLegalService) {

        common.setMenuTab('party');
        var contractId = $state.params.id;

        var vm = this;

        vm.contract = null;
        vm.authorizedParty = null;
        vm.contractHtml = '';
        vm.displayPublishButton = false;
        vm.displayAgreeButton = false;
        vm.displayWarningAccord = false;
        vm.displayAgreeCgvButton = false;

        // functions
        vm.publishContract = publishContract;
        vm.agreeContract = agreeContract;
        vm.agreeCgvContract = agreeCgvContract;
        vm.agreeWarningContract = $mdDialog.showConfirm({template: 'legalContract.close'});

        activate();

        /**
         * @ngdoc method
         * @name activate
         * @methodOf party.controllers:partyController
         * @description
         * Initialize data
         */
        function activate() {
            return contractLegalService.loadLegalDetails(contractId).then(function (data) {
                vm.contract = data.contract;
                vm.authorizedParty = data.authorizedParty;
                vm.contractHtml = data.contractHtml;
                vm.displayPublishButton = data.displayPublishButton;
                vm.displayAgreeButton = data.displayAgreeButton;
                vm.displayWarningAccord = data.displayWarningAccord;
                vm.displayAgreeCgvButton = data.displayAgreeCgvButton;
            });
        }

        /**
         * @ngdoc method
         * @name publishContract
         * @methodOf contract.controllers:ContractLegalController
         * @description
         * Resend invitation for a contract
         *
         */
        function publishContract() {
            contractLegalService.publishContract(vm.contract).then(function () {
                $state.go('party.home');
            });
        }

        /**
         * @ngdoc method
         * @name agreeContract
         * @methodOf contract.controllers:ContractLegalController
         * @description
         * Agree a contract
         *
         */
        function agreeContract() {
            contractLegalService.agreeContract(vm.contract).then(function () {
                $state.go('party.home');
            });
        }

        /**
         * @ngdoc method
         * @name agreeCgvContract
         * @methodOf contract.controllers:ContractLegalController
         * @description
         * Agree a cgv contract
         *
         */
        function agreeCgvContract() {
            contractLegalService.agreeCgvContract(vm.contract).then(function () {
                $state.go($state.current, {}, {reload: true});
            });
        }
    }

    return ContractLegalController;
});