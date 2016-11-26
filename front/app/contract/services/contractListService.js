/**
 * @ngdoc service
 * @name contract.services:contractRepository
 * @description
 * This file defines the contract service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function contractListService($q, config, collaboratorRepository, memberRepository, contractRepository, contractService) {

        var service = {
            loadCollaboratorContracts: loadCollaboratorContracts,
            reinviteContract: reinviteContract,
            createContractRecruitment: createContractRecruitment
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
        function loadCollaboratorContracts(collaborator, filterContract) {
            var promises = [
                collaboratorRepository.getCollaboratorContracts(collaborator.id, {filterContract: filterContract}),
                memberRepository.getMemberAutoEntrepreneur(),
                memberRepository.getMemberParticipates({toAeProgram: config.idProgramAE}),
            ];

            return $q.all(promises).then(function (data) {
                return {
                    contracts: data[0].contracts,
                    autoEntrepreneur: data[1].autoEntrepreneur,
                    canCreateContractsAE: data[2].participates.length > 0
                };
            });
        }

        /**
         * @ngdoc method
         * @name reinviteContract
         * @methodOf contract.controllers:contractListController
         * @description
         * Resend invitation for a contract
         *
         * @param {object} contract
         *
         */
        function reinviteContract(contract) {
            var patch = [{op: 'reinvite', path: '/invitation'}];
            return contractRepository.patchContract(contract.id, patch);
        }

        /**
         * @ngdoc method
         * @name createContractRecruitment
         * @methodOf contract.controllers:contractListController
         * @description
         * Create contract recruitment
         *
         * @param {number} recruitmentContract
         * @param {number} customerContract
         *
         */
        function createContractRecruitment(recruitmentContract, customerContract) {
            var patch = [{
                op: 'createRecruitment',
                path: '/recruitment',
                recruitmentContractId: recruitmentContract.id,
                customerContractId: customerContract.id
            }];
            return collaboratorRepository.patchContract(contract.id, patch);
        }
    }

    return contractListService;
});
