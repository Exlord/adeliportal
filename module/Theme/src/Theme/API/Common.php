<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/30/13
 * Time: 12:10 PM
 */
namespace Theme\API;

use Application\API\App;

class Common
{
    public static function Attributes($attributes)
    {
        $text = ' ';
        if (is_array($attributes) && count($attributes)) {
            foreach ($attributes as $key => $value) {
                if (is_array($value))
                    $value = implode(' ', $value);
                $text .= "$key='$value' ";
            }
        }
        return $text;
    }

    public static function Link($text, $href, $attr = array())
    {
        return sprintf("<a href='%s' %s>%s</a>", $href, self::Attributes($attr), $text);
    }

    public static function Img($src, $alt = '', $title = '', $attr = array(), $tooltip = null)
    {
        $titleTemp = $tooltip ? " data-tooltip='%s'" : " title='%s'";
        $title = $tooltip ? $tooltip : $title;
        return sprintf("<img src='%s' alt='%s' $titleTemp %s />", $src, $alt, $title, self::Attributes($attr));
    }

    public static function Select($options, $name, $id, $attr = array())
    {
        $temp = "<select name='%s' id='%s' %s>%s</select>";
        $optionsHtml = array();
        foreach ($options as $key => $value) {
            $optionsHtml[] = sprintf("<option value='%s'>%s</option>", $key, t($value));
        }
        $optionsHtml = implode("\n", $optionsHtml);
        return sprintf($temp, $name, $id, self::Attributes($attr), $optionsHtml);
    }

    /**
     * @param array $items
     * @param array $attributes
     * @param string $type
     * @param string $title
     * @return string
     */
    public static function ItemList(array $items, $attributes = array(), $type = 'ul', $title = '')
    {
        // Only output the list container and title, if there are any list items.
        // Check to see whether the block title exists before adding a header.
        // Empty headers are not semantic and present accessibility challenges.
        $output = '<div class="item-list">';
        if (isset($title) && $title !== '') {
            $output .= '<h3>' . $title . '</h3>';
        }

        if (!empty($items)) {
            $output .= "<$type" . self::Attributes($attributes) . '>';
            $num_items = count($items);
            $i = 0;
            foreach ($items as $item) {
                $attributes = array();
                $children = array();
                $data = '';
                $i++;
                if (is_array($item)) {
                    foreach ($item as $key => $value) {
                        if ($key == 'data') {
                            $data = $value;
                        } elseif ($key == 'children') {
                            $children = $value;
                        } else {
                            $attributes[$key] = $value;
                        }
                    }
                } else {
                    $data = $item;
                }
                if (count($children) > 0) {
                    // Render nested list.
                    $data .= self::ItemList($children, $attributes, $type);
                }
                if ($i == 1) {
                    $attributes['class'][] = 'first';
                }
                if ($i == $num_items) {
                    $attributes['class'][] = 'last';
                }
                $output .= '<li' . self::Attributes($attributes) . '>' . $data . "</li>\n";
            }
            $output .= "</$type>";
        }
        $output .= '</div>';
        return $output;
    }

    public static function Panel($body, $heading = null, $theme = 'default')
    {
        $temp = "<div class='panel panel-{$theme}'>";
        if ($heading)
            $temp .= "<div class='panel-heading'>{$heading}</div>";
        $temp .= "<div class='panel-body'>";
        $temp .= $body;
        $temp .= "</div>";
        $temp .= "</div>";

        return $temp;
    }

    public static function Column($size, $data, $attributes = array())
    {
        $attributes['class'][] = $size;
        $attributes = self::Attributes($attributes);
        return "<div {$attributes}>{$data}</div>";
    }

    public static function Row($data, $attributes = array())
    {
        $attributes['class'][] = 'row';
        $attributes = self::Attributes($attributes);
        return "<div {$attributes}>{$data}</div>";
    }
} 