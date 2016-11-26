/**
 * @ngdoc service
 * @name corporate.services:corporateRepository
 * @description
 * This file defines the corporateRepository
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function corporateRepository($q, Restangular) {

        var repository = {
            getCorporates: getCorporates,
            getCorporate: getCorporate,
            getCorporateAdministrator: getCorporateAdministrator,
            getCorporateDelegate: getCorporateDelegate,
            getCorporateColleges: getCorporateColleges,
            getCorporateParty: getCorporateParty,
            getCorporateYearlyAttestations: getCorporateYearlyAttestations,
            newCorporate: newCorporate,
            postCorporate: postCorporate,
            putCorporate: putCorporate,
            patchCorporate: patchCorporate,
            downloadCorporateAccord: downloadCorporateAccord,
        };

        return repository;

        /**
         * @ngdoc method
         * @name getCorporates
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get Corporates list
         *
         * @param {string} filter Filter Key
         *
         * @returns {object} Corporates data
         */
        function getCorporates(filter) {
            return Restangular.all('corporates').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name getCorporate
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get Corporate by siren
         *
         * @param {string} siren Siren of corporate
         *
         * @returns {object} Corporate data
         */
        function getCorporate(siren) {
            return Restangular.one('corporates', siren).get().then(function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name getCorporateAdministrator
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get corporate administrator
         *
         * @param {string} id Id of corporate
         *
         * @returns {object} Corporate administrator data
         */
        // TODO: Note = added to manage corporate independently of party
        function getCorporateAdministrator(id) {
            return Restangular.one('corporates', id).one('administrator').get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name getCorporateDelegate
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get corporate delegate
         *
         * @param {string} id Id of corporate
         *
         * @returns {object} Corporate delegate data
         */
        function getCorporateDelegate(id) {
            return Restangular.one('corporates', id).one('delegate').get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name getCorporateColleges
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get all corporate colleges
         *
         * @param {string} siren Siren of corporate
         *
         * @returns {object} Corporate colleges data
         */
        function getCorporateColleges(siren) {
            return Restangular.one('corporates', siren).all('colleges').getList().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name getCorporateParty
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get corporate party
         *
         * @param {number} siren Siren of corporate
         *
         * @returns {object} Corporate party data
         */
        function getCorporateParty(siren) {
            return Restangular.one('corporates', siren).one('party').get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name getCorporateYearlyAttestations
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get yearly attestations related to corporate
         *
         * @param {number} id Id of corporate
         *
         * @returns {object} Corporate party data
         */
        function getCorporateYearlyAttestations(id) {
            return Restangular.one('corporates', id).all('yearlyattestations').getList().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name settingCorporate
         * @methodOf corporate.services:corporateRepository
         * @description
         * Get Corporations list
         *
         * @returns {object} Corporates data
         */
        function newCorporate() {
            return Restangular.one('corporates').one('new').getList().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name postCorporate
         * @methodOf corporate.services:corporateRepository
         * @description
         * Create corporate
         *
         * @param {object} corporate Corporate data
         *
         * @returns {object} Corporate data
         */
        function postCorporate(corporate) {
            return Restangular.one('corporates').customPOST(corporate).then(
                function (response) {
                    return response;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name putCorporate
         * @methodOf corporate.services:corporateRepository
         * @description
         * Update corporate
         *
         * @param {object} corporate Corporate data
         *
         * @returns {object} Corporate data
         */
        function putCorporate(corporate) {
            return Restangular.one('corporates').customPUT(corporate, corporate.id).then(
                function (response) {
                    return response;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * @ngdoc method
         * @name patchCorporate
         * @methodOf corporate.services:corporateRepository
         * @description
         * Update corporate
         *
         * @param {number} id Id of corporate
         * @param {object} patch data to patching
         *
         * @returns {object} Corporate data
         */
        function patchCorporate(id, patch) {
            return Restangular.one('corporates', id).patch(patch).then(function (response) {
                return response;
            }, function (fallback) {
                return $q.reject(fallback);
            });
        }

        /**
         * @ngdoc method
         * @name downloadCorporateAccord
         * @methodOf corporate.services:corporateRepository
         * @description
         * Download corporate accord in pdf format
         *
         * @param {number} id Id of corporate
         *
         * @returns {object} Corporate accord data
         */
        function downloadCorporateAccord(id) {
            return Restangular.one('corporates', id).one('accord').withHttpConfig({responseType: 'blob'}).get().then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

    }

    return corporateRepository;
});