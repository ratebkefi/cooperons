/**
 * @ngdoc controller
 * @name autoEntrepreneur.controllers:settingSettlementsController
 * @description
 * This file defines the new settlements contract controller
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function settingSettlementsController($state, $mdDialog, common, settingSettlementsService) {

        common.setMenuTab('party');
        var contractId = $state.params.id;

        var vm = this;
        vm.contract = null;
        vm.recruitmentContract = null;
        vm.recruitmentSettings = null;

        vm.recalculate = recalculate;
        vm.postSettlements = postSettlements;


        activate();

        function activate() {
            return settingSettlementsService.loadContractDetails(contractId).then(function (data) {
                vm.contract = data.contract;
                vm.contract.mandataire = data.mandataire;
                vm.contract.recruitment = data.recruitment;
                vm.contract.serviceTypes = data.serviceTypes;
                settingSettlementsService.calculateTotalServiceAmount(vm.contract);
                if (vm.contract && vm.contract.recruitment && !vm.contract.recruitment.isExpired) {
                    vm.recruitmentContract = recruitment.recruitmentContract;
                    vm.recruitmentSettings = recruitment.recruitmentSettings;
                    recruitmentSettings.levelLabel1 = recruitmentSettings.offset1 > 0 ? 'et augmentée de' : 'et diminuée de';
                    recruitmentSettings.levelLabel2 = recruitmentSettings.offset2 > 0 ? 'et augmentée de' : 'et diminuée de';
                }

            });
        }

        /**
         * @ngdoc method
         * @name recalculate
         * @methodOf autoEntrepreneur.controllers:settingSettlementsController
         * @description
         * Recalculate amount on change values from view
         */
        function recalculate() {
            settingSettlementsService.calculateTotalServiceAmount(vm.contract);
            if (vm.contract.recruitment && !vm.contract.recruitment.isExpired) {
                settingSettlementsService.calculateCommisionning(vm.contract, vm.recruitmentSettings);
            }
        }

        /**
         * @ngdoc method
         * @name postSettlementsDialog
         * @methodOf autoEntrepreneur.controllers:settingSettlementsController
         * @description
         * Display confirm/standby add settlements dialog
         */
        function postSettlements() {
            var data = {contract: vm.contract};
            $mdDialog.showConfirm({template: 'prestation.confirm', data: data}).then(function () {
                settingSettlementsService.createSettlements(vm.contract).then(function () {
                    $state.go('party.home');
                });
            });
        }
    }

    return settingSettlementsController;
});