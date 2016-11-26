/**
 * This file defines the party service
 *
 * @category Service
 * @package Party
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 0.0.1
 * @since File available since Release 0.0.1
 */

define([], function () {
    'use strict';

    /*@ngInject*/
    function habilitationService($q, filterFilter, habilitationRepository, habilitationPrototype) {

        var service = {
            loadHabilitations: loadHabilitations,
            loadHabilitationDetails: loadHabilitationDetails,
        };

        return service;


        /**
         * @ngdoc method
         * @name loadPartyDetails
         * @methodOf party.services:habilitationService
         * @description
         * Load details for member & party
         *
         * @param {number} partyId
         * @returns {object} promises resolving party details
         */
        function loadHabilitations() {
            //return habilitationRepository.getHabilitations().then(function (data) {
            return habilitationPrototype.getHabilitations().then(function (data) {
                var habilitations = data.habilitations;
                var activeHabilitation = filterFilter(data.habilitations, {active: true})[0];
                return loadHabilitationDetails(activeHabilitation).then(function (data) {
                    return {
                        habilitations: habilitations,
                        activeHabilitation: activeHabilitation,
                        legalDocuments: data.legalDocuments,
                        file: data.file
                    };
                });
            });
        }

        /**
         * @ngdoc method
         * @name resolvePartyTabs
         * @methodOf party.services:habilitationService
         * @description
         * Get list of tabs for party
         *
         * @param {object} habilitation
         */
        function loadHabilitationDetails(habilitation) {
            var promises = [
                //habilitationRepository.getHabilitationLegalDocuments(habilitation.id),
                //habilitationRepository.getHabilitationFile(habilitation.id)
                habilitationPrototype.getHabilitationLegalDocuments(habilitation.id),
                habilitationPrototype.getHabilitationFile(habilitation.id)
            ];

            return $q.all(promises).then(function (data) {
                return {
                    legalDocuments: data[0].legalDocuments,
                    file: data[1].file
                };

            });
        }

        /**
         * @ngdoc method
         * @name resolveSelectedPartyTab
         * @methodOf party.services:habilitationService
         * @description
         * Get selected party tab
         *
         * @param {object} collaborator
         * @param {object} party
         */
        function resolveSelectedPartyTab(collaborator, party) {
            var selectedPartyTab;
            if (collaborator && collaborator.isAdministrator && party.status.Contracts.cgu) {
                selectedPartyTab = 'users';
            } else if (!collaborator || party.corporate && party.corporate.colleges && party.corporate.colleges.length > 0) {
                selectedPartyTab = 'college';
            } else if (collaborator) {
                selectedPartyTab = 'prestataires';
            }

            return selectedPartyTab;
        }

    }

    return habilitationService;
});