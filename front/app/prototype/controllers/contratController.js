/**
 * @ngdoc controller
 * @name prototype.controllers:contratController
 * @description This file defines the contract controller for prototype module
 */
define([], function () {
    "use strict";


    /*ngInject*/
    function contratController($rootScope, $scope, $state, contratPrototype, habilitationPrototype, Notification) {

        // Initialize global parameters
        $rootScope.space = 'member';
        $rootScope.menuTab = 'party';

        // Get contract id from state(exposed in url)
        var contratId = $state.params.contratId;
        // Get legal document id from state
        var legalDocumentId = $state.params.legalDocumentId;

        // Actions: 'view' for showing, 'edit' on updating and 'new' on creating
        $scope.action = 'view';
        $scope.dateNow = new Date();

        // DatePicker configuration
        $scope.dateOptions = {
            formatYear: 'yy',
            startingDay: 1
        };

        $scope.deadline = {
            types: ['undetermined', 'date', 'duration'],
            units: [{value: 'month', label: 'Mois'}, {value: 'year', label: 'Années'}],
        };

        $scope.cancellationPeriod = {
            units: [{value: 'day', label: 'Jours'}, {value: 'month', label: 'Mois'}]
        };

        /**
         * @ngdoc method
         * @name loadContrat
         * @methodOf prototype.controllers:contratController
         * @description Retrieve contract by id
         */
        var loadContrat = function () {
            contratPrototype.getContrat(contratId)
                .then(function (data) {
                    $scope.contrat = data.contrat;
                });
        };


        /**
         * @ngdoc method
         * @name loadLegalDocuments
         * @methodOf prototype.controllers:contratController
         * @description Load list of legal document on contract
         */
        var loadLegalDocuments = function () {
            contratPrototype.getContratLegalDocuments(contratId)
                .then(function (data) {
                    $scope.legalDocuments = data.legalDocuments;
                    // If list of legal documents isn't empty then select one, else show the creation interface
                    if ($scope.legalDocuments.length) {
                        $scope.selectedLegalDocument = $scope.legalDocuments[0];

                        angular.forEach($scope.legalDocuments, function (legalDocument) {
                            // Convert dates to javascript dates
                            legalDocument.agreeDate = new Date(legalDocument.agreeDate);
                            legalDocument.cancelDate = new Date(legalDocument.cancelDate);
                            legalDocument.publishDate = new Date(legalDocument.publishDate);
                            legalDocument.deadlineDate = new Date(legalDocument.deadlineDate);
                            legalDocument.effectiveDate = new Date();
                            legalDocument.effectiveDate.setDate(legalDocument.deadlineDate.getDate()
                                + legalDocument.cancellationPeriod);
                            if (legalDocumentId && legalDocument.id == legalDocumentId) {
                                $scope.selectedLegalDocument = legalDocument;
                                legalDocumentId = null;
                            }
                        });

                        $scope.selectLegalDocument();
                        $scope.action = 'view';
                    } else {
                        $scope.addLegalDocument();
                    }
                });
        };

        /**
         * @ngdoc method
         * @name loadLegalDocumentFile
         * @methodOf prototype.controllers:contratController
         * @description Load PDF file of legal document
         * @param legalDocumentId Id of legal document
         */
        var loadLegalDocumentFile = function (legalDocumentId) {
            contratPrototype.getLegalDocumentFile(contratId, legalDocumentId)
                .then(function (data) {
                    $scope.selectedLegalDocument.file = data.file;
                });
        };


        /**
         * @ngdoc method
         * @name loadLegalDocumentHabilitation
         * @methodOf prototype.controllers:contratController
         * @description Load PDF file of legal document
         * @param habilitationId Id of habilitation
         */
        var loadLegalDocumentHabilitation = function (habilitationId) {
            habilitationPrototype.getHabilitation(habilitationId)
                .then(function (data) {
                    $scope.selectedLegalDocument.habilitation = data.habilitation;
                });
        };

        /**
         * @ngdoc method
         * @name selectLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Load PDF file when selecting a new legal document
         */
        $scope.selectLegalDocument = function () {
            loadLegalDocumentFile($scope.selectedLegalDocument.id);
            loadLegalDocumentHabilitation($scope.selectedLegalDocument.habilitationId);
        };

        /**
         * @ngdoc method
         * @name addLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Prepare view to create a new legal document
         */
        $scope.addLegalDocument = function () {
            $scope.action = 'new';
            $scope.newLegalDocument = {
                contratId: contratId,
                deadlineType: 'date',
                deadlineDate: $scope.dateNow,
                agreeDate: $scope.dateNow,
                cancelDate: $scope.dateNow,
                publishDate: $scope.dateNow,
                effectiveDate: $scope.dateNow,
                deadlineValue: 1,
                deadlineUnit: 'year',
                cancellationPeriodUnit: 'month',
                cancellationPeriod: 1,
                status: 'En cours de création'
            };
            $scope.selectedLegalDocument = $scope.newLegalDocument;
        };

        /**
         * @ngdoc method
         * @name editLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Prepare view to updating a selected legal document
         */
        $scope.editLegalDocument = function () {
            $scope.selectedLegalDocument = angular.copy($scope.selectedLegalDocument);
            $scope.action = 'edit';
        };

        /**
         * @ngdoc method
         * @name removeLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Delete selected legal document
         */
        $scope.removeLegalDocument = function () {
            contratPrototype.deleteContratLegalDocument(contratId, $scope.selectedLegalDocument.id)
                .then(function () {
                    loadLegalDocuments();
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                        message: 'Le document légal est supprimé'
                    });
                });
        };


        /**
         * @ngdoc method
         * @name cancelLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Cancel create and update legal document
         */
        $scope.cancelLegalDocument = function (form) {
            form.$submitted = false;
            if ($scope.legalDocuments.length) {
                $scope.selectedLegalDocument = $scope.legalDocuments[0];
                $scope.selectLegalDocument();
                $scope.action = 'view';
            } else {
                $scope.addLegalDocument();
            }
        };

        /**
         * @ngdoc method
         * @name saveLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Save updated/new legal document
         * @param form Form to be validated
         */
        $scope.saveLegalDocument = function (form) {
            if (form.$valid) {
                if ($scope.action == 'new' && (!$scope.selectedLegalDocument.file ||
                    $scope.selectedLegalDocument.file && $scope.selectedLegalDocument.file.type !== 'application/pdf')) {
                    return false;
                }
                $scope.selectedLegalDocument.agreeDate ? $scope.selectedLegalDocument.agreeDate.toISOString() : null;
                $scope.selectedLegalDocument.cancelDate ? $scope.selectedLegalDocument.cancelDate.toISOString() : null;
                $scope.selectedLegalDocument.publishDate ? $scope.selectedLegalDocument.publishDate.toISOString() : null;
                $scope.selectedLegalDocument.deadlineDate ? $scope.selectedLegalDocument.deadlineDate.toISOString() : null;
                $scope.selectedLegalDocument.effectiveDate ? $scope.selectedLegalDocument.effectiveDate.toISOString() : null;

                if ($scope.action == 'edit') {
                    contratPrototype.putContratLegalDocument(contratId, $scope.selectedLegalDocument.id, $scope.selectedLegalDocument)
                        .then(function (data) {
                            form.$submitted = false;
                            loadLegalDocuments();
                            Notification.success({
                                title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                                message: 'Le document légal a été mis à jour'
                            });
                            $rootScope.terminate();
                        });
                } else if ($scope.action == 'new') {
                    contratPrototype.postContratLegalDocument(contratId, $scope.selectedLegalDocument)
                        .then(function (data) {
                            form.$submitted = false;
                            loadLegalDocuments();
                            Notification.success({
                                title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                                message: 'Le document légal a été créé avec succès'
                            });
                        });
                }
            }
        };

        /**
         * @ngdoc method
         * @name shareLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Share legal document
         */
        $scope.shareLegalDocument = function () {
            var patch = [{op: 'share', path: '/'}];
            contratPrototype.patchContratLegalDocument(contratId, $scope.selectedLegalDocument.id, patch)
                .then(function () {
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                        message: 'Le document légal a été partagé'
                    });
                });
        };

        /**
         * @ngdoc method
         * @name terminateLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Terminate legal document contract
         */
        $scope.terminateLegalDocument = function () {
            var patch = [{op: 'terminate', path: '/'}];
            contratPrototype.patchContratLegalDocument(contratId, $scope.selectedLegalDocument.id, patch)
                .then(function () {
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                        message: 'Le document légal a été résilié'
                    });
                });
        };

        /**
         * @ngdoc method
         * @name signLegalDocument
         * @methodOf prototype.controllers:contratController
         * @description Sign selected legal document
         */
        $scope.signLegalDocument = function () {
            var patch = [{op: 'sign', path: '/'}];
            contratPrototype.patchContratLegalDocument(contratId, $scope.selectedLegalDocument.id, patch)
                .then(function () {
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                        message: 'Le document légal a été signé'
                    });
                });
        };

        /**
         * @ngdoc method
         * @name inviteCollaborator
         * @methodOf prototype.controllers:contratController
         * @description Invite collaborator to manage contract
         */
        $scope.inviteCollaborator = function () {
            alert('inviter un collaborateur');
        };

        /**
         * @ngdoc method
         * @name inviteCollaborator
         * @methodOf prototype.controllers:contratController
         * @description Download habilitation document
         */
        $scope.downloadHabilitation = function () {
            alert('Télécharger habilitation');
        };

        /**
         * @ngdoc method
         * @name updateDeadlineDate
         * @methodOf prototype.controllers:contratController
         * @description Updating deadline date when changing type or value
         */
        $scope.updateDeadlineDate = function (type) {
            switch (type) {
                case 'undetermined':
                    $scope.selectedLegalDocument.deadlineDate = null;
                    break;
                case 'duration':
                    // Select type «duration» when changing value without selecting type
                    $scope.selectedLegalDocument.deadlineType = 'duration';
                    $scope.selectedLegalDocument.deadlineDate = new Date();
                    if ($scope.selectedLegalDocument.deadlineUnit === 'month') {
                        $scope.selectedLegalDocument.deadlineDate.setMonth($scope.selectedLegalDocument.deadlineDate.getMonth()
                            + $scope.selectedLegalDocument.deadlineValue);
                    } else if ($scope.selectedLegalDocument.deadlineUnit === 'year') {
                        $scope.selectedLegalDocument.deadlineDate.setYear($scope.selectedLegalDocument.deadlineDate.getFullYear()
                            + $scope.selectedLegalDocument.deadlineValue);
                    }
                    break;
                case 'date':
                    if ($scope.selectedLegalDocument.deadlineDate === null) {
                        $scope.selectedLegalDocument.deadlineDate = new Date();
                    }
                    break;
            }
        };

        loadContrat();
        loadLegalDocuments();

    }

    return contratController;
});