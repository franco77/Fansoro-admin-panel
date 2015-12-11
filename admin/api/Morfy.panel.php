<?php

defined('PANEL_ACCESS') or die('No direct script access.');

/**
 * Panel.
 *
 * @author Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @link http://monchovarela.es
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Panel
{
    private $routes = array();

    /**
     * The Name of Panel.
     *
     * @var string
     */
    public $appName = 'Morfy Panel';

    /**
     * The version of Panel.
     *
     * @var string
     */
    public $version = '2.2.1';

    /**
     * Site Config array.
     *
     * @var array
     */
    public static $site;

    /**
     * Site language array.
     *
     * @var array
     */
    public static $lang;

    /**
     * Load Config.
     */
    protected function loadConfig()
    {
        if (file_exists($site_config_path = SITE)) {
            static::$site = Spyc::YAMLLoad(file_get_contents($site_config_path));
        } else {
            die('Oops.. Where is config files ?!');
        }
    }

    /**
     * Load Language.
     */
    protected function loadLanguage()
    {
        if (file_exists($language = ROOT.'/'.'lang'.'/'.static::$site['backend_language'].'.yml')) {
            static::$lang = Spyc::YAMLLoad(file_get_contents($language));
        } else {
            die('Oops.. Where is language file ?!');
        }
    }

    /**
     *
     */
    public function getMsg()
    {
        //Top of file
        if (Session::get('msg')) {
            $message = Session::get('msg');
            Session::delete('msg');
        }
        if (isset($message)) {
            echo
            '<div class="alert alert-success notification">
					<img src="'.$this->Assets('morfy-icon.png', 'img').'" />
					<span>'.$message.'</span>
				</div>
				<script type="text/javascript">
					var notif = document.querySelector(".notification"),
					m = setTimeout(function(){
						notif.classList.add("notif-hide");
						var d = setTimeout(function(){notif.remove();clearTimeout(d);},1000);
						clearTimeout(m);
					},3000);
				</script>';
        }
    }

    /**
     * @param unknown $msg
     */
    public function setMsg($msg)
    {
        Session::set('msg', $msg);
    }

    /*
    * Get size of folder
    * $p->folderSize(path);
    *
    * @param string $dir
    * @return string
    */

    /**
     * @param unknown $dir
     *
     * @return unknown
     */
    public function folderSize($dir)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $totalSize = 0;
        foreach ($iterator as $file) {
            $totalSize += $file->getSize();
        }

        return  round($totalSize / 1024, 2).' KB';
    }

    /**
     * Get size of file
     * $p->fileSize(file);.
     *
     * @param string $file
     *
     * @return string
     */
    public function filesize($file)
    {
        $a = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $pos = 0;
        $size = filesize($file);
        while ($size >= 1024) {
            $size /= 1024;
            ++$pos;
        }

        return round($size, 2).' '.$a[$pos];
    }

    /*
    *   Short text  TextCut('lorem ipsum dolor',$chars='25')
    */

    /**
     * @param unknown $text
     * @param unknown $chars (optional)
     *
     * @return unknown
     */
    public function TextCut($text, $chars = '25')
    {
        // length of text
        $length = strlen($text);
        // strip tags
        $text = strip_tags($text);
        // reducce
        $text = substr($text, 0, $chars);
        // in end put ..
        if ($length > $chars) {
            $text = $text.'...';
        }

        return $text;
    }

    /**
     * Convert html to plain text
     *  https://github.com/monstra-cms/monstra/blob/dev/libraries/Gelato/Html/Html.php
     *  $p->toText('test');.
     *
     * @param string $str String
     *
     * @return string
     */
    public function toText($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
    }

    /**
     * Convert plain text to html
     * https://github.com/monstra-cms/monstra/blob/dev/libraries/Gelato/Text/Text.php
     * $p->toHtml('test');.
     *
     * @param string $str
     *
     * @return string
     */
    public function toHtml($str)
    {
        // Redefine vars
        $str = (string) $str;

        return html_entity_decode($str, ENT_QUOTES, 'utf-8');
    }

    /**
     *   Get  pretty url like hello-world.
     *
     *
     * @param unknown $str
     *
     * @return string
     */
    public function SeoLink($str)
    {
        //Lower case everything
        $str = strtolower($str);
        //Make alphanumeric (removes all other characters)
        $str = preg_replace("/[^a-z0-9_\s-]/", '', $str);
        //Clean up multiple dashes or whitespaces
        $str = preg_replace("/[\s-]+/", ' ', $str);
        //Convert whitespaces and underscore to dash
        $str = preg_replace("/[\s_]/", '-', $str);

        return $str;
    }

    /**
     * Debug
     * $p->Debug($var);.
     *
     * @param string $var
     *
     * @return string
     */
    public function Debug($var)
    {
        return print_r('<pre class="debuger">$var</pre>');
    }

    /**
     * Compress a folder to a zip archive
     * $p->compress('public','backups/name.zip');.
     *
     * @param string  $source,$dest
     * @param unknown $dest
     *
     * @return string
     */
    public function Zip($source, $dest)
    {
        // Get real path for our folder
        $rootPath = realpath($source);
        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($dest, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing object
        return $zip->close();
    }

    /**
     * Uncompress files
     * $p->unZip('file.zip','storage');.
     *
     * @return bolean
     *
     * @param unknown $temp
     * @param unknown $path
     */
    public function unZip($temp, $path)
    {
        $zip = new ZipArchive();
        $res = $zip->open($temp);
        if ($res === true) {
            $zip->extractTo($path);
            $zip->close();
            unlink($temp);
        }
    }

    /**
     * Image resize.
     *
     * @param unknown $name
     * @param unknown $path
     * @param int     $width
     * @param int     $height
     * @param unknown $upload (optional)
     *
     * @return unknown
     */
    public function resize($name, $path, $width = 1024, $height = 768, $upload = true)
    {
        if ($upload) {
            $file = $name['tmp_name'];
        } else {
            $file = $name;
        }

        // Get original image x y
        list($w, $h, $type) = getimagesize($file);
        // calculate new image size with ratio
        $ratio = max($width / $w, $height / $h);
        $h = ceil($height / $ratio);
        $x = ($w - $width / $ratio) / 2;
        $w = ceil($width / $ratio);
        // read binary data from image file
        $imgString = file_get_contents($file);
        // create image from string
        $image = imagecreatefromstring($imgString);
        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $image, 0, 0, $x, 0, $width, $height, $w, $h);
        // Save image
        switch (strtolower(image_type_to_mime_type($type))) {
        case 'image/jpeg':
            imagejpeg($tmp, $path, 100);
            break;
        case 'image/png':
            imagepng($tmp, $path, 0);
            break;
        case 'image/gif':
            imagegif($tmp, $path);
            break;
        default:
            exit;
            break;
        }

        return $path;
        // cleanup memory
        imagedestroy($image);
        imagedestroy($tmp);
    }

    /**
     * Get url.
     *
     *  <code>
     *       $p->url()
     *  </code>
     *
     * @return var
     */
    public function url()
    {
        // Create the Home URL
        return static::$site['site_url'].'/'.static::$site['backend_folder'];
    }

    /**
     * Get Views.
     *
     *  <code>
     *       $p->partial($path, $vars = ['title' => 'i am home'])
     *  </code>
     *
     * @return require
     *
     * @param string $path folder of views
     * @param array  $vars (optional) array of options
     */
    public function partial($path, $vars = array())
    {
        if ($vars) {
            extract($vars);
        }
        include_once PARTIALS.'/'.trim($path, '/').'.html';
    }

    /**
     * Get Views.
     *
     *  <code>
     *       $p->view($path, $vars = ['title' => 'i am home'])
     *  </code>
     *
     * @return require
     *
     * @param string $path folder of views
     * @param array  $vars (optional) array of options
     */
    public function view($path, $vars = array())
    {
        if ($vars) {
            extract($vars);
        }
        require VIEWS.'/'.trim($path, '/').'.html';
    }

    /**
     * Get Assets file.
     *
     *  <code>
     *       $p->Assets($file,$type)
     *  </code>
     *
     * @param string $file style.css
     * @param string $type folder
     *
     * @return echo
     */
    public function Assets($file, $type)
    {
        return  $this->url().'/assets/'.$type.'/'.trim($file, '/');
    }

    /**
     * Get routes.
     *
     *  <code>
     *       $m = new Panel();
     *       $m->route($patterns, $callback)
     *  </code>
     *
     * @param patterns $patterns links
     * @param callback $callback function
     */
    public function route($patterns, $callback)
    {
        if (!is_array($patterns)) {
            $patterns = array($patterns);
        }
        foreach ($patterns as $pattern) {
            $pattern = trim($pattern, '/');
            $pattern = str_replace(
                array('\(', '\)', '\|', '\:any', '\:num', '\:all', '#'),
                array('(', ')', '|', '[^/]+', '\d+', '.*?', '\#'),
                preg_quote($pattern, '/'));
            $this->routes['#^'.$pattern.'$#'] = $callback;
        }
    }

    /**
     * Execute routes routes.
     *
     *  <code>
     *       $m->lauch()
     *  </code>
     */
    public function lauch()
    {

        // require classes
        require_once LIBRARIES.'/mustangostang/spyc/Spyc.php';
        require_once LIBRARIES.'/force/arr/Arr.php';
        require_once LIBRARIES.'/force/session/Session.php';
        require_once LIBRARIES.'/force/token/Token.php';
        require_once LIBRARIES.'/force/http/Request.php';
        require_once LIBRARIES.'/force/http/Response.php';
        require_once LIBRARIES.'/force/url/Url.php';
        require_once LIBRARIES.'/force/filesystem/File.php';
        require_once LIBRARIES.'/force/filesystem/Dir.php';

        // Load config file
        $this->loadConfig();

        // Load language file
        $this->loadLanguage();

        // Sanitize URL to prevent XSS - Cross-site scripting
        Url::runSanitizeURL();

        // Send default header and set internal encoding
        header('Content-Type: text/html; charset='.static::$site['backend_charset']);
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(static::$site['backend_charset']);
        function_exists('mb_internal_encoding') and mb_internal_encoding(static::$site['backend_charset']);

        // Gets the current configuration setting of magic_quotes_gpc and kill magic quotes
        if (get_magic_quotes_gpc()) {

            /**
             * @param unknown $value (reference)
             *
             * @return unknown
             */
            function stripslashesGPC(&$value)
            {
                $value = stripslashes($value);
            }
            array_walk_recursive($_GET, 'stripslashesGPC');
            array_walk_recursive($_POST, 'stripslashesGPC');
            array_walk_recursive($_COOKIE, 'stripslashesGPC');
            array_walk_recursive($_REQUEST, 'stripslashesGPC');
        }

        // Start the session
        Session::start();

        $url = $_SERVER['REQUEST_URI'];
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if (strpos($url, $base) === 0) {
            $url = substr($url, strlen($base));
        }
        $url = trim($url, '/');
        foreach ($this->routes as $pattern => $callback) {
            if (preg_match($pattern, $url, $params)) {
                array_shift($params);

                return call_user_func_array($callback, array_values($params));
            }
        }
    }
}
