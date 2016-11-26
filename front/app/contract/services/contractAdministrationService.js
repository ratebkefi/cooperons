/**
 * @ngdoc service
 * @name contract.services:contractRepository
 * @description
 * This file defines the contract service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function contractAdministrationService($q, contractRepository) {

        var service = {
            loadContractDetails: loadContractDetails
        };

        return service;

        /**
         * @ngdoc method
         * @name loadContractDetails
         * @methodOf party.services:partyService
         * @description
         * Load contracts details
         *
         * @param {number} contractId Contract id
         */
        function loadContractDetails(contractId) {
            var promises = [
                contractRepository.getContract(contractId),
                contractRepository.getContractMandataire(contractId),
            ];

            return $q.all(promises).then(function (data) {
                return {
                    contract: data[0].contract,
                    mandataire: data[1].mandataire,
                };
            });
        }

    }

    return contractAdministrationService;
});
