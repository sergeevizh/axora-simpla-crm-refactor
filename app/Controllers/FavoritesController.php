<?PHP

namespace App\Controllers;

class FavoritesController extends Controller
{
    public function fetch()
    {

        if ( isset($_COOKIE['favorites']) ) {
            $products_ids = explode(',',$_COOKIE['favorites']) ;
        }

        if(!empty($products_ids)){
            $filter = array();
            $filter['visible'] = 1;
            $filter['sort_priority_stock'] = 1;
            $filter['id'] = $products_ids;

            // Товары
            $products = $this->products->renders($filter);
            $this->design->assign('products', $products);
        }




        // Метатеги
        if ($this->page) {
            $this->design->assign('meta_title', $this->page->meta_title);
            $this->design->assign('meta_keywords', $this->page->meta_keywords);
            $this->design->assign('meta_description', $this->page->meta_description);
        }
        return $this->design->fetch('favorites.tpl');
    }
}
