/**
 * @ngdoc controller
 * @name autoEntrepreneur.controllers:autoEntrepreneurController
 * @description
 * This file defines the home autoEnrepreneur controller
 */
define([], function () {
    'use strict';

    // TODO Removed
    autoEntrepreneurController.$inject = ['$scope', '$rootScope', 'memberRepository', 'autoEntrepreneurRepository', 'contractRepository'];

    function autoEntrepreneurController($scope, $rootScope, memberRepository, autoEntrepreneurRepository, contractRepository) {
        /**
         * Init global parameters
         */
        $rootScope.menuTab = 'autoEntrepreneur';
        $rootScope.isProgram = false;

        $scope.classes = {clients: 'active', distributeurs: '', prestataires: ''};
        $scope.filterContract = 'default:owner';

        $scope.typesContract = {
            clients: 'default:owner',
            distributeurs: 'affair:client',
            prestataires: 'affair:owner'
        };
        $scope.listLabelContract = {
            clients: 'client',
            distributeurs: 'commercial',
            prestataires: 'prestataire'
        };
        $scope.typeInvitationsLabel = {
            clients: 'client',
            distributeurs: 'auto-entrepreneur partenaire (commercial)',
            prestataires: 'auto-entrepreneur partenaire (prestataire)'
        };

        $scope.contracts = [];
        $scope.firstDisplay = true;
        $scope.currentOnglet = 'clients';
        $scope.labelContract = $scope.listLabelContract.clients;
        $scope.invitationTitle = $scope.typeInvitationsLabel.clients;
        $scope.typeInvitation = $scope.typesContract.clients;

        /**
         * @ngdoc method
         * @name selectPartyTab
         * @methodOf autoEntrepreneur.controllers:autoEntrepreneurController
         * @description
         * Select navbar users/college
         *
         * @param {object} navbar A navigation bar data
         */
        $scope.selectPartyTab = function (navbar) {
            $scope.firstDisplay = false;
            $scope.currentOnglet = navbar;
            $scope.classes.clients = '';
            $scope.classes.distributeurs = '';
            $scope.classes.prestataires = '';
            $scope.classes[navbar] = 'active';
            $scope.filterContract = $scope.typesContract[navbar];
            $scope.contracts = $scope[navbar];
            $scope.labelContract = $scope.listLabelContract[navbar];
            $scope.invitationTitle = $scope.typeInvitationsLabel[navbar];
            $scope.typeInvitation = $scope.typesContract[navbar];
        };

        /**
         * @ngdoc method
         * @name getMemberAutoEntrepreneur
         * @methodOf autoEntrepreneur.controllers:autoEntrepreneurController
         * @description
         * Load member autoEntrepreneur
         *
         */
        $scope.getMemberAutoEntrepreneur = function () {
            memberRepository.getMemberAutoEntrepreneur().then(function (data) {
                $scope.autoEntrepreneur = data.autoEntrepreneur;
                $scope.getAutoEntrepreneurParty($scope.autoEntrepreneur.id);
            });
        };

        /**
         * @ngdoc method
         * @name loadMemberContracts
         * @methodOf autoEntrepreneur.controllers:autoEntrepreneurController
         * @description
         * Load all contracts
         *
         * @param {string} filterContract A type of contract
         * @param {number} key A key
         */
        $scope.loadMemberContracts = function (filterContract, key) {
            var filter = {
                filterContract: filterContract
            };

            memberRepository.getMemberContracts(filter).then(function (data) {
                $scope[key] = data.contracts;
                angular.forEach($scope[key], function (contract) {
                    $scope.loadContractRecruitment(contract);
                    contractRepository.setPendingContract(contract);

                    if (contract.pendingContract.invitation) {
                        contract.labelContract = contract.invitation.firstName + ' ' + contract.invitation.lastName;
                    } else {
                        if (filterContract == 'default:owner' || filterContract == 'affair:owner') {
                            contract.labelContract = contract.clientLabel;
                        } else {
                            contract.labelContract = contract.ownerLabel;
                        }
                    }
                    contract.statusContract = contractRepository.formatStatusContract(contract, filterContract);
                });

                if ($scope.firstDisplay) {
                    $scope.contracts = $scope.clients;
                } else {
                    $scope.contracts = $scope[key];
                }
                contractRepository.allowedActions($scope[key], $scope.autoEntrepreneur, null);
            });
        };

        /**
         * @ngdoc method
         * @name loadContractRecruitment
         * @methodOf autoEntrepreneur.controllers:autoEntrepreneurController
         * @description
         * Get recruitment contract
         *
         * @param {object} contract to append to recruitment
         */
        $scope.loadContractRecruitment = function (contract) {
            contractRepository.getContractRecruitment(contract.id).then(function (data) {
                contract.recruitment = data.recruitment;
                if (!contract.recruitment || contract.owner.autoEntrepreneur.id !== $scope.autoEntrepreneur.id
                    || contract.recruitment.isExpired) {
                    if (contract.allActiveRecruitments) {
                        if (contract.client && contract.client.autoEntrepreneur) {
                            contract.recruitmentGroup = '';
                            for (var index in contract.allActiveRecruitments) {
                                var recruitment = contract.allActiveRecruitments[index];
                                if (index != 0) {
                                    contract.recruitmentGroup += ',';
                                }
                                contract.recruitmentGroup += recruitment.recruiterCorpContract.client.label;
                            }

                        } else if (contract.client && contract.client.corporate) {
                            contract.recruitmentGroup = '';
                            for (var index in contract.allActiveRecruitments) {
                                var recruitment = contract.allActiveRecruitments[index];
                                if (index != 0) {
                                    contract.recruitmentGroup += ',';
                                }
                                contract.recruitmentGroup += recruitment.recruitmentContract.client.label;
                            }
                        }
                    }
                }
            });
        };

        /**
         * @ngdoc method
         * @name getAutoEntrepreneurParty
         * @methodOf autoEntrepreneur.controllers:autoEntrepreneurController
         * @description
         * Load AutoEntrepreneur party
         *
         * @param {number} autoEntrepreneurId Id of AutoEntrepreneur
         */
        $scope.getAutoEntrepreneurParty = function (autoEntrepreneurId) {
            autoEntrepreneurRepository.getAutoEntrepreneurParty(autoEntrepreneurId).then(function (data) {
                $scope.autoEntrepreneur.party = data.party;
                angular.forEach($scope.typesContract, function (filterContract, key) {
                    $scope.loadMemberContracts(filterContract, key);
                    if (filterContract == 'affair:owner') {
                        $scope.loadCorporateContracts();
                    }
                });
            });
        };

        /**
         * @ngdoc method
         * @name loadCorporateContracts
         * @methodOf autoEntrepreneur.controllers:autoEntrepreneurController
         * @description
         * Load all corporate contracts
         */
        $scope.loadCorporateContracts = function () {
            var filter = {
                filterContract: $scope.filterContract,
                activeOnly: true
            };

            memberRepository.getMemberContracts(filter).then(function (data) {
                $scope.allCorporateContracts = data.contracts;
            });
        };

        /**
         * Load data
         */
        $scope.getMemberAutoEntrepreneur();
    }

    return autoEntrepreneurController;
});
