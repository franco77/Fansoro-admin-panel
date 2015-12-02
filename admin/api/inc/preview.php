<?php defined('PANEL_ACCESS') or die('No direct script access.');




/*    DOWNLOAD FILES
-----------------------------*/


/*
* @name   Download files
*/
$p->route(array('/action/backups/download/(:any)/(:any)'), function($token,$file) use($p){
	if(Session::exists('user')){
		if (Token::check($token)) {
			$path = base64_decode($file);
			$download = str_replace(ROOTBASE,'',$path);
			Request::redirect($p::$site['site_url'].$download);
		}else{
			die('crsf detect!');
		}
	}
});







/*    PREVIEW PAGES
-----------------------------*/


/*
* @name   Preview
* @desc   Open preview on blank page
*/
$p->route(array('/action/preview/(:any)'), function($file) use($p){
		// remove dir
		$link = str_replace(PAGES, '', base64_decode($file));
		// remove .md
		$link = str_replace('.md', '', $link);
		Request::redirect($p::$site['site_url'].$link);
});






/*    PREVIEW UPLOADS
-----------------------------*/


/*
* @name   Preview
* @desc   Open preview on blank page
*/
$p->route('/action/uploads/preview/(:any)', function($file) use($p){
		// remove dir
		$link = str_replace(UPLOADS, '', base64_decode($file));
		$link = str_replace('\\', '/', $link);
		$link = str_replace('//', '/', $link);
		// check mime types
		$template = '';
		// decode file
		$path = base64_decode($file);
		$link = Url::sanitizeURL($link);
		// check mime types
		if(File::mime($path)){
			if(File::ext($path) == 'jpg'  || File::ext($path) == 'JPG'  ||
				File::ext($path) == 'png'   || File::ext($path) == 'PNG'  ||
				File::ext($path) == 'jpeg'  || File::ext($path) == 'JPEG' ||
				File::ext($path) == 'gif'){
				// get image size
				list($width, $height) = getimagesize($path);
				// image template
				$template = '
					<div class="col-lg-6">
						<div class="thumbnail">
							<img class="expand img-responsive" src="'.$p::$site['site_url'].'/public/uploads/'.$link.'"/>
						</div>
					</div>
					<div class="col-lg-6">
						<ul class="list-group">
							<li class="list-group-item"><b>Filename: </b>'.File::name($path).'</li>
							<li class="list-group-item"><b>Extension: </b>'.File::ext($path).'</li>
							<li class="list-group-item"><b>Size: </b>'.$width.'x'.$height.'px</li>
							<li class="list-group-item"><b>Markdown: </b><code>![text img](<a target="_blank" href="'.Panel::$site['site_url'].'/public/uploads/'.$link.'">'.Panel::$site['site_url'].'/public/uploads/'.$link.'</a>){.img-responsive}</code></li>
							<li class="list-group-item"><b>Html: </b><code>&lt;img src="<a target="_blank" href="'.Panel::$site['site_url'].'/public/uploads/'.$link.'">'.Panel::$site['site_url'].'/public/uploads/'.$link.'</a> class="img-responsive" /&gt;</code></li>
							<li class="list-group-item"><a class="btn btn-danger" href="'.$p->url().'/uploads">'.Panel::$lang['back_to_uploads'].'</a></li>
						</ul>
					</div>';

			}else{
				// other template files
				$template = '
				<div class="col-lg-6">
					<ul class="list-group">
						<li class="list-group-item">'.Panel::$lang['no_preview_for_this_file'].'</li>
						<li class="list-group-item"><b>Filename: </b>'.File::name($path).'</li>
						<li class="list-group-item"><b>Extension: </b>'.File::ext($path).'</li>
						<li class="list-group-item"><b>Markdown: </b><code>[text link](<a target="_blank" href="'.Panel::$site['site_url'].'/public/uploads/'.$link.'">'.Panel::$site['site_url'].'/public/uploads/'.$link.'</a>)</code></li>
						<li class="list-group-item"><b>Html: </b><code>&lt;a href="<a target="_blank" href="'.Panel::$site['site_url'].'/public/uploads/'.$link.'">'.Panel::$site['site_url'].'/public/uploads/'.$link.'</a>" download &gt;text link&lt;/a&gt;</code></li>
						<li class="list-group-item"><a class="btn btn-danger" href="'.$p->url().'/uploads">'.Panel::$lang['back_to_uploads'].'</a></li>
					</ul>
				</div>';

			}
		}

		$p->view('actions',array(
			'type' => 'Upload Preview',
			'title' => Panel::$lang['Preview'],
			'content' => $file,
			'html' => $template
		));
});


