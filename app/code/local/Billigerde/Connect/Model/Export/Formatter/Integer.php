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
class Billigerde_Connect_Model_Export_Formatter_Integer implements Billigerde_Connect_Model_Export_Formatter_Interface
{
    public function format($value)
    {
        if (is_numeric($value)) {
            $value = (int)$value;
        }
        return $value;
    }

}