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
	
	/**
	 * Get the source of this field as an array
	 *
	 * @return array
	 */
	public function getSourceAsArray()
	{
		$source = $this->getSource();
		if (is_array($source)) {
			return $source;
		} else {
			$sourceArray = array();
			foreach ($source as $item) {
				$sourceArray[$item->ID] = $item->Title;
			}
		}
		
		return $sourceArray;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($validator) {
		if (!$this->value) {
			return true;
		}

		return parent::validate($validator);
	}
    
}
