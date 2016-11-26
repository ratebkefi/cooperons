/**
 * @ngdoc service
 * @name corporate.services:collaboratorService
 * @description
 * This file defines the collaboratorService
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function collaboratorService($q, partyRepository, collaboratorRepository, programRepository) {

        var service = {
            loadPartyCollaborators: loadPartyCollaborators,
            loadCollaboratorContracts: loadCollaboratorContracts,
            transferContract: transferContract,
            reInviteCollaborator: reInviteCollaborator,
            removeCollaborator: removeCollaborator,
            transferToCollaborators: transferToCollaborators
        };

        return service;

        /**
         * @ngdoc method
         * @name loadPartyCollaborators
         * @methodOf party.controllers:partyController
         * @description
         * Load all contracts of collaborator
         *
         * @param {object} party
         *
         * @returns {object} promise to recuperate list of collaborators
         */
        function loadPartyCollaborators(party) {
            return partyRepository.getPartyCollaborators(party.id).then(function (data) {
                var promises = [];
                angular.forEach(data.collaborators, function (collaborator) {
                    promises.push(loadCollaboratorContracts(collaborator));
                });

                return $q.all(promises).then(function (contracts) {
                    angular.forEach(data.collaborators, function (collaborator, index) {
                        collaborator.contracts = contracts[index];
                    });
                    return data.collaborators;
                });

            });
        }

        /**
         * @ngdoc method
         * @name loadCollaboratorContracts
         * @methodOf party.controllers:partyController
         * @description
         * Load all contracts of collaborator
         *
         * @param {object} collaborator
         *
         * @returns {object} promise to recuperate list of contracts
         */
        function loadCollaboratorContracts(collaborator) {
            return collaboratorRepository.getCollaboratorContracts(collaborator.id).then(function (data) {
                angular.forEach(data.contracts, function (contract) {
                    if (contract.program) {
                        contract.program.statusLabel = programRepository.getProgramStatusLabel(contract.program);
                    }
                });

                return data.contracts;
            });
        }

        /**
         * @ngdoc method
         * @name transferContract
         * @methodOf party.controllers:partyController
         * @description
         * Transfer contract to another collaborator
         *
         * @param {object} contract
         * @param {object} transferTo A collaborator to delegate contract
         *
         * @returns {object} promise
         */
        function transferContract(contract, transferTo) {
            var patch = [{op: 'transfer', path: '/contract', contractId: contract.id}];
            collaboratorRepository.patchCollaborator(transferTo.id, patch);

        }

        /**
         * @ngdoc method
         * @name transferContract
         * @methodOf party.controllers:partyController
         * @description
         * Load all programs of collaborator
         *
         * @param {object} collaborator
         *
         * @returns {object} promise
         */
        function reInviteCollaborator(collaborator) {
            var patch = [{op: 're-invite', 'path': '/'}];
            collaboratorRepository.patchCollaborator(collaborator.id, patch);

        }

        /**
         * @ngdoc method
         * @name removeCollaborator
         * @methodOf party.controllers:partyController
         * @description
         * Check if collaborator can leave party
         *
         * @param {object} collaborator
         * @param {object} party
         * @param {object} newAdministrator
         *
         * @returns {object} promise
         */
        function removeCollaborator(collaborator, newAdministrator) {
            var patch = [{op: 'leave', path: '/'}];

            if (newAdministrator) {
                patch[0].transferId = newAdministrator.id;
            }

            return collaboratorRepository.patchCollaborator(collaborator.id, patch)
        }

        /**
         * @ngdoc method
         * @name canLeaveParty
         * @methodOf party.controllers:partyController
         * @description
         * Check if collaborator can leave party
         *
         * @param {object} collaborator
         * @param {object} party
         * @param {object} newAdministrator
         *
         * @returns {object} promise
         */
        function transferToCollaborators(collaborators, collaborator) {
            var filteredCollaborators = [];
            angular.forEach(collaborators, function (collaboratorIterator) {
                if (collaboratorIterator.member && (!collaborator || collaboratorIterator.id !== collaborator.id)) {
                    filteredCollaborators.push(collaboratorIterator);
                }
            });
            return filteredCollaborators;
        }
    }

    return collaboratorService;
});