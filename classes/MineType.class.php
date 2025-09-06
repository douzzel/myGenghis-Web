<?php

    class MineType {

        public static function mime_content_type($filename) {
            
            $mime_types = array(
        
                'txt' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'swf' => 'application/x-shockwave-flash',
                'flv' => 'video/x-flv',
            
                // images
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'svgz' => 'image/svg+xml',
            
                // archives
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'exe' => 'application/x-msdownload',
                'msi' => 'application/x-msdownload',
                'cab' => 'application/vnd.ms-cab-compressed',
            
                // audio/video
                'mp3' => 'audio/mpeg',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',
                'wav' => 'audio/wav',
            
                // adobe
                'pdf' => 'application/pdf',
                'psd' => 'image/vnd.adobe.photoshop',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',
            
                // ms office
                'doc' => 'application/msword',
                'rtf' => 'application/rtf',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',
            
                // open office
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            );
            
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            //$ext = strtolower(array_pop(explode('.', $filename)));
            
           
            if (array_key_exists($ext, $mime_types)) {
                return self::font_awesome_file_icon_class($mime_types[$ext]);
            }elseif (function_exists('finfo_open')) {
                if(file_exists(FILEINFO_MIME)){
                    $finfo = finfo_open(FILEINFO_MIME);
                    $mimetype = finfo_file($finfo, $filename);
                    finfo_close($finfo);
                    return $mimetype;
                }else{
                    return;
                }
                
            } else {
                return 'application/octet-stream';
            }
        }

        public static function font_awesome_file_icon_class( $mime_type ) {
            // List of official MIME Types: http://www.iana.org/assignments/media-types/media-types.xhtml
            static $font_awesome_file_icon_classes = array(
              // Images
              'image' => 'far fa-file-image',
              // Audio
              'audio' => 'far fa-file-audio',
              // Video
              'video' => 'far fa-file-video',
              // Documents
              'application/pdf' => 'fas fa-file-pdf',
              'text/plain' => 'far fa-file-alt',
              'text/html' => 'far fa-file-code',
              'application/json' => 'far fa-file-code',
              // Archives
              'application/gzip' => 'fas fa-file-archive',
              'application/zip' => 'fas fa-file-archive',
              // Misc
              'application/octet-stream' => 'far fa-file-alt',
            );
            if (isset($font_awesome_file_icon_classes[ $mime_type ])) {
              return $font_awesome_file_icon_classes[ $mime_type ];
            }
            $mime_parts = explode('/', $mime_type, 2);
            $mime_group = $mime_parts[0];
            if (isset($font_awesome_file_icon_classes[ $mime_group ])) {
              return $font_awesome_file_icon_classes[ $mime_group ];
            }
            return "far fa-file-alt";
        }
    }
?>