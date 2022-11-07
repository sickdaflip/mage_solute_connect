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
abstract class Billigerde_Connect_Model_Export_Writer_Abstract extends Varien_Object
{
    protected $_filePath = '';
    protected $_fileName = '';

    public function getFilePath()
    {
        return $this->_filePath;
    }

    public function setFilePath($filePath)
    {
        if (substr($filePath, -1,1) !== DS) {
            $filePath .= DS;
        }
        $this->_filePath = $filePath;
        return $this;
    }

    public function getFileName()
    {
        return $this->_fileName;
    }

    public function setFileName($fileName)
    {

        $this->_fileName = $this->correctFileName($fileName);
        return $this;
    }

    public function getFullFileName()
    {
        return Mage::getBaseDir('base') . $this->getFilePath() . $this->getFileName();
    }

    abstract public function correctFileName($fileName);


}