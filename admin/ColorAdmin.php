<?php

namespace Axora;

use App\Api\Axora;

class ColorAdmin extends Axora
{
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function fetch()
    {
        $color = new stdClass;
        if ($this->request->method('post')) {
            $color->id = $this->request->post('id', 'integer');
            $color->name = $this->request->post('name');
            $color->url = $this->request->post('url');
            $color->color = $this->request->post('color');

            if (empty($color->name)) {
                $this->design->assign('message_error', 'name_empty');
            } else {
                if (empty($color->id)) {
                    $color->id = $this->colors->add_color($color);
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->colors->update_color($color->id, $color);
                    $this->design->assign('message_success', 'updated');
                }
                // Удаление изображения
                if ($this->request->post('delete_image')) {
                    $this->colors->delete_image($color->id);
                }
                // Загрузка изображения
                $image = $this->request->files('image');
                if (!empty($image['name']) && in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)),
                        $this->allowed_image_extentions)) {

                    $this->colors->delete_image($color->id);

                    $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
                    $base = translit(pathinfo($image['name'], PATHINFO_FILENAME));
                    $new_name = $base . '.' . $ext;

                    while (file_exists($this->config->root_dir . $this->config->colors_images_dir . $new_name)) {
                        $new_base = pathinfo($new_name, PATHINFO_FILENAME);
                        if (preg_match('/_([0-9]+)$/', $new_base, $parts)) {
                            $new_name = $base . '_' . ($parts[1] + 1) . '.' . $ext;
                        } else {
                            $new_name = $base . '_1.' . $ext;
                        }
                    }

                    move_uploaded_file($image['tmp_name'],
                        $this->root_dir . $this->config->colors_images_dir . $new_name);

                    $this->colors->update_color($color->id, array('image' => $new_name));
                }

                $color = $this->colors->get_color($color->id);
            }
        } else {
            $color->id = $this->request->get('id', 'integer');
            $color = $this->colors->get_color($color->id);
        }

        $this->design->assign('color', $color);
        return $this->design->fetch('color.tpl');
    }
}

function translit($text)
{
    $ru = explode('-',
        "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я");
    $en = explode('-',
        "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya");

    $res = str_replace($ru, $en, $text);
    $res = preg_replace("/[\s]+/ui", '-', $res);
    $res = preg_replace('/[^\p{L}\p{Nd}\d-]/ui', '', $res);
    $res = strtolower($res);
    return $res;
}
