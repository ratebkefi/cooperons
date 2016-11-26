/**
 * @ngdoc service
 * @name corporate.services:collaboratorRepository
 * @description
 * This file defines the collaboratorRepository
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function collaboratorRepository($q, Restangular) {

        var repository = {
            getCollaboratorContracts: getCollaboratorContracts,
            patchCollaborator: patchCollaborator,
            postCollaboratorContract: postCollaboratorContract,
            postContractInvitation: postContractInvitation
        };

        return repository;

        /**
         * @ngdoc method
         * @name getCollaboratorContracts
         * @methodOf corporate.services:collaboratorRepository
         * @description
         * Get all collaborator contracts
         *
         * @param {number} collaboratorId Id of collaborator
         * @param {object} filter Filter key
         *
         * @returns {object} Collaborator contracts data
         */
        function getCollaboratorContracts(collaboratorId, filter) {
            return Restangular.one('collaborators', collaboratorId).all('contracts').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name patchCollaborator
         * @methodOf corporate.services:collaboratorRepository
         * @description
         * Partial updating of collaborator
         *
         * @param {number} collaboratorId Id of collaborator
         * @param {object} patch Data to patching
         *
         * @returns {object} Collaborator data
         */
        function patchCollaborator(collaboratorId, patch) {
            return Restangular.one('collaborators', collaboratorId).patch(patch).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name postCollaboratorContract
         * @methodOf corporate.services:collaboratorRepository
         * @description
         * Create collaborator contract
         *
         * @param {number} collaboratorId Id of collaborator
         * @param {object} data Collaborator contract data
         *
         * @returns {object} Collaborator contract data
         */
        function postCollaboratorContract(collaboratorId, data) {
            return Restangular.one('collaborators', collaboratorId).all('contracts').post(data).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name postCollaboratorContractInvitation
         * @methodOf corporate.services:collaboratorRepository
         * @description
         * Invite collaborator
         *
         * @param {number} collaboratorId Id of collaborator
         * @param {object} data Invitation data
         *
         * @returns {object} Collaborator contract data
         */
        function postContractInvitation(collaboratorId, data) {
            return Restangular.one('collaborators', collaboratorId).all('contractinvitations').post(data).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

    }

    return collaboratorRepository;
});