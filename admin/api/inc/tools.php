<?php

defined('PANEL_ACCESS') or die('No direct script access.');

/*    TOOLS / CACHE
---------------------------------*/

/*
* @name   Clear cache
* @desc   Clear cache on click
*/
$p->route('/action/clearCache/(:any)/', function ($token) use ($p) {
        if (Session::exists('user')) {
            if (Token::check($token)) {
                if(Dir::exists(CACHE.'/doctrine/'))Dir::delete(CACHE.'/doctrine/');
                if(Dir::exists(CACHE.'/doctrine/'))Dir::delete(CACHE.'/fenom/');
                // set notification
                $p->setMsg($p::$lang['Success_cache']);
                // redirect
                Request::redirect($p->Url().'/pages');
            } else {
                die('Crsf detect !');
            }
        } else {
            Request::redirect($p::$site['site_url'].'/'.$p::$site['backend_folder']);
        }
    });
