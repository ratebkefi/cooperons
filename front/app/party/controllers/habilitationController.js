/**
 * @ngdoc controller
 * @name party.controllers:HabilitationController
 * @description
 * This file defines the habilitation controller for party module
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function HabilitationController(common, habilitationRepository, habilitationService, habilitationPrototype) {

        common.setMenuTab('party');

        var vm = this;

        vm.newHabilitation = {file: {}};

        vm.selectHabilitation = selectHabilitation;
        vm.addHabilitation = addHabilitation;

        activate();

        /**
         * @ngdoc method
         * @name loadHabilitations
         * @methodOf party.controllers:HabilitationController
         * @description Retrieve list of habilitations
         */
        function activate() {
            return habilitationService.loadHabilitations().then(function (data) {
                vm.habilitations = data.habilitations;
                vm.selectedHabilitation = data.activeHabilitation;
                if (vm.selectedHabilitation) {
                    vm.selectedHabilitation.legalDocuments = data.legalDocuments;
                    vm.selectedHabilitation.file = data.file;
                }
            });
        }

        /**
         * @ngdoc method
         * @name selectHabilitation
         * @methodOf party.controllers:HabilitationController
         * @description Load data related with selected habilitation
         */
        function selectHabilitation() {
            habilitationService.loadHabilitationDetails(vm.selectedHabilitation).then(function (data) {
                vm.selectedHabilitation.legalDocuments = data.legalDocuments;
                vm.selectedHabilitation.file = data.file;
            });
        }


        /**
         * @ngdoc method
         * @name addHabilitation
         * @methodOf party.controllers:HabilitationController
         * @description create new habilitation
         * @param form Form to be validated
         */
        function addHabilitation(form) {
            if (form.$valid) {
                if (!vm.newHabilitation.file || vm.newHabilitation.file
                    && vm.newHabilitation.file.type !== 'application/pdf') {
                    return false;
                }
                //habilitationRepository.postHabilitation(vm.newHabilitation).then(function () {
                habilitationPrototype.postHabilitation(vm.newHabilitation).then(function () {
                    form.$submitted = false;
                    activate().then(function () {
                        toastr.success('Document ajouté avec succès', 'Succès');
                    });
                });
            }
        }

    }

    return HabilitationController;
});
