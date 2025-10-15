<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/22/14
 * Time: 1:09 PM
 */

namespace PM\API;

use JBBCode\CodeDefinitionBuilder;
use JBBCode\DefaultCodeDefinitionSet;

class BBCodeDefinitionSet extends DefaultCodeDefinitionSet{
    public function __construct()
    {
        parent::__construct();

        /* [blockquote] bold tag */
        $builder = new CodeDefinitionBuilder('blockquote', '<blockquote>{param}</blockquote>');
        array_push($this->definitions, $builder->build());

        /* [quote] bold tag */
        $builder = new CodeDefinitionBuilder('quote', '<blockquote>{param}</blockquote>');
        array_push($this->definitions, $builder->build());
    }

} 