<?php defined('PANEL_ACCESS') or die('No direct script access.');


/*    SEARCH PAGES/BLOCKS
-----------------------------*/


/*
* @name   Search
* @sample /action/search/pages/about
*/
$p->route('/action/search/(:any)/(:any)', function($dir = '',$query = '') use($p) {

    // search pages or blocks in url
    $url = '';
    if(preg_match('/pages/i',$dir)){
      $url = 'pages';
    }else if(preg_match('/blocks/i',$dir)){
      $url = 'blocks';
    }

    // get file url
    $directory = STORAGE.'/'.$dir;
    // scan to obtain files
    $scan = File::scan($directory);
    // start template
    $result = '<ul class="list-group">';
    // init count to 0
    $count = 0;
    foreach ($scan as $item) {
      // remove storage\$dir
      $item = str_replace(STORAGE.'/'.$dir, '', $item);
      // search query with preg_match
      if(preg_match('/'.urldecode($query).'/i', $item)){
        // count +1
        $count++;
        // template
        $result .= '<li class="list-group-item clearfix">
                      '.$item.'
                      <a class="btn btn-primary pull-right" href="
                        '.$p->Url().'/action/edit/'.Token::generate().'/'.
                        base64_encode($directory.$item).'">
                        '.File::name($item).'
                        | <i class="fa fa-arrow-right"></i>
                      </a>
                    </li>';
      }
    }
    $result .= '</ul>';
    // render view
    $p->view('actions',array(
        'title' => Panel::$lang['Search'],
        'html' => '<div class="row">
                      <div class="col-lg-6">
                        <h3><span class="btn btn-primary">'.$count.'</span>
                        results for '.$query.'</h3>
                          '.$result.'
                          <a class="btn btn-danger" href="'.$p->Url().'/'.$url.'" >
                            '.Panel::$lang['back'].'
                          </a>
                      </div>
                    </div>'
    ));

});



/*    SEARCH IN UPLOADS
-----------------------------*/

/*
* @name   Search
* @sample /action/searchfiles/findme
*/
$p->route('/action/searchfiles/(:any)', function($query = '') use($p) {
    // get file url
    $directory = UPLOADS;
    // scan to obtain files
    $scan = File::scan($directory);
    // start template
    $result = '<ul class="list-group">';
    // init count to 0
    $count = 0;
    foreach ($scan as $item) {
      // remove storage\$dir
      $item = str_replace(UPLOADS, '', $item);
      // search query with preg_match
      if(preg_match('/'.urldecode($query).'/i', $item)){
        // count +1
        $count++;
        // template
        $result .=
                    '<li class="list-group-item clearfix">
                      '.$item.'
                      <a class="btn btn-primary pull-right" href="
                        '.$p->Url().'/action/uploads/preview/'.base64_encode($directory.$item).'">
                        '.File::name($item).'.'.File::ext($item).'">
                        '.File::name($item).'
                        | <i class="fa fa-arrow-right"></i>
                      </a>
                    </li>';
      }
    }
    $result .= '</ul>';
    // render view
    $p->view('actions',array(
        'title' => Panel::$lang['Search'],
        'html' => '<div class="row">
                      <div class="col-lg-6">
                        <h3><span class="btn btn-primary">'.$count.'</span>
                        results for '.$query.'</h3>
                          '.$result.'
                          <a class="btn btn-danger" href="'.$p->Url().'/uploads" >
                            '.Panel::$lang['back'].'
                          </a>
                      </div>
                    </div>'
    ));

});



/*    SEARCH MEDIA
-----------------------------*/

/*
* @name   Search
* @sample /action/searchmedia/findme
*/

$p->route('/action/searchmedia/(:any)', function($query = '') use($p) {
    // get file url
    $jsonFile = ROOTBASE.'/public/media/mdb.json';
    $json = json_decode(File::getContent($jsonFile),true);
    // start template
    $result = '<ul class="list-group">';
    // init count to 0
    $count = 0;
    foreach ($json as $item) {
      if(preg_match('/'.urldecode($query).'/i', $item['title'])){
          // count +1
          $count++;
          // template
          $result .= '<li class="list-group-item clearfix">
                      '.$p->TextCut($p->toHtml($item['desc']),20).'
                      <a class="btn btn-primary pull-right" href="
                        '.$p->Url().'/action/media/edit/'.$item['id'].' "
                          title="'.$item['title'].'">
                        '.$item['title'].'
                        | <i class="fa fa-arrow-right"></i>
                      </a>
                    </li>';


      }
    }
    $result .= '</ul>';
    // render view
    $p->view('actions',array(
        'title' => Panel::$lang['Search'],
        'html' => '<div class="row">
                      <div class="col-lg-6">
                        <h3><span class="btn btn-primary">'.$count.'</span>
                        results for '.$query.'</h3>
                          '.$result.'
                          <a class="btn btn-danger" href="'.$p->Url().'/media">
                            '.Panel::$lang['back'].'
                          </a>
                      </div>
                    </div>'
    ));

});





/*    SEARCH IN THEMES
-----------------------------*/

/*
* @name   Search
* @sample /action/searchinthemes/findme
*/
$p->route('/action/searchinthemes/(:any)', function($query = '') use($p) {
    // get file url
    $directory = THEMES;
    // scan to obtain files
    $scan = File::scan($directory);
    // start template
    $result = '<ul class="list-group">';
    // init count to 0
    $count = 0;
    foreach ($scan as $item) {
      // remove storage\$dir
      $item = str_replace(THEMES, '', $item);
      // search query with preg_match
      if(preg_match('/'.urldecode($query).'/i', $item)){
        // count +1
        $count++;
        // template
        $result .=
                    '<li class="list-group-item clearfix">
                      '.$item.'
                      <a class="btn btn-primary pull-right" href="
                      '.$p->Url().'/action/themes/edit/'.Token::generate().'/'.base64_encode($directory.$item).'">
                        '.File::name($item).'.'.File::ext($item).'
                        '.File::name($item).'
                        | <i class="fa fa-arrow-right"></i>
                      </a>
                    </li>';
      }
    }
    $result .= '</ul>';
    // render view
    $p->view('actions',array(
        'title' => Panel::$lang['Search'],
        'html' => '<div class="row">
                      <div class="col-lg-6">
                        <h3><span class="btn btn-primary">'.$count.'</span>
                        results for '.$query.'</h3>
                          '.$result.'
                          <a class="btn btn-danger" href="javascript:void(0);" onclick="return history.back(1)">
                            '.Panel::$lang['back'].'
                          </a>
                      </div>
                    </div>'
    ));

});
