<?php


defined('PANEL_ACCESS') or die('No direct script access.');

/*    EDIT PAGES/BLOCKS
-----------------------------*/

/*
* @name   Edit
* @desc   Edit page ( :any use base64_encode remenber decode file)
*/
$p->route('/action/edit/(:any)/(:any)', function ($token, $file) use ($p) {
        if (Session::exists('user')) {
            if (Token::check($token)) {

                // directory
                $path = base64_decode($file);

                // search pages or blocks in url
                $url = '';
                if (preg_match('/pages/i', $path)) {
                    $url = 'pages';
                } elseif (preg_match('/blocks/i', $path)) {
                    $url = 'blocks';
                }

                // update file
                if (Request::post('saveFile')) {
                    if (Request::post('token')) {
                        $content = Request::post('updateFile');
                        // save content
                        File::setContent($path, $content);
                        // set notification
                        $p->setMsg($p::$lang['Success_edit']);
                        // redirect
                        Request::redirect($p->Url().'/'.$url);
                    } else {
                        die('crsf Detect!');
                    }
                }

                $p->view('actions', array(
                        'url' => $url,
                        'title' => Panel::$lang['Edit_File'],
                        'html' => ' <form method="post">
								<div class="row">
									<div class="col-lg-12">
										<input type="hidden" name="token" value="'.Token::generate().'">
										<h4><label class="label label-primary"><b>Name: </b>'.File::name($path).'</label></h4>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<textarea class="form-control" data-provide="markdown" rows="20" name="updateFile">'.File::getContent($path).'</textarea>
										<br>
										<input class="btn btn-primary" type="submit" name="saveFile" value="'.Panel::$lang['Update'].'">
										<a class="btn btn-danger" role="button" href="'.$p->url().'/'.$url.'">'.Panel::$lang['Cancel'].'</a>
									</div>
								</div>
							</form>',
                    ));
            } else {
                die('crsf Detect');
            }
        }
    });

/*  EDIT FILE MEDIA
------------------------------------*/

/*
* @name   Edit media Media
* @desc   if session user get Edit Media
*/
$p->route('/action/media/edit/(:num)', function ($id) use ($p) {

        if (Session::exists('user')) {
            $error = '';
            $AllowedExtensions = array('gif', 'jpeg', 'jpg', 'png');
            // json file
            $jsonFile = PUBLICFOLDER.'/media/mdb.json';
            // decode json
            $json = json_decode(File::getContent($jsonFile), true);
            if (Request::post('upload')) {
                if (Request::post('token')) {
                    // json array remenber encode
                    $json[$id] = array(
                        'id' => $id,
                        'title' => (Request::post('title')) ? $p->toText(Request::post('title')) : $p->toText($json[$id]['title']),
                        'desc' => (Request::post('desc')) ? $p->toText(Request::post('desc')) : $p->toText($json[$id]['desc']),
                        'thumb' => ($_FILES['file_upload']['name']) ? '/public/media/album_thumbs/album_'.$id.'.'.File::ext($_FILES['file_upload']['name']) : $json[$id]['thumb'],
                        'images' => '/public/media/albums/'.'album_'.$id,
                        'tag' => (Request::post('tag')) ? $p->toText(Request::post('tag')) : $p->toText($json[$id]['tag']),
                        'width' => (Request::post('width')) ? $p->toText(Request::post('width')) : $p->toText($json[$id]['width']),
                        'height' => (Request::post('height')) ? $p->toText(Request::post('height')) : $p->toText($json[$id]['height']),
                    );
                    // check  input file
                    if (!empty($_FILES['file_upload']['name'])) {
                        // check file types
                        if (!in_array(File::ext($_FILES['file_upload']['name']), $AllowedExtensions)) {
                            die('Extension not allowed');
                        }

                        // move to upload dire
                        $name = $_FILES['file_upload'];
                        $width = Request::post('width');
                        $height = Request::post('height');
                        $path = PUBLICFOLDER.'/media/album_thumbs/album_'.$id.'.'.File::ext($_FILES['file_upload']['name']);
                        // if save image
                        if ($p->resize($name, $path, $width, $height)) {
                            // save content
                            File::setContent($jsonFile, json_encode($json));
                            // set notification
                            $p->setMsg($p::$lang['Success_edit']);
                            // redirect
                            Request::redirect($p->Url().'/media');
                        }
                    } else {
                        // resize old thumb if change values
                        $name = $json[$id]['thumb'];
                        $width = $json[$id]['width'];
                        $height = $json[$id]['height'];
                        $path = ROOTBASE.$json[$id]['thumb'];
                        // set resize funtion to false = is not upload
                        if ($p->resize($path, $path, $width, $height, false)) {
                            // save content
                            File::setContent($jsonFile, json_encode($json));
                            // set notification
                            $p->setMsg($p::$lang['Success_edit']);
                            // redirect
                            Request::redirect($p->Url().'/media');
                        }
                    }
                } else {
                    die('crsf Detect !');
                }
            }

            // template
            $template = '
					<div class="row">
						<div class="col-lg-6">
							'.$error.'
							<form class="form-horizontal" method="post"  enctype="multipart/form-data">
								<input type="hidden" name="token" value="'.Token::generate().'"/>
								<div class="form-group">
									<input type="file" name="file_upload" id="image-input" class="form-control" value="'.$json[$id]['thumb'].'" accept="image/x-png, image/gif, image/jpeg"  />
								</div>
								<div class="form-group">
									<input type="number" class="form-control" name="width" value="'.$json[$id]['width'].'" required>
								</div>
								<div class="form-group">
									<input type="number" class="form-control" name="height" value="'.$json[$id]['height'].'" required>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="title" value="'.$json[$id]['title'].'" required>
								</div>
								<div class="form-group">
									<textarea name="desc" class="form-control" rows="3" required>'.$json[$id]['desc'].'</textarea>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" required name="tag" value="'.$json[$id]['tag'].'" required>
								</div>
								<input type="submit" name="upload" id="upload" class="btn btn-primary" value="'.Panel::$lang['Upload'].'">
								<a href="'.$p->Url().'/media" role="button" class="btn btn-danger">'.Panel::$lang['Cancel'].'</a>
							</form>
						</div>
						<div class="col-lg-6">
							<div class="thumbnail">
								<img class="img-responsive" id="image-display" src="'.Panel::$site['site_url'].$json[$id]['thumb'].'?timestamp=1357571065"/>
							</div>
						</div>
					</div>';

            // show Media
            $p->view('actions', array(
                    'title' => Panel::$lang['Create_media'],
                    'content' => '',
                    'html' => $template,
                ));
        } else {
            Request::redirect($p::$site['site_url'].'/'.$p::$site['backend_folder']);
        }
    });

/*    EDIT THEMES
-----------------------------*/

/*
* @name   Edit themes
* @desc   Edit themes ( :any use base64_encode remenber decode file)
*/
$p->route('/action/themes/edit/(:any)/(:any)', function ($token, $file) use ($p) {
        if (Session::exists('user')) {
            if (Token::check($token)) {

                // directory
                $path = base64_decode($file);

                // search templates/css/js in url
                $url = '';
                if (preg_match('/tpl/i', $path)) {
                    $url = 'templates';
                } elseif (preg_match('/css/i', $path)) {
                    $url = 'stylesheets';
                } elseif (preg_match('/js/i', $path)) {
                    $url = 'javascript';
                }

                // update file
                if (Request::post('saveFile')) {
                    if (Request::post('token')) {
                        $content = Request::post('updateFile');
                        // save content
                        File::setContent($path, $content);
                        // set notification
                        $p->setMsg($p::$lang['Success_edit']);
                        // redirect
                        Request::redirect($p->Url().'/'.$url);
                    } else {
                        die('crsf Detect!');
                    }
                }

                $p->view('actions', array(
                        'url' => $url,
                        'title' => Panel::$lang['Edit_File'],
                        'html' => ' <form method="post">
								<div class="row">
									<div class="col-lg-12">
										<input type="hidden" name="token" value="'.Token::generate().'">
										<h4><label class="label label-primary"><b>Name: </b>'.File::name($path).'.'.File::ext($path).'</label></h4>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<textarea class="theme-editor form-control" rows="20" name="updateFile">'.File::getContent($path).'</textarea>
										</div>
										<input class="btn btn-primary" type="submit" name="saveFile" value="'.Panel::$lang['Update'].'">
										<a class="btn btn-danger" role="button" href="'.$p->url().'/'.$url.'">'.Panel::$lang['Cancel'].'</a>
									</div>
								</div>
							</form>',
                    ));
            } else {
                die('crsf Detect');
            }
        } else {
            Request::redirect($p::$site['site_url'].'/'.$p::$site['backend_folder']);
        }
    });

/*    EDIT SITE.YML
-----------------------------*/

/*
* @name   Edit site
* @desc   Edit site ( :any use base64_encode remenber decode file)
*/
$p->route('/config/site', function () use ($p) {
        if (Session::exists('user')) {

            // update file
            if (Request::post('saveFile')) {
                if (Request::post('token')) {
                    $content = Request::post('updateFile');
                    // save content
                    File::setContent(SITE, $content);
                    // set notification
                    $p->setMsg($p::$lang['Success_edit']);
                    // redirect
                    Request::redirect($p->Url());
                } else {
                    die('crsf Detect!');
                }
            }

            $p->view('actions', array(
                    'url' => 'Config',
                    'title' => Panel::$lang['Siteyml'],
                    'html' => ' <form method="post">
							<div class="row">
								<div class="col-lg-12">
									<input type="hidden" name="token" value="'.Token::generate().'">
									<h4><label class="label label-primary"><b>Name: </b> '.Panel::$lang['Siteyml'].'.yml</label></h4>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<textarea class="editor form-control" rows="20" name="updateFile">'.File::getContent(SITE).'</textarea>
									</div>
									<input class="btn btn-primary" type="submit"  name="saveFile" value="'.Panel::$lang['Update'].'">
									<a class="btn btn-danger" role="button" href="'.$p->url().'">'.Panel::$lang['Cancel'].'</a>
								</div>
							</div>
						</form>',
                ));
        } else {
            Request::redirect($p::$site['site_url'].'/'.$p::$site['backend_folder']);
        }
    });

/*
* @name   Edit system
* @desc   Edit system ( :any use base64_encode remenber decode file)
*/
$p->route('/config/system', function () use ($p) {
        if (Session::exists('user')) {

            // update file
            if (Request::post('saveFile')) {
                if (Request::post('token')) {
                    $content = Request::post('updateFile');
                    // save content
                    File::setContent(SYSTEM, $content);
                    // set notification
                    $p->setMsg($p::$lang['Success_edit']);
                    // redirect
                    Request::redirect($p->Url());
                } else {
                    die('crsf Detect!');
                }
            }

            $p->view('actions', array(
                    'url' => 'Config',
                    'title' => Panel::$lang['Systemyml'],
                    'html' => ' <form method="post">
							<div class="row">
								<div class="col-lg-12">
									<input type="hidden" name="token" value="'.Token::generate().'">
									<h4><label class="label label-primary"><b>Name: </b> '.Panel::$lang['Systemyml'].'.yml</label></h4>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<textarea class="editor form-control" rows="20" name="updateFile">'.File::getContent(SYSTEM).'</textarea>
									</div>
									<input class="btn btn-primary" type="submit"  name="saveFile" value="'.Panel::$lang['Update'].'">
									<a class="btn btn-danger" role="button" href="'.$p->url().'">'.Panel::$lang['Cancel'].'</a>
								</div>
							</div>
						</form>',
                ));
        } else {
            Request::redirect($p::$site['site_url'].'/'.$p::$site['backend_folder']);
        }
    });
