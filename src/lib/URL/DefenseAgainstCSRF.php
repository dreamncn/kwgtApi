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
        return Session::getInstance()->get("csrftoken")===Cookie::getInstance()->get("csrftoken");
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
        Session::getInstance()->set("csrftoken",$token,20*60);
        return $token;
    }

    public function setCSRFToken($session){
       return $this->getCSRFToken($session,time());
    }
}
