<?php

/*
=====================================================
 Author: MaxLazar
 http://eec.ms
=====================================================
 File: pi.qr_code.php
-----------------------------------------------------
 Purpose: QR Code Generator
=====================================================
#   d = data         URL encoded data.
#   e = ECC level    L or M or Q or H   (default M)
#   s = module size  (dafault PNG:4 JPEG:8)
#   v = version      1-40 or Auto select if you do not set.
#   t = image type   J:jpeg image , other: PNG image
*/

$plugin_info = array(
    'pi_name'   => 'MX QR code',
    'pi_version'   => '3.0.1',
    'pi_author'   => 'Max Lazar',
    'pi_author_url'  => 'http://eec.ms',
    'pi_description' => 'QR Code Generator',
    'pi_usage'   => Qr_code::usage()
);


class Qr_code {

    var $return_data="";

    /**
     * [__construct description]
     */
    public function __construct() {

        $libfolder = PATH_THIRD.'qr_code/';

        $base_path = ( ! ee()->TMPL->fetch_param( 'base_path' ) ) ? $_SERVER['DOCUMENT_ROOT']."/" : ee()->TMPL->fetch_param( 'base_path' );

        $base_path = str_replace( "\\", "/", $base_path );
        $base_path = reduce_double_slashes( $base_path );

        $cache = ( ! ee()->TMPL->fetch_param( 'cache' ) ) ? '' : ee()->TMPL->fetch_param( 'cache' );

        $data = array(
            'd' =>  ( !ee()->TMPL->fetch_param( 'data' ) ) ? ee()->TMPL->tagdata : str_replace( SLASH, '/', ee()->TMPL->fetch_param( 'data' ) ),
            'e' => ( !ee()->TMPL->fetch_param( 'ecc' ) ) ? 'M' : ee()->TMPL->fetch_param( 'ecc' ),
            't' => ( !ee()->TMPL->fetch_param( 'type' ) ) ? 'PNG' : ee()->TMPL->fetch_param( 'type' ),
            's' => ( !ee()->TMPL->fetch_param( 'size' ) ) ? '' : ee()->TMPL->fetch_param( 'size' ),
            'v' => ( !ee()->TMPL->fetch_param( 'version' ) ) ? null : ee()->TMPL->fetch_param( 'version' ),
        );

        $action = ( !ee()->TMPL->fetch_param( 'action' ) ) ? ee()->TMPL->tagdata : ee()->TMPL->fetch_param( 'action' );

        $data['bk_color']  = ( ee()->TMPL->fetch_param( 'bk_color' ) ) ? ltrim( ee()->TMPL->fetch_param( 'bk_color' ), '#' ) : 'ffffff';
        $data['px_color']  = ( ee()->TMPL->fetch_param( 'px_color' ) ) ? ltrim( ee()->TMPL->fetch_param( 'px_color' ), '#' ) : '000000';
        $data['outline_size']  = ( ee()->TMPL->fetch_param( 'outline_size' ) ) ? ee()->TMPL->fetch_param( 'outline_size' ) : 2;

        switch ( $action ) {
        case "sms":
            $tel = ( !ee()->TMPL->fetch_param( 'tel' ) ) ? '': ee()->TMPL->fetch_param( 'tel' );
            $data['d'] = "SMSTO:".( ( !ee()->TMPL->fetch_param( 'tel' ) ) ? '': ee()->TMPL->fetch_param( 'tel' ) ).':'.$data['d'];
            break;
        case "email":
            $data['d'] = "SMTP:".( ( !ee()->TMPL->fetch_param( 'email' ) ) ? '': ee()->TMPL->fetch_param( 'email' ) ).':'.( ( !ee()->TMPL->fetch_param( 'sabj' ) ) ? '': ee()->TMPL->fetch_param( 'sabj' ) ).':'.$data['d'];
            break;
        case "tel":
            $data['d'] = "TEL:".( ( !ee()->TMPL->fetch_param( 'tel' ) ) ? '': ee()->TMPL->fetch_param( 'tel' ) );
            break;
        case "site":
            $data['d'] = $this->SmartUrlEncode( $data['d'] );
            break;
        case "bm":
            $data['d'] = "MEBKM:TITLE:".( ( !ee()->TMPL->fetch_param( 'title' ) ) ? '': ee()->TMPL->fetch_param( 'title' ) ).':'.urlencode( $data['d'] );
            break;
        }

        $base_cache = reduce_double_slashes( $base_path."images/cache/" );
        $base_cache = ( !ee()->TMPL->fetch_param( 'base_cache' ) ) ? $base_cache : ee()->TMPL->fetch_param( 'base_cache' );
        $base_cache = reduce_double_slashes( $base_cache );

        if ( !is_dir( $base_cache ) ) {
            // make the directory if we can
            if ( !mkdir( $base_cache, 0777, true ) ) {
                ee()->TMPL->log_item( "Error: could not create cache directory ".$base_cache." with 777 permissions" );
                return ee()->TMPL->no_results();
            }
        }

        $file_ext = ( $data['t'] =='J'?'.jpeg':'.png' );
        $file_name = md5( serialize( $data ) ).$file_ext;

        if ( !is_readable( $base_cache.$file_name ) ) {
            $qrcode_data_string   = $data['d'];
            $qrcode_error_correct = $data['e'];
            $qrcode_module_size   =  $data['s'];
            $qrcode_version       = $data['v'];
            $qrcode_image_type    = $data['t'];

            $path  = $libfolder.'qrcode_lib/data';
            $image_path = $libfolder.'qrcode_lib/image';

            require_once $libfolder.'qrcode/qrlib.php';

            QRcode::png( $qrcode_data_string, $base_cache.$file_name, $qrcode_error_correct , $qrcode_module_size, $data['outline_size'], false, $data['px_color'], $data['bk_color'] );
        }


        return $this->return_data =reduce_double_slashes( "/".str_replace( $base_path, '', $base_cache.$file_name ) );
    }

    /**
     * [SmartUrlEncode description]
     *
     * @param [type]  $url [description]
     */
    private function SmartUrlEncode( $url ) {
        if ( strpos( $url, '=' ) === false ):
            return $url;
        else:
            $startpos = strpos( $url, "?" );
        $tmpurl=substr( $url, 0 , $startpos+1 ) ;
        $qryStr=substr( $url, $startpos+1 ) ;
        $qryvalues=explode( "&", $qryStr );
        foreach ( $qryvalues as $value ):
            $buffer=explode( "=", $value );
        $buffer[1]=urlencode( $buffer[1] );
        endforeach;
        $finalqrystr = implode( "&amp;", $qryvalues );
        $finalURL=$tmpurl . $finalqrystr;
        return $finalURL;
        endif;
    }

    // ----------------------------------------
    //  Plugin Usage
    // ----------------------------------------

    // This function describes how the plugin is used.
    //  Make sure and use output buffering

    public static function usage() {
        // for performance only load README if inside control panel
        return REQ === 'CP' ? file_get_contents( dirname( __FILE__ ).'/README.md' ) : null;
    }
    /* END */

}
