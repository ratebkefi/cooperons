/**
 * @ngdoc controller
 * @name program.controllers:journalController
 * @description
 * This file defines the journal controller
 */
define([], function () {
    'use strict';

    journalController.$inject = ['$rootScope', '$scope', '$state', 'programService', 'ngDialog', 'Notification'];

    function journalController($rootScope, $scope, $state, programService, ngDialog, Notification) {
        /**
         * Init global parameters
         */
        $rootScope.menuTab = 'programmes';
        $rootScope.isProgram = true;

        var programId = $state.params.id;

        /**
         * @ngdoc method
         * @name loadProgram
         * @methodOf program.controllers:journalController
         * @description
         * Load program data
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgram = function (programId) {
            programService.getProgram(programId).then(function (data) {
                $scope.program = data.program;
                $rootScope.isEasyProgram = $scope.program.isEasy ? 1 : 0;
            });
        };

        /**
         * @ngdoc method
         * @name loadProgramJournal
         * @methodOf program.controllers:journalController
         * @description
         * Load journal for api program
         *
         * @param {number} programId Id of program
         */
        $scope.loadProgramJournal = function (programId) {
            programService.getProgramJournals(programId).then(function (data) {
                $scope.journals = data.journals;
            });
        };

        /**
         * @ngdoc method
         * @name initProgramJournal
         * @methodOf program.controllers:journalController
         * @description
         * Init journal for api program
         *
         */
        $scope.initProgramJournal = function () {
            ngDialog.openConfirm({template: 'journal.reset', scope: $scope})
                .then(function () {
                    var patch = [{op: 'clear', path: '/journals'}];
                    programService.patchProgram($scope.program.id, patch).then(function () {
                        $scope.loadProgramJournal($scope.program.id);
                        Notification.success('Le journal du programme ' + $scope.program.label + ' a été réinitialisé');
                    });
                });

        };

        // Load program data & journal
        $scope.loadProgram(programId);
        $scope.loadProgramJournal(programId);

    }

    return journalController;
});
