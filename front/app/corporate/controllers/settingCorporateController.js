/**
 * @ngdoc controller
 * @name corporate.controllers:SettingCorporateController
 * @description
 * This file defines the home corporate's setting controller
 */
define([], function () {
        'use strict';

        /*ngInject*/
        function SettingCorporateController($state, $mdDialog, common, memberService, settingCorporateService, corporateRepository) {

            common.setMenuTab('party');

            var vm = this;

            vm.idCorporate = $state.params.idCorporate;
            vm.originCorporate = null;
            vm.corporate = null;
            vm.countries = [];

            // Functions
            vm.saveCorporate = vm.idCorporate > 0 ? editCorporate : createCorporate;

            activate();

            function activate() {
                return settingCorporateService.settingCorporate(vm.idCorporate).then(function (data) {
                    vm.originCorporate = data.corporate;
                    vm.corporate = angular.copy(vm.originCorporate);
                    vm.countries = data.countries;
                    if (!vm.corporate) {
                        vm.corporate = {country: data.france.id};
                    }
                });
            }


            function editCorporate(form) {
                if (form.$valid) {
                    var data = {corporate: vm.originCorporate};
                    $mdDialog.showConfirm({template: 'edit.corporate', data: data}).then(function () {
                        corporateRepository.putCorporate(vm.corporate).then(function () {
                            $state.go("party.home");
                        });
                    });
                }
            }

            function createCorporate(form) {
                if (form.$valid) {
                    $mdDialog.showConfirm({template: 'create.corporate'}).then(function () {
                        corporateRepository.postCorporate(vm.corporate).then(function () {
                            memberService.loadMemberDetails(true, false, true, true).then(function () {
                                $state.go("party.home");
                            });
                        }).catch(function (fallback) {
                            if (fallback.data.code == 40063) {
                                toastr.remove();
                                var administratorMember = fallback.data.data.administratorMember;
                                var existingCorporate = fallback.data.data.existingCorporate;
                                var message = 'L\'entreprise identifiée sous le numéro de TVA Intracommunautaire '
                                    + existingCorporate.tvaIntracomm + ' dispose déjà d\'un compte Coopérons. Contactez '
                                    + administratorMember.firstName + ' ' + administratorMember.lastName + ' ('
                                    + administratorMember.email + ') si vous souhaitez disposer des fonctions de '
                                    + 'gestionnaire du compte de ' + existingCorporate.raisonSocial;
                                toastr.error(message, 'Entreprise existante!');
                            }
                        });
                    });
                }

            }
        }

        return SettingCorporateController;
    }
)
;
