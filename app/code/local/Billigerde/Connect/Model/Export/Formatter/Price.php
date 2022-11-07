<?php
/**
 *
 * @desc
 *
 * @category   Billigerde
 * @package    Billigerde_Connect
 * @subpackage
 * @author   Steffen Kieckhäven  <steffen.kieckhaeven@shopping.de>
 *
 */
class Billigerde_Connect_Model_Export_Formatter_Price implements Billigerde_Connect_Model_Export_Formatter_Interface
{
    public function format($value)
    {
        if ($value > 0) {
            $value = number_format($value, 2, '.', '');
        }
        return $value;
    }

}