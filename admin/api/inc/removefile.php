<?php defined('PANEL_ACCESS') or die('No direct script access.');




/*    REMOVE FILE PAGES/BLOCKS
--------------------------------------*/



/*
* @name   Remove File
* @desc   Remove File ( :any use base64_encode remenber decode file)
*/
$p->route('/action/removefile/(:any)/(:any)', function($token,$file) use($p){
	if(Session::exists('user')){
		if (Token::check($token)) {
			$filename = base64_decode($file);
			// check url
			$url = '';
			if(preg_match('/pages/', $filename)) $url = 'pages';
			else if(preg_match('/blocks/', $filename)) $url = 'blocks';
			// delete file
			File::delete($filename);
			// set notification
			$p->setMsg($p::$lang['Success_remove']);
			// redirect to edit index
			Request::redirect($p->url().'/'.$url);
		}else{
			die('crsf Detect');
		}
	}
});






/*    REMOVE FILE UPLOADS
-----------------------------*/


/*
* @name   Uploads removefile
* @desc   New file ( :any use base64_encode remenber decode file)
*/
$p->route('/action/uploads/removefile/(:any)/(:any)', function($token,$file) use($p){
  if(Session::exists('user')){
	if (Token::check($token)) {
		// decode
		$path = base64_decode($file);
		File::delete($path);
		// set notification
		$p->setMsg($p::$lang['Success_remove']);
		// redirect to edit index
		Request::redirect($p->url().'/uploads');
	}else{
	  die('crsf Detect');
	}
  }
});







/*    REMOVE FILE MEDIA
-----------------------------*/


/*
* @name   Media removefile
* @desc   Remove file on media ( :any use base64_encode remenber decode file)
*/
$p->route('/action/media/removefile/(:any)/(:any)', function($token,$file) use($p){
  if(Session::exists('user')){
	if (Token::check($token)) {
	  // get json file fow with and height
	  $jsonFile = PUBLICFOLDER.'/media/mdb.json';
	  $json = json_decode(File::getContent($jsonFile),true);
	  // if remove thumb and dir unlik json file
	  File::delete(ROOTBASE.$json[$file]['thumb']);
	  Dir::delete(ROOTBASE.$json[$file]['images']);
	  unset($json[$file]);
	  if(File::setContent($jsonFile,json_encode($json))){
		// set notification
		$p->setMsg($p::$lang['Success_remove']);
		Request::redirect($p->Url().'/media');
	  }
	}else{
	  die('crsf Detect');
	}
  }
});






/*
* @name   Media removefile
* @desc   Remove file on media ( :any use base64_encode remenber decode file)
*/
$p->route('/action/media/uploads/removefile/(:any)/(:any)', function($id,$file) use($p){
  if(Session::exists('user')){
	  // remove file
	  File::delete(base64_decode($file));
	  // set notification
	  $p->setMsg($p::$lang['Success_remove']);
	  // redirect
	  Request::redirect($p->Url().'/media/uploads/'.$id);
  }
});


/*
* @name   Themes removefile
* @desc   Remove file on themes ( :any use base64_encode remenber decode file)
*/
$p->route('/action/themes/removefile/(:any)/(:any)', function($id,$file) use($p){
	if(Session::exists('user')){
		// function to redirect
		$url = '';
		if(preg_match('/css/i', base64_decode($file))) $url = 'stylesheets';
		else if(preg_match('/js/i', base64_decode($file))) $url = 'javascript';
		else $url = 'templates';
		// delete file
		File::delete(base64_decode($file));
		// set notification
		$p->setMsg($p::$lang['Success_remove']);
		// redirect
		Request::redirect($p->Url().'/'.$url);
	}
});


/*
* @name   Backups removefile
* @desc   Remove file on media ( :any use base64_encode remenber decode file)
*/
$p->route('/action/backups/removefile/(:any)/(:any)', function($id,$file) use($p){
	if(Session::exists('user')){
		// delete file
		File::delete(base64_decode($file));
		// set notification
		$p->setMsg($p::$lang['Success_remove']);
		// redirect
		Request::redirect($p->Url().'/backups');
	}
});