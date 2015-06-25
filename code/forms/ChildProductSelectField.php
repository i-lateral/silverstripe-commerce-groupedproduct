<?php

class ChildProductSelectField extends OptionsetField {
    
    public function Field($properties = array()) {
		$source = $this->getSource();
		$odd = 0;
		$options = array();
		
		if($source) {
			foreach($source as $item) {
                $price = new Currency("Price");
                $price->setValue($item->Price);
                
                $price_diff = new Currency("PriceDiff");
                $price_diff->setValue($item->PriceDiff);
                
				$options[] = new ArrayData(array(
					'ID' => $item->ID,
					'Name' => $this->name,
					'Value' => $item->ID,
					'Title' => $item->Title,
					'isChecked' => $item->ID == $this->value,
					'isDisabled' => $this->disabled || in_array($item->ID, $this->disabledItems),
                    'Price' => $price,
                    'PriceDiff' => $price_diff
				));
			}
		}

		$properties = array_merge($properties, array(
			'Options' => new ArrayList($options)
		));

		return $this->customise($properties)->renderWith(
			$this->getTemplates()
		);
	}
    
}
