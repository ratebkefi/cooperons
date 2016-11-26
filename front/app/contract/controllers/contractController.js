/**
 * @ngdoc controller
 * @name contract.controllers:ContractController
 * @description This file defines the contract controller for contract & legal documents editing
 */
define([], function () {
    'use strict';


    function ContractController($state, common, legalDocumentRepository, contractService) {

        common.setMenuTab('party');
        var contractId = $state.params.id;

        var vm = this;

        vm.contract = null;
        vm.legalDocuments = [];
        vm.selectedLegalDocument = null;
        vm.party = null;
        vm.collaborator = null;
        vm.local = null;
        vm.action = 'view';// Actions: 'view' for showing, 'edit' on updating and 'new' on creating
        vm.dateNow = new Date();
        vm.deadline = {
            types: ['undetermined', 'date', 'duration'],
            units: [{value: 'month', label: 'Mois'}, {value: 'year', label: 'Années'}]
        };
        vm.cancellationPeriod = {units: [{value: 'day', label: 'Jours'}, {value: 'month', label: 'Mois'}]};

        // Functions
        vm.selectLegalDocument = selectLegalDocument;
        vm.addLegalDocument = addLegalDocument;
        vm.editLegalDocument = editLegalDocument;
        vm.removeLegalDocument = removeLegalDocument;
        vm.cancelLegalDocument = cancelLegalDocument;
        vm.saveLegalDocument = saveLegalDocument;
        vm.terminateLegalDocument = terminateLegalDocument;
        vm.signLegalDocument = signLegalDocument;
        vm.inviteCollaborator = inviteCollaborator;
        vm.downloadHabilitation = downloadHabilitation;
        vm.updateDeadlineDate = updateDeadlineDate;

        activate();

        /**
         * @ngdoc method
         * @name activate
         * @methodOf prototype.controllers:ContractController
         * @description Retrieve contract by id
         */
        function activate() {
            return contractService.loadContractDetails(contractId).then(function (data) {
                vm.contract = data.contract;
                vm.legalDocuments = data.legalDocuments;
                vm.party = data.party;
                vm.collaborator = data.collaborator;
                vm.local = data.local;
                vm.selectedLegalDocument = vm.legalDocuments[0] || null;
                if (vm.selectedLegalDocument) {
                    vm.action = 'view';
                    selectLegalDocument();
                } else {
                    addLegalDocument();
                }
            })
        }

        /**
         * @ngdoc method
         * @name selectLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Load PDF file when selecting a new legal document
         */
        function selectLegalDocument() {
            toastr.info('Sélectionner un document légal', 'Non implémenté');

        }

        /**
         * @ngdoc method
         * @name addLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Prepare view to create a new legal document
         */
        function addLegalDocument() {
            vm.action = 'new';
            vm.newLegalDocument = {
                contractId: contractId,
                deadlineType: 'date',
                deadlineDate: vm.dateNow,
                agreeDate: vm.dateNow,
                cancelDate: vm.dateNow,
                publishDate: vm.dateNow,
                effectiveDate: vm.dateNow,
                deadlineValue: 1,
                deadlineUnit: 'year',
                cancellationPeriodUnit: 'month',
                cancellationPeriod: 1,
                status: 'En cours de création'
            };
            vm.selectedLegalDocument = vm.newLegalDocument;
        }

        /**
         * @ngdoc method
         * @name editLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Prepare view to updating a selected legal document
         */
        function editLegalDocument() {
            vm.selectedLegalDocument = angular.copy(vm.selectedLegalDocument);
            vm.action = 'edit';
        }

        /**
         * @ngdoc method
         * @name removeLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Delete selected legal document
         */
        function removeLegalDocument() {
            legalDocumentRepository.deleteLegalDocument(vm.selectedLegalDocument.id).then(function () {
                activate().then(function () {
                    toastr.success('Le document légal a était supprimé', 'Succès');
                });
            });
        }


        /**
         * @ngdoc method
         * @name cancelLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Cancel create and update legal document
         */
        function cancelLegalDocument(form) {
            form.$submitted = false;
            if (vm.legalDocuments.length) {
                vm.selectedLegalDocument = vm.legalDocuments[0];
                selectLegalDocument();
                vm.action = 'view';
            } else {
                addLegalDocument();
            }
        }

        /**
         * @ngdoc method
         * @name saveLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Save updated/new legal document
         * @param form Form to be validated
         */
        function saveLegalDocument(form) {
            if (form.$valid) {
                if (vm.action == 'new' && (!vm.selectedLegalDocument.file ||
                    vm.selectedLegalDocument.file && vm.selectedLegalDocument.file.type !== 'application/pdf')) {
                    return false;
                }
                contractService.saveLegalDocument(vm.contract, vm.selectedLegalDocument).then(function (data) {
                    form.$submitted = false;
                    var message = vm.action == 'edit' ? 'Le document légal a été mis à jour' : 'Le document légal a été créé avec succès';
                    activate().then(function () {
                        toastr.success(message, 'Succès');
                    });
                });

            }
        }

        /**
         * @ngdoc method
         * @name shareLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Share legal document
         */
        function shareLegalDocument() {
            contractService.shareLegalDocument(vm.selectedLegalDocument).then(function () {
                toastr.success('Le document légal a été partagé', 'Succès');
            })
        }

        /**
         * @ngdoc method
         * @name terminateLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Terminate legal document contract
         */
        function terminateLegalDocument() {
            contractService.terminateLegalDocument(vm.selectedLegalDocument).then(function () {
                toastr.success('Le document légal a été résilié', 'Succès');
            });
        }

        /**
         * @ngdoc method
         * @name signLegalDocument
         * @methodOf prototype.controllers:ContractController
         * @description Sign selected legal document
         */
        function signLegalDocument() {
            contractService.signLegalDocument(vm.selectedLegalDocument).then(function () {
                toastr.success('Le document légal a été signé', 'Succès');
            });
        }

        /**
         * @ngdoc method
         * @name inviteCollaborator
         * @methodOf prototype.controllers:ContractController
         * @description Invite collaborator to manage contract
         */
        function inviteCollaborator() {
            toastr.info('inviter un collaborateur', 'Non implémenté');
        }

        /**
         * @ngdoc method
         * @name downloadHabilitation
         * @methodOf prototype.controllers:ContractController
         * @description Download habilitation document
         */
        function downloadHabilitation() {
            toastr.info('Télécharger habilitation', 'Non implémenté');
        }

        /**
         * @ngdoc method
         * @name updateDeadlineDate
         * @methodOf prototype.controllers:ContractController
         * @description Updating deadline date when changing type or value
         */
        function updateDeadlineDate(type) {
            switch (type) {
                case 'undetermined':
                    vm.selectedLegalDocument.deadlineDate = null;
                    break;
                case 'duration':
                    // Select type «duration» when changing value without selecting type
                    vm.selectedLegalDocument.deadlineType = 'duration';
                    vm.selectedLegalDocument.deadlineDate = new Date();
                    if (vm.selectedLegalDocument.deadlineUnit === 'month') {
                        vm.selectedLegalDocument.deadlineDate.setMonth(vm.selectedLegalDocument.deadlineDate.getMonth()
                            + vm.selectedLegalDocument.deadlineValue);
                    } else if (vm.selectedLegalDocument.deadlineUnit === 'year') {
                        vm.selectedLegalDocument.deadlineDate.setYear(vm.selectedLegalDocument.deadlineDate.getFullYear()
                            + vm.selectedLegalDocument.deadlineValue);
                    }
                    break;
                case 'date':
                    if (vm.selectedLegalDocument.deadlineDate === null) {
                        vm.selectedLegalDocument.deadlineDate = new Date();
                    }
                    break;
            }
        }
    }

    return ContractController;
});