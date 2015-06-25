<?php

class GroupedProduct extends Product {
    
    /**
     * @config
     */
    private static $description = "A product containing other products";
    
    private static $has_many = array(
        "ChildProducts" => "Product"
    );
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $fields->addFieldToTab(
            "Root.Children",
            $grid = GridField::create(
                "Products",
                null,
                $this->ChildProducts()
            )
        );
        
        $config = GridfieldConfig_RelationEditor::create();
        $grid->setConfig($config);
        
        $config
            ->getComponentByType("GridFieldDataColumns")
            ->setDisplayFields(array(
                "CMSThumbnail"  => "Thumbnail",
                "ClassName" => "Product",
                "Title" => "Title",
                "StockID" => "StockID",
                "Price" => "Price",
                "Disabled" => "Disabled"
            ));
        
        return $fields;
    }
    
    public function onBeforeWrite() {
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
