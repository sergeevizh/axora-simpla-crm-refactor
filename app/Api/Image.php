<?php

namespace App\Api;

use Imagick;

class Image extends Axora
{
    private $allowed_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function __construct()
    {
        parent::__construct();
    }

    public function resize_image($filename, $width=0, $height=0, $set_watermark=false) {
        $resized_filename = $this->add_resize_params($filename, '', $width, $height, $set_watermark);
        $resized_filename_encoded = $resized_filename;

        if (substr($resized_filename_encoded, 0, 7) == 'http://' || substr($resized_filename_encoded, 0, 8) == 'https://') {
            $resized_filename_encoded = rawurlencode($resized_filename_encoded);
        }

        $resized_filename_encoded = rawurlencode($resized_filename_encoded);

        return $this->config->root_url.'/'.$this->config->resized_images_dir.$resized_filename_encoded.'?'.$this->config->token($resized_filename);
    }

    public function crop_image($filename, $width=0, $height=0, $set_watermark=false) {
        $resized_filename = $this->add_resize_params($filename, 'crop', $width, $height, $set_watermark);
        $resized_filename_encoded = $resized_filename;

        if (substr($resized_filename_encoded, 0, 7) == 'http://' || substr($resized_filename_encoded, 0, 8) == 'https://') {
            $resized_filename_encoded = rawurlencode($resized_filename_encoded);
        }

        $resized_filename_encoded = rawurlencode($resized_filename_encoded);

        return $this->config->root_url.'/'.$this->config->resized_images_dir.$resized_filename_encoded.'?'.$this->config->token($resized_filename);
    }
    /**
     * Создание превью изображения
     * @param  $filename файл с изображением (без пути к файлу)
     * @return string имя файла превью
     */
    public function resize($filename)
    {
        list($source_file, $type, $width, $height, $set_watermark) = $this->get_resize_params($filename);

        // Если вайл удаленный (http://), зальем его себе
        if (substr($source_file, 0, 7) == 'http://' || substr($source_file, 0, 8) == 'https://') {
            // Имя оригинального файла
            if (!$original_file = $this->download_image($source_file)) {
                return false;
            }
        } else {
            $original_file = $source_file;
        }

        $resized_file = $this->add_resize_params($original_file, $type, $width, $height, $set_watermark);


        // Пути к папкам с картинками
        $originals_dir = $this->config->root_dir.$this->config->original_images_dir;
        $preview_dir = $this->config->root_dir.$this->config->resized_images_dir;

        $watermark_offset_x = $this->settings->watermark_offset_x;
        $watermark_offset_y = $this->settings->watermark_offset_y;

        $sharpen = min(100, $this->settings->images_sharpen)/100;
        $watermark_transparency =  1-min(100, $this->settings->watermark_transparency)/100;


        if ($set_watermark && is_file($this->config->root_dir.$this->config->watermark_file)) {
            $watermark = $this->config->root_dir.$this->config->watermark_file;
        } else {
            $watermark = null;
        }

        if (class_exists('Imagick') && $this->config->use_imagick) {
            $this->image_constrain_imagick($originals_dir.$original_file, $preview_dir.$resized_file, $type, $width, $height, $watermark, $watermark_offset_x, $watermark_offset_y, $watermark_transparency, $sharpen);
        } else {
            $this->image_constrain_gd($originals_dir.$original_file, $preview_dir.$resized_file, $type, $width, $height, $watermark, $watermark_offset_x, $watermark_offset_y, $watermark_transparency);
        }

        return $preview_dir.$resized_file;
    }

    /**
     * @param $filename
     * @param string $type
     * @param int $width
     * @param int $height
     * @param bool $set_watermark
     * @return string
     */
    public function add_resize_params($filename, $type='', $width=0, $height=0, $set_watermark=false)
    {
        if ('.' != ($dirname = pathinfo($filename,  PATHINFO_DIRNAME))) {
            $file = $dirname.'/'.pathinfo($filename, PATHINFO_FILENAME);
        } else {
            $file = pathinfo($filename, PATHINFO_FILENAME);
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($width>0 || $height>0) {
            $resized_filename = $file.'.'.$type.($width>0?$width:'').'x'.($height>0?$height:'').($set_watermark?'w':'').'.'.$ext;
        } else {
            // TODO fix этот вариант сейчас не работает
            $resized_filename = $file.'.'.$type.($set_watermark?'w':'').'.'.$ext;
        }

        return $resized_filename;
    }

    /**
     * @param  string $filename
     * @return array|false
     */
    public function get_resize_params($filename)
    {
        // Определаяем параметры ресайза
        if (!preg_match('/(.+)\.(resize|crop)?([0-9]*)x([0-9]*)(w)?\.([^\.]+)$/', $filename, $matches)) {
            return false;
        }

        $file = $matches[1];                    // имя запрашиваемого файла
        $type = $matches[2];                    // ресайз или кроп
        $width = $matches[3];                   // ширина будущего изображения
        $height = $matches[4];                  // высота будущего изображения
        $set_watermark = $matches[5] == 'w';    // ставить ли водяной знак
        $ext = $matches[6];                     // расширение файла

        return array($file.'.'.$ext, $type, $width, $height, $set_watermark);
    }

    /**
     * @param  string $filename
     * @return string|false
     */
    public function download_image($filename)
    {
        // Заливаем только есть такой файл есть в базе
        $this->db->query('SELECT 1 FROM __images WHERE filename=? LIMIT 1', $filename);
        if (!$this->db->result()) {
            return false;
        }

        $parse_url = parse_url($filename);

        // Имя оригинального файла
        $basename = basename($parse_url['path']);
        $base = $this->correct_filename(pathinfo($basename, PATHINFO_FILENAME));
        $ext = pathinfo($basename, PATHINFO_EXTENSION);

        // Если такой файл существует, нужно придумать другое название
        $new_name = $base.'.'.$ext;

        while (file_exists($this->config->root_dir.$this->config->original_images_dir.$new_name)) {
            $new_base = pathinfo($new_name, PATHINFO_FILENAME);
            if (preg_match('/_([0-9]+)$/', $new_base, $parts)) {
                $new_name = $base.'_'.($parts[1]+1).'.'.$ext;
            } else {
                $new_name = $base.'_1.'.$ext;
            }
        }

        $this->db->query('UPDATE __images SET filename=? WHERE filename=?', $new_name, $filename);

        // Перед долгим копированием займем это имя
        fclose(fopen($this->config->root_dir.$this->config->original_images_dir.$new_name, 'w'));
        copy($filename, $this->config->root_dir.$this->config->original_images_dir.$new_name);
        return $new_name;
    }

    /**
     * @param  string $filename
     * @param  string $name
     * @return string|false
     */
    public function upload_image($filename, $name)
    {
        // Имя оригинального файла
        $uploaded_file = $new_name = pathinfo($name, PATHINFO_BASENAME);
        $base = pathinfo($uploaded_file, PATHINFO_FILENAME);
        $base = $this->correct_filename($base);
        $ext = pathinfo($uploaded_file, PATHINFO_EXTENSION);

        if (in_array(strtolower($ext), $this->allowed_extentions)) {
            while (file_exists($this->config->root_dir.$this->config->original_images_dir.$new_name)) {
                $new_base = pathinfo($new_name, PATHINFO_FILENAME);
                if (preg_match('/_([0-9]+)$/', $new_base, $parts)) {
                    $new_name = $base.'_'.($parts[1]+1).'.'.$ext;
                } else {
                    $new_name = $base.'_1.'.$ext;
                }
            }
            if (move_uploaded_file($filename, $this->config->root_dir.$this->config->original_images_dir.$new_name)) {
                return $new_name;
            }
        }

        return false;
    }

    /**
     * Создание превью средствами gd
     *
     * @param  string $src_file исходный файл
     * @param  string $dst_file файл с результатом
     * @param  string $type
     * @param  int $max_w максимальная ширина
     * @param  int $max_h максимальная высота
     * @param  null $watermark
     * @param  int $watermark_offset_x
     * @param  int $watermark_offset_y
     * @param  int $watermark_opacity
     * @return bool
     */
    private function image_constrain_gd($src_file, $dst_file, $type='', $max_w, $max_h, $watermark=null, $watermark_offset_x=0, $watermark_offset_y=0, $watermark_opacity=1)
    {
        // todo вынести в настройки
        $quality = 90;

        // Параметры исходного изображения
        @list($src_w, $src_h, $src_type) = array_values(getimagesize($src_file));
        $src_type = image_type_to_mime_type($src_type);

        if (empty($src_w) || empty($src_h) || empty($src_type)) {
            return false;
        }

        // Нужно ли обрезать?
        if (!$watermark && ($src_w <= $max_w) && ($src_h <= $max_h) && $type == 'resize') {
            // Нет - просто скопируем файл
            if (!copy($src_file, $dst_file)) {
                return false;
            }
            return true;
        }

        // Читаем изображение
        switch ($src_type) {
            case 'image/jpeg':
                $src_img = imageCreateFromJpeg($src_file);
                break;
            case 'image/gif':
                $src_img = imageCreateFromGif($src_file);
                break;
            case 'image/png':
                $src_img = imageCreateFromPng($src_file);
                imagealphablending($src_img, true);
                break;
            default:
                return false;
        }

        if (empty($src_img)) {
            return false;
        }

        // Размеры превью при пропорциональном уменьшении
        @list($dst_w, $dst_h) = $this->calc_contrain_size($src_w, $src_h, $max_w, $max_h, $type);

        $src_colors = imagecolorstotal($src_img);

        // create destination image (indexed, if possible)
        if ($src_colors > 0 && $src_colors <= 256) {
            $dst_img = imagecreate($dst_w, $dst_h);
        } else {
            $dst_img = imagecreatetruecolor($dst_w, $dst_h);
        }

        if (empty($dst_img)) {
            return false;
        }

        $transparent_index = imagecolortransparent($src_img);
        if ($transparent_index >= 0 && $transparent_index <= 128) {
            $t_c = imagecolorsforindex($src_img, $transparent_index);
            $transparent_index = imagecolorallocate($dst_img, $t_c['red'], $t_c['green'], $t_c['blue']);
            if ($transparent_index === false) {
                return false;
            }
            if (!imagefill($dst_img, 0, 0, $transparent_index)) {
                return false;
            }
            imagecolortransparent($dst_img, $transparent_index);
        }
        // or preserve alpha transparency for png
        elseif ($src_type === 'image/png') {
            if (!imagealphablending($dst_img, false)) {
                return false;
            }
            $transparency = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
            if (false === $transparency) {
                return false;
            }
            if (!imagefill($dst_img, 0, 0, $transparency)) {
                return false;
            }
            if (!imagesavealpha($dst_img, true)) {
                return false;
            }
        }

        // re-sample the image with new sizes
        if (!imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h)) {
            return false;
        }

        if ($type == 'crop') {
            $x0 = ($dst_w - $max_w) / 2;
            $y0 = ($dst_h - $max_h) / 2;
            $_dst_img = imagecreatetruecolor($max_w, $max_h);

            imagecopy(
                $_dst_img,
                $dst_img,
                0, 0,
                $x0, $y0,
                $max_w, $max_h
            );

            $dst_img = $_dst_img;
            $dst_w = $max_w;
            $dst_h = $max_h;
        }

        // Watermark
        if (!empty($watermark) && is_readable($watermark)) {
            $overlay = imagecreatefrompng($watermark);

            // Get the size of overlay
            $owidth = imagesx($overlay);
            $oheight = imagesy($overlay);

            $watermark_x = min(($dst_w-$owidth)*$watermark_offset_x/100, $dst_w);
            $watermark_y = min(($dst_h-$oheight)*$watermark_offset_y/100, $dst_h);

            //imagecopy($dst_img, $overlay, $watermark_x, $watermark_y, 0, 0, $owidth, $oheight);
            //imagecopymerge($dst_img, $overlay, $watermark_x, $watermark_y, 0, 0, $owidth, $oheight, $watermark_opacity*100);
            $this->imagecopymerge_alpha($dst_img, $overlay, $watermark_x, $watermark_y, 0, 0, $owidth, $oheight, $watermark_opacity*100);
        }


        // recalculate quality value for png image
        if ('image/png' === $src_type) {
            $quality = round(($quality / 100) * 10);
            if ($quality < 1) {
                $quality = 1;
            } elseif ($quality > 10) {
                $quality = 10;
            }
            $quality = 10 - $quality;
        }

        // Сохраняем изображение
        switch ($src_type) {
            case 'image/jpeg':
                return imageJpeg($dst_img, $dst_file, $quality);
            case 'image/gif':
                return imageGif($dst_img, $dst_file);
            case 'image/png':
                imagesavealpha($dst_img, true);
                return imagePng($dst_img, $dst_file, $quality);
            default:
                return false;
        }
    }

    /**
     * Создание превью средствами imagick
     *
     * @param  resource $src_file исходный файл
     * @param  resource $dst_file файл с результатом
     * @param  string $type
     * @param  int $max_w максимальная ширина
     * @param  int $max_h максимальная высота
     * @param  null $watermark
     * @param  int $watermark_offset_x
     * @param  int $watermark_offset_y
     * @param  int $watermark_opacity
     * @param  float $sharpen
     * @return bool
     */
    private function image_constrain_imagick($src_file, $dst_file, $type='', $max_w, $max_h, $watermark=null, $watermark_offset_x=0, $watermark_offset_y=0, $watermark_opacity=1, $sharpen=0.2)
    {
        $thumb = new Imagick();

        // Читаем изображение
        if (!$thumb->readImage($src_file)) {
            return false;
        }

        // Размеры исходного изображения
        $src_w = $thumb->getImageWidth();
        $src_h = $thumb->getImageHeight();

        // Нужно ли обрезать?
        if (!$watermark && ($src_w <= $max_w) && ($src_h <= $max_h)) {
            // Нет - просто скопируем файл
            if (!copy($src_file, $dst_file)) {
                return false;
            }
            return true;
        }

        // Размеры превью при пропорциональном уменьшении
        list($dst_w, $dst_h) = $this->calc_contrain_size($src_w, $src_h, $max_w, $max_h, $type);

        // Уменьшаем
        if ($type == 'crop') {
            $x0 = ($dst_w - $max_w) / 2;
            $y0 = ($dst_h - $max_h) / 2;
            $thumb->thumbnailImage($dst_w, $dst_h);
            $dst_w = $max_w;
            $dst_h = $max_h;
            $thumb->cropImage($dst_w, $dst_h, $x0, $y0);
        } else {
            $thumb->thumbnailImage($dst_w, $dst_h);
        }
        $watermark_x = null;
        $watermark_y = null;
        // Устанавливаем водяной знак
        if ($watermark && is_readable($watermark)) {
            $overlay = new Imagick($watermark);
            //$overlay->setImageOpacity($watermark_opacity);
            //$overlay_compose = $overlay->getImageCompose();
            $overlay->evaluateImage(Imagick::EVALUATE_MULTIPLY, $watermark_opacity, Imagick::CHANNEL_ALPHA);

            // Get the size of overlay
            $owidth = $overlay->getImageWidth();
            $oheight = $overlay->getImageHeight();

            $watermark_x = min(($dst_w-$owidth)*$watermark_offset_x/100, $dst_w);
            $watermark_y = min(($dst_h-$oheight)*$watermark_offset_y/100, $dst_h);
        }


        // Анимированные gif требуют прохода по фреймам
        foreach ($thumb as $frame) {
            // Уменьшаем
            $frame->thumbnailImage($dst_w, $dst_h);

            /* Set the virtual canvas to correct size */
            $frame->setImagePage($dst_w, $dst_h, 0, 0);

            // Наводим резкость
            if ($sharpen > 0) {
                $thumb->adaptiveSharpenImage($sharpen, $sharpen);
            }

            if (isset($overlay) && is_object($overlay)) {
                // $frame->compositeImage($overlay, $overlay_compose, $watermark_x, $watermark_y, imagick::COLOR_ALPHA);
                $frame->compositeImage($overlay, imagick::COMPOSITE_OVER, $watermark_x, $watermark_y, imagick::COLOR_ALPHA);
            }
        }

        // Убираем комменты и т.п. из картинки
        $thumb->stripImage();

        // TODO вынести в настройки
        $quality = 90;
        $thumb->setImageCompressionQuality($quality);

        // Записываем картинку
        if (!$thumb->writeImages($dst_file, true)) {
            return false;
        }

        // Уборка
        $thumb->destroy();
        if (isset($overlay) && is_object($overlay)) {
            $overlay->destroy();
        }

        return true;
    }

    /**
     * Вычисляет размеры изображения, до которых нужно его пропорционально уменьшить, чтобы вписать в квадрат $max_w x $max_h
     *
     * @param  int $src_w ширина исходного изображения
     * @param  int $src_h высота исходного изображения
     * @param  int $max_w максимальная ширина
     * @param  int $max_h максимальная высота
     * @param  string $type
     * @return array|bool
     */
    private function calc_contrain_size($src_w, $src_h, $max_w = 0, $max_h = 0, $type = 'resize')
    {
        if ($src_w == 0 || $src_h == 0) {
            return false;
        }

        $dst_w = $src_w;
        $dst_h = $src_h;
        // image cropping calculator
        if ($type == 'crop') {
            $source_aspect_ratio = $src_w / $src_h;
            $desired_aspect_ratio = $max_w / $max_h;

            if ($source_aspect_ratio > $desired_aspect_ratio) {
                $dst_h = $max_h;
                $dst_w = ( int ) ($max_h * $source_aspect_ratio);
            } else {
                $dst_w = $max_w;
                $dst_h = ( int ) ($max_w / $source_aspect_ratio);
            }
        } else {
            // image resize calculator
            if ($src_w > $max_w && $max_w>0) {
                $dst_h = $src_h * ($max_w/$src_w);
                $dst_w = $max_w;
            }
            if ($dst_h > $max_h && $max_h>0) {
                $dst_w = $dst_w * ($max_h/$dst_h);
                $dst_h = $max_h;
            }
        }
        return array($dst_w, $dst_h);
    }

    /**
     * @param  string $filename
     * @return mixed|string
     */
    public function correct_filename($filename)
    {
        $ru = explode('-', "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я- ");
        $en = explode('-', "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch-_-Y-y-_-E-e-YU-yu-YA-ya-_");
        $res = str_replace($ru, $en, $filename);
        $res = strtolower($res);
        $res = preg_replace("/[^a-z0-9_-]/", "", $res);
        return $res;
    }

     /**
     * merge two true colour images while maintaining alpha transparency of both
     * images.
     *
     * known issues : Opacity values other than 100% get a bit screwy, the source
     *                composition determines how much this issue will annoy you.
     *                if in doubt, use as you would imagecopy_alpha (i.e. keep
     *                opacity at 100%)
     *
     * @param resource $dst_im Destination image link resource
     * @param resource $src_im Source image link resource
     * @param int $dst_x x-coordinate of destination point
     * @param int $dst_y y-coordinate of destination point
     * @param int $src_x x-coordinate of source point
     * @param int $src_y y-coordinate of source point
     * @param int $src_w Source width
     * @param int $src_h Source height
     * @param int $pct Opacity or source image
     */
    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }
}
