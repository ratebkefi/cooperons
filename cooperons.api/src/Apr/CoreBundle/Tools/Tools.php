<?php

namespace Apr\CoreBundle\Tools;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Tools
{
    public static $myDate = '2014-01-01';

    /**
     * Initialise user session
     *
     * @param $user      Instance of user object to connect
     * @param $request   Symfony request object
     *
     * @static
     * @return void
     */
    public static function connectUser($user, $request)
    {
        // Create a new token
        $token = new UsernamePasswordToken($user, $user->getPassword(), $user->getSalt(), $user->getRoles());
        // Retrieve the security context and set the token
        $session = $request->getSession();
        $session->set('_security_main', serialize($token));
    }

    public static function uncamelize($camel, $splitter = "_")
    {
        $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter . '$0', $camel));
        return strtolower($camel);
    }

    public static function simpleName($sString)
    {
        //Conversion des majuscules en minuscule
        $string = strtolower(htmlentities($sString));
        //Listez ici tous les balises HTML que vous pourriez rencontrer
        $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml|grave);/", "$1", $string);
        //Tout ce qui n'est pas caractère alphanumérique  -> _
        $string = preg_replace("/([^a-z0-9]+)!./", "_", html_entity_decode($string));
        return $string;
    }

    public static function displayMonth($month)
    {
        switch ($month) {
            case 1: return 'Janvier';
                break;
            case 2: return 'Février';
                break;
            case 3: return 'Mars';
                break;
            case 4: return 'Avril';
                break;
            case 5: return 'Mai';
                break;
            case 6: return 'Juin';
                break;
            case 7: return 'Juillet';
                break;
            case 8: return 'Août';
                break;
            case 9: return 'Septembre';
                break;
            case 10: return 'Octobre';
                break;
            case 11: return 'Novembre';
                break;
            case 12: return 'Décembre';
                break;
        }
    }

    public static function getQuarter($date){
        return ceil($date->format('m')/3);
    }

    public static function DateTime($el='now'){
        $dateNow = new \DateTime('now');
       $date = self::$myDate.$dateNow->format('H:i:s');
        $dateNow = new \DateTime($date);
        if($el == 'now') return $dateNow;
        if($el == 'Y') return $dateNow->format('Y');
        if($el == 'm') return $dateNow->format('m');
        if($el == 'Q') return self::getQuarter($dateNow);
        if($el == 'd') return $dateNow->format('d');
        // Par défaut: modify
        return $dateNow->modify($el);
    }

    /**
     * Return the first day of the Week/Month/Quarter/Year that the
     * current/provided date falls within
     *
     * @param string   $period The period to find the first day of. ('year', 'quarter', 'month', 'week')
     * @param \DateTime $date   The date to use instead of the current date
     *
     * @return \DateTime
     * @throws \InvalidArgumentException
     */
    public static function firstDayOf($period, \DateTime $date = null)
    {
        $period = strtolower($period);
        $validPeriods = array('year', 'quarter', 'month', 'week');

        if ( ! in_array($period, $validPeriods))
            throw new \InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

        $newDate = ($date === null) ? Tools::DateTime() : clone $date;

        switch ($period) {
            case 'year':
                $newDate->modify('first day of january ' . $newDate->format('Y'));
                break;
            case 'quarter':
                $month = $newDate->format('n') ;

                if ($month < 4) {
                    $newDate->modify('first day of january ' . $newDate->format('Y'));
                } elseif ($month > 3 && $month < 7) {
                    $newDate->modify('first day of april ' . $newDate->format('Y'));
                } elseif ($month > 6 && $month < 10) {
                    $newDate->modify('first day of july ' . $newDate->format('Y'));
                } elseif ($month > 9) {
                    $newDate->modify('first day of october ' . $newDate->format('Y'));
                }
                break;
            case 'month':
                $newDate->modify('first day of this month');
                break;
            case 'week':
                $newDate->modify(($newDate->format('w') === '0') ? 'monday last week' : 'monday this week');
                break;
        }

        return $newDate->setTime(0,0);
    }

    public static function initMenu($menu, $user, $legend){
        $roles = $user->getRoles();
        $navBar = $menu->initMemberMenu($legend, in_array('ROLE_AE', $roles));
        $menu->setActive(strtoupper(str_replace(' ','_',$legend)));
        return $navBar;
    }

    public static function processQuery($container, $url, $method, $data=NULL, $apikey){
        if (!function_exists('curl_init')){
            die('Sorry cURL is not installed!');
        }

        $baseUrl = $container->getParameter('api.coopPlus');
        $baseUrl.= $url;
        $data = ($data)?json_encode($data):NULL;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$baseUrl);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/json;charset=UTF-8',
            'Content-Length:'.strlen($data),
            'apikey:'.$apikey
        ));
        if(strtoupper($method) == 'POST' || strtoupper($method) == 'PUT'){
            if(strtoupper($method) == 'POST'){
                curl_setopt($ch,CURLOPT_POST, 1);
            }else{
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            }
            curl_setopt($ch,CURLOPT_POSTFIELDS,  $data);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        curl_close ($ch);
        return $response;
    }

    public static function getLabelOperationById($i)
    {
        if ($i > 4)
            $i = $i % 4;
        switch ($i) {
            case 1;
                return "Hiver";
                break;
            case 2;
                return "Printemps";
                break;
            case 3;
                return "Été";
                break;
            case 4;
                return "Automne";
                break;
        }
    }

    // input: My Test Email <some.test.email@somewhere.net>
    public static function get_displayname_from_rfc_email($rfc_email_string) {
        // match all words and whitespace, will be terminated by '<'
        $name       = preg_match('/[^<]*/', $rfc_email_string, $matches);
        $matches[0] = trim($matches[0]);
        return $matches[0];
    }
    // Output: My Test Email

    public static function  get_email_from_rfc_email($rfc_email_string) {
        // extract parts between the two parentheses
        $mailAddress = preg_match('/(?:<)(.+)(?:>)$/', $rfc_email_string, $matches);
        return isset($matches[1])?$matches[1]:null;
    }
    // Output: some.test.email@somewhere.net

    public static function get_array_from_multiple_rfc_email($rfc_emails_string) {
        $result = array();

        foreach(explode(",", $rfc_emails_string) as $rfc_email_string) {
            $email =  self::get_email_from_rfc_email($rfc_email_string);
            if(!$email) {
                // Pas au format rfc ...
                $email = $rfc_email_string;
                $firstName = '';
                $lastName = '';
            } else {
                $name = self::get_displayname_from_rfc_email($rfc_email_string);
                $arr = explode(" ", trim($name));
                $firstName = $arr[0];
                unset($arr[0]);
                $lastName = implode(" ", $arr);
            }
            array_push($result, array(
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
            ));
        }
        return $result;
    }
}
