/**
 * This file defines the routes of main module
 *
 * @returns {String}
 */
define([],
    function () {
        'use strict';

        var routesModuleName = 'main.routes';

        angular.module(routesModuleName, ['ui.router'])
            .config(['$stateProvider', function ($stateProvider) {
                $stateProvider
                    .state('spaceMember', {
                        abstract: true,
                        template: '<div ui-view></div>',
                        resolve: {
                            memberData: /*@ngInject*/function (common, memberService) {
                                var savedMemberData = common.getMemberData();
                                if (savedMemberData.member) {
                                    return savedMemberData;
                                } else {
                                    return memberService.loadMemberDetails(true, true, true, true).then(function () {
                                        return common.getMemberData();
                                    });
                                }
                            }
                        }
                    })
                    .state('admin', {
                        url: '/admin',
                        template: '<div ui-view></div>',
                        abstract: true,
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('admin');
                            }
                        }
                    })
                    .state('member', {
                        url: '/member',
                        template: '<div ui-view></div>',
                        abstract: true,
                        parent: 'spaceMember',
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('member');
                            }
                        }
                    })
                    .state('program', {
                        url: '/program',
                        template: '<div ui-view></div>',
                        abstract: true,
                        parent: 'spaceMember',
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('member');
                            }
                        }
                    })
                    .state("party", {
                        url: '/party',
                        template: '<div ui-view></div>',
                        abstract: true,
                        parent: 'spaceMember',
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('member');
                            }
                        }
                    })
                    .state(
                    "corporate", {
                        url: '/corporate',
                        template: '<div ui-view></div>',
                        abstract: true,
                        parent: 'spaceMember',
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('member');
                            }
                        }
                    })
                    .state(
                    "autoEntrepreneur", {
                        url: '/autoentrepreneur',
                        template: '<div ui-view></div>',
                        abstract: true,
                        parent: 'spaceMember',
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('member');
                            }
                        }
                    })
                    .state(
                    "contract", {
                        url: '/contract',
                        template: '<div ui-view></div>',
                        abstract: true,
                        parent: 'spaceMember',
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('member');
                            }
                        }
                    })
                    .state(
                    "mandataire", {
                        url: '/mandataire',
                        template: '<div ui-view></div>',
                        abstract: true,
                        parent: 'spaceMember',
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('member');
                            }
                        }
                    })
                    .state('public', {
                        params: {
                            token: null
                        },
                        url: '/public',
                        template: '<div ui-view></div>',
                        abstract: true,
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('public');
                            }
                        }
                    })
                    .state('user', {
                        url: '',
                        template: '<div ui-view></div>',
                        abstract: true,
                        resolve: {
                            security: /*@ngInject*/function (common) {
                                return common.checkSecurity('user');
                            }
                        }
                    })
                    .state('prototype', {
                        url: '/prototype',
                        template: '<div ui-view></div>',
                        abstract: true
                    })
                    .state('logout', {
                        url: '/logout',
                        controller: /*@ngInject*/function (common) {
                            common.logout();
                        }
                    })
                    .state('otherwise', {
                        url: '/{str:.*}',
                        controller: /*@ngInject*/function (common) {
                            common.dispatch();
                        }
                    });
            }]);
        return routesModuleName;
    }
);

