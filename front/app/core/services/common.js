/**
 * This file defines the back routes
 *
 * @category service
 * @package config
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 0.0.1
 * @since File available since Release 0.0.1
 */

define([], function () {
    'use strict';

    common.$inject = ['$rootScope', '$state', '$window', 'config'];

    function common($rootScope, $state, $window, config) {
        var service = {
            getBackRoute: getBackRoute,
            getUploadedFile: getUploadedFile,
            redirectToFAQ: redirectToFAQ,
            broadcast: broadcast,
            onEvent: onEvent,
            loadSession: loadSession,
            checkSecurity: checkSecurity,
            saveMemberData: saveMemberData,
            getMemberData: getMemberData,
            saveAuthenticationToken: saveAuthenticationToken,
            saveMemberSession: saveMemberSession,
            saveMenuStatus: setMenuStatus,
            getMenuStatus: getMenuStatus,
            disableNextLoading: disableNextLoading,
            concatQueryParams: concatQueryParams,
            setMenuTab: setMenuTab,
            dispatch: dispatch,
            redirect: redirect,
            openTab: openTab,
            logout: logout,
            getObjectById: getObjectById
        };

        return service;

        /**
         * Return backend route with parameters
         *
         * @param routeName
         * @param params
         * @returns {string}
         */
        function getBackRoute(routeName, params) {
            if (backRoutes[routeName] === undefined) {
                toastr.error('La route «' + routeName + '» est non définie', 'Exception');
                throw 'Cooperons Exception: undefined route «' + routeName + '»';
            }
            var route = config.backBaseUrl + backRoutes[routeName];
            var queryParams = {};
            angular.forEach(params, function (value, key) {
                if (route.indexOf('__' + key + '__') !== -1) {
                    route = route.replace('__' + key + '__', value);
                } else {
                    queryParams[key] = value;
                }
            });
            return concatQueryParams(route, queryParams);
        }

        /**
         * Return uploaded file path
         *
         * @param fileName
         * @returns {string}
         */
        function getUploadedFile(fileName) {
            return config.uploadedFilesUrl + '/' + fileName;
        }

        function redirectToFAQ(token, isEasyProgram) {
            var faq = '';
            if ($rootScope.space == 'program') {
                faq = getBackRoute('faq_program', {isEasy: isEasyProgram, token: token});
            } else {
                faq= getBackRoute('faq_member', {token: token});
            }
            $window.location = faq;
        }

        /**
         * @ngdoc method
         * @name broadcast
         * @methodOf core.services:common
         * @description
         * Broadcast event
         *
         * @returns {boolean} A status of checking
         */
        function broadcast() {
            return $rootScope.$broadcast.apply($rootScope, arguments);
        }


        function onEvent(event, func) {
            $rootScope.$on(event, func);
        }

        /**
         * @ngdoc method
         * @name loadSession
         * @methodOf core.services:common
         * @description
         * Check if user is authenticated
         *
         * @returns {boolean} A status of checking
         */
        function loadSession() {
            var auth = {
                token: $window.localStorage.getItem('jwt_token'),
                roles: $window.localStorage.getItem('roles'),
                memberToken: $window.localStorage.getItem('member_jwt_token'),
                memberRoles: $window.localStorage.getItem('member_roles')
            };

            if (auth.token && auth.roles) {
                $rootScope.auth = auth;
                if (auth.roles.indexOf('ROLE_SUPER_ADMIN') !== -1 || auth.roles.indexOf('ROLE_ADMIN') !== -1) {
                    $rootScope.role = 'ADMIN';
                } else if (auth.roles.indexOf('ROLE_USER') !== -1) {
                    $rootScope.role = 'MEMBER';
                } else {
                    $rootScope.role = 'ANONYMOUS';
                }
            } else {
                $rootScope.role = 'ANONYMOUS';
            }

            $rootScope.authenticated = $rootScope.role !== 'ANONYMOUS';
        }

        /**
         * @ngdoc method
         * @name checkSecurity
         * @methodOf core.services:common
         * @description
         * Access control
         */
        function checkSecurity(space) {
            var authorized = false;
            switch (space) {
                case 'member':
                    if ($rootScope.role === 'MEMBER' || $rootScope.auth.memberToken && $rootScope.role === 'ADMIN') {
                        authorized = true;
                    }
                    break;
                case 'admin':
                    if ($rootScope.role === 'ADMIN') {
                        authorized = true;
                    }
                    break;
                default:
                    authorized = true;
                    break;
            }

            authorized ? $rootScope.space = space : dispatch();
        }

        /**
         * @ngdoc method
         * @name saveMember
         * @methodOf core.services:common
         * @description
         * Save member in $rootScope
         *
         * @param {object} memberData
         */
        function saveMemberData(memberData) {
            if (memberData.member) {
                $rootScope.member = memberData.member;
            }
            if (memberData.college) {
                $rootScope.member.college = memberData.college;
            }
            if (memberData.parties) {
                $rootScope.parties = memberData.parties;
            }
            if (memberData.participates) {
                $rootScope.member.participates = memberData.participates;
                angular.forEach(memberData.participates, function (participate) {
                    if (participate.program.id == config.idProgramPlus) {
                        $rootScope.tokenPlus = participate.token;
                        // TODO review
                        $rootScope._token = participate.token;
                    } else if (participate.program.id == config.idProgramAE) {
                        $rootScope.tokenAE = participate.token;
                    }
                });
            }

        }


        /**
         * @ngdoc method
         * @name saveMember
         * @methodOf core.services:common
         * @description
         * get member from $rootScope
         *
         * @returns {object} member
         */
        function getMemberData() {
            return {
                member: $rootScope.member,
                parties: $rootScope.parties,
                tokenPlus: $rootScope.tokenPlus,
                tokenAE: $rootScope.tokenAE
            };
        }

        /**
         * @ngdoc method
         * @name saveAuthenticationToken
         * @methodOf core.services:common
         * @description
         * Save token of authenticated user
         *
         * @param {string} token A token of member
         * @param {Array} roles A roles of member
         * @param {boolean} redirect Redirect user if redirect's value is true
         */
        function saveAuthenticationToken(token, roles, redirect) {
            $window.localStorage.clear();
            $window.localStorage.setItem('jwt_token', token);
            $window.localStorage.setItem('roles', roles);
            loadSession();
            if (redirect) dispatch();
        }

        /**
         * @ngdoc method
         * @name saveMemberSession
         * @methodOf core.services:common
         * @description
         * Save member session
         *
         * @param {string} token A token of member
         * @param {Array} roles A roles of member
         */
        function saveMemberSession(token, roles) {
            $window.localStorage.setItem('member_jwt_token', token);
            $window.localStorage.setItem('member_roles', roles);
        }

        function setMenuStatus(isReduced) {
            $window.localStorage.setItem('menu-reduced', isReduced);
        }

        function getMenuStatus() {
            return $window.localStorage.getItem('menu-reduced') == 'true';
        }

        function disableNextLoading() {
            $rootScope.pendingRequests--;
        }

        function concatQueryParams(url, queryParams) {
            if (Object.keys(queryParams).length === 0) {
                return url;
            }
            if (url.indexOf('?') === -1) {
                url += '?';
            } else if (url.substring(0, url.length - 1) !== '&') {
                url += '&';
            }

            angular.forEach(queryParams, function (value, key) {
                url += key + '=' + value;

            });


            if (url.substring(0, url.length - 1) === '&') {
                url = url.slice(0, -1);

            }
            return url;
        }

        function setMenuTab(menuTab) {
            $rootScope.menuTab = menuTab;
        }

        /**
         * @ngdoc method
         * @name dispatch
         * @methodOf core.services:common
         * @description
         * Redirect user for his space
         */
        function dispatch() {
            $rootScope.spaceReady = false;
            if (!$rootScope.authenticated || $rootScope.role === 'ANONYMOUS') {
                redirect('user.login');
            } else if ($rootScope.role === 'ADMIN') {
                redirect('admin.home');
            } else if ($rootScope.role === 'MEMBER') {
                redirect('member.home');
            }
        }

        /**
         * @ngdoc method
         * @name redirect
         * @methodOf core.services:common
         * @description
         * Redirect with absolute url
         *
         * @param {string} state A state redirection
         */
        function redirect(state) {
            $rootScope.spaceReady = false;
            $window.location = $state.href(state, {}, {absolute: true});
        }

        /**
         * @ngdoc method
         * @name redirect
         * @methodOf core.services:common
         * @description
         * Redirect with absolute url
         *
         * @param {string} state A state redirection
         */
        function openTab(state) {
            $window.open($state.href(state, {}, {absolute: true}));
        }

        /**
         * @ngdoc method
         * @name logout
         * @methodOf core.services:common
         * @description
         * Logout: delete token
         */
        function logout() {
            if ($rootScope.auth && $rootScope.auth.openSession && $rootScope.space === 'member') {
                $window.localStorage.setItem('member_jwt_token', '');
                $window.localStorage.setItem('open_session', '');
            } else {
                $window.localStorage.clear();
                $rootScope.authenticated = false;
            }
            dispatch();
        }

        /**
         * @ngdoc method
         * @name getObjectById
         * @methodOf core.services:common
         * @description
         * Retrieve object by id
         */
        function getObjectById(list, id) {
            var object = null;
            angular.forEach(list, function (objectIterator) {
                if (objectIterator.id == id) {
                    object = objectIterator;
                }
            });
            return object;
        }
    }

    return common;
});