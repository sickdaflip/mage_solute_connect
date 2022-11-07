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
class Billigerde_Connect_Model_Export_Writer_Csv extends Billigerde_Connect_Model_Export_Writer_Abstract
{

    protected $_enclosure = '"';
    protected $_delimiter = ';';

    protected $_headline = array();

    protected $_handle = null;

    public function setHeadline(array $headline)
    {
        $this->_headline = $headline;
        return $this;
    }

    public function getHeadline()
    {
        return $this->_headline;
    }

    public function setEnclosure($value)
    {
        $this->_enclosure = $value;
        return $this;
    }

    public function getEnclosure()
    {
        return $this->_enclosure;
    }

    public function setDelimiter($value)
    {
        $this->_delimiter = $value;
        return $this;
    }

    public function getDelimiter()
    {
        return $this->_delimiter;
    }

    public function getHandle()
    {
        return $this->_handle;
    }

    public function setHandle($handle)
    {
        $this->_handle = $handle;
        return $this;
    }

    public function openFile($fileMode)
    {
        $this->setHandle(fopen($this->getFullFileName(), $fileMode));
        return $this;
    }

    public function correctFileName($fileName)
    {
        if (substr($fileName, -4, 4) != '.csv') {
            $fileName = $fileName . '.csv';
        }
        return $fileName;
    }

    public function closeFile()
    {
        if ($this->getHandle()) {
            fclose($this->getHandle());
        }
        $this->setHandle(null);
        return $this;
    }

    public function addCollectionToFile(Varien_Data_Collection $collection)
    {
        $this->openFile('a');

        foreach ($collection as $model) {
            $this->_writeLineFromModel($model);
        }

        $this->closeFile();
        return $this;
    }

    public  function addModelToFile(Varien_Object $model)
    {
        $this->openFile('a');

        $this->_writeLineFromModel($model);

        $this->closeFile();
        return $this;
    }

    public function writeFileFromCollection(Varien_Data_Collection $collection, $addHeadline = true)
    {
        $this->openFile('w');

        if ($addHeadline) {
            $this->_writeHeadline();
        }

        foreach ($collection as $model) {
            $this->_writeLineFromModel($model);
        }

        $this->closeFile();
        return $this;
    }

    public function addDataToFile(array $data)
    {
        $this->openFile('a');

        foreach ($data as $line) {
            $this->_writeLineFromData($line);
        }

        $this->closeFile();
        return $this;
    }

    public function addDataLineToFile(array $data)
    {
        $this->openFile('a');

        $this->_writeLineFromData($data);

        $this->closeFile();
        return $this;
    }

    public function writeFileFromData(array $data, $addHeadline = true)
    {
        $this->openFile('w');

        if ($addHeadline) {
            $this->_writeHeadline();
        }

        foreach ($data as $line) {
            $this->_writeLineFromData($line);
        }

        $this->closeFile();
        return $this;
    }

    public function writeHeadline()
    {
        $this->openFile('w');

        $this->_writeHeadline();

        $this->closeFile();
        return $this;
    }


    protected function _writeHeadline()
    {
        $headline = $this->getHeadline();
        $this->_writeData($headline);
        return $this;
    }

    protected function _writeLineFromModel(Varien_Object $model)
    {
        $data = array();

        $headline = $this->getHeadline();
        if (!empty($headline)) {
            foreach ($headline as $index => $key) {
                $data[] = $model->getData($key);
            }
        } else {
            $data = $model->getData();
        }

        $this->_writeData($data);

        return $this;
    }

    protected function _writeLineFromData(array $data)
    {
        $line = array();

        $headline = $this->getHeadline();
        if (!empty($headline)) {
            foreach ($headline as $index => $key) {
                $line[] =  isset($data[$key])? $data[$key]:'';
            }
        } else {
            $line = $data;
        }

        $this->_writeData($line);

        return $this;
    }

    protected function _writeData(array &$data)
    {
        fputcsv($this->getHandle(), $data, $this->getDelimiter(), $this->getEnclosure());
        return $this;
    }


}