/**
 * @ngdoc controller
 * @name mandataire.controllers:partyAdministrationController
 * @description
 * This file defines the payment's administration controller
 */
define([], function () {
    'use strict';

    partyAdministrationController.$inject = ['$state', '$rootScope', '$scope', 'partyRepository'];

    function partyAdministrationController($state, $rootScope, $scope, partyRepository) {

        /**
         * Init global parameters
         */
        var partyId = $state.params.id;

        /**
         * @ngdoc method
         * @name loadParty
         * @methodOf mandataire.controllers:partyAdministrationController
         * @description
         * Get party
         *
         * @param {number} partyId Id of party
         */
        $scope.loadParty = function (partyId) {
            partyRepository.getParty(partyId).then(function (data) {
                $scope.party = data.party;
                $rootScope.menuTab = $scope.party.type;
            });
        };

        /**
         * @ngdoc method
         * @name loadPartyMandataire
         * @methodOf mandataire.controllers:partyAdministrationController
         * @description
         * Get party mandataire
         *
         * @param {number} partyId Id of party
         */
        $scope.loadPartyMandataire = function (partyId) {
            partyRepository.getPartyMandataire(partyId).then(function (data) {
                $scope.mandataire = data.mandataire;
            });
        };

        /**
         * Load data
         */
        $scope.loadParty(partyId);
        $scope.loadPartyMandataire(partyId);
    }

    return partyAdministrationController;
});