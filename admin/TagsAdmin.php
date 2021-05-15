<?PHP

namespace Axora;

use App\Api\Axora;

class TagsAdmin extends Axora
{
    function fetch()
    {
        $filter = array();
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        $filter['limit'] = $this->settings->products_num_admin;

        // Все категории
        $categories = $this->categories->get_categories_tree();
        $this->design->assign('categories', $categories);

        $category_id = $this->request->get('category_id', 'integer');
        if ($category_id && $category = $this->categories->get_category($category_id)) {
            $filter['category_id'] = $category->children;
        }
        // Обработка действий
        if ($this->request->method('post')) {

            // Действия с выбранными
            $ids = $this->request->post('check');

            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'delete':
                        {
                            foreach ($ids as $id) {
                                $this->tags->delete_tag($id);
                            }
                            break;
                        }
                }
            }

            // Сортировка
            $positions = $this->request->post('positions');
            $ids = array_keys($positions);
            sort($positions);
            $positions = array_reverse($positions);
            foreach ($positions as $i => $position) {
                $this->tags->update_tag($ids[$i], array('position' => $position));
            }


        }
        if (isset($category)) {
            $this->design->assign('category', $category);
        }

        $tags = $this->tags->gets_tags($filter);
        $this->design->assign('tags', $tags);


        return $this->design->fetch('tags.tpl');
    }
}

