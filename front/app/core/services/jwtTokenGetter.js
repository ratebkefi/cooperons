/**
 * This file defines the jwt token getter
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

    jwtTokenGetter.$inject = ['$rootScope'];

    function jwtTokenGetter($rootScope) {
        if (!$rootScope.auth) {
            return null;
        } else if ($rootScope.auth.memberToken && $rootScope.space !== 'admin') {
            return $rootScope.auth.memberToken;
        } else {
            return $rootScope.auth.token;
        }
    }

    return jwtTokenGetter;
});