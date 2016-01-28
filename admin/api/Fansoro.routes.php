<?php

defined('PANEL_ACCESS') or die('No direct script access.');

// new panel
$p = new Panel();

/*  PAGES AND BLOCKS SECTION GOES HERE
------------------------------------------------*/
include_once 'inc/pages.php';

/*  ACTIONS GOES HERE
------------------------------------------------*/
$inc = array(
    'search', 'preview', 'edit',
    'newfile', 'newfolder', 'rename',
    'removefile', 'removefolder', 'tools',
);
foreach ($inc as $inc_file) {
    include_once "inc/$inc_file.php";
}

/*
* @name   Logout
* @desc   rediterct to hombe url
*/
$p->route('/action/logout', function () use ($p) {
        if (Session::exists('user')) {
            Session::delete('user');
            Session::destroy();
            Request::redirect($p::$site['site_url']);
        }
    });

// start
$p->lauch();
