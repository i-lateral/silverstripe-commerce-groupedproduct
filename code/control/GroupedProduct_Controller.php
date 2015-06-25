<?php

class GroupedProduct_Controller extends Product_Controller {
    
    
    
    private static $allowed_actions = array(
        "Form"
    );
    
    /**
     * Overwrite default product form and add our custom list
     * 
     * @return Form 
     */
    public function Form() {
        $object = $this->dataRecord;
        $field_type = $this->ShowChildrenAs . "Field";
        $children = ArrayList::create();
        
        $form = Form::create(
            $this,
            "Form",
            FieldList::create(
                HiddenField::create('ClassName')
                    ->setValue("Product"),
                $id_field = ChildProductSelectField::create(
                    "ID",
                    _t("GroupedProduct.PleaseSelect", "Please Select:")
                )->addExtraClass('forms-list'),
                QuantityField::create('Quantity', _t('Commerce.Qty','Qty'))
                    ->setValue('1')
                    ->addExtraClass('checkout-additem-quantity')
            ),
            FieldList::create(
                FormAction::create('doAddItemToCart',_t('Commerce.AddToCart','Add to Cart'))
                    ->addExtraClass('btn')
                    ->addExtraClass('btn-green')
            ),
            new RequiredFields(array(
                "ID",
                "Quantity"
            ))
        );
        
        // Generate our list of children
        foreach($object->ChildProducts() as $product) {
            
            $price = ((int)$product->Price) ? $product->Price : $object->Price;
            $price_diff = $price - $object->Price;
                        
            $children->add(new ArrayData(array(
                "ID" => $product->ID,
                "Title" => $product->Title,
                "Price" => $price,
                "PriceDiff" => $price_diff
            )));
        }
        
        $id_field->setSource($children);
        
        return $form;
    }
    
    public function doAddItemToCart($data, $form) {
        $classname = $data["ClassName"];
        $id = $data["ID"];
        $cart = ShoppingCart::get();
        
        $object = $classname::get()->byID($id);
        $parent = $object->ProductGroup();
        
        if($object && $parent) {
            if($parent->TaxRateID && $parent->TaxRate()->Amount)
                $tax_rate = $parent->TaxRate()->Amount;
            else
                $tax_rate = 0;
                
            $price = ((int)$object->Price) ? $object->Price : $parent->Price;
            $image = ($object->Images()->exists()) ? $object->SortedImages()->first() : $parent->SortedImages()->first();
            $weight = ($object->Weight) ? $object->Weight : $parent->Weight;
            
            $item_to_add = array(
                "Key" => $object->ID,
                "Title" => $parent->Title . " - " . $object->Title,
                "Content" => $object->Content,
                "BasePrice" => $price,
                "TaxRate" => $tax_rate,
                "Image" => $image,
                "StockID" => $object->StockID,
                "ID" => $object->ID,
                "Weight" => $weight,
                "ClassName" => $object->ClassName
            );
            
            // Try and add item to cart, return any exceptions raised
            // as a message
            try {
                $cart->add($item_to_add, $data['Quantity']);
                $cart->save();
                
                $message = _t('GroupedProduct.AddedItemToCart', 'Added item to your shopping cart');
                $message .= ' <a href="'. $cart->Link() .'">';
                $message .= _t('GroupedProduct.ViewCartNow', 'View cart now');
                $message .= '</a>';

                $this->owner->setSessionMessage(
                    "success",
                    $message
                );
            } catch(ValidationException $e) {
                $this->owner->setSessionMessage(
                    "bad",
                    $e->getMessage()
                );
            } catch(Exception $e) {
                $this->owner->setSessionMessage(
                    "bad",
                    $e->getMessage()
                );
            }
        } else {
            $this->setSessionMessage(
                "bad",
                _t("GroupedProduct.ThereWasAnError", "There was an error")
            );
        }

        return $this->redirectBack();
    }
    
}
