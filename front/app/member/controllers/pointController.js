/**
 * @ngdoc controller
 * @name member.controllers:pointController
 * @description
 * This file defines the member point controller
 */
define([], function () {
    'use strict';

    pointController.$inject = ['$rootScope', '$scope', 'common', 'memberRepository', 'corporateRepository'];

    function pointController($rootScope, $scope, common, memberRepository, corporateRepository) {

        common.setMenuTab('points');

        $scope.filter = {};
        $scope.selected = {};

        /**
         * @ngdoc method
         * @name loadMemberPoints
         * @methodOf member.controllers:pointController
         * @description
         * Retrieve details of member's points
         */
        $scope.loadMemberPoints = function () {
            memberRepository.getMemberPoints($scope.filter).then(function (data) {
                $scope.allAvantages = data.allAvantages;
                $scope.allPoints = data.allPoints;
                $rootScope.tokenPlus = data.tokenPlus;
                $scope.totalAvantages = data.totalAvantages;
                $scope.totalPoints = data.totalPoints;

                if (!$scope.allYears) {
                    $scope.allYears = [];
                    $scope.allYears[0] = {id: 0, value: 'Année'};
                    var i = 0;

                    angular.forEach(data.allYears, function (key, value) {
                        $scope.allYears[++i] = {id: value, value: value};
                    });

                    $scope.selected.year = $scope.allYears[0];
                }

                if (!$scope.allPrograms) {
                    $scope.allPrograms = [];
                    $scope.allPrograms[0] = {id: 0, value: 'Programme'};
                    i = 0;

                    angular.forEach(data.allPrograms, function (key, value) {
                        $scope.allPrograms[++i] = {id: value, value: key};

                    });

                    $scope.selected.program = $scope.allPrograms[0];
                }

            });
        };

        /**
         * @ngdoc method
         * @name loadMemberCollege
         * @methodOf member.controllers:pointController
         * @description
         * Load last member college
         */
        $scope.loadMemberCollege = function () {
            memberRepository.getMemberCollege().then(function (data) {
                $scope.member.college = data.college;
                if (data.college) {
                    $scope.corporateCollege = data.college.corporate;
                    if (data.college.corporate) {
                        corporateRepository.getCorporateDelegate(data.college.corporate.siren).then(function (data) {
                            $scope.delegate = data.delegate;
                        });
                    }
                }
            });
        };

        /**
         * @ngdoc method
         * @name filterPoints
         * @methodOf member.controllers:pointController
         * @description
         * Filter points by year/program
         */
        $scope.filterPoints = function () {
            $scope.filter = {};
            if ($scope.selected) {
                if ($scope.selected.year.id !== 0) {
                    $scope.filter.year = $scope.selected.year.value;
                }
                if ($scope.selected.program.id !== 0) {
                    $scope.filter.programId = $scope.selected.program.id;
                }
                $scope.loadMemberPoints();
            }
        };

        /**
         * Load data
         */
        $scope.loadMemberPoints();
        $scope.loadMemberCollege();
    }

    return pointController;
});
