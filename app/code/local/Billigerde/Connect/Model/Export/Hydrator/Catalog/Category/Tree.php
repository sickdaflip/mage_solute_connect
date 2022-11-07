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
class Billigerde_Connect_Model_Export_Hydrator_Catalog_Category_Tree extends Billigerde_Connect_Model_Export_Hydrator_Abstract
{
    protected $_categories = array();

    public function mapValue()
    {
        $categoryPath = $this->getMappedValue();

        if ($categoryPath == null || strlen($categoryPath) == 0) {
            return $this;
        }

        $categoryIds = explode('/', $categoryPath);

        $toLoadCategoryIds = array();

        foreach ($categoryIds as $categoryId) {
            if (!isset($this->_categories[$categoryId])) {
                $toLoadCategoryIds[] = $categoryId;
            }
        }

        if (!empty($toLoadCategoryIds)) {
            /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
            $categoryCollection = mage::getModel('catalog/category')->getCollection();
            $categoryCollection->addAttributeToSelect('name');
            $categoryCollection->addFieldToFilter('entity_id', array('in' => $toLoadCategoryIds));
            foreach ($categoryCollection as $category) {
                $this->_categories[$category->getId()] = $category;
            }
        }

        $c = array();
        foreach ($categoryIds as $categoryId) {
            if ($categoryId == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                continue;
            }
            if ($categoryId == Mage::app()->getStore()->getRootCategoryId()) {
                continue;
            }
            $c[] = $this->_categories[$categoryId]->getName();
        }

        $this->setResult(implode(' > ', $c)); // TODO ShopCat Trennzeichen als Store Config // prevent being Csv delimiter
        return $this;
    }


}