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
class Billigerde_Connect_Model_Adminhtml_System_Config_Source_Export_Field_Filter_Category
{
    protected $_categoryList = null;

    public function getCategoriesTreeView()
    {
        if ($this->_categoryList === null) {
            $categoryCollection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSort('path', 'asc')
                ->load();

            $categoryList = array();
            foreach ($categoryCollection as $category) {
                $categoryList[$category->getId()] = array(
                    'label' => $category->getName(),
                    'level'  =>$category->getLevel(),
                    'value' => $category->getId(),
                );
            }

            $this->_categoryList = $categoryList;
        }

        return $this->_categoryList;
    }


    // Return options to system config


    public function toOptionArray()
    {

        $options = array();

        $categoriesTreeView = $this->getCategoriesTreeView();

        foreach($categoriesTreeView as $categoryTreeEntry)
        {
            $categoryName    = $categoryTreeEntry['label'];
            $categoryId      = $categoryTreeEntry['value'];
            $categoryLevel   = $categoryTreeEntry['level'];

            $hyphen = '- ';
            for ( $i=1; $i < $categoryLevel; $i++) {
                $hyphen = $hyphen . '- ';
            }

            $categoryName = $hyphen . '> ' . $categoryName . ' (ID:' . $categoryId . ')';

            $options[] = array(
               'label' => $categoryName,
               'value' => $categoryId,
            );
        }

        return $options;
    }
}