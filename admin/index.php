<?php
    // required version
    if (version_compare(PHP_VERSION, "5.3.0", "<")) exit("Panel requires PHP 5.3.0 or greater.");
    // panel folder
    $backend = 'admin';
    // Root directory
    define('ROOT', rtrim(dirname(__FILE__), '\\/'));
    // define access
    define('PANEL_ACCESS', true);
    // api
    define('API', ROOT.'/api');
    // templates
    define('TEMPLATES', ROOT.'/templates');
    // partials
    define('PARTIALS', ROOT.'/partials');
    // views
    define('VIEWS', ROOT.'/views');


    // Morfy paths
    define('ROOTBASE', rtrim(str_replace(array($backend), array(''), dirname(__FILE__)), '\\/'));
    define('LIBRARIES', ROOTBASE.'/libraries');
    define('STORAGE', ROOTBASE.'/storage');
    define('PAGES', ROOTBASE.'/storage/pages');
    define('BLOCKS', ROOTBASE.'/storage/blocks');
    define('SITE', ROOTBASE.'/config/site.yml');
    define('THEMES', ROOTBASE.'/themes');
    define('PLUGINS', ROOTBASE.'/plugins');
    // new folders
    define('BACKUPS', ROOTBASE.'/backups');
    define('PUBLICFOLDER', ROOTBASE.'/public');
    define('MEDIA', PUBLICFOLDER.'/media');
    define('UPLOADS', PUBLICFOLDER.'/uploads');
    define('CACHE', ROOTBASE.'/cache');


    // Morfy panel class/routes
    include_once(API.'/Morfy.panel.php');
    include_once(API.'/Morfy.routes.php');

    // check if exist this folders
    if(!Dir::exists(MEDIA)) Dir::create(MEDIA);
    if(!Dir::exists(UPLOADS)) Dir::create(UPLOADS);
    if(!Dir::exists(BACKUPS)) Dir::create(BACKUPS);
    
    date_default_timezone_set(Panel::$site['timezone']);
?>

