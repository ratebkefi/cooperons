/**
 * @ngdoc controller
 * @name member.controllers:filleulController
 * @description
 * This file defines the home controller for admin space
 */
define([], function () {
    'use strict';

    filleulController.$inject = ['$scope', 'common', 'memberRepository'];

    function filleulController($scope, common, memberRepository) {

        common.setMenuTab('filleuls');

        $scope.filter = {};
        $scope.selected = {};

        /**
         * @ngdoc method
         * @name loadMemberFilleuls
         * @methodOf member.controllers:filleulController
         * @description
         * Retrieve user's filleuls
         */
        $scope.loadMemberFilleuls = function () {
            memberRepository.getMemberFilleuls($scope.filter).then(function (data) {
                $scope.filleuls = data.filleuls;

                angular.forEach($scope.filleuls, function (filleul) {
                    filleul.programs = filleul.programs.split('-');
                });

                $scope.nbreFilleuls = data.nbre_filleuls;
                $scope.totalMultipoints = data.total_multipoints;
                $scope.totalMultipointsFilleuls = data.total_multipoints_filleuls;

                if (!$scope.allYears) {
                    $scope.allYears = [];
                    $scope.allYears[0] = {id: 0, value: 'Année'};
                    var i = 0;
                    angular.forEach(data.listeDates, function (key, value) {
                        $scope.allYears[++i] = {id: value, value: value};
                    });
                    $scope.selected.year = $scope.allYears[0];
                }

                if (!$scope.allPrograms) {
                    $scope.allPrograms = [];
                    $scope.allPrograms[0] = {id: 0, value: 'Programme'};
                    i = 0;

                    angular.forEach(data.listePrograms, function (key, value) {
                        $scope.allPrograms[++i] = {id: value, value: key};

                    });

                    $scope.selected.program = $scope.allPrograms[0];
                }

            });
        };

        /**
         * @ngdoc method
         * @name filterFilleuls
         * @methodOf member.controllers:filleulController
         * @description
         * Filter filleul by year/program
         */
        $scope.filterFilleuls = function () {
            $scope.filter = {};

            if ($scope.selected) {

                if ($scope.selected.year.id !== 0) {
                    $scope.filter.year = $scope.selected.year.value;
                }

                if ($scope.selected.program.id !== 0) {
                    $scope.filter.programId = $scope.selected.program.id;
                }

                $scope.loadMemberFilleuls();
            }
        };

        /**
         * Load data
         */
        $scope.loadMemberFilleuls();
    }

    return filleulController;
});

