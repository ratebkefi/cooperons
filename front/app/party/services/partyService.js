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
    function partyService($q, common, partyRepository, memberRepository) {

        var service = {
            loadPartyDetails: loadPartyDetails,
            resolvePartyTabs: resolvePartyTabs,
            resolveSelectedPartyTab: resolveSelectedPartyTab
        };

        return service;

        /**
         * @ngdoc method
         * @name loadPartyDetails
         * @methodOf party.services:partyService
         * @description
         * Load details for member & party
         *
         * @param {number} partyId
         * @returns {object} promises resolving party details
         */
        function loadPartyDetails(party) {
            var isAdministrator = party.administrator && party.administrator.member.id === common.getMemberData().member.id;
            var promises = [memberRepository.getMemberCollaborators({party_id: party.id})];
            if (isAdministrator) {
                promises.push(partyRepository.getPartyMandataire(party.id));
            }

            return $q.all(promises).then(function (data) {
                return {
                    collaborator: data[0].collaborators[0],
                    mandataire: data[1] ? data[1].mandataire : null
                }
            });
        }

        /**
         * @ngdoc method
         * @name resolvePartyTabs
         * @methodOf party.services:partyService
         * @description
         * Get list of tabs for party
         *
         * @param {object} collaborator
         * @param {object} party
         */
        function resolvePartyTabs(collaborator, party) {
            var tabs = [];

            if (collaborator && collaborator.isAdministrator && party.status.Contracts.cgu) {
                tabs.push('users');
                tabs.push('college');
            }
            tabs.push('prestataires');
            if (collaborator && party.status.Contracts.cpContracts) {
                tabs.push('clients')
            }
            return tabs;
        }

        /**
         * @ngdoc method
         * @name resolveSelectedPartyTab
         * @methodOf party.services:partyService
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

    return partyService;
});