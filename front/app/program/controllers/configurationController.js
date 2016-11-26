/**
 * @ngdoc controller
 * @name program.controllers:configurationController
 * @description
 * This file defines the program's configuration controller
 */
define([], function () {
    'use strict';

    configurationController.$inject = ['$rootScope', '$scope', '$state', 'programService', 'easySettingService',
        'mailInvitationService', '$sce', 'ngDialog', 'Notification'];

    function configurationController($rootScope, $scope, $state, programService, easySettingService,
                                     mailInvitationService, $sce, ngDialog, Notification) {
        /**
         * Init global parameters
         */
        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        // Get program id from state(id exposed in url)
        var programId = $state.params.id;
        // Attach forms to scope
        $scope.forms = {};
        // Attach selected value to scope
        $scope.selected = {};
        $scope.urlRegex = /\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i;
        // Init new document used for uploading file
        $scope.newDocument = {file: null};
        // Tinymce configuration
        $scope.tinymceOptions = {
            inline: false,
            plugins: 'image lists link',
            toolbar: 'styleselect bold italic underline outdent indent alignjustify bullist image link',
            menubar: false,
            image_advtab: true,
            remove_linebreaks: false,
            force_br_newlines: true,
            force_p_newlines: false,
            forced_root_block: false
        };

        /**
         * @ngdoc method
         * @name init
         * @methodOf program.controllers:configurationController
         * @description
         * Get program
         *
         * @param {number} programId Id of program
         */
        $scope.init = function (programId) {
            programService.getProgram(programId).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
                // Init object to updated mailing parameters
                $scope.configuration = {
                    senderEmail: $scope.program.senderEmail,
                    senderName: $scope.program.senderName
                };
                $scope.program.isEasy ? null : $scope.configuration.landingUrl = $scope.program.landingUrl;

                // Load program corporate
                programService.getProgramCorporate(programId).then(function (data) {
                    $scope.corporate = data.corporate;
                });

                // If program type is easy then load presentation document
                if ($scope.program.isEasy) {
                    $scope.loadDocument(programId);
                }

                // Load invitation mails
                $scope.loadMailInvitations(programId);
            });
        };

        /**
         * @ngdoc method
         * @name loadDocument
         * @methodOf program.controllers:configurationController
         * @description
         * Load PDF document
         *
         * @param {number} programId Id of program
         */
        $scope.loadDocument = function (programId) {
            easySettingService.getProgramEasySetting(programId).then(function (data) {
                $scope.document = data.easySetting.document;
                // If document is not null load pdf document else load empty_document.pdf
                if ($scope.document) {
                    easySettingService.getEasySettingDocument(programId).then(function (response) {
                        var file = new Blob([response], {type: 'application/pdf'});
                        var fileURL = URL.createObjectURL(file);
                        $scope.programFile = {content: $sce.trustAsResourceUrl(fileURL)};
                    });
                } else {
                    $scope.programFile = {content: 'assets/doc/modules/member/emptyDocument.pdf'};
                }
            });
        };

        /**
         * @ngdoc method
         * @name loadMailInvitations
         * @methodOf program.controllers:configurationController
         * @description
         * Load all invitation mails
         *
         * @param {number} programId Id of program
         */
        $scope.loadMailInvitations = function (programId) {
            mailInvitationService.getProgramMailInvitations(programId).then(function (data) {
                $scope.mailInvitations = data.mailInvitations;
                $scope.selected.mail = $scope.mailInvitations[0];
            });
        };

        /**
         * @ngdoc method
         * @name uploadDocument
         * @methodOf program.controllers:configurationController
         * @description
         * Upload program presentation document
         *
         * @return {boolean} Return false document is not valid
         */
        $scope.uploadDocument = function () {
            if (!$scope.newDocument.file || $scope.newDocument.file.type !== 'application/pdf') {
                return false;
            } else {
                easySettingService.postEasySettingDocument($scope.program.id, $scope.newDocument.file).then(function () {
                    $scope.loadDocument($scope.program.id);
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                        message: 'Le document de présentation est mis à jour'
                    });
                });
            }
        };

        /**
         * @ngdoc method
         * @name updateConfiguration
         * @methodOf program.controllers:configurationController
         * @description
         * Update email configuration
         *
         * @param {number} form Document form to be validated
         * @return {boolean} Return value of form's checking
         */
        $scope.updateConfiguration = function (form) {
            if (form.$valid) {
                var patch = [];
                patch[0] = {op: 'replace', path: '/senderName', value: $scope.configuration.senderName};
                patch[1] = {op: 'replace', path: '/senderEmail', value: $scope.configuration.senderEmail};

                if (!$scope.program.isEasy) {
                    patch[2] = {op: 'replace', path: '/landingUrl', value: $scope.configuration.landingUrl};
                }

                programService.patchProgram($scope.program.id, patch).then(function () {
                    $scope.init($scope.program.id);
                    Notification.success({
                        title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                        message: 'Le programme est mis à jour'
                    });
                });
                return true;
            } else {
                return false;
            }
        };

        /**
         * @ngdoc method
         * @name saveEmailTemplate
         * @methodOf program.controllers:configurationController
         * @description
         * Update invitation email template
         *
         * @param {number} form Document form to be validated
         * @param {string} mail A mail invitation
         * @return {boolean} Return value of form's checking
         */
        $scope.saveEmailTemplate = function (form, mail) {
            if (form.$valid) {
                if (mail.new) {
                    mailInvitationService.postProgramMailInvitation($scope.program.id, mail).then(function () {
                        $scope.loadMailInvitations($scope.program.id);
                        Notification.success({
                            title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                            message: 'Template du mail crée avec succès'
                        });
                    });
                } else {
                    mailInvitationService.putProgramMailInvitation($scope.program.id, mail.codeMail, mail).then(function () {
                        $scope.loadMailInvitations($scope.program.id);
                        Notification.success({
                            title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                            message: 'Template du mail mise à jour'
                        });
                    });
                }
                return true;
            } else {
                return false;
            }
        };

        /**
         * @ngdoc method
         * @name removeMailTemplate
         * @methodOf program.controllers:configurationController
         * @description
         * Remove email template
         *
         * @param {string} mail A mail invitation
         */
        $scope.removeMailTemplate = function (mail) {
            var scopeDialog = $scope.$new();
            scopeDialog.mail = mail;
            ngDialog.openConfirm({template: 'delete.email', scope: scopeDialog})
                .then(function () {
                    mailInvitationService.deleteProgramMailInvitation($scope.program.id, mail.codeMail, mail).then(function () {
                        Notification.success({
                            title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                            message: 'La template a été supprimée avec succès'
                        });
                        $scope.loadMailInvitations($scope.program.id);
                    });
                });
        };

        /**
         * @ngdoc method
         * @name newMail
         * @methodOf program.controllers:configurationController
         * @description
         * Init object for create new mail template
         */
        $scope.newMail = function () {
            $scope.selected.mail = {new: true, codeMail: '', subject: '', content: '', header: '', footer: ''};
            $scope.forms.formAddMail.$submitted = false;
        };

        /**
         * @ngdoc method
         * @name resetProgram
         * @methodOf program.controllers:configurationController
         * @description
         * Reset program
         */
        $scope.resetProgram = function () {
            var patch = [{op: 'clear', path: '/'}];
            programService.patchProgram($scope.program.id, patch).then(function () {
                $scope.init($scope.program.id);
                Notification.success({
                    title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                    message: 'Le programme a été réinitialisé'
                });
            });
        };

        /**
         * @ngdoc method
         * @name activateProgram
         * @methodOf program.controllers:configurationController
         * @description
         * Activate program
         *
         * @param {object} formConfig A form configuration
         */
        $scope.activateProgram = function (formConfig) {
            if (!$scope.program.isEasy && !$scope.program.landingUrl) {
                formConfig.$submitted = true;
                Notification.warning({
                    title: '<i class="fa fa-warning" style="color: white;">  Attention</i>',
                    message: 'Vous devez saisir d\'abord l\'URL sur votre site vers laquelle seront redirigées toutes vos invitations.'
                });
                return false;
            }

            ngDialog.openConfirm({template: 'activate.program', scope: $scope})
                .then(function () {
                    var patch = [{op: 'activate', path: '/'}];
                    programService.patchProgram($scope.program.id, patch).then(function () {
                        Notification.success({
                            title: '<i class="fa fa-check-circle" style="color: white"> Succès</i>',
                            message: 'Le programme est en mode production'
                        });
                        if ($scope.program.oldProgram) {
                            $state.go('member.programs');
                        } else {
                            $scope.init($scope.program.id);
                        }
                    });
                });
        };

        /**
         * @ngdoc method
         * @name toTrusted
         * @methodOf program.controllers:configurationController
         * @description
         * Trust html code to be exposed in template
         *
         * @param {string} htmlCode A html code
         */
        $scope.toTrusted = function (htmlCode) {
            return $sce.trustAsHtml(htmlCode);
        };

        /**
         * Load data
         */
        $scope.init(programId);

    }

    return configurationController;
});
