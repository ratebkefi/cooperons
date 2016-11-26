/**
 * @ngdoc service
 * @name member.services:memberService
 * @description
 * This file defines the memberService
 */
define([], function () {
    'use strict';

    /*@ngInject*/
    function memberService($q, common, memberRepository) {

        var service = {
            loadMemberDetails: loadMemberDetails
        };

        return service;

        /////////////////////

        /**
         * @ngdoc method
         * @name loadPartyDetails
         * @methodOf party.services:partyService
         * @description
         * Get data related to member {member, participates, college, parties}
         */
        function loadMemberDetails(member, participates, college, parties) {
            var promises = [];
            if (member) {
                promises.push(memberRepository.getMember());
            }
            if (participates) {
                promises.push(memberRepository.getMemberParticipates());
            }
            if (college) {
                promises.push(memberRepository.getMemberCollege());
            }
            if (parties) {
                promises.push(memberRepository.getMemberParties());
            }
            return $q.all(promises).then(function (data) {
                var memberData = [];
                var i = 0;
                if (member) {
                    memberData.member = data[i++].member;
                }
                if (participates) {
                    memberData.participates = data[i++].participates;
                }
                if (college) {
                    memberData.college = data[i++].college;
                }
                if (parties) {
                    memberData.parties = data[i].parties;
                }

                common.saveMemberData(memberData);
            });


        }
    }

    return memberService;
});
