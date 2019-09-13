<?php
namespace Mx\Qr_code;

require_once __DIR__.'/vendor/autoload.php';

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;


/**
 * MX QR Code plugin for ExpressionEngine CMS 5.x
 *
 * MX QR Code helps you to generate qr codes.
 *
 * @link      https://eec.ms
 * @copyright Copyright (c) 2019 Max Lazar
 */


$plugin_info = array(
    'pi_name' => 'MX QR code',
    'pi_version' => '3.0.1',
    'pi_author' => 'Max Lazar',
    'pi_author_url' => 'http://eec.ms',
    'pi_description' => 'QR Code Generator',
    'pi_usage' => Qr_code::usage(),
);

class Qr_code
{
    public $return_data = '';

    private $ecc_levels = array(
            'L' => 'low',
            'M' => 'medium',
            'Q' => 'quartile',
            'H' => 'high',
        );

    private $_cache_path = '';

    /**
     * Package name.
     *
     * @var string
     */
    protected $package;

    /**
     * [__construct description].
     */
    public function __construct()
    {
        $this->_cache_path = (!$this->_cache_path) ? str_replace('\\', '/', PATH_CACHE).'/email_from_template' : false;
        $this->package = basename(__DIR__);
        $this->info = ee('App')->get($this->package);

        $data = array(
            'd' => (!ee()->TMPL->fetch_param('data')) ? ee()->TMPL->tagdata : str_replace(SLASH, '/', ee()->TMPL->fetch_param('data')),
            'e' => (!ee()->TMPL->fetch_param('ecc')) ? 'medium' :
                    (isset($this->ecc_levels[strtoupper(ee()->TMPL->fetch_param('ecc'))]) ? $this->ecc_levels[strtoupper(ee()->TMPL->fetch_param('ecc'))] :
                        'medium'),
            't' => (!ee()->TMPL->fetch_param('type')) ? 'PNG' : ee()->TMPL->fetch_param('type'),
            's' => (!ee()->TMPL->fetch_param('size')) ? '200' : ee()->TMPL->fetch_param('size'),
            'v' => (!ee()->TMPL->fetch_param('version')) ? null : ee()->TMPL->fetch_param('version'),
        );

        $action = (!ee()->TMPL->fetch_param('action')) ? ee()->TMPL->tagdata : ee()->TMPL->fetch_param('action');

        $data['bk_color'] = (ee()->TMPL->fetch_param('bk_color')) ? ltrim(ee()->TMPL->fetch_param('bk_color'), '#') : 'ffffff';
        $data['px_color'] = (ee()->TMPL->fetch_param('px_color')) ? ltrim(ee()->TMPL->fetch_param('px_color'), '#') : '000000';
        $data['bk_opacity'] = (ee()->TMPL->fetch_param('bk_opacity')) ? ee()->TMPL->fetch_param('bk_opacity') : '1';
        $data['px_opacity'] = (ee()->TMPL->fetch_param('px_opacity')) ? ee()->TMPL->fetch_param('px_opacity') : '1';

        $data['outline_size'] = (ee()->TMPL->fetch_param('outline_size')) ? ee()->TMPL->fetch_param('outline_size') : 2;
        $data['margin'] = (ee()->TMPL->fetch_param('margin')) ? ee()->TMPL->fetch_param('margin') : 0;
        $data['logo'] = (ee()->TMPL->fetch_param('logo')) ? ee()->TMPL->fetch_param('logo') : false;
        $data['logo_size'] = (ee()->TMPL->fetch_param('logo_size')) ? ee()->TMPL->fetch_param('logo_size') : false;
        $data['base64_encode'] = ee()->TMPL->fetch_param('base64_encode', 'no');

        switch ($action) {
        case 'sms':
            $tel = (!ee()->TMPL->fetch_param('tel')) ? '' : ee()->TMPL->fetch_param('tel');
            $data['d'] = 'SMSTO:'.((!ee()->TMPL->fetch_param('tel')) ? '' : ee()->TMPL->fetch_param('tel')).':'.$data['d'];
            break;
        case 'email':
            $data['d'] = 'SMTP:'.((!ee()->TMPL->fetch_param('email')) ? '' : ee()->TMPL->fetch_param('email')).':'.((!ee()->TMPL->fetch_param('sabj')) ? '' : ee()->TMPL->fetch_param('sabj')).':'.$data['d'];
            break;
        case 'tel':
            $data['d'] = 'TEL:'.((!ee()->TMPL->fetch_param('tel')) ? '' : ee()->TMPL->fetch_param('tel'));
            break;
        case 'site':
            $data['d'] = $this->SmartUrlEncode($data['d']);
            break;
        case 'bm':
            $data['d'] = 'MEBKM:TITLE:'.((!ee()->TMPL->fetch_param('title')) ? '' : ee()->TMPL->fetch_param('title')).':'.urlencode($data['d']);
            break;
        }

        return $this->return_data = $this->_doQRcode($data);
    }

    private function _doQRcode($data)
    {
        $output = '';
        $base_path = (!ee()->TMPL->fetch_param('base_path')) ? $_SERVER['DOCUMENT_ROOT'].'/' : ee()->TMPL->fetch_param('base_path');
        $base_path = str_replace('\\', '/', $base_path);
        $base_path = reduce_double_slashes($base_path);
        $cache = (!ee()->TMPL->fetch_param('cache')) ? '' : ee()->TMPL->fetch_param('cache');

        $base_cache = reduce_double_slashes($base_path.'images/cache/');
        $base_cache = (!ee()->TMPL->fetch_param('base_cache')) ? $base_cache : ee()->TMPL->fetch_param('base_cache');
        $base_cache = reduce_double_slashes($base_cache);

        if (!is_dir($base_cache)) {
            // make the directory if we can
            if (!mkdir($base_cache, 0777, true)) {
                ee()->TMPL->log_item('Error: could not create cache directory '.$base_cache.' with 777 permissions');

                return ee()->TMPL->no_results();
            }
        }

        $file_ext = ('J' == $data['t'] ? '.jpeg' : '.png');
        $file_name = md5(serialize($data)).$file_ext;

        if (!is_readable($base_cache.$file_name)) {
            $qrcode_data_string = $data['d'];
            $qrcode_error_correct = $data['e'];
            $qrcode_module_size = $data['s'];
            $qrcode_version = $data['v'];
            $qrcode_image_type = $data['t'];

            $qrCode = new QrCode($qrcode_data_string);

            $qrCode->setSize($data['s']);
            $qrCode->setMargin($data['margin']);
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel($data['e']));
            $qrCode->setForegroundColor($this->hex2rgba($data['px_color'], $data['px_opacity']));
            $qrCode->setBackgroundColor($this->hex2rgba($data['bk_color'], $data['bk_opacity']));

            if ($data['logo']) {
                $qrCode->setLogoPath($this->url2local($data['logo']));
                if ($data['logo_size']) {
                    $size = explode(',', $data['logo_size']);
                    if (2 == count($size)) {
                        $qrCode->setLogoSize($size[0], $size[1]);
                    }
                }
            }

            if ('yes' == $data['base64_encode']) {
                $output = 'data:image/png;base64, '.base64_encode($qrCode->writeString());
            } else {
                $qrCode->writeFile($base_cache.$file_name);
                $output = reduce_double_slashes('/'.str_replace($base_path, '', $base_cache.$file_name));
            }
        } else {
            $output = reduce_double_slashes('/'.str_replace($base_path, '', $base_cache.$file_name));
        }


        return $output;
    }

    /**
     * [SmartUrlEncode description].
     *
     * @param [type] $url [description]
     */
    private function SmartUrlEncode($url)
    {
        if (false === strpos($url, '=')):
            return $url; else:
            $startpos = strpos($url, '?');
        $tmpurl = substr($url, 0, $startpos + 1);
        $qryStr = substr($url, $startpos + 1);
        $qryvalues = explode('&', $qryStr);
        foreach ($qryvalues as $value):
            $buffer = explode('=', $value);
        $buffer[1] = urlencode($buffer[1]);
        endforeach;
        $finalqrystr = implode('&amp;', $qryvalues);
        $finalURL = $tmpurl.$finalqrystr;

        return $finalURL;
        endif;
    }

    public function url2local($url)
    {
        $this->log_debug_message('Load remote file', 'start');
        //Download the file using file_get_contents.
        $downloadedFileContents = file_get_contents($url);

        //Check to see if file_get_contents failed.
        if (false === $downloadedFileContents) {
            throw new Exception('Failed to download file at: '.$url);
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $file_name = $this->_cache_path.'/'.time().'.'.$extension;

        file_put_contents($file_name, $downloadedFileContents);

        return $file_name;
    }

    /* Convert hexdec color string to rgb(a) string */

    public function hex2rgba($color, $opacity = false)
    {
        $output = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];

        //Return default if no color provided
        if (empty($color)) {
            return $output;
        }

        //Sanitize $color if "#" is provided
        if ('#' == $color[0]) {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (6 == strlen($color)) {
            $hex = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
        } elseif (3 == strlen($color)) {
            $hex = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        } else {
            return $output;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output['a'] = $opacity;
        }

        $output['r'] = $rgb[0];
        $output['g'] = $rgb[1];
        $output['b'] = $rgb[2];
        //Return rgb(a) color string
        return $output;
    }

    /**
     * Simple method to log a debug message to the EE Debug console.
     *
     * @param string $method
     * @param string $message
     */
    protected function log_debug_message($method = '', $message = '')
    {
        ee()->TMPL->log_item('&nbsp;&nbsp;***&nbsp;&nbsp;'.$this->package." - $method debug: ".$message);
    }

    // ----------------------------------------
    //  Plugin Usage
    // ----------------------------------------

    // This function describes how the plugin is used.
    //  Make sure and use output buffering

    public static function usage()
    {
        // for performance only load README if inside control panel
        return REQ === 'CP' ? file_get_contents(dirname(__FILE__).'/README.md') : null;
    }

    /* END */
}
