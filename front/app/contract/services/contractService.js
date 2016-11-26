/**
 * @ngdoc service
 * @name contract.services:contractRepository
 * @description
 * This file defines the contract service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function contractService($q, contractRepository, legalDocumentRepository) {

        var service = {
            loadContractDetails: loadContractDetails,
            saveLegalDocument: saveLegalDocument,
            shareLegalDocument: shareLegalDocument,
            terminateLegalDocument: terminateLegalDocument,
            signLegalDocument: signLegalDocument,
        };

        return service;

        function loadContractDetails(id) {
            // TODO : contractRepository.getContract return multiple resources !! => try to use promises
            //var promises =[
            //    contractRepository.getContract(id),
            //    legalDocumentRepository.getContractLegalDocuments(id),
            //    contractRepository.getContractParty(id),
            //    contractRepository.getContractLocal(id)
            //];
            return contractRepository.getContract(id).then(function (data) {
                angular.forEach(data.legalDocuments, function (legalDocument) {
                    // TODO use momentjs
                    legalDocument.agreeDate = new Date(legalDocument.agreeDate);
                    legalDocument.cancelDate = new Date(legalDocument.cancelDate);
                    legalDocument.publishDate = new Date(legalDocument.publishDate);
                    legalDocument.deadlineDate = new Date(legalDocument.deadlineDate);
                    legalDocument.effectiveDate = new Date();
                    legalDocument.effectiveDate.setDate(legalDocument.deadlineDate.getDate()
                        + legalDocument.cancellationPeriod);
                });

                return data;
            });
        }

        /**
         * @ngdoc method
         * @name saveLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Save updated/new legal document
         * @param legalDocument
         */
        function saveLegalDocument(contract, legalDocument) {
            legalDocument.agreeDate ? legalDocument.agreeDate.toISOString() : null;
            legalDocument.cancelDate ? legalDocument.cancelDate.toISOString() : null;
            legalDocument.publishDate ? legalDocument.publishDate.toISOString() : null;
            legalDocument.deadlineDate ? legalDocument.deadlineDate.toISOString() : null;
            legalDocument.effectiveDate ? legalDocument.effectiveDate.toISOString() : null;

            if (legalDocument.id) {
                return legalDocumentRepository.putContractLegalDocument(legalDocument.id, legalDocument);
            } else {
                return contractRepository.postContractLegalDocument(contract.id, legalDocument);
            }
        }

        /**
         * @ngdoc method
         * @name shareLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Share legal document
         */
        function shareLegalDocument(legalDocument) {
            var patch = [{op: 'share', path: '/'}];
            return legalDocumentRepository.patchLegalDocument(legalDocument.id, patch);
        }

        /**
         * @ngdoc method
         * @name terminateLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Terminate legal document contract
         */
        function terminateLegalDocument(legalDocument) {
            var patch = [{op: 'terminate', path: '/'}];
            return legalDocumentRepository.patchLegalDocument(legalDocument.id, patch);
        }

        /**
         * @ngdoc method
         * @name signLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Sign selected legal document
         */
        function signLegalDocument(legalDocument) {
            var patch = [{op: 'sign', path: '/'}];
            return legalDocumentRepository.patchLegalDocument(legalDocument.id, patch);
        }

    }

    return contractService;
});
