<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/15/13
 * Time: 11:27 AM
 */

namespace Theme\API;


use System\IO\Directory;
use Theme\Model\Theme;

class Themes
{
    const DEFAULT_CLIENT = 'default_client';
    const DEFAULT_ADMIN = 'default_admin';
    const NOT_AVAILABLE = 'not_available';
    const AVAILABLE = 'available';

    const TYPE_ADMIN = 2;
    const TYPE_CLIENT = 1;

    public static $types = array(
        'admin' => self::TYPE_ADMIN,
        'client' => self::TYPE_CLIENT
    );

    public $themesDir;

    private $templates;
    private static $activeThemes = null;
    private static $templateConfig = null;
    /**
     * @var callable
     */
    private static $blocksHelper = null;
    private static $rowTemplate = "<div class='row' id='%s'>%s</div>";
    private static $devices = array('xs', 'sm', 'md', 'lg');

    public function __construct()
    {
        $this->themesDir = ROOT . '/module/Theme/public/themes';
    }

    /**
     * @return Themes
     */
    public static function getSelf()
    {
        return getSM('theme_api');
    }

    public function getTemplates()
    {
        if ($this->templates)
            return $this->templates;

        $themes_dirs = Directory::getDirs($this->themesDir);
        $themes = array();
        if (count($themes_dirs)) {
            foreach ($themes_dirs as $themeDir) {
                $iniFile = $this->themesDir . '/' . $themeDir . '/template.ini';
                if (file_exists($iniFile) && is_file($iniFile)) {
                    $config = parse_ini_file($iniFile);
                    $access = true;
                    if (isset($config['access'])) {
                        if ($config['access'] != 'public') {
                            $access = false;
                            $domains = $config['access'];
                            if (!is_array($domains))
                                $domains = array($domains);
                            if (in_array(ACTIVE_SITE, $domains))
                                $access = true;
                        }
                    }
                    if ($access)
                        $config['status'] = Themes::AVAILABLE;
                    else
                        $config['status'] = Themes::NOT_AVAILABLE;
                    $themes[$config['name']] = $config;
                }
            }
        }
        $this->templates = $themes;
        return $themes;
    }

    /**
     * @return Theme
     */
    public static function defaultAdminTheme()
    {
        $theme = new Theme();
        $theme->name = 'IptCmsAdmin';
        $theme->type = self::TYPE_ADMIN;
        return $theme;
    }

    /**
     * @return Theme
     */
    public static function defaultClientTheme()
    {
        $theme = new Theme();
        $theme->name = 'default';
        $theme->type = self::TYPE_CLIENT;
        return $theme;
    }

    /**
     * @return array
     */
    private static function getActiveThemes()
    {
        if (is_null(self::$activeThemes)) {
            $themes = getSM()->get('theme_table')->getDefaults();
            self::$activeThemes = $themes;
        }
        return self::$activeThemes;
    }

    /**
     * @return Theme
     */
    public static function getClientTheme()
    {
        $themes = self::getActiveThemes();
        if (!isset($themes[Themes::TYPE_CLIENT]))
            $clientTheme = Themes::defaultClientTheme();
        else
            $clientTheme = $themes[Themes::TYPE_CLIENT];
        return $clientTheme;
    }

    /**
     * @return Theme
     */
    public static function getAdminTheme()
    {
        $themes = self::getActiveThemes();
        if (!isset($themes[Themes::TYPE_ADMIN]))
            $adminTheme = Themes::defaultAdminTheme();
        else
            $adminTheme = $themes[Themes::TYPE_ADMIN];
        return $adminTheme;
    }

    /**
     * @param Theme $theme
     * @return array
     */
    public function getThemeConfig($theme)
    {
        return parse_ini_file(self::getSelf()->themesDir . '/' . $theme . '/template.ini', true);
    }

    public static function getClientThemeConfig()
    {
        $theme = self::getClientTheme();
        return self::getThemeConfig($theme);
    }

    public static function getTemplateConfig()
    {
        if (self::$templateConfig == null) {
            self::$templateConfig = getConfig('theme')->varValue;
        }
        return self::$templateConfig;
    }

    public static function getLayout()
    {
        $config = self::getTemplateConfig();
        $layout = false;
        if (isset($config['layout'])) {
            $layout = trim($config['layout']);

            $layout = json_decode($layout);
        }
        if (!$layout) {
            $layout = json_decode(self::getDefaultLayout());
        }
        return $layout;
    }

    public static function getDefaultLayout()
    {
        $pos = array(
            'row-1' => array(
                'col-1' => array('column-width-md' => 12,),
            ),
            'row-2' => array(
                'col-1' => array('column-width-md' => 3,),
                'col-2' => array('column-width-md' => 6, 'isMainContent' => '1'),
                'col-3' => array('column-width-md' => 3,)
            ),
            'row-3' => array(
                'col-1' => array('column-width-md' => 12,)
            )
        );
        $pos = json_encode($pos);
        return $pos;
    }

    public static function renderLayout($view)
    {
        $config = self::getTemplateConfig();
        $isFluid = isset($config['fluid']) && $config['fluid'] == '1';
        $fluid = $isFluid ? '-fluid' : '';

        $wrapper = "<div class='container%s'>%s</div>";

        $layout = self::getLayout(false);

        $output = self::renderRows($layout, $view);
        $output = sprintf($wrapper, $fluid, $output);
        return $output;
    }

    private static function renderRows($rows, $view)
    {
        $output = "";
        if (is_array($rows) || $rows instanceof \stdClass) {
            foreach ($rows as $rowId => $columns) {
                $rowContent = self::renderColumns($columns, $rowId, $view);
                if (!empty($rowContent)) {
                    $rowContent = sprintf(self::$rowTemplate, $rowId, $rowContent);
                    $output .= $rowContent;
                }
            }
        }
        return $output;
    }

    private static function renderColumns($columns, $rowId, $view)
    {
        $col = "<div class='%s' id='%s'>%s</div>";
        $html = '';
        $mainContent = false;
        $emptyColumnsWidth = array();
        foreach (self::$devices as $dev) {
            $emptyColumnsWidth['column_width_' . $dev] = 0;
        }

        foreach ($columns as $colName => $options) {
            $colId = $rowId . '-' . $colName;
//            if (!isset($options->width))
//                $options->width = 12;
//            $width = (int)$options->width;

            $isMainContainer = false;
            if (isset($options->main_content_column))
                $isMainContainer = $options->main_content_column == '1' && !$mainContent;
            //.col-md-
            $content = $view->blocks($colName);

            if ($isMainContainer) {
                $flashMsg = trim($view->flashMessenger()->render());
                if (!empty($flashMsg))
                    $content .= "<div id='system_messages'>{$flashMsg}</div>";

                $content .= $view->content;
                $mainContent = $colName;

                foreach (self::$devices as $dev) {
                    $dev = 'column_width_' . $dev;
                    if (!isset($options->{$dev}))
                        $options->{$dev} = 0;
                    else
                        $options->{$dev} = (int)$options->{$dev};

                    if (!isset($options->{'column_offset_' . $dev}))
                        $options->{'column_offset_' . $dev} = 0;
                    else
                        $options->{'column_offset_' . $dev} = (int)$options->{'column_offset_' . $dev};
                }
            }

            if (isset($options->rows) && $options->rows) {
                $content .= self::renderRows($options->rows, $view);
            }
            if (empty($content) && !$isMainContainer) {
                foreach (self::$devices as $dev) {
                    $dev = 'column_width_' . $dev;
                    if (isset($options->{$dev}))
                        $emptyColumnsWidth[$dev] += $options->{$dev};
                }
            }

            $options->content = $content;
        }

        if ($mainContent) {
            foreach (self::$devices as $dev) {
                $dev = 'column_width_' . $dev;
                $columns->{$mainContent}->{$dev} += $emptyColumnsWidth[$dev];
                if ($columns->{$mainContent}->{$dev} > 12)
                    $columns->{$mainContent}->{$dev} = 12;
            }
        }


        foreach ($columns as $colName => $options) {
            if (!empty($options->content)) {
                $colId = $rowId . '-' . $colName;
                $class = array();
                foreach (self::$devices as $dev) {
                    if (isset($options->{'column_width_' . $dev})) {
                        $w = $options->{'column_width_' . $dev};
                        if ($w > 0)
                            $class[] = 'col-' . $dev . '-' . $w;
                    }

                    if (isset($options->{'column_offset_' . $dev})) {
                        $offset = $options->{'column_offset_' . $dev};
                        if ($offset > 0)
                            $class[] = 'col-' . $dev . '-offset-' . $offset;
                    }

                    if (isset($options->{'column_visible_' . $dev})) {
                        $v = (int)$options->{'column_visible_' . $dev};
                        if ($v == 1)
                            $class[] = 'visible-' . $dev;
                    }
                }
                $html .= sprintf($col, implode(' ', $class), $colId, $options->content);
            }
        }
        return $html;
    }

    public static function getBlockPositions()
    {
        $positions = array();
        $themeConfig = Themes::getClientThemeConfig();
        if (isset($themeConfig['positions']))
            return $themeConfig['positions'];

        if (!(isset($themeConfig['has-designer']) && $themeConfig['has-designer'] == true))
            return $positions;

        $cacheKey = 'layout_block_positions';
        if ($blockPositions = getCache(true)->getItem($cacheKey))
            return $blockPositions;

        $layout = self::getLayout();
        self::_extractBlockPositions($layout, $positions);

        getCache(true)->setItem($cacheKey, $positions);

        return $positions;
    }

    private static function _extractBlockPositions($rows, &$positions)
    {
        if (is_array($rows) || $rows instanceof \stdClass) {
            foreach ($rows as $rowId => $columns) {
                foreach ($columns as $colName => $options) {
                    $colId = '[' . $rowId . '] - [' . $colName . ']';
                    $positions[$colName] = $colId;

                    if (isset($options->rows) && $options->rows) {
                        self::_extractBlockPositions($options->rows, $positions);
                    }
                }
            }
        }
    }
}