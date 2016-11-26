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

/**
 *
 * @returns {object} partyRepository
 */
define([], function () {
    'use strict';

    /*@ngInject*/
    function partyRepository($q, Restangular) {

        var repository = {
            getParties: getParties,
            getParty: getParty,
            getPartyCollaborators: getPartyCollaborators,
            getPartyMandataire: getPartyMandataire
        };

        return repository;

        /**
         * Get Parties list
         *
         * @param {string} filter
         * @returns object Parties
         */
        function getParties(filter) {
            return Restangular.all('parties').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                });
        }

        /**
         * Get Party
         *
         * @param {number} id
         * @returns object Party
         */
        function getParty(id) {
            return Restangular.one('parties', id).get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }

        /**
         * Get all party collaborators
         *
         * @param {number} id
         * @returns {array} data response
         */
        function getPartyCollaborators(id) {
            return Restangular.one('parties', id).all('collaborators').getList().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }


        /**
         * Load party mandataire
         *
         * @param {number} id
         * @returns object
         */
        function getPartyMandataire(id) {
            return Restangular.one('parties', id).one('mandataire').get().then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }


    }

    return partyRepository;
});