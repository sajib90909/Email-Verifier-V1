<?php
include 'pagename.php';
    class Session{
        public static function init(){
            session_start();
        }
        public static function set($key,$val){
            $_SESSION[$key] = $val;
        }
        public static function get($key){
            if(isset($_SESSION[$key])){

                return $_SESSION[$key];
            }else{
                return false;
            }
        }
        public static function checkSession(){
          global $login;
            self::init();
            if(self::get("login") == false){
                self::destroy();
                header("location:".$login);
            }
        }
        public static function checkSession_d(){
          global $login;
            self::init();
            if(self::get("login") == false){
                self::destroy();
                header("location:app/".$login);
            }
        }
        public static function memberSession(){
          global $login;
            self::init();
            if(self::get("memberlogin") == false){
                self::destroy();
                header("location:app/".$login);
            }
        }
        public static function destroy(){
          global $login;
            session_destroy();
            header("location:".$login);
        }
        public static function destroy_d(){
            global $login;
            session_destroy();
            header("location:app/".$login);
        }
    }
?>
