/**
 * @ngdoc controller
 * @name autoEntrepreneur.controllers:settingContractController
 * @description
 * This file defines the new setting contract controller
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function settingContractController($state, common, contractRepository, serviceTypeRepository, settingContractService) {

        common.setMenuTab('party');
        var contractId = $state.params.id;

        var vm = this;

        vm.contract = null;
        vm.filterContract= '';
        vm.serviceTypes = [];
        vm.newServiceType = null;
        vm.recruitmentSettings = null;

        vm.calculateTotalCommission = calculateTotalCommission;
        vm.simulateTotalCommission = simulateTotalCommission;
        vm.updateRecruitmentSettings = updateRecruitmentSettings;
        vm.updateServiceType = updateServiceType;
        vm.createServiceType = createServiceType;
        vm.removeServiceType = removeServiceType;
        vm.updateMinDeposit = updateMinDeposit;

        activate();

        function activate(){
            settingContractService.loadContractDetails(contractId).then(function(data){
                if(!data.ready){
                    //$state.go('contract.legal', {'id': contractId});
                } else {
                    vm.contract = data.contract;
                    vm.filterContract = data.filterContract;
                    vm.serviceTypes = data.serviceTypes;
                    vm.newServiceType = {label: '', unitLabel: '', unitDefaultAmount:''};
                    if(data.recruitmentSettings){
                        vm.recruitmentSettings = data.recruitmentSettings;
                        vm.recruitmentSettings.amount = 0;
                        vm.recruitmentSettings.commissionTotal = 0;
                    }
                }
            });
        }

        /**
         * @ngdoc method
         * @name calculateCommissionTotal
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Clculate the commission total
         */
        function calculateTotalCommission() {
            vm.recruitmentSettings.commissionTotal = settingContractService.getTotalCommission(vm.recruitmentSettings);
        }

        /**
         * @ngdoc method
         * @name simulateTotalCommission
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Test and relculate the commission total
         *
         */
        function simulateTotalCommission() {
            if (vm.recruitmentSettings.range1 > vm.recruitmentSettings.range2) {
                toastr.error('Seuils invalides.', 'Erreur !');
            } else {
                calculateTotalCommission();
            }
        }

        /**
         * @ngdoc method
         * @name putContractRecruitmentSettings
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Update a contract recruitment settings
         *
         * @param {object} form Contract form to be validated
         */
        function updateRecruitmentSettings(form) {
            if (form.$valid) {
                contractRepository.putContractRecruitmentSettings(vm.contract.id, vm.recruitmentSettings).then(function (data) {
                    $state.go('contract.legal', {'id': vm.contract.id});
                });
            }
        }

        /**
         * @ngdoc method
         * @name putContractServiceType
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Update a contract service type
         *
         * @param {number} contractId Id of contract
         * @param {number} servicetypeId Id of service type
         * @param {string} serviceType A service type
         */
        function updateServiceType(serviceType) {
            return serviceTypeRepository.putContractServiceType(contract.id, serviceType.id, serviceType).then(function () {
                activate().then(function () {
                    toastr.success('Modification de la prestation effectuée avec succès', 'Succès');
                });
            });
        }

        /**
         * @ngdoc method
         * @name postContractServicetype
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Create contract service type
         *
         * @param {number} contractId Id of contract
         */
        function createServiceType() {
            if (vm.newServiceType.label == null) {
                toastr.info('Vous devez entrer l\'intitulé de la prestation.', 'Attention !');
            } else {
                serviceTypeRepository.postContractServicetype(vm.contract.id, vm.newServiceType).then(function (data) {
                    activate().then(function(){
                        toastr.success('Nouvelle prestation créée avec succès', 'Succès');
                    });
                });
            }
        }

        /**
         * @ngdoc method
         * @name deleteContractServicetype
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Delete contract service type
         *
         * @param {number} serviceTypeId Id of service type
         */
        function removeServiceType(serviceType) {
            serviceTypeRepository.deleteContractServicetype(vm.contract.id, serviceType.id).then(function (data) {
                activate();
            });
        }

        /**
         * @ngdoc method
         * @name putMandataireMinDepot
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Update a mandataire min minDeposit
         *
         * @param {number} mandataireId Id of mandataire
         * @param {number} minDeposit A min diposit value
         */
        function updateMinDeposit () {
            settingContractService.updateMinDeposit(vm.contract).then(function(){
                activate().then(function(){
                toastr.success('Modification du dépôt minimum effectué avec succès.', 'Succès');
                });
            });
        }

    }

    return settingContractController;
});