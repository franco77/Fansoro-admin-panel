<?php defined('PANEL_ACCESS') or die('No direct script access.');




/*
* @name   Remove folder
* @desc   Remove folder ( :any use base64_encode remenber decode file)
*/
$p->route('/action/removefolder/(:any)/(:any)', function($token,$file) use($p){
	if(Session::exists('user')){
		if (Token::check($token)) {
			// decode file
			$filename = base64_decode($file);
			// redirect to edit index
			$url = str_replace(STORAGE.'/', '', $filename);
			$url = str_replace('/'.File::name($url).'.'.File::ext($url),'',$url);
			// check url
			if($url == 'blocks') $url = 'blocks'; else $url = 'pages';
			Dir::delete(STORAGE.'/'.$filename);
			// set notification
			$p->setMsg($p::$lang['Success_remove']);
			// redirect to edit index
			Request::redirect($p->url().'/'.$url);
		}else{
			die('crsf Detect');
		}
	}
});



/*
* @name   Uploads rename
* @desc   New folder ( :any use base64_encode remenber decode file)
*/
$p->route('/action/uploads/removefolder/(:any)/(:any)', function($token,$file) use($p){
	if(Session::exists('user')){
		if (Token::check($token)) {
			// decode file
			$path = base64_decode($file);
			Dir::delete(PUBLICFOLDER.'/'.$path);
			// set notification
			$p->setMsg($p::$lang['Success_remove']);
			// redirect to edit index
			Request::redirect($p->url().'/uploads');
		}else{
			die('crsf Detect');
		}
	}
});

