/**
 * @ngdoc service
 * @name member.repositories:memberRepository
 * @description
 * This file defines the memberRepository
 */
define([], function () {
    'use strict';

    /*@ngInject*/
    function memberRepository($q, Restangular, $rootScope, $window, config) {

        var baseMember = Restangular.one('member');

        var repository = {
            getMember: getMember,
            getMemberFilleuls: getMemberFilleuls,
            getMemberPoints: getMemberPoints,
            getMemberContracts: getMemberContracts,
            getMemberParties: getMemberParties,
            getMemberCollaborators: getMemberCollaborators,
            getMemberColleges: getMemberColleges,
            getMemberCollege: getMemberCollege,
            getMemberParticipates: getMemberParticipates,
            getMemberAutoEntrepreneur: getMemberAutoEntrepreneur

        };

        return repository;


        /**
         * @ngdoc method
         * @name getMember
         * @methodOf member.repositories:memberRepository
         * @description
         * Retrieve member
         *
         * @returns {object} Member data
         */
        function getMember() {
            return baseMember.getList().then(
                function (response) {

                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberFilleuls
         * @methodOf member.repositories:memberRepository
         * @description
         * Retrieve member's filleuls
         *
         * @param {sitring} filter Filter key
         *
         * @returns {object} Member data
         */
        function getMemberFilleuls(filter) {
            return baseMember.all('filleuls').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberPoints
         * @methodOf member.repositories:memberRepository
         * @description
         * Retrieve member's points
         *
         * @param {sitring} filter Filter key
         *
         * @returns {object} Points data
         */
        function getMemberPoints(filter) {
            return baseMember.all('points').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberContracts
         * @methodOf member.repositories:memberRepository
         * @description
         * Retrieve member contracts
         *
         * @param {sitring} filter Filter key
         *
         * @returns {object} Contracts data
         */
        function getMemberContracts(filter) {
            return baseMember.all('contracts').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberParties
         * @methodOf member.repositories:memberRepository
         * @description
         * Retrieve member parties
         *
         * @returns {object} Corporates data
         */
        function getMemberParties() {
            return baseMember.all('parties').getList().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberCollaborators
         * @methodOf member.repositories:memberRepository
         * @description
         * Retrieve member collaborators
         *
         * @param {object} filter Filter by {party_id}
         *
         * @returns {object} Collaborators data
         */
        function getMemberCollaborators(filter) {
            return baseMember.all('collaborators').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberCollege
         * @methodOf member.repositories:memberRepository
         * @description
         * Get member college
         *
         * @returns {object} College data
         */
        function getMemberCollege() {
            return baseMember.one('college').get().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberColleges
         * @methodOf member.repositories:memberRepository
         * @description
         * Get all member colleges
         *
         * @param {sitring} siren Corporate siren
         *
         * @returns {object} Colleges data
         */
        function getMemberColleges(siren) {
            return baseMember.all('colleges').getList({siren: siren}).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberParticipates
         * @methodOf member.repositories:memberRepository
         * @description
         * Get member participates
         *
         * @param {sitring} filter filter key
         *
         * @returns {object} Participates data
         */
        function getMemberParticipates(filter) {
            return baseMember.all('participates').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }

        /**
         * @ngdoc method
         * @name getMemberAutoEntrepreneur
         * @methodOf member.repositories:memberRepository
         * @description
         * Get member autoEntrepreneur
         *
         * @returns {object} AutoEntrepreneur data
         */
        function getMemberAutoEntrepreneur() {
            return baseMember.one('autoentrepreneur').get().then(
                function (response) {
                    return response.data;
                },
                function (fallback) {
                    return $q.reject(fallback);
                }
            );
        }
    }

    return memberRepository;
});