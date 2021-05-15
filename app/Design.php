<?php

namespace App;

use App\Api\Settings;
use App\Helpers\ConfigHelper;
use Smarty;
use Smarty_Internal_Data;

class Design
{
    public $smarty;
    public $compile_dir;
    public $template_dir;
    public $cache_dir = 'cache';
    public $theme;

    private $config;
    private $settings;

    public function __construct(ConfigHelper $config, Settings $settings)
    {
        $this->smarty = new Smarty();
        $this->config = $config;
        $this->settings = $settings;
        $this->setupSettings();
    }

    private function setupSettings()
    {
        $this->smarty->compile_check = $this->config->smarty_compile_check;
        $this->smarty->caching = $this->config->smarty_caching;
        $this->smarty->cache_lifetime = $this->config->smarty_cache_lifetime;
        $this->smarty->debugging = $this->config->smarty_debugging;
        $this->smarty->error_reporting = E_ALL & ~E_NOTICE;

        // Берем тему из настроек
        $this->theme = $this->settings->theme;
        $_SERVER['CURRENT_DESIGN_THEME'] = $this->theme;
        $this->compile_dir = $this->config->root_dir . '/compiled/' . $this->theme;
        $this->template_dir = $this->config->root_dir . '/design/' . $this->theme . '/html';
        $this->cache_dir = $this->config->root_dir . '/cache';

        $this->set_compiled_dir($this->compile_dir);
        $this->set_templates_dir($this->template_dir);

        if (!is_dir($this->config->root_dir . '/compiled')) {
            mkdir($this->config->root_dir . '/compiled', 0777);
        }

        // Создаем папку для скомпилированных шаблонов текущей темы
        if (!is_dir($this->compile_dir)) {
            mkdir($this->compile_dir, 0777);
        }

        $this->smarty->setCacheDir($this->cache_dir);
        $this->smarty->addPluginsDir([$this->config->root_dir . '/smarty-plugins']);

        if ($this->config->smarty_html_minify) {
            $this->smarty->loadFilter('output', 'trimwhitespace');
        }
    }

    function render(string $templateFilename)
    {
        $content = $this->fetch($templateFilename);

        $this->assign('config', $this->config);
        $this->assign('settings', $this->settings);
        $this->assign('content', $content);

        header("Content-type: text/html; charset=UTF-8");
        print $this->fetch('index.tpl');
        die;
    }

    /**
     * @param array|string $tpl_var
     * @param mixed $value
     * @param boolean $nocache
     * @return Smarty_Internal_Data
     */
    public function assign($tpl_var, $value = null, $nocache = false)
    {
        return $this->smarty->assign($tpl_var, $value, $nocache);
    }


    public function fetch(string $template): string
    {
        return $this->smarty->fetch($template);
    }

    public function set_compiled_dir(string $compile_dir): void
    {
        $this->smarty->setCompileDir($compile_dir);
    }

    public function set_templates_dir(string $template_dir)
    {
        $this->smarty->setTemplateDir($template_dir);
    }

    public function get_var(string $name)
    {
        return $this->smarty->getTemplateVars($name);
    }

    public function clear_cache()
    {
        $this->smarty->clearAllCache();
    }

}
