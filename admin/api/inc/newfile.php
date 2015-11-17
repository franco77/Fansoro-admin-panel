<?php defined('PANEL_ACCESS') or die('No direct script access.');




/*    NEW FILE PAGES/BLOCKS
-----------------------------*/


/*
* @name   New File
* @desc   New file pages ( :any use base64_encode remenber decode file)
*/
$p->route('/action/newfile/(:any)/(:any)', function($token,$file) use($p){
  if(Session::exists('user')){
    if (Token::check($token)) {
      // directory
      $path = base64_decode($file);

      // search pages or blocks in url
      $url = '';
      $textContent = '';
      if(preg_match('/pages/i',$path)){
        $url = 'pages';
        $textContent = '
---
title: title goes here
date: 16/11/2015
template: index
---
';
      }else if(preg_match('/blocks/i',$path)){
        $url = 'blocks';
        $textContent = 'Write here your text block...';
      }

      // get directory without base url
      $directory = str_replace(STORAGE.'/', '', $path);
      $directory = str_replace(STORAGE.'/'.File::name($path).'.'.File::ext($path), '', $path);

      $error = '';

      // save file
      if(Request::post('saveFile')){
        if(Request::post('token')){
          $filename = $p->SeoLink(Request::post('filename'));
          $content = Request::post('newfile');
          if(File::exists(STORAGE.'/'.$path.'/'.$filename.'.md')){
            $error = '<span class="label label-danger">'.Panel::$lang['File_Name_Exists'].'</span>';
          }else{
            // save content
            File::setContent(STORAGE.'/'.$path.'/'.$filename.'.md',$content);
            // set notification
            $p->setMsg($p::$lang['Success_save']);
            // redirect
            Request::redirect($p->Url().'/'.$url);
          }
        }else{
          die('crsf Detect!');
        }
      }


      $p->view('actions',array(
        'url' => $url,
        'title' => Panel::$lang['New_File'],
        'html' => ' <form method="post">
                      <div class="row">
                        <div class="col-lg-12">
                          '.$error.'
                          <input type="hidden" name="token" value="'.Token::generate().'">
                          <input type="text" value="" class="form-control" name="filename" required placeholder="File name">
                          <br>
                        </div>
                        </div>
                          <div class="row">
                            <div class="col-lg-12">
                              <textarea class="form-control" data-provide="markdown"  rows="20" name="newfile">'.$textContent.'</textarea>
                              <br>
                              <input class="btn btn-primary" type="submit" name="saveFile" value="'.Panel::$lang['Save_file'].'">
                              <a class="btn btn-danger" href="'.$p->url().'/'.$url.'">
                              '.Panel::$lang['Cancel'].'</a>
                            </div>
                          </div>
                        </form>'
      ));
    }else{
      die('crsf Detect');
    }
  }
});






/*    NEW FILE UPLOADS
-----------------------------*/

/*
* @name   Uploads New File
* @desc   New file ( :any use base64_encode remenber decode file)
*/
$p->route('/action/uploads/newfile/(:any)/(:any)', function($token,$file) use($p){
  if(Session::exists('user')){
    if (Token::check($token)) {

      $path = base64_decode($file);
      $error = '';
      $AllowedExtensions = array('gif','jpeg','jpg','png','md','txt','zip','pdf','mp4','webm','html','css','js','mp3','vaw','doc');
      if(Request::post('uploadFile')){
          if(Request::post('token')){
              $files = $_FILES['file']['name'];
              $path = str_replace('\\','/',$path);
              // change blank spaces for -
              $fileUploaded = PUBLICFOLDER.'/'.$path.'/'.$p->SeoLink(File::name($_FILES['file']['name'])).'.'.File::ext($_FILES['file']['name']);
              if(File::exists($fileUploaded)){
                $error = '<span class="label label-danger">'.Panel::$lang['File_Name_Exists'].'</span>';
              }else{
                if(!in_array(File::ext($_FILES['file']['name']), $AllowedExtensions)) {
                  die('Extension not allowed');
                }
                if(move_uploaded_file($_FILES['file']['tmp_name'], $fileUploaded)) {
                  // set notification
                  $p->setMsg($p::$lang['Success_save']);
                  // redirect
                  Request::redirect($p->Url().'/action/uploads/preview/'.base64_encode($fileUploaded));
                }
              }
          }else{
            die('crsf Detect !');
          }
      }

      $p->View('actions',array(
        'title' => 'Upload File',
        'content' => $path,
        'html' => '
            <div class="row">
              <div class="col-lg-12">
                '.$error.'
                <h3><b>'.Panel::$lang['Upload_file'].' on:</b> '.$path.'</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <form method="post" action="" enctype="multipart/form-data">
                  <input type="hidden" name="token" value="'.Token::generate().'">
                  <input name="file" class="upload" type="file" required/>
                  <br>
                  <input class="btn btn-primary"  type="submit" name="uploadFile" value="'.Panel::$lang['Upload'].'">
                  <a class="btn btn-danger" href="'.$p->url().'/uploads">'.Panel::$lang['Cancel'].'</a>
                </form>
              </div>
            </div>'
      ));

    }else{
      die('crsf Detect');
    }
  }
});


/*  NEW FILE MEDIA
------------------------------------*/

/*
* @name   Create media Media
* @desc   if session user get Create Media
*/
$p->route('/media/create',function($offset = 1) use($p){
  if(Session::exists('user')){

    $error = '';
    $AllowedExtensions = array('gif','jpeg','jpg','png');
    if(Request::post('upload')){
      if(Request::post('token')){

        $jsonFile = PUBLICFOLDER.'/media/mdb.json';
        $json = array();
        // if not exists create
        if(!File::exists($jsonFile)){
          File::setContent($jsonFile,'[]');
          // create folders album and album_thumbs
          Dir::create(PUBLICFOLDER.'/media');
          Dir::create(PUBLICFOLDER.'/media/albums');
          Dir::create(PUBLICFOLDER.'/media/album_thumbs');
        }else{
          // decode json
          $json = json_decode(File::getContent($jsonFile),true);
          // id
          $id = time();
          // json array remenber encode
          $json[$id] = array(
            'id' => $id,
            'title' => (Request::post('title')) ? $p->toText(Request::post('title')) : 'No title',
            'desc' =>  (Request::post('desc')) ? $p->toText(Request::post('desc')) : 'No desc',
            'thumb' => '/public/media/album_thumbs/album_'.$id.'.'.File::ext($_FILES['file_upload']['name']),
            'images' => '/public/media/albums/'.'album_'.$id,
            'tag' => (Request::post('tag')) ? $p->toText(Request::post('tag')) : 'No tag',
            'width' => (Request::post('width')) ? $p->toText(Request::post('width')) : 'No width',
            'height' => (Request::post('height')) ? $p->toText(Request::post('height')) : 'No height'
          );
          // check if exists
          if(File::exists(PUBLICFOLDER.'/media/albums_thumbs/'.$_FILES['file_upload']['name'])){
            $error = '<span class="label label-danger">'.Panel::$lang['File_Name_Exists'].'</span>';
          }else{
            // check file types
            if(!in_array(File::ext($_FILES['file_upload']['name']), $AllowedExtensions)) {
              die('Extension not allowed');
            }
            // move to upload dire
            $name = $_FILES['file_upload'];
            $width = Request::post('width');
            $height = Request::post('height');
            $path = PUBLICFOLDER.'/media/album_thumbs/album_'.$id.'.'.File::ext($_FILES['file_upload']['name']);
            if($p->resize($name,$path,$width,$height)){
                // save content
                File::setContent($jsonFile,json_encode($json));
                // create dir
                Dir::create(PUBLICFOLDER.'/media/albums/album_'.$id);
                // set notification
                $p->setMsg($p::$lang['Success_save']);
                // redirect
                Request::redirect($p->Url().'/media');
            }
          }
        }
      }else{
        die('crsf Detect !');
      }
    }

    // template
    $template = ' <div class="row">
                    <div class="col-lg-6">
                      '.$error.'
                      <form class="formFile" method="post"  enctype="multipart/form-data">
                          <input type="hidden" name="token" value="'.Token::generate().'"/>
                          <input type="file" class="form-control" name="file_upload" id="image-input" accept="image/x-png, image/gif, image/jpeg"  />
                          <br>
                          <input type="number" class="form-control" name="width" placeholder="width" required>
                          <br>
                          <input type="number" class="form-control" name="height" placeholder="height" required>
                          <br>
                          <input type="text" class="form-control" name="title" placeholder="title" required>
                          <br>
                          <textarea name="desc" class="form-control" rows="3" placeholder="Description" required></textarea>
                          <br>
                          <input type="text"  class="form-control"  required  name="tag" id="tag"  placeholder="Tag" required>
                          <br>
                          <a href="'.$p->Url().'/media"  class="btn btn-danger">Cancel</a>
                          <input type="submit" name="upload" id="upload" class="btn btn-primary" value="Upload">
                      </form>
                    </div>
                    <div  class="col-lg-6">
                      <img  id="image-display" class="img-thumbnail" src="'.$p->Assets('nomediapreview.jpg','img').'"/>
                    </div>
                  </div>';



    // show Media
    $p->view('actions',array(
      'title' => Panel::$lang['Create_media'],
      'content' => '',
      'html' => $template
    ));

  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});
















/*
* @name   Media uploads
* @desc   if session user get Media
*/
$p->route(array('/media/uploads/(:num)','/media/uploads/(:num)/(:num)'),function($id,$offset = 1) use($p){
  if(Session::exists('user')){
    $info = '';
    // items per page
    $per_page = $p::$site['backend_pagination_media'];
    // array json
    $json = array();
    // next prev
    $prev = '';
    $next = '';
    // template
    $templateAll = '';
    // json file
    $file = ROOTBASE.'/public/media/albums/album_'.$id;
    // get json file
    $scan = File::scan($file);

    // get json file fow with and height
    $jsonFile = PUBLICFOLDER.'/media/mdb.json';
    $json = json_decode(File::getContent($jsonFile),true);

    // upload files
    $error = '';
    $AllowedExtensions = array('gif','jpeg','jpg','png','md','txt','zip','pdf','mp4','webm','html','css','js','mp3','vaw','doc');
    if(Request::post('uploadMedia')){
        if(Request::post('token')){
                    // check if exists
          if(File::exists(PUBLICFOLDER.'/media/albums/album_'.$id.'/'.$_FILES['media_upload']['name'])){
            $error = '<span class="label label-danger">'.Panel::$lang['File_Name_Exists'].'</span>';
          }else{
            // check file types
            if(!in_array(File::ext($_FILES['media_upload']['name']), $AllowedExtensions)) {
              die('Extension not allowed');
            }
            // move to upload dire
            $name = $_FILES['media_upload'];
            $width = Request::post('width');
            $height = Request::post('height');

            $path = PUBLICFOLDER.'/media/albums/album_'.$id.'/'.
            // seo name
            $p->SeoLink(File::name($_FILES['media_upload']['name'])).'.'.
            // extension
            File::ext($_FILES['media_upload']['name']);
            // if resize redirect
            if($p->resize($name,$path,$width,$height)){
                // set notification
                $p->setMsg($p::$lang['Success_save']);
                // redirect
                Request::redirect($p->Url().'/media/uploads/'.$id);
            }
          }
        }else{
          die('crsf Detect !');
        }
    }



    // if folder is not empty
    $total = count($scan);
    if($total > 0){
      rsort($scan);
      $showPag = array_chunk($scan, $per_page);
      if($offset > 1) {
          $prev = '<a class="btn btn-primary" href="'.$p->Url().'/media/uploads/'.$id.'/'.($offset - 1).'"><i class="fa fa-arrow-left"></i></a>';
      } else {
          $prev = '<span class="btn black"><i class="fa fa-arrow-left"></i></span>';
      }
      if($offset < ceil(count($scan) / $per_page)) {
          $next = '<a class="btn btn-primary" href="' . $p->Url().'/media/uploads/'.$id.'/'.($offset + 1).'"><i class="fa fa-arrow-right"></i></a>';
      } else {
          $next = '<span class="btn black"><i class="fa fa-arrow-right"></i></span>';
      }




      // template html
      $templateAll .=  $info.'
                  
                  <!-- modal -->
                  <a href="#" class="btn btn-primary"
                  data-toggle="modal"
                  data-target="#uploadFile">
                    <i class="fa fa-upload"></i>
                    '.Panel::$lang['Upload'].'
                  </a>
                  <a href="'.$p->Url().'/media"  class="btn btn-danger">'.$p::$lang['Cancel'].'</a>

                  <div class="pull-right">
                    <div class="label label-primary">
                        <b><i class="ti-folder"></i> : </b>  album_'.$id.'
                    </div>
                    <br>
                    <div class="label label-primary">
                        <b><i class="ti-harddrive"></i> : </b>  '.$p->folderSize(ROOTBASE.$json[$id]['images']).'
                    </div>
                  </div>




                  <div class="modal fade" id="uploadFile" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-dialog" role="document" id="uploadFile">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h4 class="text-primary info">'.$p::$lang['Upload_media'].'</h4>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <div class="col-lg-6">
                                      <form method="post" class="form" enctype="multipart/form-data">
                                          <input type="hidden" name="token" value="'.Token::generate().'"/>
                                          <input type="file"  class="form-control" name="media_upload"  id="media-input" accept="image/x-png, image/gif, image/jpeg"  required/>
                                          <br>
                                          <input type="number" class="form-control"  name="width" value="'.$json[$id]['width'].'" required>
                                          <br>
                                          <input type="number" class="form-control"  name="height" value="'.$json[$id]['height'].'" required>
                                          <br>
                                          <input type="submit" name="uploadMedia" class="btn btn-primary" value="Upload">
                                          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                          <br>
                                      </form>
                                  </div>
                                  <div class="col-lg-6">
                                      <img  id="media-display" 
                                      class="thumbnail img-responsive" 
                                      src="'.$p->Assets('nomediapreview.jpg','img').'"/>
                                  </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                
                <hr>
                <div class="thumbs">';


      // all media files
      foreach($showPag[$offset - 1] as $media) {
        $image = str_replace(PUBLICFOLDER, Panel::$site['url'].'/public', $media);
        $templateAll .= '
            <div class="thumb">
              <a class="btn btn-danger" onclick="return confirm(\''.Panel::$lang['Are_you_sure_to_delete'].' !\')"
              href="'.$p->Url().'/action/media/uploads/removefile/'.$id.'/'.base64_encode($media).'">
                <i class="ti-trash"></i>
              </a>
              <img src="'.$image.'" alt="'.File::name($media).'"/>
            </div>';
      }

      // end template
      $templateAll .=  '</div></div>';


      // show Media
      $p->view('media',array(
        'title' => Panel::$lang['Uploads_Media'],
        'offset' => $offset,
        'total' => ceil(count($total)/$per_page),
        'prev' => $prev,
        'next' => $next,
        'content' => $templateAll
      ));

    // if folder is empty
    }else{
      $templateAll .= '
              <section class="subheader">
                <div class="row">
                  <div class="box-1 col">
                    '.$error.'
                    <h3><b>'.Panel::$lang['Upload_file'].' on:</b> '.$json[$id]['title'].'</h3>
                  </div>
                </div>
              </section>
                <div class="row">
                  <div class="box-1 col">
                      <div class="info">
                      <form method="post"  enctype="multipart/form-data">
                          <input type="hidden" name="token" value="'.Token::generate().'"/>
                          <input type="file" name="media_upload" accept="image/x-png, image/gif, image/jpeg"  required/>
                          <input type="number" name="width" value="'.$json[$id]['width'].'" required>
                          <input type="number" name="height" value="'.$json[$id]['height'].'" required>
                          <a href="'.$p->Url().'/media"  class="btn btn-danger">Cancel</a>
                          <input type="submit" name="uploadMedia" id="upload" class="btn btn-primary" value="Upload">
                      </form>
                  </div>';

         // show Media
        $p->view('actions',array(
          'title' => Panel::$lang['Uploads_Media'],
          'content' => '',
          'html' => $templateAll
        ));
    }
  }else{
    Request::redirect($p::$site['url'].'/'.$p::$site['backend_folder']);
  }
});











/*    NEW FILE THEMES
-----------------------------*/


/*
* @name   New File
* @desc   New file pages ( :any use base64_encode remenber decode file)
*/
$p->route('/action/themes/newfile/(:any)/(:any)', function($token,$file) use($p){
  if(Session::exists('user')){
    if (Token::check($token)) {
      // directory
      $path = base64_decode($file);
      // search pages or blocks in url
      $url = '';
      $ext = '';
      $textContent = '';
      // check files
      if(preg_match('/css/i',$path)){
        $url = 'stylesheets';
        $ext = '.css';
        $textContent = '// Write here your stylesheet';
      }else if(preg_match('/js/i',$path)){
        $url = 'javascript';
        $ext = '.js';
        $textContent = '// Write here your javasacript';
      }else{
        $url = 'templates';
        $ext = '.tpl';
        $textContent = '<!-- Write here your tpl -->';
      }

      // get directory without base url
      $directory = str_replace(THEMES.'/', '', $path);
      $directory = str_replace(THEMES.'/'.File::name($path).'.'.File::ext($path), '', $path);

      $error = '';

      // save file
      if(Request::post('saveFile')){
        if(Request::post('token')){
          $filename = $p->SeoLink(Request::post('filename'));
          $content = Request::post('newfile');
          if(File::exists(THEMES.'/'.$path.'/'.$filename.$ext)){
            $error = '<span class="label label-danger">'.Panel::$lang['File_Name_Exists'].'</span>';
          }else{
            // save content
            File::setContent(THEMES.'/'.$path.'/'.$filename.$ext,$content);
            // set notification
            $p->setMsg($p::$lang['Success_save']);
            // redirect
            Request::redirect($p->Url().'/'.$url);
          }
        }else{
          die('crsf Detect!');
        }
      }


      $p->view('actions',array(
        'url' => $url,
        'title' => Panel::$lang['New_File'],
        'html' => ' <form method="post">
                      <div class="row">
                        <div class="col-lg-12">
                          '.$error.'
                          <input type="hidden" name="token" value="'.Token::generate().'">
                          <input type="text" value="" class="form-control" name="filename" required placeholder="File name">
                          <br>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-12">
                          <textarea class="form-control" rows="20" name="updateFile">'.$textContent.'</textarea>
                          <br>
                          <input class="btn btn-primary" type="submit" name="saveFile" value="'.Panel::$lang['Save_file'].'">
                          <a class="btn btn-danger" href="'.$p->url().'/'.$url.'">'.Panel::$lang['Cancel'].'</a>
                        </div>
                      </div>
                    </form>'
      ));
    }else{
      die('crsf Detect');
    }
  }
});





/*    COMPRESS FOLDERS
-----------------------------*/

/*
* @name   Compress public folder
*/
$p->route('/action/compress/public/(:any)' ,function($token) use($p){
  if(Session::exists('user')){
    if (Token::check($token)) {
      $date = date('d-m-y');
      $name = 'public_'.$date.'.zip';
      $dir = PUBLICFOLDER;
      $dest = BACKUPS.'/'.$name;
      if($p->Zip($dir,$dest)){
        Request::redirect($p->Url().'/backups');
      };
    }else{
      die('crsf detect!');
    }
  }
});


/*
* @name   Compress storage folder
*/
$p->route('/action/compress/storage/(:any)' ,function($token) use($p){
  if(Session::exists('user')){
    if (Token::check($token)) {
      $date = date('d-m-y');
      $name = 'storage_'.$date.'.zip';
      $dir = STORAGE;
      $dest = BACKUPS.'/'.$name;
      if($p->Zip($dir,$dest)){
        Request::redirect($p->Url().'/backups');
      };
    }else{
      die('crsf detect!');
    }
  }
});

/*
* @name   Compress themes folder
*/
$p->route('/action/compress/themes/(:any)' ,function($token) use($p){
  if(Session::exists('user')){
    if (Token::check($token)) {
      $date = date('d-m-y');
      $name = 'themes_'.$date.'.zip';
      $dir = THEMES;
      $dest = BACKUPS.'/'.$name;
      if($p->Zip($dir,$dest)){
        Request::redirect($p->Url().'/backups');
      };
    }else{
      die('crsf detect!');
    }
  }
});



