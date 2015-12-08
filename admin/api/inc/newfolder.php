<?php


defined('PANEL_ACCESS') or die('No direct script access.');

/*    NEW FOLDER PAGES/BLOCKS
----------------------------------*/

/*
* @name   New Folder
* @desc   Create new folder ( :any use base64_encode remenber decode file)
*/
$p->route('/action/newfolder/(:any)/(:any)', function ($token, $file) use ($p) {
        if (Session::exists('user')) {
            if (Token::check($token)) {
                // directory
                $dir = base64_decode($file);
                // redirect to edit index
                $url = str_replace(STORAGE.'/', '', $dir);
                if ($url !== 'pages') {
                    $url = 'pages';
                }
                // error
                $error = '';
                // submit function
                if (Request::post('create_new_folder')) {
                    // check token
                    if (Token::check(Request::post('token'))) {
                        // if empty
                        if (Request::post('new_folder_name') !== '') {
                            // name of folder
                            $foldername = STORAGE.'/'.$dir.'/'.$p->SeoLink(Request::post('new_folder_name'));
                            // if exists
                            if (!Dir::exists($foldername)) {
                                // create folder
                                Dir::create($foldername);
                                $tmpl = '
---
title: title goes here
description:
keywords:
template: index
---
';
                                // create index file with folder name
                                File::setContent($foldername.'/index.md', $tmpl);
                                // set notification
                                $p->setMsg($p::$lang['Success_save']);
                                // redirect to edit index
                                Request::redirect($p->url().'/action/edit/'.Token::generate().'/'.base64_encode($foldername.'/index.md'));
                            } else {
                                // if exists
                                $error = '<span class="label label-danger">'.Panel::$lang['Folder_Already_Exists'].'</span>';
                            }
                        } else {
                            // if empty input value
                            $error = '<span class="label label-danger">'.Panel::$lang['Folder_Name_Required'].'</span>';
                        }
                    } else {
                        die('crsf detect');
                    }
                }
                // template
                $p->view('actions', array(
                        'title' => Panel::$lang['New_Folder'],
                        'content' => $dir,
                        'html' => '<div class="col-lg-12">
								<form class="form-inline" method="post">
									<input type="hidden" name="token" value="'.Token::generate().'">
									<div class="form-group">
									<label>'.Panel::$lang['New_Folder'].' : <code>'.base64_decode($file).'</code></label>
									</div>
									<div class="form-group">
									<input type="text" class="form-control" name="new_folder_name">
									</div>
									<input class="btn btn-primary" type="submit" name="create_new_folder" value="'.Panel::$lang['New_Folder'].'">
									<a class="btn btn-danger" href="'.$p->url().'/'.$url.'">'.Panel::$lang['Cancel'].'</a>
								</form>
								<br>
								'.$error.'
							</div>',
                    ));
            } else {
                die('crsf Detect');
            }
        }
    });

/*    NEW FOLDER UPLOADS
-----------------------------*/

/*
* @name   Uploads New Folder
* @desc   New folder ( :any use base64_encode remenber decode file)
*/
$p->route('/action/uploads/newfolder/(:any)/(:any)', function ($token, $file) use ($p) {
        if (Session::exists('user')) {
            if (Token::check($token)) {
                // directory
                $dir = base64_decode($file);
                // error
                $error = '';
                // submit function
                if (Request::post('create_new_folder')) {
                    // check token
                    if (Token::check(Request::post('token'))) {
                        // if empty
                        if (Request::post('new_folder_name') !== '') {
                            $dir = str_replace('\\', '/', $dir);
                            // name of folder
                            $foldername = PUBLICFOLDER.'/'.$dir.'/'.$p->SeoLink(Request::post('new_folder_name'));
                            $foldername = str_replace('//', '/', $foldername);
                            // if exists
                            if (!Dir::exists($foldername)) {
                                // create folder
                                Dir::create($foldername);
                                // init folder with one file
                                File::setContent($foldername.'/folder.html',
                                    $foldername);
                                // set notification
                                $p->setMsg($p::$lang['Success_save']);
                                // redirect to edit index
                                Request::redirect($p->url().'/uploads');
                            } else {
                                // if exists
                                $error = '<span class="label label-danger">'.Panel::$lang['Folder_Already_Exists'].'</span>';
                            }
                        } else {
                            // if empty input value
                            $error = '<span class="label label-danger">'.Panel::$lang['Folder_Name_Required'].'</span>';
                        }
                    } else {
                        die('crsf detect');
                    }
                }
                // template
                $p->view('actions', array(
                        'title' => Panel::$lang['New_Folder'],
                        'content' => $dir,
                        'html' => '<div class="col-md-12">
								<form class="form-inline" method="post">
									<input type="hidden" name="token" value="'.Token::generate().'">
									<div class="form-group">
									<label>'.Panel::$lang['New_Folder'].' : <code>'.base64_decode($file).'</code></label>
									</div>
									<div class="form-group">
									<input type="text" class="form-control" name="new_folder_name">
									</div>
									<input class="btn btn-primary" type="submit" name="create_new_folder" value="'.Panel::$lang['New_Folder'].'">
									<a class="btn btn-danger" href="'.$p->url().'/uploads">'.Panel::$lang['Cancel'].'</a>
								</form>
								<br>
								'.$error.'
							</div>',
                    ));
            } else {
                die('crsf Detect');
            }
        }
    });
