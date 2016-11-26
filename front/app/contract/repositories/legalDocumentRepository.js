/**
 * @ngdoc service
 * @name contract.repositories:legalDocumentRepository
 * @description
 * This file defines the contractRepository service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function legalDocumentRepository(Restangular, $q) {

        var repository = {
            deleteLegalDocument: deleteLegalDocument,
            putLegalDocument: putLegalDocument,
            patchLegalDocument: patchLegalDocument,
            getLegalDocumentFile: getLegalDocumentFile
        };

        return repository;

        /**
         * @ngdoc method
         * @name getContractLegalDocumentFile
         * @methodOf contract.repositories:legalDocumentRepository
         * @description  Remove legal document
         * @param id Id of legal document
         * @returns {object} Response
         */
        function deleteLegalDocument(id) {
            return Restangular.one('legaldocuments', id).remove()
                .then(function (response) {
                    return response;
                }, function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name putContractLegalDocument
         * @methodOf contract.repositories:legalDocumentRepository
         * @description  Update legal document
         * @param legalDocumentId Legal document id
         * @param data Legal document data
         * @returns {object} Response
         */
        function putLegalDocument(id, data) {
            return Restangular.one('legaldocuments', id).customPUT(data)
                .then(function (response) {
                    return response;
                }, function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name patchLegalDocument
         * @methodOf contract.repositories:legalDocumentRepository
         * @description  Partial modification for legal document «sign, terminate, share»
         * @param id Legal document id
         * @param patch Patch describing the changes requested
         * @returns {object} Response
         */
        function patchLegalDocument(id, patch) {
            return Restangular.one('legaldocuments', id).patch(patch)
                .then(function (response) {
                    return response;
                }, function (fallback) {
                    return $q.reject(fallback);
                });
        }

        /**
         * @ngdoc method
         * @name getlegalDocumentFile
         * @methodOf contract.repositories:legalDocumentRepository
         * @description  Get PDF file for legal document
         * @param id Id of legal document
         * @returns {object} Response
         */
        function getLegalDocumentFile(id) {
            return Restangular.one('legaldocuments', id).one('file').withHttpConfig({responseType: 'blob'}).get()
                .then(function (response) {
                    return response.data;
                }, function (fallback) {
                    return $q.reject(fallback);
                });
        }
    }

    return legalDocumentRepository;
});