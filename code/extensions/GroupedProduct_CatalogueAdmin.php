<?php

class GroupedProduct_CatalogueAdmin extends Extension {
    
    public function updateList(&$list) {
        
        if($this->owner->modelClass == 'CatalogueProduct') {
            // Remove any items that are nested under a grouped product
            $list = $list->filter("ProductGroupID", 0);
        }
    } 
    
}
