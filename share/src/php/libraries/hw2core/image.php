<?php namespace Hw2;
S_Core::checkAccess();

class S_Image extends S_Object {
    public static function fhw2VerticleText($string) {
        $tlen = strlen($string);
        for ($i = 0; $i < $tlen; $i++) {
            $vtext .= mb_substr($string, $i, 1, 'UTF-8') . "<br />";
        }
        return $vtext;
    }

    /*
    function fhw2SimpleRenderText($text, $filename) {
        $img_handle = @imagecreatetruecolor(15, 200) or die("Cannot Create image");
        // ImageColorAllocate (image, red, green, blue)
        $back_color = ImageColorAllocate($img_handle, 0, 0, 0);
        $txt_color = ImageColorAllocate($img_handle, 255, 255, 255);
        if (ImageStringUp($img_handle, 2, 1, 215, "test", $txt_color)):
            //$document =& \JFactory::getDocument();
            //$document->setMimeEncoding('image/png');
            //header('Content-type: image/png');
            create_image($img_handle, "png", $filename);
            return $filename;
        endif;
        return "";
    }
    */

    public static function fhw2RenderText($txt, $filename,$checkFileExists=true,$imagewidth,$imageheight,$fontsize,$fontangle,$font,$backgroundcolor,$textcolor,$shadowcolor) {

        if ($checkFileExists && file_exists($filename)) {
            // this check speeds up the page loading
            return $filename;
        }

        # detect if the string was passed in as unicode
        $text_encoding = mb_detect_encoding($txt, 'UTF-8, ISO-8859-1');
        # make sure it's in unicode
        if ($text_encoding != 'UTF-8') {
            $txt = mb_convert_encoding($txt, 'UTF-8', $text_encoding);
        }

        # html numerically-escape everything (&#[dec];)
        $text = mb_encode_numericentity($txt,
                        array(0x0, 0xffff, 0, 0xffff), 'UTF-8');

        ### Convert HTML backgound color to RGB
        if (eregi("([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $backgroundcolor, $bgrgb)) {
            $bgred = hexdec($bgrgb[1]);
            $bggreen = hexdec($bgrgb[2]);
            $bgblue = hexdec($bgrgb[3]);
        }

        ### Convert HTML text color to RGB
        if (eregi("([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $textcolor, $textrgb)) {
            $textred = hexdec($textrgb[1]);
            $textgreen = hexdec($textrgb[2]);
            $textblue = hexdec($textrgb[3]);
        }

        ### Convert HTML text color to RGB
        if (eregi("([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $shadowcolor, $shadowgb)) {
            $shadowred = hexdec($shadowrgb[1]);
            $shadowgreen = hexdec($shadowrgb[2]);
            $shadowblue = hexdec($shadowrgb[3]);
        }

        ### Create image
        $im = imagecreate($imagewidth, $imageheight);

        ### Declare image's background color
        $bgcolor = imagecolorallocate($im, $bgred, $bggreen, $bgblue);

        ### Declare image's text color
        $fontcolor = imagecolorallocate($im, $textred, $textgreen, $textblue);
        $shadow = imagecolorallocate($im, $shadowred, $shadowgreen, $shadowblue);

        ### Get exact dimensions of text string
        /* $box = @imageTTFBbox($fontsize,$fontangle,$font,$text);

          ### Get width of text from dimensions
          $textwidth = abs($box[4] - $box[0]);

          ### Get height of text from dimensions
          $textheight = abs($box[5] - $box[1]);

          if($imagewidth >= $imageheight) {
          ### Get x-coordinate of centered text horizontally using length of the image and length of the text
          $xcord = ($imagewidth/2)-($textwidth/2)-2;

          ### Get y-coordinate of centered text vertically using height of the image and height of the text
          $ycord = ($imageheight/2)+($textheight/2);
          } else {
          $xcord = $imagewidth-4;

          $ycord = $imageheight-4;
          } */

        //imagefilledrectangle($im, 0, 0, 0+2, 0+2, $white);

        ### Declare completed image with colors, font, text, and text location
        imagettftext($im, $fontSize, $fontangle, 0 + 4, 0 + 4, $shadowcolor, $font, $text);
        imagettftext($im, $fontsize, $fontangle, 0 + 5, 0 + 5, $fontcolor, $font, $text);

        create_image($im, 'png', $filename);
        return $filename;
    }

    public static function fhw2JpegRotate($file,$degrees = 90) {
        // Content type
        header('Content-type: image/jpeg');

        // Load
        $source = imagecreatefromjpeg($file);

        // Rotate
        $rotate = imagerotate($source, $degrees, 0);

        // Output and save
        create_image($rotate, 'jpeg', './images/rotate.jpg');
        return './images/rotate.jpg';
    }

    public static function create_image($img, $type, $fname = null) {
        ob_start(); // store output
        if ($type == "png") {
            imagepng($img); // output to buffer
        } else if ($type == "jpeg") {
            imagejpeg($img);
        } else {
            die('error image type');
        }
        file_put_contents($fname, ob_get_contents(), FILE_BINARY); // write buffer to file
        ob_end_clean(); // clear and turn off buffer
        imagedestroy($img);
    }

    public static function fixImgGalleryQuality($src_file,&$dest_qual,&$debugoutput) {
        //[hw2] workaround for quality image optimized for web
        // use value 99 to get this kind of resizing
        if ($dest_qual==99) {
            //$maxsize=409600; // 400KB
            //$temp_file = tempnam(sys_get_temp_dir(), 'checksizer');
            //imagejpeg($src_file, $temp_file,$dest_qual); 
            //$size = filesize($temp_file);
            //unlink($temp_file);
            //$debugoutput .= 'filesize: '.$size.'<br />';
            //if ($size > $maxsize)
            //    $dest_qual=90;
            $dest_qual = self::make_jpeg_target_size($src_file,400); 
            $debugoutput .= 'quality: '.$dest_qual.'<br />';
        }
    }

    public static function make_jpeg_target_size($file,$targetKB){
        $src=$file;
        $target = $targetKB*1024;
        $start_q = 1;
        $cur_q = 99;
        while($cur_q > $start_q){
            $temp_file = \tempnam(sys_get_temp_dir(), 'checksizer');
            $out = \imagejpeg($src, $temp_file, $cur_q);
            $size = \filesize($temp_file);
            unlink($temp_file);
            if($size <= $target){          
                return $cur_q;
            }
            $cur_q=$cur_q-1;
        }
    }
}
?>
