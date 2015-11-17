<?php defined('PANEL_ACCESS') or die('No direct script access.');








/*    DIAGNOSTIC
-----------------------------*/


/*
* @name   Diagnostic
* @desc   if session user get Diagnostic
*/
$p->route('/diag', function() use($p){
  if(Session::exists('user')){
    // show Diagnostic
    $p->view('diag',array('title' => 'Diagnostic'));
  }
});







/*    DASHBOARD
-----------------------------*/


/*
* @name   Dashboard | login
* @desc   if session user get Dashboard
* @desc   if not redirecto to login page
*/
$p->route('/', function() use($p){
  if(Session::exists('user')){

    // show dashboard
    $p->view('index',array(
      'title' => $p::$lang['Dashboard'],
      'pages' => count(File::scan(PAGES, 'md')),
      'media' => count(File::scan(MEDIA.'/album_thumbs')),
      'uploads' => count(File::scan(UPLOADS)),
      'blocks' => count(File::scan(BLOCKS, 'md')),
      'themes' => count(Dir::scan(ROOTBASE.'/themes')),
      'plugins' => count(Dir::scan(ROOTBASE.'/plugins'))
    ));

  }else{

    $password =     $p::$site['backend_password'];
    $secret_key1 =  '324097cfa96df3036b310d9ffc9ca78f'; // change this if you like
    $secret_key2 =  'f1e5d7a5fe13498abbdeb0f1f19136a8'; // change this if you like
    $hash = md5($secret_key1.$password.$secret_key2);
    
    // empty error
    $error = '';
    if(Request::post('login')){
      if(Request::post('csrf')){
        if(Request::post('pass') == $password && Request::post('email') == $p::$site['author']['email']){
          @Session::start();
          Session::set('user',$hash);
          Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
        }else{
          // password not correct show error
          $error = '<span class="label label-danger">'.$p::$lang['Password_Error'].'</span>';
        }
      }else{
        // crsf
        die('crsf detect');
      }
    }
    // get template login
    $p->view('login',array(
      'error' => $error
    ));
  }
});














/*    PAGES
-----------------------------*/

/*
* @name   Pages
* @desc   if session user get Pages
* @desc   if not redirecto to login page
*/
$p->route(array('/pages','/pages/(:num)'),function($offset = 1) use($p){
  if(Session::exists('user')){

    $per_page = $p::$site['backend_pagination_pages'];
    $content = File::scan(PAGES);

    // upload md files
    if(Request::post('uploadFile')){
      if(Request::post('token')){
          $filename = $_FILES['pagesFile']['tmp_name'];
          $name = $_FILES['pagesFile']['name'];
          if(File::ext($name) == 'md'){
              if(move_uploaded_file($filename, PAGES.'/blog/'.$p->SeoLink(File::name($name)).'.'.File::ext($name))) {
                // set notification
                $p->setMsg($p::$lang['Success_save']);
                // redirect
                Request::redirect($p->Url().'/pages');
              } else{
                $p->setMsg('There was an error uploading the file, please try again!');
              }
          }else{
            $p->setMsg('No md file, please try again!');
          }
      }else{
        die('csrf detect !');
      }
    }

    // upload md files
    if(Request::post('importZipFile')){
      if(Request::post('token')){
          $filename = $_FILES['importfile']['tmp_name'];
          $name = $_FILES['importfile']['name'];
          $target_path = CACHE.'/'.$name;
          if(move_uploaded_file($filename, $target_path)) {
              sleep(1);
              $p->unZip($target_path,STORAGE);
              // set notification
              $p->setMsg($p::$lang['Success_save']);
              // redirect
              Request::redirect($p->Url().'/pages');
          } else {  
            $p->setMsg('There was a problem with the upload. Please try again.');
          }
      }else{
        die('csrf detect !');
      }
    }




    if($content){
      rsort($content);
      $showPag = array_chunk($content, $per_page);

      $prev = '';
      $next = '';
      if($offset > 1) {
          $prev = '<a class="btn btn-primary" href="'.$p->Url().'/pages/'.($offset - 1).'"><i class="ti-arrow-left"></i></a>';
      } else {
          $prev = '<span class="btn black"><i class="ti-arrow-left"></i></span>';
      }
      if($offset < ceil(count($content) / $per_page)) {
          $next = '<a  class="btn btn-primary" href="' . $p->Url().'/pages/'.($offset + 1).'"><i class="ti-arrow-right"></i></a>';
      } else {
          $next = '<span class="btn black"><i class="ti-arrow-right"></i></span>';
      }
      // show pages
      $p->view('pages',array(
        'title' => Panel::$lang['Pages'],
        'offset' => $offset,
        'total' => ceil(count($content)/$per_page),
        'prev' => $prev,
        'next' => $next,
        'content' => $showPag[$offset - 1]
      ));
    }else{
      // show pages
      $p->view('pages',array(
        'title' => Panel::$lang['Pages'],
        'offset' => 1,
        'total' => 1,
        'prev' => '',
        'next' => '',
        'content' => $content
      ));
    }
  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});





















/*    BLOCKS
-----------------------------*/

/*
* @name   Blocks
* @desc   if session user get Blocks
*/
$p->route(array('/blocks','/blocks/(:num)'),function($offset = 1) use($p){
  if(Session::exists('user')){

    $per_page = $p::$site['backend_pagination_pages'];
    $content = File::scan(BLOCKS);

    if($content){
      rsort($content);
      $showPag = array_chunk($content, $per_page);

      $prev = '';
      $next = '';
      if($offset > 1) {
          $prev = '<a class="btn  blue" href="'.$p->Url().'/blocks/'.($offset - 1).'"><i class="ti-arrow-left"></i></a>';
      } else {
          $prev = '<span class="btn black"><i class="ti-arrow-left"></i></span>';
      }
      if($offset < ceil(count($content) / $per_page)) {
          $next = '<a class="btn btn-primary" href="' . $p->Url().'/blocks/'.($offset + 1).'"><i class="ti-arrow-right"></i></a>';
      } else {
          $next = '<span class="btn  black"><i class="ti-arrow-right"></i></span>';
      }
      // show blocks
      $p->view('blocks',array(
        'title' => Panel::$lang['Blocks'],
        'offset' => $offset,
        'total' => ceil(count($content)/$per_page),
        'prev' => $prev,
        'next' => $next,
        'content' => $showPag[$offset - 1]
      ));
    }else{
      // show blocks
      $p->view('blocks',array(
        'title' => Panel::$lang['Blocks'],
        'offset' => 1,
        'total' => 1,
        'prev' => '',
        'next' => '',
        'content' => $content
      ));
    }
  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});









/*    UPLOADS
-----------------------------*/


/*
* @name   Uploads
* @desc   if session user get Uploads
* @desc   if not redirecto to login page
*/
$p->route(array('/uploads','/uploads/(:num)'),function($offset = 1) use($p){
  if(Session::exists('user')){
    // results per page
    $per_page = $p::$site['backend_pagination_uploads'];
    $content = File::scan(UPLOADS);


    // import public
    if(Request::post('importZipFile')){
      if(Request::post('token')){
          $filename = $_FILES['importfile']['tmp_name'];
          $name = $_FILES['importfile']['name'];
          $target_path = CACHE.'/'.$name;
          if(move_uploaded_file($filename, $target_path)) {
              sleep(1);
              $p->unZip($target_path,PUBLICFOLDER);
              // set notification
              $p->setMsg($p::$lang['Success_save']);
              // redirect
              Request::redirect($p->Url().'/uploads');
          } else {  
            $p->setMsg('There was a problem with the upload. Please try again.');
          }
      }else{
        die('csrf detect !');
      }
    }



    // check files
    if($content){
      rsort($content);
      $showPag = array_chunk($content, $per_page);

      $prev = '';
      $next = '';
      if($offset > 1) {
          $prev = '<a class="btn btn-primary" href="'.$p->Url().'/uploads/'.($offset - 1).'"><i class="ti-arrow-left"></i></a>';
      } else {
          $prev = '<span class="btn black"><i class="ti-arrow-left"></i></span>';
      }
      if($offset < ceil(count($content) / $per_page)) {
          $next = '<a  class="btn btn-primary" href="' . $p->Url().'/uploads/'.($offset + 1).'"><i class="ti-arrow-right"></i></a>';
      } else {
          $next = '<span class="btn black"><i class="ti-arrow-right"></i></span>';
      }

      // show blocks
      $p->view('uploads',array(
        'title' => Panel::$lang['Uploads'],
        'offset' => $offset,
        'total' => ceil(count($content)/$per_page),
        'prev' => $prev,
        'next' => $next,
        'content' => $showPag[$offset - 1]
      ));

  }else{
          // show blocks
      $p->view('uploads',array(
        'title' => Panel::$lang['Uploads'],
        'offset' => 1,
        'total' => 1,
        'prev' => '',
        'next' => '',
        'content' => $content
      ));
  }

  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});









/*    MEDIA
-----------------------------*/

/*
* @name   Media
* @desc   if session user get Media
*/
$p->route(array('/media','/media/(:num)'),function($offset = 1) use($p){
  if(Session::exists('user')){
    // items per page
    $per_page = $p::$site['backend_pagination_media_all'];
    // array json
    $json = array();
    $total = '';
    // next prev
    $prev = '';
    $next = '';
    // template
    $templateAll = '';
    // json file
    $jsonFile = ROOTBASE.'/public/media/mdb.json';
    // if not exists create
    if(!File::exists($jsonFile)){
      File::setContent($jsonFile,'[]');
      // create folders album and album_thumbs
      Dir::create(ROOTBASE.'/public/media');
      Dir::create(ROOTBASE.'/public/media/albums');
      Dir::create(ROOTBASE.'/public/media/album_thumbs');
    }else{
      // get json file
      $json = json_decode(File::getContent($jsonFile),true);
      $total = count($json);
      if($total > 0){
        rsort($json);
        $showPag = array_chunk($json, $per_page);
        if($offset > 1) {
            $prev = '<a class="btn btn-primary" href="'.$p->Url().'/media/'.($offset - 1).'"><i class="ti-arrow-left"></i></a>';
        } else {
            $prev = '<span class="btn black"><i class="ti-arrow-left"></i></span>';
        }
        if($offset < ceil(count($json) / $per_page)) {
            $next = '<a class="btn btn-primary" href="' . $p->Url().'/media/'.($offset + 1).'"><i class="ti-arrow-right"></i></a>';
        } else {
            $next = '<span class="btn black"><i class="ti-arrow-right"></i></span>';
        }

        // all media files
        foreach($showPag[$offset - 1] as $media) {
            $templateAll .= '
            <div class="row">
			  <div class="col-lg-6">
				  <img class="img-thumbnail" src="'.Panel::$site['url'].$media['thumb'].'?timestamp=1357571065"/>
			  </div>
			  <div class="col-lg-6">
				<ul class="list-group">
				  <li class="list-group-item"><b>Title: </b>'.$p->toHtml($media['title']).'</lI>
				  <li class="list-group-item"><b>Description: </b>'.$p->TextCut($p->toHtml($media['desc']),20).'</lI>
				  <li class="list-group-item"><b>Width: </b>'.$media['width'].'</li>
				  <li class="list-group-item"><b>Height: </b>'.$media['height'].'</li>
				  <li class="list-group-item"><b>Tag: </b>'.$p->toHtml($media['tag']).'</li>
				  <li class="list-group-item"><b>Markdown: </b></li>
				  <li class="list-group-item"><code>[link text](<a target="_blank" href="'.Panel::$site['url'].'/media?action=view&id='.$media['id'].'">'.Panel::$site['url'].'/media?action=view&id='.$media['id'].'</a>)</code></li>
				  <li class="list-group-item"><b>Html: </b></li>
				  <li class="list-group-item"><code>&lt;a href="<a target="_blank" href="'.Panel::$site['url'].'/media?action=view&id='.$media['id'].'">'.Panel::$site['url'].'/media?action=view&id='.$media['id'].'</a>"&gt;link Text&lt;/a&gt;</code></li>
				  <li class="list-group-item">
					  <a class="btn btn-primary"
					  href="'.$p->Url().'/action/media/edit/'.$media['id'].'"
					  title="'.Panel::$lang['Edit_File'].'"><i class="ti-pencil-alt"></i></a>
					  <a class="btn btn-warning"
					  href="'.$p->Url().'/media/uploads/'.$media['id'].'"
					  title="'.Panel::$lang['Upload_media'].'"><i class="fa fa-upload"></i></a>
					  <a class="btn btn-danger"
					  onclick="return confirm(\''.Panel::$lang['Are_you_sure_to_delete'].' !\')"
					  href="'.$p->Url().'/action/media/removefile/'.Token::generate().'/'.$media['id'].'"
					  title="'.Panel::$lang['Remove_File'].'"><i class="ti-trash"></i></a>
				  </li>
				</ul>
			  </div>
            </div>';
        }
      }
    }

    // show Media
    $p->view('media',array(
      'title' => Panel::$lang['Media'],
      'offset' => $offset,
      'total' => ceil(count($total)/$per_page),
      'prev' => $prev,
      'next' => $next,
      'content' => (count($json) > 0) ? $templateAll : '<div class="label label-danger">Empty Media albums</div>'
    ));

  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});








/*    TEMPLATES
-----------------------------*/

/*
* @name   Templates
* @desc   if session user get Templates
* @desc   if not redirecto to login page
*/
$p->route(array('/templates','/templates/(:num)'),function($offset = 1) use($p){
  if(Session::exists('user')){

    $per_page = $p::$site['backend_pagination_pages'];
    $content = File::scan(THEMES,'.tpl');


    // import themes
    if(Request::post('importZipFile')){
      if(Request::post('token')){
          $filename = $_FILES['importfile']['tmp_name'];
          $name = $_FILES['importfile']['name'];
          $target_path = CACHE.'/'.$name;
          if(move_uploaded_file($filename, $target_path)) {
              sleep(1);
              $p->unZip($target_path,THEMES);
              // set notification
              $p->setMsg($p::$lang['Success_save']);
              // redirect
              Request::redirect($p->Url().'/templates');
          } else {  
            $p->setMsg('There was a problem with the upload. Please try again.');
          }
      }else{
        die('csrf detect !');
      }
    }


    if($content){
      rsort($content);
      $showPag = array_chunk($content, $per_page);

      $prev = '';
      $next = '';
      if($offset > 1) {
          $prev = '<a class="btn btn-primary" href="'.$p->Url().'/templates/'.($offset - 1).'"><i class="ti-arrow-left"></i></a>';
      } else {
          $prev = '<span class="btn black"><i class="ti-arrow-left"></i></span>';
      }
      if($offset < ceil(count($content) / $per_page)) {
          $next = '<a class="btn btn-primary" href="' . $p->Url().'/templates/'.($offset + 1).'"><i class="ti-arrow-right"></i></a>';
      } else {
          $next = '<span class="btn black"><i class="ti-arrow-right"></i></span>';
      }
      // show pages
      $p->view('templates',array(
        'title' => Panel::$lang['Templates'],
        'offset' => $offset,
        'total' => ceil(count($content)/$per_page),
        'prev' => $prev,
        'next' => $next,
        'content' => $showPag[$offset - 1]
      ));
    }else{
      // show pages
      $p->view('templates',array(
        'title' => Panel::$lang['Templates'],
        'offset' => 1,
        'total' => 1,
        'prev' => '',
        'next' => '',
        'content' => $content
      ));
    }
  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});






/*    STYLESHEETS
-----------------------------*/

/*
* @name   Stylesheets
* @desc   if session user get Stylesheets
* @desc   if not redirecto to login page
*/
$p->route(array('/stylesheets','/stylesheets/(:num)'),function($offset = 1) use($p){
  if(Session::exists('user')){

    $per_page = $p::$site['backend_pagination_pages'];
    $content = File::scan(THEMES,'.css');
    if($content){
      rsort($content);
      $showPag = array_chunk($content, $per_page);

      $prev = '';
      $next = '';
      if($offset > 1) {
          $prev = '<a class="btn btn-primary" href="'.$p->Url().'/stylesheets/'.($offset - 1).'"><i class="ti-arrow-left"></i></a>';
      } else {
          $prev = '<span class="btn black"><i class="ti-arrow-left"></i></span>';
      }
      if($offset < ceil(count($content) / $per_page)) {
          $next = '<a class="btn btn-primary" href="' . $p->Url().'/stylesheets/'.($offset + 1).'"><i class="ti-arrow-right"></i></a>';
      } else {
          $next = '<span class="btn black"><i class="ti-arrow-right"></i></span>';
      }
      // show pages
      $p->view('templates',array(
        'title' => Panel::$lang['Stylesheets'],
        'offset' => $offset,
        'total' => ceil(count($content)/$per_page),
        'prev' => $prev,
        'next' => $next,
        'content' => $showPag[$offset - 1]
      ));
    }else{
      // show pages
      $p->view('templates',array(
        'title' => Panel::$lang['Stylesheets'],
        'offset' => 1,
        'total' => 1,
        'prev' => '',
        'next' => '',
        'content' => $content
      ));
    }
  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});



/*    JAVASCRIPT
-----------------------------*/

/*
* @name   Javascript
* @desc   if session user get Javascript
* @desc   if not redirecto to login page
*/
$p->route(array('/javascript','/javascript/(:num)'),function($offset = 1) use($p){
  if(Session::exists('user')){

    $per_page = $p::$site['backend_pagination_pages'];
    $content = File::scan(THEMES,'.js');
    if($content){
      rsort($content);
      $showPag = array_chunk($content, $per_page);

      $prev = '';
      $next = '';
      if($offset > 1) {
          $prev = '<a class="btn btn-primary" href="'.$p->Url().'/javascript/'.($offset - 1).'"><i class="ti-arrow-left"></i></a>';
      } else {
          $prev = '<span class="btn black"><i class="ti-arrow-left"></i></span>';
      }
      if($offset < ceil(count($content) / $per_page)) {
          $next = '<a class="btn btn-primary" href="' . $p->Url().'/javascript/'.($offset + 1).'"><i class="ti-arrow-right"></i></a>';
      } else {
          $next = '<span class="btn black"><i class="ti-arrow-right"></i></span>';
      }
      // show pages
      $p->view('templates',array(
        'title' => Panel::$lang['Javascript'],
        'offset' => $offset,
        'total' => ceil(count($content)/$per_page),
        'prev' => $prev,
        'next' => $next,
        'content' => $showPag[$offset - 1]
      ));
    }else{
      // show pages
      $p->view('templates',array(
        'title' => Panel::$lang['Javascript'],
        'offset' => 1,
        'total' => 1,
        'prev' => '',
        'next' => '',
        'content' => $content
      ));
    }
  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});








/*    TEMPLATES
-----------------------------*/

/*
* @name   Templates
* @desc   if session user get Templates
* @desc   if not redirecto to login page
*/
$p->route('/backups',function() use($p){
  if(Session::exists('user')){

    $content = File::scan(BACKUPS,'.zip');
    if($content){
      // show pages
      $p->view('backups',array(
        'title' => Panel::$lang['Backups'],
        'content' => $content
      ));
    }else{
      // show pages
      $p->view('backups',array(
        'title' => Panel::$lang['Backups'],
        'content' => $content
      ));
    }
  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});
