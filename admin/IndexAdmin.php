<?php


namespace Axora;

use App\Api\Axora;
// Этот класс выбирает модуль в зависимости от параметра Section и выводит его на экран
class IndexAdmin extends Axora
{
    // Соответсвие модулей и названий соответствующих прав
    private $modules_permissions = array(
        '\Axora\ProductsAdmin'       => 'products',
        '\Axora\BannersAdmin'        => 'banners',
        '\Axora\BannerAdmin'         => 'banners',
        '\Axora\ProductAdmin'        => 'products',
        '\Axora\CategoriesAdmin'     => 'categories',
        '\Axora\CategoryAdmin'       => 'categories',
        '\Axora\BrandsAdmin'         => 'brands',
        '\Axora\BrandAdmin'          => 'brands',
        '\Axora\FeaturesAdmin'       => 'features',
        '\Axora\FeatureAdmin'        => 'features',
        '\Axora\OrdersAdmin'         => 'orders',
        '\Axora\OrderAdmin'          => 'orders',
        '\Axora\OrdersLabelsAdmin'   => 'labels',
        '\Axora\OrdersLabelAdmin'    => 'labels',
        '\Axora\UsersAdmin'          => 'users',
        '\Axora\UserAdmin'           => 'users',
        '\Axora\ExportUsersAdmin'    => 'users',
        '\Axora\GroupsAdmin'         => 'groups',
        '\Axora\GroupAdmin'          => 'groups',
        '\Axora\CouponsAdmin'        => 'coupons',
        '\Axora\CouponAdmin'         => 'coupons',
        '\Axora\PagesAdmin'          => 'pages',
        '\Axora\PageAdmin'           => 'pages',
        '\Axora\BlogAdmin'           => 'blog',
        '\Axora\TagsAdmin'           => 'tags',
        '\Axora\TagAdmin'            => 'tags',
        '\Axora\PostAdmin'           => 'blog',
        '\Axora\CommentsAdmin'       => 'comments',
        '\Axora\FeedbacksAdmin'      => 'feedbacks',
        '\Axora\ImportAdmin'         => 'import',
        '\Axora\ExportAdmin'         => 'export',
        '\Axora\BackupAdmin'         => 'backup',
        '\Axora\StatsAdmin'          => 'stats',
        '\Axora\ThemeAdmin'          => 'design',
        '\Axora\StylesAdmin'         => 'design',
        '\Axora\TemplatesAdmin'      => 'design',
        '\Axora\ImagesAdmin'         => 'design',
        '\Axora\SettingsAdmin'       => 'settings',
        '\Axora\CurrencyAdmin'       => 'currency',
        '\Axora\DeliveriesAdmin'     => 'delivery',
        '\Axora\DeliveryAdmin'       => 'delivery',
        '\Axora\PaymentMethodAdmin'  => 'payment',
        '\Axora\PaymentMethodsAdmin' => 'payment',
        '\Axora\ManagersAdmin'       => 'managers',
        '\Axora\ManagerAdmin'        => 'managers',

    );
	private $module = null;
    // Конструктор
    public function __construct()
    {

        // Вызываем конструктор базового класса
        parent::__construct();


        $this->design->set_templates_dir('admin/design/html');

        if (!is_dir($this->config->root_dir.'/compiled')) {
            mkdir($this->config->root_dir.'admin/design/compiled', 0777);
        }

        $this->design->set_compiled_dir('admin/design/compiled');

        $this->design->assign('settings',  $this->settings);
        $this->design->assign('config',    $this->config);

        // Администратор
        $manager = $this->managers->get_manager();
        $this->design->assign('manager', $manager);

        // Берем название модуля из get-запроса
        $module = $this->request->get('module', 'string');
        $module = preg_replace("/[^A-Za-z0-9]+/", "", $module);


        // Если не запросили модуль - используем модуль первый из разрешенных
        if (empty($module) || !is_file('admin/'.$module.'.php')) {
            foreach ($this->modules_permissions as $m=>$p) {
                if ($this->managers->access($p)) {
                    $module = $m;
                    break;
                }
            }
        }




        if (empty($module)) {
            $module = 'Axora\ProductsAdmin';
        }

        if ( strpos($module, '\Axora\\') === false ) {
            $module = '\Axora\\' . $module ;
        }

        // Создаем соответствующий модуль
        if (class_exists( $module)) {
            $this->module = new $module();
        } else {
            die("Error creating  $module class");
        }
    }

    public function fetch()
    {
        $currency = $this->money->get_currency();
        $this->design->assign("currency", $currency);

	    $content = null;

        if (isset($this->modules_permissions['\\' .get_class($this->module)])
        && $this->managers->access($this->modules_permissions['\\' .get_class($this->module)])) {
            $content = $this->module->fetch();
            $this->design->assign("content",  $content);
        } else {
            $this->design->assign("content", "Permission denied");
        }

        // Счетчики для верхнего меню
        $new_orders_counter = $this->orders->count_orders(array('status'=>0));
        $this->design->assign("new_orders_counter", $new_orders_counter);

        $new_comments_counter = $this->comments->count_comments(array('approved'=>0));
        $this->design->assign("new_comments_counter", $new_comments_counter);

        $new_feedback_counter = $this->feedbacks->count_not_read();
        $this->design->assign("new_feedback_counter", $new_feedback_counter);

        // Создаем текущую обертку сайта (обычно index.tpl)
        $wrapper = $this->design->smarty->getTemplateVars('wrapper');
        if (is_null($wrapper)) {
            $wrapper = 'index.tpl';
        }

        if (!empty($wrapper)) {
            return $this->design->fetch($wrapper);
        } else {
            return $content;
        }
    }
}


