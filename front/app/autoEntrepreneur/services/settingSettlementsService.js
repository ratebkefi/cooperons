/**
 * @ngdoc service
 * @name contract.services:contractRepository
 * @description
 * This file defines the contract service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function settingSettlementsService($q, contractRepository, serviceTypeRepository) {

        var service = {
            loadContractDetails: loadContractDetails,
            calculateTotalServiceAmount: calculateTotalServiceAmount,
            calculateCommisionning: calculateCommisionning,
            createSettlements: createSettlements
        };

        return service;

        /**
         * @ngdoc method
         * @name loadCollaboratorContracts
         * @methodOf party.services:partyService
         * @description
         * Load contracts of collaborator
         *
         * @param {object} collaborator
         * @param {object} filterContract Contracts type
         */
        function loadContractDetails(contractId) {
            var promises = [
                contractRepository.getContract(contractId),
                contractRepository.getContractMandataire(contractId),
                contractRepository.getContractRecruitment(contractId),
                serviceTypeRepository.getContractServiceTypes(contractId)
            ];

            return $q.all(promises).then(function (data) {
                return {
                    contract: data[0].contract,
                    mandataire: data[1].mandataire,
                    recruitment:  data[2].recruitment,
                    serviceTypes: data[3].serviceTypes
                };
            });
        }

        function calculateTotalServiceAmount(contract){
            contract.totalServiceAmount = 0;
            angular.forEach(contract.serviceTypes, function (serviceType) {
                if(!serviceType.quantity){
                    serviceType.quantity = 1;
                }
                serviceType.amount = serviceType.unitDefaultAmount * serviceType.quantity;
                contract.totalServiceAmount += serviceType.amount;
            });
        }

        /**
         * @ngdoc method
         * @name recalculateCommisionning
         * @methodOf autoEntrepreneur.controllers:settingSettlementsController
         * @description
         * Recalculate commisionning
         */
        function calculateCommisionning (contract, recruitmentSettings) {
            var cumulCA = Math.floor(contract.recruitment.cumulatedBillings);
            var cumulRemise = Math.floor(contract.recruitment.cumulatedRebate);
            var offset1 = recruitmentSettings.offset1;
            var offset2 = recruitmentSettings.offset2;
            var range1 = recruitmentSettings.range1;
            var range2 = recruitmentSettings.range2;
            var rate1 = recruitmentSettings.rate1 / 100;
            var rate2 = recruitmentSettings.rate2 / 100;
            var rateBeyond = recruitmentSettings.rateBeyond / 100;
            var cumulCACalc = Math.floor(cumulCA + contract.totalServiceAmount);
            var cumulRemiseCalc;

            if (cumulCACalc < range1) {
                cumulRemiseCalc = Math.floor(cumulCACalc * rate1);
            } else if (cumulCACalc < range2) {
                cumulRemiseCalc = Math.floor(cumulCACalc * rate2 + offset1);
            } else {
                cumulRemiseCalc = Math.floor(cumulCACalc * rateBeyond + offset2);
            }

            contract.cumulCACalc = cumulCACalc;
            contract.cumulRemiseCalc = cumulRemiseCalc;
            contract.remise = cumulRemiseCalc - cumulRemise;
            contract.netServiceAmount = contract.totalServiceAmount - (cumulRemiseCalc - cumulRemise);
        }

        function createSettlements(contract){
            var settlements = [];
            angular.forEach(contract.serviceTypes, function (serviceType) {
                settlements.push({
                    serviceType: serviceType.id,
                    unitAmount: serviceType.unitDefaultAmount,
                    quantity: serviceType.quantity
                });
            });

            return contractRepository.postContractSettlements(contract.id, settlements);
        }

    }

    return settingSettlementsService;
});
