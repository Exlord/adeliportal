<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/16/14
 * Time: 1:27 PM
 */

namespace System\Captcha;


use Zend\Captcha\AbstractWord;
use Zend\Captcha\Image;
use Zend\Session\Container;
use Zend\Validator\Exception;

class Math extends AbstractWord
{
    protected $allowNegativeResult = false;
    protected $allowNegativeOperators = false;
    protected $min = 0;
    protected $max = 9;

    protected $lowestMin = -19;
    protected $highestMin = 1;

    protected $lowestMax = 9;
    protected $highestMax = 19;

    protected $operand1;
    protected $operand2;
    protected $operator;

    protected $operands = array(
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
    );

    protected $operatorValues = array(
        '+' => 'CAPTCHA_OPERAND_PLUS',
        '-' => 'CAPTCHA_OPERATOR_MINUS',
    );
    protected $operators = array(
        1 => '+',
        2 => '-',
    );

    public function __construct($config = null, $options = null)
    {
        parent::__construct($options);

        if (!$config)
            $config = getConfig('system_captcha')->varValue;

        if (isset($config['math'])) {
            $config = $config['math'];

            if (isset($config['min'])) {
                $min = (int)$config['min'];
                if ($min >= $this->lowestMin && $min <= $this->highestMin)
                    $this->min = $min;
            }

            if (isset($config['max'])) {
                $max = (int)$config['max'];
                if ($max >= $this->lowestMax && $max <= $this->highestMax)
                    $this->max = $max;
            }

            if (isset($config['negative_operators'])) {
                $no = (int)$config['negative_operators'];
                $this->allowNegativeOperators = $no == 1;
                if (!$this->allowNegativeOperators) {
                    if ($this->min < 0)
                        $this->min = 0;
                }
            }

            if (isset($config['negative_result'])) {
                $nr = (int)$config['negative_result'];
                $this->allowNegativeResult = $nr == 1;
                if (!$this->allowNegativeResult) {
                    if ($this->min < 0)
                        $this->min = 0;
                }
            }
        }
    }

    /**
     * Generate a new captcha
     *
     * @return string new captcha ID
     */
//    public function generate()
//    {
//        $id = parent::generate();
//        return $id;
//    }

    /**
     * Generate new random word
     *
     * @return string
     */
    protected function generateWord()
    {
        $word = '';
        $this->operand1 = mt_rand($this->min, $this->max);
        $this->operator = mt_rand(1, 2);

        $max = $this->max;
        if (!$this->allowNegativeResult) {
            if ($this->operator == 2) {
                $max = $this->operand1;
            }
        }
        $this->operand2 = mt_rand(0, $max);

        switch ($this->operators[$this->operator]) {
            case '+':
                $word = $this->operand1 + $this->operand2;
                break;
            case '-':
                $word = $this->operand1 - $this->operand2;
                break;
        }

        return (string)$word;
    }

    /**
     * Get helper name used to render captcha
     *
     * @return string
     */
    public function getHelperName()
    {
        return 'system_captcha_math';
    }

    /**
     * @return mixed
     */
    public function getOperand1()
    {
        $op = $this->operand1;
        $rand = rand(0, 1);
        if ($rand) {
            $isNeg = $op < 0;
            if ($isNeg)
                $op *= -1;
            $result = t($this->operands[$op]);
            if ($isNeg)
                $result = t('CAPTCHA_OPERAND_NEGATIVE') . ' ' . $result;

            return $result;
        } else
            return $op;
    }

    /**
     * @return mixed
     */
    public function getOperand2()
    {
        $op = $this->operand2;
        $rand = rand(0, 1);
        if ($rand) {
            $isNeg = $op < 0;
            if ($isNeg)
                $op *= -1;
            $result = t($this->operands[$op]);
            if ($isNeg)
                $result = t('CAPTCHA_OPERAND_NEGATIVE') . ' ' . $result;

            return $result;
        } else
            return $op;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        $op = $this->operators[$this->operator];
        $rand = rand(0, 1);
        if ($rand)
            return t($this->operatorValues[$op]);
        else
            return $op;
    }

    public function getEqualSign()
    {
        $rand = rand(0, 1);
        if ($rand)
            return '=';
        else
            return t('CAPTCHA_OPERATOR_EQUAL');
    }
}