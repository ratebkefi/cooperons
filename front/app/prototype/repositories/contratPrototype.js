/**
 * @ngdoc service
 * @name prototype.services:contratPrototype
 * @description
 * This file defines the contratPrototype service
 */
define([], function () {
    "use strict";

    contratPrototype.$inject = ["Restangular", "$q", "prototypeModel"];

    /**
     * @ngdoc method
     * @name contratPrototype
     * @description
     * Return contractService
     *
     * @param Restangular A service to handle Rest API Restful Resources properly and easily
     * @param $q A service used to throw exception when the request failed
     * @param prototypeModel A service to manipulate fictitious resources
     * @returns {object} contratPrototype
     */
    function contratPrototype(Restangular, $q, prototypeModel) {
        return {
            // Restangular will be used instead of prototypeModel when handling real resources

            /**
             * @ngdoc method
             * @name getContrats
             * @methodOf prototype.services:contratPrototype
             * @description  Get list of contracts
             * @returns {array} Data response
             */
            getContrats: function () {
                //return Restangular.all('contrats').getList()
                return prototypeModel.getContrats()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name getContrat
             * @methodOf prototype.services:contratPrototype
             * @description  Get contract by id
             * @param contratId Id of contract
             * @returns {array} Data response
             */
            getContrat: function (contratId) {
                //return Restangular.one('contrats', id).get()
                return prototypeModel.getContrat(contratId)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name getContratLegalDocuments
             * @methodOf prototype.services:contratPrototype
             * @description  Get list of legal documents for contract
             * @param contratId Id of contract
             * @returns {array} Data response
             */
            getContratLegalDocuments: function (contratId) {
                //return Restangular.one('contrats', contratId).all('legaldocuments').getList()
                return prototypeModel.getContratLegalDocuments(contratId)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name getContratLegalDocumentFile
             * @methodOf prototype.services:contratPrototype
             * @description  Remove legal document
             * @param contratId Id of contract
             * @param legalDocumentId Id of legal document
             * @returns {object} Response
             */
            deleteContratLegalDocument: function (contratId, legalDocumentId) {
                //return Restangular.one('contrats', contratId).one('legaldocuments', legalDocumentId).remove()
                return prototypeModel.deleteContratLegalDocument(contratId, legalDocumentId)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name postContratLegalDocument
             * @methodOf prototype.services:contratPrototype
             * @description  Create new legal document
             * @param contratId Id of contract
             * @param data Legal document data
             * @returns {object} Response
             */
            postContratLegalDocument: function (contratId, data) {
                //return Restangular.one('contrats', contratId).all('legaldocuments').customPOST(data)
                return prototypeModel.postContratLegalDocument(contratId, data)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name putContratLegalDocument
             * @methodOf prototype.services:contratPrototype
             * @description  Update legal document
             * @param contratId Id of contract
             * @param legalDocumentId Legal document id
             * @param data Legal document data
             * @returns {object} Response
             */
            putContratLegalDocument: function (contratId, legalDocumentId, data) {
                //return Restangular.one('contrats', contratId).one('legaldocuments', legalDocumentId).customPUT(data)
                return prototypeModel.putContratLegalDocument(contratId, legalDocumentId, data)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name patchContratLegalDocument
             * @methodOf prototype.services:contratPrototype
             * @description  Partial modification for legal document «sign, terminate, share»
             * @param contratId Id of contract
             * @param legalDocumentId Legal document id
             * @param patch Patch describing the changes requested
             * @returns {object} Response
             */
            patchContratLegalDocument: function (contratId, legalDocumentId, patch) {
                //return Restangular.one('contrats', contratId).one('legaldocuments', legalDocumentId).patch(patch)
                return prototypeModel.putContratLegalDocument(contratId, legalDocumentId, patch)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name getlegalDocumentFile
             * @methodOf prototype.services:contratPrototype
             * @description  Get PDF file for legal document
             * @param contratId Id of contract
             * @param legalDocumentId Id of legal document
             * @returns {object} Response
             */
            getLegalDocumentFile: function (contratId, legalDocumentId) {
                //return Restangular.one('contrats', contratId).one('legaldocuments', legalDocumentId).one('file).withHttpConfig({responseType: 'blob'}).get()
                return prototypeModel.getLegalDocumentFile(contratId, legalDocumentId)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return contratPrototype;
});