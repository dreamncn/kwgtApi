<?php


namespace app\lib\URL;

use app\vendor\web\Cookie;
use app\vendor\web\Request;
use app\vendor\web\Response;
use app\vendor\web\Session;
use http\Header;

/**
 * Class DefenseAgainstCSRF
 * @package Security\URLSecurity
 */
class DefenseAgainstCSRF
{
    /**
     * DefenseAgainstCSRF constructor.
     */
    public function __construct()
    {
    }


    public function verifyCSRFToken()
    {
        $headers=Request::getHeaderValue("csrfToken");
        if($headers){
            if($headers==Cookie::getInstance()->get("csrftoken")){
                return true;
            }
        }
        return false;
    }


    /**
     * @param $session
     * @param $salt
     * @return string
     */
    private function getCSRFToken($session, $salt)
    {
        $token=md5(md5($session.'|'.$salt).'|'.$salt);
        Cookie::getInstance()->set("csrftoken",$token);
        return $token;
    }

    public function setCSRFToken($session){
       return $this->getCSRFToken($session,time());
    }
}
