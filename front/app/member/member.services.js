/**
 * This file defines the list services of member module
 *
 * @param {service} memberService
 * @returns {String}
 */
define([
    './repositories/memberRepository',
    './services/memberService'
], function (memberRepository, memberService) {
    'use strict';
    var servicesModuleName = "member.services";
    angular.module(servicesModuleName, [])
        /* View handlers */
        .factory("memberRepository", memberRepository)
        .factory("memberService", memberService);

    return servicesModuleName;
});