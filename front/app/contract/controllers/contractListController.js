/**
 * @ngdoc controller
 * @name contract.controllers:ContractListController
 * @description
 * This file defines the contract controller
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function ContractListController($mdDialog, cooperons, common, contractListService, collaboratorRepository, contractRepository) {


        var vm = this;

        vm.party = null;
        vm.collaborator = null;
        vm.autoEntrepreneur = null;
        vm.canCreateContractsAE = false;
        vm.tokenAE = common.getMemberData().tokenAE;
        vm.contracts = [];
        vm.labelContract = '';
        vm.invitationTitle = '';

        // functions
        vm.init = init;
        vm.showInvitationDialog = showInvitationDialog;
        vm.signOutContract = signOutContract;
        vm.reinviteContract = reinviteContract;
        vm.recruitmentContract = recruitmentContract;

        /**
         * Note: To use Angular components:
         *  - remove onEvent & init functions
         *  - pass party, typeContract & collaborator like parameters
         *  - init labelContract, invitationTitle & filterContract in action function
         *
         *  => Don't use vm.parent outside of onEvent and init
         */

        common.onEvent(cooperons.events.partyChanged, function (event) {
            init(vm.parent);
        });


        function init(parent, typeContract) {
            vm.parent = parent;
            vm.party = parent.party;
            vm.collaborator = parent.collaborator;

            if (typeContract) {
                vm.labelContract = cooperons.contractLabels[typeContract];
                vm.invitationTitle = cooperons.invitationTypes[typeContract];
                vm.filterContract = cooperons.contractTypes[typeContract];
            }
            activate();
        }

        function activate() {
            return contractListService.loadCollaboratorContracts(vm.collaborator, vm.filterContract).then(function (data) {
                vm.contracts = data.contracts;
                vm.autoEntrepreneur = data.autoEntrepreneur;
                vm.canCreateContractsAE = data.canCreateContractsAE;
            });
        }


        /**
         * @ngdoc method
         * @name showInvitationDialog
         * @methodOf contract.controllers:ContractListController
         * @description
         * Show invitation dialog
         */
        function showInvitationDialog() {
            var data = {invitationTitle: vm.invitationTitle};
            if (!vm.canCreateContractsAE) {
                $mdDialog.showConfirm({template: 'contract.new.disable', data: data});
            } else {
                data.invitationTitle = vm.invitationTitle;
                data.labelContract = vm.labelContract;
                data.invitation = {invitationType: vm.filterContract, firstName: '', lastName: '', email: ''};
                data.sendInvitation = sendInvitationContract;
                $mdDialog.showConfirm({template: 'contract.new', data: data});
            }
        }

        /**
         * @ngdoc method
         * @name signOutContract
         * @methodOf contract.controllers:ContractListController
         * @description
         * SignOut Contract action
         *
         * @param {object} contract A contract object
         *
         */
        function signOutContract(contract) {
            if (contract.newContract) {
                contractRepository.deleteContract(contract.newContract.id).then(function () {
                    activate();
                });
            } else {
                var data = {contract: contract, filterContract: vm.filterContract};
                $mdDialog.showConfirm({template: 'contract.signOut', data: data}).then(function () {
                    contractRepository.deleteContract(contract.id).then(function () {
                        activate();
                    });
                });
            }
        }


        /**
         * @ngdoc method
         * @name reinviteContract
         * @methodOf contract.controllers:ContractListController
         * @description
         * Resend invitation for a contract
         *
         * @param {number} contract
         *
         */
        function reinviteContract(contract) {
            return contractListService.reinviteContract(contract).then(function () {
                toastr.success('Invitation renvoyée avec succès', 'Succès');
            });
        }

        /**
         * @ngdoc method
         * @name recruitmentContract
         * @methodOf contract.controllers:ContractListController
         * @description
         * Recruitment a contract
         *
         * @param {number} contract
         *
         */
        function recruitmentContract(contract) {
            // TODO corporateContracts has been loaded in autoEntrepreneur
            var corporateContracts = [];
            var selectedContract = corporateContracts[0] || null;
            var data = {
                contract: contract,
                corporateContracts: corporateContracts,
                selectedContract: selectedContract
            };

            $mdDialog.showConfirm({template: 'contract.recruitment', data: data}).then(function () {
                // TODO Review
                contractListService.createContractRecruitment(vm.collaborator, contract, selectedContract).then(function (response) {
                    if (typeof response.data.error === 'undefined') {
                        activate();
                    } else {
                        toastr.error(response.data.error, 'Attention !');
                    }
                });
            });
        }


        /**
         * @ngdoc method
         * @name sendInvitationContract
         * @methodOf contract.controllers:ContractListController
         * @description
         * Invite contract action, used in new contract dialog
         *
         * @param {object} form InvitationContract form to be validated
         *
         */
        function sendInvitationContract(form, invitation) {
            if (!form.$valid) {
                return false;
            }
            if (form.$valid) {
                $mdDialog.hide();
                collaboratorRepository.postContractInvitation(vm.collaborator.id, invitation).then(function (response) {
                    if (angular.isUndefined(response.collaborators)) {
                        activate();
                    } else {
                        successInvitationHandler(response.collaborators);
                    }
                });
            }

            function successInvitationHandler(collaborators) {
                var selectedCollaborator = Object.keys(collaborators)[0] ? {id: Object.keys(collaborators)[0]}: null;
                var data = {
                    labelContract: vm.labelContract,
                    collaborators: collaborators,
                    hasCollaborators: selectedCollaborator !== null,
                    selectedCollaborator: selectedCollaborator
                };
                $mdDialog.showConfirm({template: 'contract.create', data: data}).then(function () {
                    selectedCollaborator.corporateLabel = collaborators[selectedCollaborator.id];
                    createContract(selectedCollaborator);
                });
            }

            function createContract(selectedCollaborator) {
                var data = {
                    filterContract: vm.filterContract,
                    otherCollaboratorId: selectedCollaborator.id
                };
                collaboratorRepository.postCollaboratorContract(vm.collaborator.id, data).then(function () {
                    activate();
                }).catch(function (fallback) {
                    if (fallback.data.code == 400110) {
                        toastr.remove();
                        toastr.error(vm.party.label + ' dispose déjà d\'un contrat ' + vm.labelContract
                            + ' avec ' + selectedCollaborator.corporateLabel, 'Contrat existant!');
                    }
                });
            }
        }
    }

    return ContractListController;
});