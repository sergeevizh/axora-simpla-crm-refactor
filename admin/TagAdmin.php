<?PHP

namespace Axora;

use App\Api\Axora;

class TagAdmin extends Axora
{
    public function fetch()
    {

        $tag = new \stdClass();
        if ($this->request->method('post')) {
            $tag->id = $this->request->post('id', 'integer');
            $tag->name = $this->request->post('name');
            $tag->h1 = $this->request->post('h1');
            $tag->url = $this->request->post('url', 'string');

            $tag->visible = $this->request->post('visible', 'boolean');

            //$tag->in_category = $this->request->post('in_category', 'boolean');
            //$tag->in_category_name = $this->request->post('in_category_name');

            $tag->meta_title = $this->request->post('meta_title');
            $tag->meta_keywords = $this->request->post('meta_keywords');
            $tag->meta_description = $this->request->post('meta_description');
            $tag->description = $this->request->post('description');
            $tag->category_id = $this->request->post('category_id', 'integer');

            $tag->features = http_build_query($this->request->post('features'));

//            $tag->colors = http_build_query($this->request->post('colors'));
            $tag->brands = http_build_query($this->request->post('brands'));
            //$tag->keywords = $this->request->post('keywords');

            //$tag->sort = $this->request->post('sort', 'string');


            if (($t = $this->tags->get_tag($tag->url)) && $t->id!=$tag->id) {
                $this->design->assign('message_error', 'url_exists');
            } elseif (empty($tag->name)) {
                $this->design->assign('message_error', 'name_empty');
            } elseif (empty($tag->url)) {
                $this->design->assign('message_error', 'url_empty');
            } else {

                if (empty($tag->id)) {
                    $tag->id = $this->tags->add_tag($tag);
                    $tag = $this->tags->get_tag(intval($tag->id));
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->tags->update_tag(intval($tag->id), $tag);
                    $tag = $this->tags->get_tag(intval($tag->id));
                    $this->design->assign('message_success', 'updated');
                }

            }


        } else {

            $tag->id = $this->request->get('id', 'integer');
            $tag = $this->tags->get_tag(intval($tag->id));
        }
        if(!empty($tag->features)) {
            parse_str($tag->features, $tag->features);
        }
//        if(!empty($tag->colors)) {
//            parse_str($tag->colors, $tag->colors);
//        }
        if(!empty($tag->brands)) {
            parse_str($tag->brands, $tag->brands);
        }
        $this->design->assign('tag', $tag);


        // Все бренды
        $brands = $this->brands->get_brands();
        $this->design->assign('brands', $brands);

        // Все цвета
//        $colors = $this->colors->get_colors();
//        $this->design->assign('colors', $colors);

        // Все категории
        $categories = $this->categories->get_categories_tree();
        $this->design->assign('categories', $categories);

        $features = $this->features->get_features(array('in_filter' => 1));
        foreach ($features as &$f) {
            $f->options = $this->features->get_options(array('feature_id' => $f->id));
        }

        $this->design->assign('features', $features);


        return $this->design->fetch('tag.tpl');
    }
}
