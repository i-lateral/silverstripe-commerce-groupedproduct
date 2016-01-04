<?php

class GroupedProduct_ProductController extends Extension
{
    
    public function onBeforeInit()
    {
        
        // If we are accessing a product directly that is a child
        // we need to redirect to its parent
        if ($this->owner->ProductGroupID) {
            return $this->owner->redirect($this->owner->ProductGroup()->Link());
        }
    }
}
