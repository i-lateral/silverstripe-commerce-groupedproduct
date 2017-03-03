<?php

class GroupedProduct extends Product {
    
    /**
     * @config
     */
    private static $description = "A product containing other products";

    /**
     * List of fields to show on the child product GridField
     *
     * @config
     */
    private static $child_display_fields = array(
        "CMSThumbnail"  => "Thumbnail",
        "ClassName" => "Product",
        "Title" => "Title",
        "StockID" => "StockID",
        "Price" => "Price",
        "Disabled" => "Disabled"
    );
    
    private static $has_many = array(
        "ChildProducts" => "Product"
    );

    public function SortedChildProducts()
    {
        return $this->ChildProducts()->sort("ProductGroupSort");
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($this->exists()) {
            $fields->addFieldToTab(
                "Root.Children",
                $grid = GridField::create(
                    "Products",
                    null,
                    $this->ChildProducts()
                )
            );
            
            $config = new GridFieldConfig_CatalogueRelated("Product",null,'ProductGroupSort');
            $grid->setConfig($config);
            
            // Set custom children display fields
            $config
                ->getComponentByType("GridFieldDataColumns")
                ->setDisplayFields($this->config()->child_display_fields);
            
            // Remove add button and replace with simple Add button
            $config
                ->removeComponentsByType("GridFieldAddNewMultiClass")
                ->addComponent(new GridFieldAddNewButton());
        }
        
        $this->extend("updateCMSFields", $fields);
        
        return $fields;
    }
    
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        
        // Loop through our children and save (incase we need to set prices)
        foreach($this->ChildProducts() as $product) {
            $write = false;
            
            if(!$product->BasePrice && $this->BasePrice) {
                $product->BasePrice = $this->BasePrice;
                $write = true;
            }
            
            if($this->isChanged("BasePrice")) {
                $product->BasePrice = $this->BasePrice;
                $write = true;
            }
            
            if($write) $product->write();
        }
    }
    
}
