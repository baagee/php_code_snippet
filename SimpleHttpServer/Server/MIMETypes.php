<?php
/**
 * Desc: Content type类型定义
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午8:33
 */

namespace SimServer;
class MIMETypes
{
    const MIME_TYPE_MAP = [
        'flv'   => self::VIDEO_X_FLV,
        'avi'   => self::VIDEO_X_MSVIDEO,
        'wmv'   => self::VIDEO_X_MS_WMV,
        'mov'   => self::VIDEO_QUICKTIME,
        'mp4'   => self::VIDEO_MP4,
        'mpg'   => self::VIDEO_MPEG,
        'mpeg'  => self::VIDEO_MPEG,
        'ogg'   => self::AUDIT_OGG,
        'mp3'   => self::AUDIT_MPEG,
        'json'  => self::APPLICATION_JSON,
        'pdf'   => self::APPLICATION_PDF,
        'js'    => self::APPLICATION_X_JAVASCRIPT,
        'html'  => self::TEXT_HTML,
        'shtml' => self::TEXT_HTML,
        'htm'   => self::TEXT_HTML,
        'css'   => self::TEXT_CSS,
        'xml'   => self::TEXT_XML,
        'gif'   => self::IMAGE_GIF,
        'jpeg'  => self::IMAGE_JPEG,
        'jpg'   => self::IMAGE_JPEG,
        'png'   => self::IMAGE_PNG,
        'ico'   => self::IMAGE_X_ICON,
        'webp'  => self::IMAGE_WEBP,
        'txt'   => self::TEXT_PLAIN
    ];
    //application/x-javascript              js;
    const APPLICATION_X_JAVASCRIPT = 'application/x-javascript';
    //        'html', 'htm', 'shtml'
    const TEXT_HTML = 'text/html';

    // css
    const TEXT_CSS = 'text/css';

    //xml
    const TEXT_XML = 'text/xml';

    //gif
    const IMAGE_GIF = 'image/gif';

    //'jpeg', 'jpg'
    const IMAGE_JPEG = 'image/jpeg';

    //png
    const IMAGE_PNG = 'image/png';

    //icon
    const IMAGE_X_ICON = 'image/x-icon';

    //webp
    const IMAGE_WEBP = 'image/webp';

    //txt
    const TEXT_PLAIN = 'text/plain';

    const APPLICATION_JSON = 'application/json';
    const APPLICATION_PDF  = 'application/pdf';
    const AUDIT_OGG        = 'audit/ogg';
    const AUDIT_MPEG       = 'audit/mpeg';
    const VIDEO_MP4        = 'video/mp4';
    const VIDEO_MPEG       = 'video/mpeg';
    const VIDEO_X_MS_WMV   = 'video/x-ms-wmv';
    const VIDEO_QUICKTIME  = 'video/quicktime';
    const VIDEO_X_MSVIDEO  = 'video/x-msvideo';
    const VIDEO_X_FLV      = 'video/x-flv';
}