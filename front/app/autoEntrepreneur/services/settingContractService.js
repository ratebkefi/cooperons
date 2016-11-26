/**
 * @ngdoc service
 * @name corporate.services:settingContarctService
 * @description
 * This file defines the collaboratorRepository
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function settingContarctService($q, cooperons, contractRepository, mandataireRepository) {

        var service = {
            loadContractDetails: loadContractDetails,
            updateMinDeposit: updateMinDeposit,
            getTotalCommission: getTotalCommission
        };

        return service;

        function loadContractDetails(contractId) {
            return contractRepository.getContract(contractId).then(function (data) {
                var result = {
                    contract: data.contract,
                    filterContract: data.filterContract
                };

                if (result.contract.status === 'waitForPublish' || result.contract.status === 'waitForAgree') {
                    if (filterContract === cooperons.contractTypes.fournisseurs) {
                        result.ready = true;
                        return contractRepository.getContractRecruitmentSettings(contractId).then(function () {
                            result.recruitmentSettings = data.recruitmentSettings;
                            return result;
                        });
                    } else if (vm.filterContract === cooperons.contractTypes.clients) {
                        result.ready = true;
                        var promises = [
                            contractRepository.getContractMandataire(contractId),
                            contractRepository.getContractServiceTypes(contractId)
                        ];
                        return $q.all(promises).then(function (data) {
                            contract.mandataire = data[0].mandataire;
                            result.serviceTypes = data[1].serviceTypes;
                            return result;
                        });
                    } else {
                        result.ready = false;
                        return result;
                    }
                } else {
                    result.ready = false;
                    return result;
                }
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
        function updateMinDeposit(contract) {
            var patch = [{op: 'replace', path: '/mindeposit', value: contract.minDeposit}];
            return mandataireRepository.patchMandataire(contract.mandataire.id, patch)
        }

        /**
         * @ngdoc method
         * @name calculateCommissionTotal
         * @methodOf autoEntrepreneur.controllers:settingContractController
         * @description
         * Clculate the commission total
         */
        function getTotalCommission(recruitmentSettings) {
            var totalCommission = 0;
            var rate1 = Number(recruitmentSettings.rate1);
            var rate2 = Number(recruitmentSettings.rate2);
            var range1 = Number(recruitmentSettings.range1);
            var range2 = Number(recruitmentSettings.range2);
            var rateBeyond = Number(recruitmentSettings.rateBeyond);
            var amount = Number(recruitmentSettings.amount);
            if (amount <= range1) {
                totalCommission = Math.floor(amount * rate1 / 100);
            } else if (amount <= range2) {
                totalCommission = Math.floor(((amount - range1) * rate2 + range1 * rate1) / 100);
            } else {
                totalCommission = Math.floor(((amount - range2) * rateBeyond + (range2 - range1) * rate2 + range1 * rate1) / 100);
            }
            return totalCommission;
        }
    }

    return settingContarctService;
});