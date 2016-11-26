/**
 * @ngdoc controller
 * @name party.controllers:CollaboratorController
 * @description
 * This file defines the home collaborator controller
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function CollaboratorController($mdDialog, cooperons, common, collaboratorService) {

        var vm = this;

        vm.party = null;
        vm.corporate = null;
        vm.collaborator = null;
        vm.member = null;
        vm.memberCollaborators = [];

        // functions
        vm.init = init;
        vm.showUserDialog = showUserDialog;
        vm.deleteUser = deleteUser;
        vm.transferContract = transferContract;
        vm.resendCollaboratorInvitation = resendCollaboratorInvitation;

        /**
         * Note: To use Angular components:
         *  - remove onEvent & init functions
         *  - pass party & collaborator like parameters
         *
         *  => Don't use vm.parent outside of onEvent and init
         */

        common.onEvent(cooperons.events.partyChanged, function (event) {
            init(vm.parent);
        });


        /**
         * @ngdoc method
         * @name init
         * @methodOf party.controllers:partyController
         * @description
         * Retireve parameters
         */
        function init(parent) {
            vm.parent = parent;
            vm.collaborator = parent.collaborator;
            vm.party = parent.party;
            vm.corporate = parent.party.corporate;
            activate();
        }

        /**
         * @ngdoc method
         * @name activate
         * @methodOf party.controllers:partyController
         * @description
         * Initialize data
         */
        function activate() {
            return collaboratorService.loadPartyCollaborators(vm.party).then(function (collaborators) {
                vm.collaborators = collaborators;
            });
        }


        /**
         * @ngdoc method
         * @name loadCollaboratorContracts
         * @methodOf party.controllers:partyController
         * @description
         * Load all programs of collaborator
         *
         * @param {object} collaborator A collaborator data
         */
        function loadCollaboratorContracts(collaborator) {
            return collaboratorService.getCollaboratorContracts(collaborator.id).then(function (contracts) {
                collaborator.contracts = contracts;
            });
        }

        /**
         * @ngdoc method
         * @name showUserDialog
         * @methodOf party.controllers:partyController
         * @description
         * Show dialog to add new user
         */
        function showUserDialog() {
            var data = {
                collaborator: vm.collaborator,
                party: vm.party,
                beforeSubmitInvitation: $mdDialog.hide,
                successCollaboratorInvitation: activate
            };
            $mdDialog.showConfirm({template: 'collaborator.invitation', data: data});
        }

        /**
         * @ngdoc method
         * @name deleteUser
         * @methodOf party.controllers:CollaboratorController
         * @description
         * Make out collaborator from party
         *
         * @param {object} collaborator A collaborator data
         */
        function deleteUser(collaborator) {
            var data = null;
            if (!collaborator.isAdministrator) {
                data = {leavingCollaborator: collaborator};
                $mdDialog.showConfirm({template: 'user.leave', data: data}).then(function () {
                    collaboratorService.removeCollaborator(collaborator).then(function () {
                        activate().then(function () {
                            toastr.success('L\'utilisateur  a été exclu de l\'entreprise', 'Succès');
                        });
                    });
                });
            } else {
                var confirmedCollaborators = collaboratorService.transferToCollaborators(vm.collaborators);
                var transferToCollaborators = collaboratorService.transferToCollaborators(vm.collaborators, collaborator);
                var newAdministrator = transferToCollaborators[0] || null;
                data = {
                    leavingCollaborator: collaborator,
                    corporate: vm.corporate,
                    confirmedCollaborators: confirmedCollaborators,
                    transferToCollaborators: transferToCollaborators,
                    canTerminateParty: vm.party.canTerminate
                };

                $mdDialog.showConfirm({template: 'administrator.leave', data: data}).then(function () {
                    collaboratorService.removeCollaborator(collaborator, newAdministrator).then(function () {
                        var message;
                        if (confirmedCollaborators.length === 1) {
                            message = 'Résiliation du Contrat Accord Cadre de ' + vm.corporate.raisonSocial;
                        } else {
                            message = 'Vous avez cédé l\'administration de l\'entreprise. Vous n\'êtes plus' +
                                ' utilisateur du compte Entreprise de ' + vm.corporate.raisonSocial;
                        }
                        toastr.success(message, 'Succès');
                        common.broadcast(cooperons.events.refreshParty);
                    });
                });
            }
        }

        /**
         * @ngdoc method
         * @name transferContract
         * @methodOf party.controllers:partyController
         * @description
         * Change contract manager
         *
         * @param {object} collaborator A collaborator data
         * @param {object} contract A contract data
         */
        function transferContract(collaborator, contract) {
            var collaborators = collaboratorService.transferToCollaborators(vm.collaborators, collaborator);
            var data = {
                contract: contract,
                collaborators: collaborators,
                transferTo: collaborators[0] || null
            };

            $mdDialog.showConfirm({template: 'collaborator.transfer.contract', data: data}).then(function () {
                collaboratorService.transferContract(contract, transferTo).then(function () {
                    activate().then(function () {
                        toastr.success('Transfert du contrat ' + contract.ownerLabel + ' effectué avec succès', 'Succès');
                    });
                })
            });
        }

        /**
         * @ngdoc method
         * @name resendCollaboratorInvitation
         * @methodOf party.controllers:partyController
         * @description
         * Resend invitation to collaborator
         *
         * @param {object} collaborator A collaborator data
         */
        function resendCollaboratorInvitation(collaborator) {
            collaboratorService.reInviteCollaborator(collaborator).then(function () {
                toastr.success('Mail envoyé avec succès.', 'Succès');
            });
        }
    }

    return CollaboratorController;
});
