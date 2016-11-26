/**
 * @ngdoc service
 * @name corporate.repositories:attestationRepository
 * @description
 * This file defines the attestationRepository
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function attestationRepository($q, Restangular) {

        var baseAttestations = Restangular.all('attestations');

        var repository = {
            getAttestations: getAttestations,
            downloadAttestation: downloadAttestation,
            patchAttestations: patchAttestations
        };

        return repository;

        /**
         * @ngdoc method
         * @name getAttestations
         * @methodOf corporate.repositories:attestationRepository
         * @description
         * Filter attestations by year and corporate
         *
         * @param {object} filter Filter attestations
         *
         * @returns {object} Attestations data
         */
        function getAttestations(filter) {
            return baseAttestations.getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name downloadAttestation
         * @methodOf corporate.repositories:attestationRepository
         * @description
         * Download attestation in pdf format
         *
         * @param {object} filter Filter attestations
         *
         * @returns {object} Attestations data
         */
        function downloadAttestation(reference) {
            return Restangular.one('attestations', reference).one('download').withHttpConfig({responseType: 'blob'}).get({_format: 'pdf'}).then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name patchAttestations
         * @methodOf corporate.repositories:attestationRepository
         * @description
         * Partial updating payments
         *
         * @param {object} patch Data to patching
         *
         * @returns {object} Attestations data
         */
        function patchAttestations(patch) {
            return baseAttestations.patch(patch).then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }
    }

    return attestationRepository;
});