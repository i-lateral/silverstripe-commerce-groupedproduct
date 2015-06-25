<?php

class GroupedProduct_CatalogueProduct extends DataExtension {
    
    private static $has_one = array(
        "ProductGroup" => "Product"
    );
    
    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldToTab(
            "Root.Settings",
            DropdownField::create(
                "ProductGroupID",
                _t("GroupedProduct.AddToGroup", "Add this product to a group"),
                Product::get()
                    ->filter("ClassName", "GroupedProduct")
                    ->map()
            )->setEmptyString(_t("GroupedProduct.SelectProduct", "Select a Product"))
        );
        
        // If this is a child product, remove some fields we don't need
        if($this->owner->ProductGroupID) {
            $fields->removeByName("Content");
            $fields->removeByName("Metadata");
            $fields->removeByName("Related");
            $fields->removeByName("Categories");
            $fields->removeByName("TaxRate");
        }
    }
    
    public function onBeforeWrite() {
        if($this->owner->ProductGroupID) {
            $group = $this->owner->ProductGroup();
            
            // Set the URL for this product to include the parent URL
            $this->owner->URLSegment = $group->URLSegment . "-" . Convert::raw2url($this->owner->Title);
               
            // Set the base price
            if(!$this->owner->BasePrice)
                $this->owner->BasePrice = $group->BasePrice;
        }
    }
    
}
