/**
 * @ngdoc service
 * @name corporate.repositories:collegeRepository
 * @description
 * This file defines the collaboratorRepository
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function collegeRepository($q, Restangular) {

        var repository = {
            patchCollege: patchCollege
        };

        return repository;

        /**
         * @ngdoc method
         * @name patchCollege
         * @methodOf corporate.repositories:collegeRepository
         * @description
         * Partial updating of college
         *
         * @param {number} id Id of college
         * @param {object} patch Data to patching
         *
         * @returns {object} Collaborator data
         */
        function patchCollege(id, patch) {
            return Restangular.one('colleges', id).patch(patch).then(
                function (response) {
                    return response;
                },
                function (fallback) {
                    return $q.reject(fallback);
                });
        }
    }

    return collegeRepository;
});