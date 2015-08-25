<?php  /* robokassa metka */
class ModelPaymentRobokassa7 extends Model {

	private $INDEX=7;

  	public function getMethod($address, $total) {
		
		if( $this->config->get('robokassa_status') && $this->config->get('robokassa__status') ){
		
		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();
		
		$RUB = '';
		
		if( !isset($currencies['RUB']) && !isset($currencies['RUR']) ) return;
		elseif( isset($currencies['RUB']) ) $RUB = 'RUB';
		elseif( isset($currencies['RUR']) ) $RUB = 'RUR';
		
		if( !empty($address['country_id']) && !empty($address['zone_id']) )
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('robokassa_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
			if (!$this->config->get('robokassa_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}	
		}
		else
		{
			$status = true;
		}
		
		
		$vr_robokassa_methods = unserialize($this->config->get('robokassa_methods'));
		if( !is_array( $vr_robokassa_methods[$this->INDEX] ) )
		{
			$robokassa_methods[$this->INDEX][$this->config->get('config_language')] = $vr_robokassa_methods[$this->INDEX];			
		}
		else
		{
			$robokassa_methods = $vr_robokassa_methods;
		}
		
		
		$method_data = array();
	
		$robokassa_currencies = unserialize($this->config->get('robokassa_currencies'));
		$robokassa_images = unserialize($this->config->get('robokassa_images'));
		
		if( empty($robokassa_images[$this->INDEX]) )
		$robokassa_images[$this->INDEX] = 'data/robokassa_icons/'.$robokassa_currencies[$this->INDEX].'.png';
		
		if ($status) 
		{
			if($this->INDEX!=0)
			$name = 'robokassa'.$this->INDEX;
			else
			$name = 'robokassa';			
			
			$image = '';
			
			if(  $this->config->get('robokassa_icons') && file_exists(DIR_IMAGE.$robokassa_images[$this->INDEX]) )
			{
				$img_url = preg_replace("/\/$/", "", HTTP_SERVER);
				$img_url .= '/image/'.$robokassa_images[$this->INDEX];
				$image = '<table><tr><td style="vertical-align: middle; width: 30px"><img src="'.$img_url.'"></td><td style="vertical-align: middle;" valign=middle>'.$robokassa_methods[$this->INDEX][$this->config->get('config_language')].'</td></tr></table>';
			}
			else
			{
				$title = $robokassa_methods[$this->INDEX][$this->config->get('config_language')];
			}
			
			$title = $robokassa_methods[$this->INDEX][$this->config->get('config_language')];
			
      		$method_data = array( 
        		'code'       => $name,
				'title'		 => $title,
				'image'		 => $image,
				'sort_order' => $this->config->get('robokassa_sort_order')
      		);
    	}
   
   
    	return $method_data;
  	}}
}
?>