<?php
namespace System\Filter;
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 9:28 AM
 */
class FilterHtml extends \Zend\Filter\StripTags
{
    public function __construct()
    {
        $tags = array(
            'a', 'abbr', 'acronym', 'address', 'area', 'article', 'aside', 'audio', 'b',
            'bdo', 'big', 'blockquote', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code',
            'col', 'colgroup', 'datalist', 'dd', 'del', 'details', 'dialog', 'dir', 'div', 'dl', 'dt', 'em',
            'embed', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'header', 'hgroup', 'hr', 'i', 'img', 'input', 'ins', 'kbd', 'keygen', 'label',
            'legend', 'li', 'link', 'main', 'map', 'mark', 'menu', 'menuitem', 'meter', 'nav',
            'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'q', 'rp',
            'rt', 'ruby', 's', 'samp', 'section', 'select', 'small', 'source', 'span', 'strike', 'strong',
            'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time',
            'title', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr');

        $attributes = array('src', 'class', 'dir', 'id', 'lang', 'rel', 'rev', 'style', 'target',
            'title', 'xml:lang', 'accesskey', 'tabindex', 'charset', 'coords', 'href', 'hreflang', 'name', 'shape');

        $options['allowTags'] = $tags;
        $options['allowAttribs'] = $attributes;
        parent::__construct($options);
    }

} 