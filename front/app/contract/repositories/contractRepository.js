/**
 * @ngdoc service
 * @name contract.services:contractRepository
 * @description
 * This file defines the contract service
 */
define([], function () {
    'use strict';
    contractRepository.$inject = ["$q", 'Restangular', '$filter'];

    /*ngInject*/
    function contractRepository($q, Restangular) {

        var repository = {
            getContract: getContract,
            getContractMandataire: getContractMandataire,
            getContractRecruitment: getContractRecruitment,
            deleteContract: deleteContract,
            getContractRecruitmentSettings: getContractRecruitmentSettings,
            putContractRecruitmentSettings: putContractRecruitmentSettings,
            patchContract: patchContract,
            postContractSettlements: postContractSettlements,
            postContractLegalDocument: postContractLegalDocument,
        };

        return repository;

        /**
         * @ngdoc method
         * @name getContract
         * @methodOf contract.services:contractRepository
         * @description
         * Get contract by id
         *
         * @param {number} id Id of contract
         *
         * @returns {object} Contract data
         */
        function getContract(id) {
            return Restangular.one('contracts', id).get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getContractMandataire
         * @methodOf contract.services:contractRepository
         * @description
         * Get contract mandataire
         *
         * @param {number} id Id of contract
         *
         * @returns {object} Contract mandataire data
         */
        function getContractMandataire(id) {
            return Restangular.one('contracts', id).one('mandataire').get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getContractRecruitment
         * @methodOf contract.services:contractRepository
         * @description
         * Get contract contract recruitement
         *
         * @param {number} id Id of contract
         *
         * @returns {object} Contract recruitement data
         */
        function getContractRecruitment(id) {
            return Restangular.one('contracts', id).one('recruitment').get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }

        /**
         * @ngdoc method
         * @name deleteContract
         * @methodOf contract.services:contractRepository
         * @description
         * Delete contract
         *
         * @param {number} contractId Id of contract
         *
         */
        function deleteContract(contractId) {
            return Restangular.one('contracts', contractId).remove().then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getContractRecruitmentSettings
         * @methodOf contract.services:contractRepository
         * @description
         * Get contract recruitement setting
         *
         * @param {number} id Id of contract
         *
         * @returns {object} Contract recruitement data
         */
        function getContractRecruitmentSettings(id) {
            return Restangular.one('contracts', id).all('recruitmentsettings').getList().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name putContractRecruitmentSettings
         * @methodOf contract.services:contractRepository
         * @description
         * Update contract recruitement setting
         *
         * @param {number} id Id of contract
         * @param {object} recruitmentSettings Recruitment settings data
         *
         * @returns {object} Contract recruitment settings data
         */
        function putContractRecruitmentSettings(id, recruitmentSettings) {
            return Restangular.one('contracts', id).one('recruitmentsettings').customPUT(recruitmentSettings).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );

        }

        /**
         * @ngdoc method
         * @name patchContract
         * @methodOf contract.services:contractRepository
         * @description
         * Update contract data
         *
         * @param {number} id Id of contract
         * @param {object} patch Data to patching
         *
         * @returns {object} Contract data
         */
        function patchContract(id, patch) {
            return Restangular.one('contracts', id).patch(patch).then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name postContractSettlements
         * @methodOf contract.services:contractRepository
         * @description
         * Add contract's settlements
         *
         * @param {number} id Id of contract
         * @param {object} settlements Settlements data
         *
         * @returns {object} Contract data
         */
        function postContractSettlements(id, settlements) {
            return Restangular.one('contracts', id).all('settlements').post(settlements).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name postContractLegalDocument
         * @methodOf prototype.services:contractRepository
         * @description  Create new legal document
         * @param id Id of contract
         * @param data Legal document data
         * @returns {object} Response
         */
        function postContractLegalDocument(id, data) {
            return Restangular.one('contracts', id).all('legaldocuments').customPOST(data).then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }


    }

    return contractRepository;
});
