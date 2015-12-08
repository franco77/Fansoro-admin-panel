<?php

/**
 * Morfy Media Plugin.
 *
 * (c) Moncho Varela / Nakome <nakome@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
* fn: Action::run('Media')
* Page/File: media/media.md
* Template: media.tpl
* Link all: media
* link by id: {$.site.url}/media?action=view&id=45433423
*/
Action::add('Media', function () {

    // id of media item
    $id = Request::get('id');

    // Obtain json file on public folder
    $json = array();
    $mediaFile = ROOT_DIR.'/public/media/mdb.json';
    if (File::exists($mediaFile)) {
        /*
        *   Json Template 
        *   {
        *       "5606e4ad88ed0": { // id of album
        *           "id": "5606e4ad88ed0", // id of image folder album
        *           "title": "Album title", // title of album
        *           "desc": "Album description", // diescription of album
        *           "thumb": "/public/media/album_thumbs/album_5606e4ad88ed0.jpg", // image preview of album
        *           "images": "/public/media/albums/album_5606e4ad88ed0", // images album
        *           "tag": "Nature", // tag of album for filter with javascript
        *           "width": "700", // style width of tumb
        *           "height": "400" // style height of tumb
        *       }
        *   }
        *
        */
        $json = json_decode(File::getContent($mediaFile), true);
    } else {
        die('OOps Whrere is media.json file!');
    }

    // get single id of album or all albums
    if (Request::get('action') == 'view' && Request::get('id')) {
        // id of album
        $id = Request::get('id');
        if ($id) {
            // get id on json
            $media = $json[$id];
            // get all images of this album
            $mediaImages = File::scan(ROOT_DIR.$media['images']);
            // get images of this album
            $albumImages = '';
            // check files
            if (count($mediaImages) > 0) {
                foreach ($mediaImages as $image) {
                    $albumImages .= '<img class="thumbnail img-responsive" src="public/media/albums/album_'.$id.'/'.File::name($image).'.'.File::ext($image).'">';
                }
            }
            // template
            $templateSingle = '<h3>'.toHtml($media['title']).'</h3>
            '.toHtml($media['desc']).'
            <p><b>Tag: </b><span class="label label-info">'.toHtml($media['tag']).'</span></p>'.
            // all images
             $albumImages;
            // return
            echo $templateSingle;
        }
    } else {
        // all media files
        $templateAll = '';
        foreach ($json as $media) {
            $templateAll .= '<figure>
                <img width="'.$media['width'].'" height="'.$media['height'].'" src="'.Config::get('site.site_url').$media['thumb'].'"/>
                <figcaption>
                    <a href="'.Config::get('site.site_url').'/media?action=view&id='.$media['id'].'" title="'.toHtml($media['title']).'">'.toHtml($media['title']).'</a>
                </figcaption>
            </figure>';
        }

        // check json file if not empty
        if (count($json) > 0) {
            echo $templateAll;
        } else {
            echo '<div class="alert alert-danger">Empty Media albums</div>';
        }

        // get config
        //print_r(json_encode(Config::getConfig(),true));
    }

});

// text to html
function toHtml($str)
{
    // Redefine vars
    $str = (string) $str;

    return html_entity_decode($str, ENT_QUOTES, 'utf-8');
}
