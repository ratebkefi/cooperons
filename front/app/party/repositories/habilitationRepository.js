/**
 * @ngdoc service
 * @name prototype.services:habilitationRepository
 * @description
 * This file defines the habilitationRepository service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function habilitationRepository(Restangular, $q) {

        var repository = {
            getHabilitations: getHabilitations,
            getHabilitation: getHabilitation,
            getHabilitationLegalDocuments: getHabilitationLegalDocuments,
            getHabilitationFile: getHabilitationFile,
            postHabilitation: postHabilitation,
        };

        return repository;


        /**
         * @ngdoc method
         * @name getHabilitations
         * @methodOf prototype.services:habilitationRepository
         * @description  Get list of habilitations
         * @returns {array} Data response
         */
        function getHabilitations() {
            return Restangular.all('habilitations').getList().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name getHabilitation
         * @methodOf prototype.services:habilitationRepository
         * @description  Get habilitation by id
         * @param id Id of habilitation
         * @returns {array} Data response
         */
        function getHabilitation(id) {
            return Restangular.one('habilitation', id).get().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name getHabilitationLegalDocuments
         * @methodOf prototype.services:habilitationRepository
         * @description  Get list of legal documents for habilitation
         * @param id Id of habilitation
         * @returns {array} Data response
         */
        function getHabilitationLegalDocuments(id) {
            return Restangular.one('habilitations', id).all('legaldocuments').getList().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name getHabilitationFile
         * @methodOf prototype.services:habilitationRepository
         * @description  Get PDF file for habilitation
         * @param id Id of habilitation
         * @returns {object} Response
         */
        function getHabilitationFile(id) {
            return Restangular.one('habilitations', id).one('file').withHttpConfig({responseType: 'blob'}).get().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name postHabilitation
         * @methodOf prototype.services:habilitationRepository
         * @description  Create new habilitation
         * @param data Legal document data
         * @returns {object} Response
         */
        function postHabilitation(data) {
            return Restangular.one('habilitations', habilitationId).customPOST(data).then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }

    }

    return habilitationRepository;
});