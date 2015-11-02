<?php
class ControllerPaymentRobokassa extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('payment/robokassa');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if( !$this->request->post['robokassa_password1'] 
			&& $this->config->get('robokassa_password1') )
			$this->request->post['robokassa_password1'] = $this->config->get('robokassa_password1');

			if( !$this->request->post['robokassa_password2'] 
			&& $this->config->get('robokassa_password2') )
			$this->request->post['robokassa_password2'] = $this->config->get('robokassa_password2');

			if( !empty($this->request->post['robokassa_hash']) )
				$this->request->post['robokassa_hash'] = $this->request->post['robokassa_hash'];

			$ext_arr = array();
			$updExt = array();

			if( !empty($this->request->post['robokassa_currencies'][0]) )
			{
				$this->request->post['robokassa__status'] = 1;
			}
			else
			{
				$this->request->post['robokassa__status'] = 0;
			}

			if( !empty($this->request->post['robokassa_methods']) )
			{
				$i=0;
				foreach( $this->request->post['robokassa_methods'] as $val )
				{
					if(!empty($this->request->post['robokassa_currencies'][$i]))
					{
						if($i!=0)
						{
							$this->request->post['robokassa'.$i.'_status'] = 1;
							$updExt[] = $i;
						}
					}
					else
					{
						if($i!=0)
						{
							$this->request->post['robokassa'.$i.'_status'] = 0;
						}
					}

					$i++;
				}
			}

			if( !empty($this->request->post['robokassa_methods']) )
			$this->request->post['robokassa_methods'] = serialize($this->request->post['robokassa_methods']);

			if( !empty($this->request->post['robokassa_currencies']) )
			$this->request->post['robokassa_currencies'] = serialize($this->request->post['robokassa_currencies']);

			if( !empty($this->request->post['robokassa_confirm_comment']) )
			$this->request->post['robokassa_confirm_comment'] = serialize($this->request->post['robokassa_confirm_comment']);

			if( !empty($this->request->post['robokassa_order_comment']) )
			$this->request->post['robokassa_order_comment'] = serialize($this->request->post['robokassa_order_comment']);

			if( !empty($this->request->post['robokassa_images']) )
			$this->request->post['robokassa_images'] = serialize($this->request->post['robokassa_images']);

			$this->model_setting_setting->editSetting('robokassa', $this->request->post);

			$this->load->model('localisation/robokassa');
			$this->model_localisation_robokassa->updateExtentions($updExt);

			if($this->request->post['robokassa_stay'])
				$this->response->redirect($this->url->link('payment/robokassa', 'success=1&token=' . $this->session->data['token'], 'SSL'));
			else
				$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if( !empty($this->request->get['success'] ) )
			$data['success'] = $this->language->get('text_success');
		else
			$data['success'] = '';

		$this->load->model('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();

		if( !isset($results['RUB']) && !isset($results['RUR']) )
		{
			$this->error[] = $this->language->get('error_rub');
		}

		$data['notice'] = $this->language->get('notice');

		$data['heading_title'] = $this->language->get('heading_title');

		/* start update: a1 */
		$data['text_saved'] = $this->language->get('text_saved');
		$data['entry_icons'] = $this->language->get('entry_icons');
		$data['text_mode_notice'] = $this->language->get('text_mode_notice');
		/* end update: a1 */
		/* start update: a3 */
		$data['text_robokassa_method'] = $this->language->get('text_robokassa_method');
		/* end update: a3 */

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');

		$data['entry_shop_login'] = $this->language->get('entry_shop_login');

		$data['entry_order_comment'] = $this->language->get('entry_order_comment');
		$data['entry_order_comment_notice'] = $this->language->get('entry_order_comment_notice');

		$data['entry_test_mode'] = $this->language->get('entry_test_mode');

		$data['entry_result_url'] = $this->language->get('entry_result_url');
		$data['entry_result_method'] = $this->language->get('entry_result_method');
		$data['entry_success_url'] = $this->language->get('entry_success_url');
		$data['entry_success_method'] = $this->language->get('entry_success_method');
		$data['entry_fail_url'] = $this->language->get('entry_fail_url');
		$data['entry_fail_method'] = $this->language->get('entry_fail_method');

		$data['entry_commission'] = $this->language->get('entry_commission');
		$data['text_commission_shop'] = $this->language->get('text_commission_shop');
		$data['text_commission_customer'] = $this->language->get('text_commission_customer');
		$data['text_commission_j'] = $this->language->get('text_commission_j');

		$data['entry_interface_language'] = $this->language->get('entry_interface_language');
		$data['entry_interface_language_ru'] = $this->language->get('entry_interface_language_ru');
		$data['entry_interface_language_en'] = $this->language->get('entry_interface_language_en');
		$data['entry_interface_language_detect'] = $this->language->get('entry_interface_language_detect');
		$data['entry_interface_language_notice'] = $this->language->get('entry_interface_language_notice');
		$data['entry_default_language'] = $this->language->get('entry_default_language');
		$data['entry_default_language_ru'] = $this->language->get('entry_default_language_ru');
		$data['entry_default_language_en'] = $this->language->get('entry_default_language_en');
		$data['entry_default_language_notice'] = $this->language->get('entry_default_language_notice');

		$data['entry_log'] = $this->language->get('entry_log');

		$data['entry_log'] = str_replace("#url#", HTTP_CATALOG.'system/logs/robokassa_log.txt', $data['entry_log']);
		
		$data['entry_no_methods'] = $this->language->get('entry_no_methods');
		$data['entry_methods'] = $this->language->get('entry_methods');
		
		$data['entry_password1'] = $this->language->get('entry_password1');
		$data['entry_password2'] = $this->language->get('entry_password2');

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_preorder_status'] = $this->language->get('entry_preorder_status');

		$data['entry_order_status2'] = $this->language->get('entry_order_status2');	
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['entry_confirm_status'] = $this->language->get('entry_confirm_status');
		$data['entry_confirm_status_notice'] = $this->language->get('entry_confirm_status_notice');
		$data['entry_confirm_status_before'] = $this->language->get('entry_confirm_status_before');
		$data['entry_confirm_status_after'] = $this->language->get('entry_confirm_status_after');
		$data['entry_confirm_notify'] = $this->language->get('entry_confirm_notify');
		$data['entry_confirm_comment'] = $this->language->get('entry_confirm_comment');
		$data['text_confirm_comment_default'] = $this->language->get('text_confirm_comment_default');
		$data['entry_no_robokass_methods'] = $this->language->get('entry_no_robokass_methods');

		$data['select_currency'] = $this->language->get('select_currency');

		$data['methods_col1'] = $this->language->get('methods_col1');
		$data['methods_col2'] = $this->language->get('methods_col2');
		$data['methods_col3'] = $this->language->get('methods_col3');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_save_and_go'] = $this->language->get('button_save_and_go');
		$data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_support'] = $this->language->get('tab_support');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_frame'] = $this->language->get('text_frame');

		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');

		$currencies = array();
		$data['currencies'] = array();
		
		if (isset($this->request->post['robokassa_test_mode'])) {
			$data['robokassa_test_mode'] = $this->request->post['robokassa_test_mode'];
		} else {
			$data['robokassa_test_mode'] = $this->config->get('robokassa_test_mode');
		}

		if (isset($this->request->post['robokassa_order_comment'])) {
			$data['robokassa_order_comment'] = $this->request->post['robokassa_order_comment'];
		} elseif( $this->config->get('robokassa_order_comment') ) 
		{
			if( is_array($this->config->get('robokassa_order_comment')) )
			{
				$data['robokassa_order_comment'] = $this->config->get('robokassa_order_comment');
			}
			else
			{
				$data['robokassa_order_comment'] = unserialize($this->config->get('robokassa_order_comment'));
			}
		} elseif( !$this->config->has('robokassa_order_comment') ) {
			
			foreach($data['languages'] as $language)
			{
				$Lang = new Language( $language['directory'] );
				$Lang->load('payment/robokassa');
				
				$data['robokassa_order_comment'][$language['code']] = $Lang->get('text_order_comment_default');
			}
		} else {
			$data['robokassa_order_comment'] = array();
		}

		if (isset($this->request->post['robokassa_interface_language'])) {
			$data['robokassa_interface_language'] = $this->request->post['robokassa_interface_language'];
		} else {
			$data['robokassa_interface_language'] = $this->config->get('robokassa_interface_language');
		}
		
		if (isset($this->request->post['robokassa_default_language'])) {
			$data['robokassa_default_language'] = $this->request->post['robokassa_default_language'];
		} else {
			$data['robokassa_default_language'] = $this->config->get('robokassa_default_language');
		}

		if (isset($this->request->post['robokassa_commission'])) {
			$data['robokassa_commission'] = $this->request->post['robokassa_commission'];
		} else {
			$data['robokassa_commission'] = $this->config->get('robokassa_commission');
		}

		$this->load->model('tool/image');

		$all_images = array();

		if( $this->config->get('robokassa_shop_login') )
		{
			$interface_lang = $this->config->get('config_admin_language');

			if( $interface_lang != 'en' ) 
				$interface_lang = 'ru';

			if( $data['robokassa_test_mode'] )
				$url = "http://test.robokassa.ru/Webservice/Service.asmx/GetCurrencies?MerchantLogin=".$this->config->get('robokassa_shop_login')."&Language=".$interface_lang;
			else
				$url = "http://merchant.roboxchange.com/Webservice/Service.asmx/GetCurrencies?MerchantLogin=".$this->config->get('robokassa_shop_login')."&Language=".$interface_lang;

			if( extension_loaded('curl') )
			{
				$c = curl_init($url);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				$page = curl_exec($c);
				curl_close($c);
			}
			else
			{
				$page = file_get_contents($url);
			}

			if( !preg_match("/<Code>0<\/Code>/i", $page) )
			{
				$data['robokassa_methods'] = '';
			}
			elseif($page)
			{
				$arr_value = array();
				$group_value = array();
				$ar = array();
				$groups = explode("<Group ", $page);

				for($i=1; $i<count($groups); $i++)
				{
					$ar = array();
					preg_match("/^Code=\"([^\"]+)\" Description=\"([^\"]+)\"/",	$groups[$i], $ar);

					if( empty($ar) ) continue;
					$group_description = $ar[2];
					$ar = array();
					preg_match_all("/(<Currency Label=\"([^\"]+)\" Name=\"([^\"]+)\" \/>)/", $groups[$i], $ar);
					if( empty($ar) ) continue;

					for($e=0; $e<count($ar[2]); $e++)
					{
						$Label = $ar[2][$e];
						$Name = $ar[3][$e];
						$currencies[ trim($Label) ] = $Name." (".$group_description.")";

						if( file_exists( DIR_IMAGE.'data/robokassa_icons/'.trim($Label).'.png' ) )
						{
							$all_images[ trim($Label) ]  =array(
								"thumb" => HTTP_CATALOG.'image/data/robokassa_icons/'.trim($Label).'.png',
								"value" => 'data/robokassa_icons/'.trim($Label).'.png'
							);
						}
						else
						{
							$all_images[ trim($Label) ] = array(
										"thumb" => $this->model_tool_image->resize('no_image.png', 40, 40),
										"value" => 'no_image.png'
								);
						}
					}
					
					$all_images["robokassa"] = array(
										"thumb" => HTTP_CATALOG.'image/data/robokassa_icons/robokassa.png',
										"value" => 'data/robokassa_icons/robokassa.png'
								);
					
				}
				
				$data['currencies'] = $currencies;
			}
		}
		else
		{
			$data['robokassa_methods'] = '';
		}
		
		if (isset($this->error)) {
			$data['error_warning'] = $this->error;
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/robokassa', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('payment/robokassa', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$data['callback'] = HTTP_CATALOG . 'index.php?route=payment/robokassa/callback';

		/* kin insert metka: a4 */

		$this->load->model('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();
		
		if( !isset($results['RUB']) && !isset($results['RUR']) )
		{
			$this->error[] = $this->language->get('error_rub');
		}			
		
		$data['opencart_currencies'] = $results;
		
		$data['entry_currency'] = $this->language->get('entry_currency');
		$data['entry_hash'] = $this->language->get('entry_hash');
		$data['text_currency_notice'] = $this->language->get('text_currency_notice');

		$data['text_img_notice'] = $this->language->get('text_img_notice');
		
		if (isset($this->request->post['robokassa_currency'])) {
			$data['robokassa_currency'] = $this->request->post['robokassa_currency'];
		} else {
			$data['robokassa_currency'] = $this->config->get('robokassa_currency');
		} 		
		/* end kin metka: a4 */

		if (isset($this->request->post['robokassa_default_language'])) {
			$data['robokassa_default_language'] = $this->request->post['robokassa_default_language'];
		} else {
			$data['robokassa_default_language'] = $this->config->get('robokassa_default_language');
		}

		if (isset($this->request->post['robokassa_order_status_id'])) {
			$data['robokassa_order_status_id'] = $this->request->post['robokassa_order_status_id'];
		} else {
			$data['robokassa_order_status_id'] = $this->config->get('robokassa_order_status_id');
		}

		if (isset($this->request->post['robokassa_order_status_id2'])) {
			$data['robokassa_order_status_id2'] = $this->request->post['robokassa_order_status_id2'];
		} else {
			$data['robokassa_order_status_id2'] = $this->config->get('robokassa_order_status_id2');
		}

		if (isset($this->request->post['robokassa_shop_login'])) {
			$data['robokassa_shop_login'] = $this->request->post['robokassa_shop_login'];
		} else {
			$data['robokassa_shop_login'] = $this->config->get('robokassa_shop_login');
		}

		if (isset($this->request->post['robokassa_shop_login'])) {
			$data['robokassa_shop_login'] = $this->request->post['robokassa_shop_login'];
		} else {
			$data['robokassa_shop_login'] = $this->config->get('robokassa_shop_login');
		}

		if (isset($this->request->post['robokassa_confirm_notify'])) {
			$data['robokassa_confirm_notify'] = $this->request->post['robokassa_confirm_notify'];
		} else {
			$data['robokassa_confirm_notify'] = $this->config->get('robokassa_confirm_notify'); 
		}

		if (isset($this->request->post['robokassa_confirm_status'])) {
			$data['robokassa_confirm_status'] = $this->request->post['robokassa_confirm_status'];
		} else {
			$data['robokassa_confirm_status'] = $this->config->get('robokassa_confirm_status');
		}

		if (isset($this->request->post['robokassa_confirm_comment'])) {
			$data['robokassa_confirm_comment'] = $this->request->post['robokassa_confirm_comment'];
		} elseif( $this->config->get('robokassa_confirm_comment') ) 
		{
			if( is_array($this->config->get('robokassa_confirm_comment')) )
			{
				$data['robokassa_confirm_comment'] = $this->config->get('robokassa_confirm_comment');
			}
			else
			{
				$data['robokassa_confirm_comment'] = unserialize($this->config->get('robokassa_confirm_comment'));
			}
		} elseif( $this->config->get('robokassa_shop_login')=='' ) {
			
			foreach($data['languages'] as $language)
			{
				$Lang = new Language( $language['directory'] );
				$Lang->load('payment/robokassa');
				
				$data['robokassa_confirm_comment'][$language['code']] = $Lang->get('text_confirm_comment_default');
			}
		} else {
			$data['robokassa_confirm_comment'] = array();
		}

		if (isset($this->request->post['robokassa_log'])) {
			$data['robokassa_log'] = $this->request->post['robokassa_log'];
		} else {
			$data['robokassa_log'] = $this->config->get('robokassa_log');
		} 

		if (isset($this->request->post['robokassa_preorder_status_id'])) {
			$data['robokassa_preorder_status_id'] = $this->request->post['robokassa_preorder_status_id'];
		} else {
			$data['robokassa_preorder_status_id'] = $this->config->get('robokassa_preorder_status_id');
		}

		$robokassa_methods = array();

		if (isset($this->request->post['robokassa_methods'])) {
			$robokassa_methods = $this->request->post['robokassa_methods'];
		} elseif( $this->config->has('robokassa_methods') ) {
			$robokassa_methods = unserialize( $this->config->get('robokassa_methods') );
		}

		$is_ru = 0;
		foreach($data['languages'] as $lang)
		{
			if( $lang['code']=='ru' )
			{
				$is_ru = 1;
			}
		}

		if($robokassa_methods)
		{
			foreach( $robokassa_methods as $value )
			{
				if( !is_array($value) )
				{
					$i = 0;
					foreach($data['languages'] as $lang_id=>$val)
					{
						$i++;
					
						if( ($is_ru && $val['code']=='ru' ) || (!$is_ru && $i==1 ) )
						{
							$method[$val['code']] = $value;
						}
						else
						{
							$method[$val['code']] = '';
						}
					}
				}
				else
				{
					$method = $value;
				}

				$data['robokassa_methods'][] = $method;
			}
		}

		if (isset($this->request->post['robokassa_currencies'])) {
			$data['robokassa_currencies'] = $this->request->post['robokassa_currencies'];
		} else {
			$data['robokassa_currencies'] = unserialize( $this->config->get('robokassa_currencies') );
		}

		if (isset($this->request->post['robokassa_images'])) {
			$robokassa_images = $this->request->post['robokassa_images'];
		} elseif( $this->config->get('robokassa_images') ) {
			$robokassa_images = unserialize( $this->config->get('robokassa_images') );

		} else {
			$robokassa_images = array();
			$data['robokassa_images'] = array();
		}

		$data['all_images'] = $all_images;
		
		for($i=0; $i<20; $i++ )
		{

			if( empty($robokassa_images[$i]) )
			{
				if( !empty($data['robokassa_currencies'][$i]) )
				{
					$thumb = $all_images[$data['robokassa_currencies'][$i]]['thumb'];
					$value = $all_images[$data['robokassa_currencies'][$i]]['value'];
				}
				else
				{
					$thumb = $this->model_tool_image->resize('no_image.png', 40, 40);
					$value = 'no_image.png';
				}
			}
			else
			{
				$thumb = HTTP_CATALOG.'image/'.$robokassa_images[$i];
				$value = $robokassa_images[$i];
			}

			if( empty($data['robokassa_currencies'][$i]) )
			$data['robokassa_currencies'][$i] = '';

			$data['robokassa_images'][$i] = array(
					"thumb" => $thumb,
					"value" => $value
				);
		}

		$data['no_image'] = $this->model_tool_image->resize('no_image.png', 40, 40);

		/* start update: a1 */
		if ( $this->config->get('robokassa_password1') ) {
			$data['robokassa_password1'] = 1;
		}
		else
		{
			$data['robokassa_password1'] = 0;
		}

		$data['token'] = $this->session->data['token'];

		if ( $this->config->get('robokassa_password2') ) {
			$data['robokassa_password2'] = 1;
		}
		else
		{
			$data['robokassa_password2'] = 0;
		}
		
		if (isset($this->request->post['robokassa_icons'])) {
			$data['robokassa_icons'] = $this->request->post['robokassa_icons'];
		} else {
			$data['robokassa_icons'] = $this->config->get('robokassa_icons');
		}
		if (isset($this->request->post['robokassa_hash'])) {
			$data['robokassa_hash'] = $this->request->post['robokassa_hash']."POST";
		} else {
			$data['robokassa_hash'] = $this->config->get('robokassa_hash');
		}
		/* end update: a1 */

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['robokassa_geo_zone_id'])) {
			$data['robokassa_geo_zone_id'] = $this->request->post['robokassa_geo_zone_id'];
		} else {
			$data['robokassa_geo_zone_id'] = $this->config->get('robokassa_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['robokassa_status'])) {
			$data['robokassa_status'] = $this->request->post['robokassa_status'];
		} else {
			$data['robokassa_status'] = $this->config->get('robokassa_status');
		}

		if (isset($this->request->post['robokassa_sort_order'])) {
			$data['robokassa_sort_order'] = $this->request->post['robokassa_sort_order'];
		} else {
			$data['robokassa_sort_order'] = $this->config->get('robokassa_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/robokassa.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/robokassa')) {
			$this->error[] = $this->language->get('error_permission');
		}

		if (!$this->request->post['robokassa_password1'] && !$this->config->get('robokassa_password1') )
		{
			$this->error[] = $this->language->get('error_robokassa_password1');
		}

		if (!$this->request->post['robokassa_password2'] && !$this->config->get('robokassa_password2') )
		{
			$this->error[] = $this->language->get('error_robokassa_password2');
		}

		if( !empty($this->request->post['robokassa_password1']) && 
			!preg_match("/^[a-zA-Z0-9]+$/", $this->request->post['robokassa_password1']) )
		{
			$this->error[] = $this->language->get('error_robokassa_password_symbols');
		}

		if (!$this->request->post['robokassa_shop_login']) {
			$this->error[] = $this->language->get('error_robokassa_shop_login');
		}

		if( !empty($this->request->post['robokassa_shop_login']) )
		$this->request->post['robokassa_shop_login'] = trim($this->request->post['robokassa_shop_login']);
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function image() {
		$this->load->model('tool/image');

		if (isset($this->request->get['image'])) {
			$this->response->setOutput(HTTP_IMAGE.$this->request->get['image']);
		}
	}

}
?>