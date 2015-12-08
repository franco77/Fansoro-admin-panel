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
                $cache = File::scan(CACHE.'/fenom', 'php');
                foreach ($cache as $item) {
                    File::delete($item);
                }
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
