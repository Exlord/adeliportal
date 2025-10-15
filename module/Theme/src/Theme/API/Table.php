<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/30/13
 * Time: 12:13 PM
 */

namespace Theme\API;

class Table extends Common
{
    public static function Cell($cell, $header = false)
    {
        $attributes = '';

        if (is_array($cell)) {
            $data = isset($cell['data']) ? $cell['data'] : '';

            $header |= isset($cell['header']);
            unset($cell['data']);
            unset($cell['header']);
            $attributes = self::Attributes($cell);
        } else {
            $data = $cell;
        }

        if ($header) {
            $output = "<th$attributes>$data</th>";
        } else {
            $output = "<td$attributes>$data</td>";
        }

        return $output;
    }

    public static function ColGroups($colgroups)
    {
        $output = '';
        // Format the table columns:
        if (count($colgroups)) {
            foreach ($colgroups as $number => $colgroup) {
                $attributes = array();

                // Check if we're dealing with a simple or complex column
                if (isset($colgroup['data'])) {
                    foreach ($colgroup as $key => $value) {
                        if ($key == 'data') {
                            $cols = $value;
                        } else {
                            $attributes[$key] = $value;
                        }
                    }
                } else {
                    $cols = $colgroup;
                }

                // Build colgroup
                if (is_array($cols) && count($cols)) {
                    $output .= ' <colgroup' . self::Attributes($attributes) . '>';
                    $i = 0;
                    foreach ($cols as $col) {
                        $output .= ' <col' . self::Attributes($col) . ' />';
                    }
                    $output .= " </colgroup>\n";
                } else {
                    $output .= ' <colgroup' . self::Attributes($attributes) . " />\n";
                }
            }
        }
        return $output;
    }

    public static function EmptyHeader(&$rows, $header, $empty)
    {
        if (!count($rows) && $empty) {
            $header_count = 0;
            foreach ($header as $header_cell) {
                if (is_array($header_cell)) {
                    $header_count += isset($header_cell['colspan']) ? $header_cell['colspan'] : 1;
                } else {
                    $header_count++;
                }
            }
            $rows[] = array(array('data' => $empty, 'colspan' => $header_count, 'class' => array('empty', 'message')));
        }
    }

    public static function Header($header)
    {
        $output = '';
        if (count($header)) {

            // Check if we're dealing with a simple or complex row
            if (isset($header['data'])) {
                $cells = $header['data'];

                // Set the attributes array and exclude 'data' and 'no_striping'.
                $attributes = $header;
                unset($attributes['data']);
                unset($attributes['no_striping']);
            } else {
                $cells = $header;
                $attributes = array();
            }

            $output .= " <thead>";
            $output .= ' <tr' . self::Attributes($attributes) . '>';
            foreach ($cells as $cell) {
                $output .= self::Cell($cell, TRUE);
            }
            $output .= " </tr></thead>\n";
        }
        return $output;
    }

    private static function Rows($rows)
    {
        $output = '';
        if (count($rows)) {
            $output .= "<tbody>\n";
            $flip = array('even' => 'odd', 'odd' => 'even');
            $class = 'even';
            foreach ($rows as $number => $row) {
                // Check if we're dealing with a simple or complex row
                if (isset($row['data'])) {
                    $cells = $row['data'];
                    $no_striping = isset($row['no_striping']) ? $row['no_striping'] : FALSE;

                    // Set the attributes array and exclude 'data' and 'no_striping'.
                    $attributes = $row;
                    unset($attributes['data']);
                    unset($attributes['no_striping']);
                } else {
                    $cells = $row;
                    $attributes = array();
                    $no_striping = FALSE;
                }
                if (count($cells)) {
                    // Add odd/even class
                    if (!$no_striping) {
                        $class = $flip[$class];
                        $attributes['class'][] = $class;
                    }

                    // Build row
                    $output .= ' <tr' . self::Attributes($attributes) . '>';
                    $i = 0;
                    foreach ($cells as $cell) {
                        $output .= self::Cell($cell);
                    }
                    $output .= " </tr>\n";
                }
            }
            $output .= "</tbody>\n";
        }
        return $output;
    }

    /**
     * @param array $header
     * @param array $rows
     * @param array $attributes
     * @param null $empty
     * @param bool $sticky
     * @param null $caption
     * @param array $colgroups
     * @return string
     */
    public static function Table(array $header, array $rows, $attributes = array(), $empty = null, $sticky = false, $caption = null, array $colgroups = array())
    {
        if (count($header) && $sticky) {
            $attributes['class'][] = 'sticky-enabled';
        }

        $output = '<table' . self::Attributes($attributes) . ">\n";

        if ($caption) {
            $output .= '<caption>' . $caption . "</caption>\n";
        }

        $output .= self::ColGroups($colgroups);
        self::EmptyHeader($rows, $header, $empty);
        $output .= self::Header($header);
        $output .= self::Rows($rows);

        $output .= "</table>\n";
        return $output;
    }
} 