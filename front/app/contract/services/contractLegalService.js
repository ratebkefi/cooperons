/**
 * @ngdoc service
 * @name contract.services:contractRepository
 * @description
 * This file defines the contract service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function contractLegalService($q, $sce, common, contractRepository) {

        var service = {
            loadLegalDetails: loadLegalDetails,
            publishContract: publishContract,
            agreeContract: agreeContract,
            agreeCgvContract: agreeCgvContract
        };

        return service;

        /**
         * @ngdoc method
         * @name loadCollaboratorContracts
         * @methodOf party.services:partyService
         * @description
         * Load contracts of collaborator
         *
         * @param {object} collaborator
         * @param {object} filterContract Contracts type
         */
        function loadLegalDetails(contractId) {
            var promises = [
                contractRepository.getContract(contractId),
                // TODO : getContractAuthorizedParty & getHtmlContract removed
                //contractRepository.getContractAuthorizedParty(contractId),
                //contractRepository.getHtmlContract(contractId)
            ];

            return $q.all(promises).then(function (data) {
                var member = common.getMemberData().member;
                var contract = data[0].contract;
                //var authorizedParty = data[1].authorizedParty;
                //var contractHtml = $sce.trustAsHtml(data[2]);
                var corporate = contract.client.corporate;

                var displayPublishButton = false;
                var displayAgreeButton = false;
                var displayWarningAccord = false;
                var displayAgreeCgvButton = false;

                if (contract.clientMember.id == member.id && corporate && corporate.accordSigned && !corporate.cgvCoopAEAgreed) {
                    displayAgreeCgvButton = true;
                } else {
                    displayPublishButton = contract.status === 'waitForPublish' && contract.owner.id === authorizedParty.id;
                    displayAgreeButton = contract.status === 'waitForAgree' && contract.client.id === authorizedParty.id;
                    if (corporate && !corporate.accordSigned && (contract.client.id == authorizedParty.id)) {
                        displayWarningAccord = true;
                        displayAgreeButton = false;
                    }
                }

                return {
                    contract: contract,
                    authorizedParty: authorizedParty,
                    contractHtml: contractHtml,
                    displayPublishButton: displayPublishButton,
                    displayAgreeButton: displayAgreeButton,
                    displayWarningAccord: displayWarningAccord,
                    displayAgreeCgvButton: displayAgreeCgvButton
                };
            });
        }

        /**
         * @ngdoc method
         * @name publishContract
         * @methodOf contract.controllers:legalController
         * @description
         * Resend invitation for a contract
         *
         * @param {object}  contract A contract object
         */
        function publishContract(contract) {
            var patch = [{"op": "publish", "path": "/"}];
            return contractRepository.patchContract(contract.id, patch);
        }

        /**
         * @ngdoc method
         * @name agreeContract
         * @methodOf contract.controllers:legalController
         * @description
         * Agree a contract
         *
         * @param {object}  contract A contract object
         */
        function agreeContract(contract) {
            var patch = [{"op": "agree", "path": "/"}];
            contractRepository.patchContract(contract.id, patch);
        }

        /**
         * @ngdoc method
         * @name agreeCgvContract
         * @methodOf contract.controllers:legalController
         * @description
         * Agree a cgv contract
         *
         * @param {object}  contract A contract object
         */
        function agreeCgvContract(contract) {
            var patch = [{"op": "agreeCgv", "path": "/"}];
            contractRepository.patchContract(contract.id, patch);
        }
    }

    return contractLegalService;
});
