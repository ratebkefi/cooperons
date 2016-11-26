/**
 * @ngdoc service
 * @name autoEntrepreneur.services:autoEntrepreneurRepository
 * @description
 * This file defines the autoEntrepreneur service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function autoEntrepreneurRepository($q, Restangular) {

        var repository = {
            postAutoEntrepreneur: postAutoEntrepreneur,
            patchAutoEntrepreneur: patchAutoEntrepreneur,
            getAutoEntrepreneurParty: getAutoEntrepreneurParty,
            // TODO postAutoEntrepreneurContract not used
            postAutoEntrepreneurContract: postAutoEntrepreneurContract
        };

        return repository;

        /**
         * @ngdoc method
         * @name postAutoEntrepreneur
         * @methodOf autoEntrepreneur.services:autoEntrepreneurRepository
         * @description
         * Create autoEntrepreneur account
         *
         * @param {object} autoEntrepreneur An autoEntrepreneur data
         *
         * @returns {object} AutoEntrepreneur data
         */
        function postAutoEntrepreneur(autoEntrepreneur) {
            return Restangular.all('autoentrepreneurs').post(autoEntrepreneur).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }

        /**
         * Update AutoEntrepreneur data
         *
         * @param {number} id
         * @param {object} patch
         * @returns {object} response
         */
        function patchAutoEntrepreneur(id, patch) {
            return Restangular.one('autoentrepreneurs', id).patch(patch).then(
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
         * @name getAutoEntrepreneurParty
         * @methodOf autoEntrepreneur.services:autoEntrepreneurRepository
         * @description
         * Retrieve autoEntrepreneur Party
         *
         * @param {number} id Id of autoEntrepreneur
         *
         * @returns {object} AutoEntrepreneur party
         */
        function getAutoEntrepreneurParty(id) {
            return Restangular.one('autoentrepreneurs', id).one('party').get().then(
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
         * @name postAutoEntrepreneurContract
         * @methodOf autoEntrepreneur.services:autoEntrepreneurRepository
         * @description
         * Create autoEntrepreneur contract
         *
         * @param {object} contract A contract data
         *
         * @returns {object} AutoEntrepreneur contract
         */
        function postAutoEntrepreneurContract(contract) {
            return Restangular.one('autoentrepreneurs').all('contracts').post(contract).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }
    }

    return autoEntrepreneurRepository;
});
