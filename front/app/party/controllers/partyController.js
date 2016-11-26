/**
 * @ngdoc controller
 * @name party.controllers:partyController
 * @description
 * This file defines the home party controller
 */
define([], function () {
    'use strict';

    /*@ngInject*/
    function PartyController(cooperons, common, partyService, memberService) {

        common.setMenuTab('party');

        var vm = this;

        vm.parties = [];
        vm.party = null;
        vm.collaborator = null;
        vm.selectedTabIndex = 0;

        vm.listLabelTab = cooperons.partyTabs;
        vm.partyTabs = [];

        //functions
        vm.selectParty = selectParty;

        ////////////////////////////

        activate();

        common.onEvent(cooperons.events.refreshParty, function (event) {
            activate(vm.party.id);
        });


        /**
         * @ngdoc method
         * @name activate
         * @methodOf party.controllers:partyController
         * @description
         * Initialize data
         */
        function activate(partyId) {
            common.broadcast(cooperons.events.refreshInfoMandataire);
            // refresh parties
            return memberService.loadMemberDetails(false, false, false, true).then(function () {
                vm.parties = angular.copy(common.getMemberData().parties);
                vm.party = common.getObjectById(vm.parties, partyId);
                if (!vm.party) {
                    vm.party = vm.parties[0] || null;
                }
                return vm.selectParty(vm.party);
            });
        }

        /**
         * @ngdoc method
         * @name selectParty
         * @methodOf party.controllers:partyController
         * @description
         * Select a new party
         *
         */
        function selectParty() {
            if (vm.party) {
                return partyService.loadPartyDetails(vm.party).then(function (data) {
                    vm.party.mandataire = data.mandataire;
                    vm.collaborator = data.collaborator;
                    vm.partyTabs = partyService.resolvePartyTabs(vm.collaborator, vm.party);
                    common.broadcast(cooperons.events.partyChanged);
                });
            }
        }

    }

    return PartyController;
});