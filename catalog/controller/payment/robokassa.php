<?php /* robokassa metka */
class ControllerPaymentRobokassa extends Controller {

	private $INDEX = 0;

	public function index() {
	
		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();

		$RUB = '';

		if( !isset($currencies['RUB']) && !isset($currencies['RUR']) ){}
		elseif( isset($currencies['RUB']) ) $RUB = 'RUB';
		elseif( isset($currencies['RUR']) ) $RUB = 'RUR';
		

		$data['button_confirm'] = $this->language->get('button_confirm');
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		if( $this->config->get('robokassa_test_mode') )
		{
			$data['action'] = "http://test.robokassa.ru/Index.aspx";
		}
		else
		{
			$data['action'] = "https://auth.robokassa.ru/Merchant/Index.aspx";
		}
		
		$mrh_pass1 = $this->config->get('robokassa_password1');
		$data['mrh_login'] = $this->config->get('robokassa_shop_login');

		$mrh_login = $data['mrh_login'];

		$out_summ = $order_info['total'];

		if( $this->config->get('config_currency')!=$this->config->get('robokassa_currency') ) 
		{
			$out_summ = $this->currency->convert($out_summ, $this->config->get('config_currency'), $this->config->get('robokassa_currency'));
		}

		$robokassa_currencies = unserialize( $this->config->get('robokassa_currencies') );
		if( $robokassa_currencies[$this->INDEX] == 'robokassa' )
			$robokassa_currencies[$this->INDEX] = '';
		$data['in_curr'] = $robokassa_currencies[$this->INDEX];

		if( $this->config->get('robokassa_commission') == 'shop' && !$this->config->get('robokassa_test_mode') )
		{
			$url = 'http://merchant.roboxchange.com/WebService/Service.asmx/CalcOutSumm?MerchantLogin='.$mrh_login.
					'&IncCurrLabel='.$data['in_curr'].'&IncSum='.$out_summ;

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

			$ar = array();

			if( $page && preg_match("/<OutSum>([\d\.]+)<\/OutSum>/", $page, $ar) )
			{
				if( !empty($ar[1]) )
				{
					$out_summ = $ar[1];
				}
			}
		}

		$shp_item = "2";

		$data['robokassa_confirm_status'] = $this->config->get('robokassa_confirm_status');

		$in_curr = $robokassa_currencies[$this->INDEX];

		$inv_id = $this->session->data['order_id'];
		$data['out_summ'] = $out_summ;
		$data['inv_id'] =  $this->session->data['order_id'];
		$data['inv_desc'] = $order_info['store_name'];

		$data['crc'] = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");
		$data['shp_item'] = $shp_item;

		$culture = $this->session->data['language'];

		if( $this->config->get('robokassa_interface_language') && $this->config->get('robokassa_interface_language')!='detect' )
		{
			$culture = $this->config->get('robokassa_interface_language');
		}
		elseif( $this->config->get('robokassa_interface_language')=='detect' )
		{
			if( $this->session->data['language'] == 'ru' || $this->session->data['language']=='en' )
			{
				$culture = $this->session->data['language'];
			}
			elseif( $this->config->get('robokassa_default_language') )
			{
				$culture = $this->config->get('robokassa_default_language');
			}
			else
			{
				$culture = 'ru';
			}
		}
		else
		{
			if( $culture!='en' )
			{
				$culture!='ru';
			}
		}

		$data['culture'] = $culture;
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payza.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/payza.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/payza.tpl', $data);
		}
	}


	public function preorder()
	{
		$order_id = $this->session->data['order_id'];

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);

		$comment = '';

		if( !empty($order_info['language_id']) )
		{
			$this->load->model('localisation/language');
			$lang = $this->model_localisation_language->getLanguage($order_info['language_id']);

			if( !empty($lang['code']) && $this->config->get('robokassa_order_comment') )
			{
				$comment_arr = unserialize($this->config->get('robokassa_order_comment'));

				if( !empty($comment_arr[$lang['code']]) )
				{
					$comment = $comment_arr[$lang['code']];
				}
			}

			if( $this->config->get('robokassa_test_mode') )
			{
				$link = "http://test.robokassa.ru/Index.aspx?";
			}
			else
			{
				$link = "https://auth.robokassa.ru/Merchant/Index.aspx?";
			}

			$arr = array();
			$arr[] = 'MrchLogin='.$this->request->get['MrchLogin'];
			$arr[] = 'OutSum='.$this->request->get['OutSum'];
			$arr[] = 'InvId='.$this->request->get['InvId'];
			$arr[] = 'Desc='.$this->request->get['Desc'];
			$arr[] = 'SignatureValue='.$this->request->get['SignatureValue'];
			$arr[] = 'Shp_item='.$this->request->get['Shp_item'];
			$arr[] = 'IncCurrLabel='.$this->request->get['IncCurrLabel'];
			$arr[] = 'Culture='.$this->request->get['Culture'];

			$link .= implode("&", $arr);

			$comment = str_replace("{link}", "<a href='".$link."'>".$link."</a>", $comment);
			$comment = preg_replace("/[\n\r\t]/", "<br>", $comment);

		}

		$this->model_checkout_order->confirm($order_id, $this->config->get('robokassa_preorder_status_id'), $comment, true);

		exit('success');
	}

	public function result() 
	{
		$IS_DEBUG = 0;

		if( empty($this->request->post["InvId"]) ) exit();

		if( $this->config->get('robokassa_log') )
		{
			$log = new Log('robokassa_log.txt');
			$IS_DEBUG = 1;

			$log->write('RESULT('.$this->request->post["InvId"].'): metka-1');
		}

		$mrh_pass2 =$this->config->get('robokassa_password2');

		if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): matka-2 OutSum='.$this->request->post['OutSum'].'|InvId='.$this->request->post["InvId"].'|Shp_item='.$this->request->post["Shp_item"].'|SignatureValue='.$this->request->post["SignatureValue"]);

		if( empty($this->request->post['OutSum']) ||
			empty($this->request->post["InvId"]) || 
			empty($this->request->post["Shp_item"]) || 
			empty($this->request->post["SignatureValue"]) )
		exit();

		if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): metka-3');

		$out_summ = $this->request->post['OutSum'];
		$inv_id = 	$this->request->post["InvId"];
		$shp_item = $this->request->post["Shp_item"];
		$crc = 		$this->request->post["SignatureValue"];

		$crc = strtoupper($crc);

		$mrh_login = $this->config->get('robokassa_shop_login');
		
		$my_crc1 = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));
		$my_crc2 = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
		$my_crc3 = strtoupper(md5("$mrh_login:$out_summ:$inv_id:$mrh_pass2"));
		$my_crc4 = strtoupper(md5("$mrh_login:$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

		if( $IS_DEBUG )
		$log->write('RESULT('.$this->request->post["InvId"].'):  metka-4 '.$crc.'|'.$my_crc1.'|'.$my_crc2.'|'.$my_crc3.'|'.$my_crc4);

		if( $my_crc1 == $crc || 
			$my_crc2 == $crc || 
			$my_crc3 == $crc || 
			$my_crc4 == $crc
		)
		{
			if( $IS_DEBUG )
				$log->write('RESULT('.$this->request->post["InvId"].'): metka-5');

			$this->load->model('checkout/order');
			
			if( $IS_DEBUG )
				$log->write('RESULT('.$this->request->post["InvId"].'): metka-6');

			if( $this->config->get('robokassa_confirm_status')=='before' )
			{
				$order_info = $this->model_checkout_order->getOrder($this->request->post["InvId"]);

				$comment = '';

				if( !empty($order_info['language_id']) )
				{
					$this->load->model('localisation/language');
					$lang = $this->model_localisation_language->getLanguage($order_info['language_id']);

					if( !empty($lang['code']) && $this->config->get('robokassa_confirm_comment') )
					{
						$comment_arr = unserialize($this->config->get('robokassa_confirm_comment'));

						if( !empty($comment_arr[$lang['code']]) )
						{
							$comment = $comment_arr[$lang['code']];
						}
					}
				}
				
				$this->model_checkout_order->update( $inv_id, 
				$this->config->get('robokassa_order_status_id'),$comment,$this->config->get('robokassa_confirm_notify') );
			}
			else
			{
				$this->model_checkout_order->confirm($inv_id, $this->config->get('robokassa_order_status_id'));
			}

			if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): metka-7');

			echo "OK$inv_id\n";
		}

		if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): metka-end');		
	}

	public function callback() 
	{
		$IS_DEBUG = 0;

		if( empty($this->request->post["InvId"]) ) exit();

		if( $this->config->get('robokassa_log') )
		{
			$log = new Log('robokassa_log.txt');
			$IS_DEBUG = 1;

			$log->write('RESULT('.$this->request->post["InvId"].'): metka-1');
		}

		$mrh_pass2 =$this->config->get('robokassa_password2');

		if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): matka-2 OutSum='.$this->request->post['OutSum'].'|InvId='.$this->request->post["InvId"].'|Shp_item='.$this->request->post["Shp_item"].'|SignatureValue='.$this->request->post["SignatureValue"]);

		if( empty($this->request->post['OutSum']) ||
			empty($this->request->post["InvId"]) || 
			empty($this->request->post["Shp_item"]) || 
			empty($this->request->post["SignatureValue"]) )
		exit();

		if( $IS_DEBUG )
		$log->write('RESULT('.$this->request->post["InvId"].'): metka-3');

		$out_summ = $this->request->post['OutSum'];
		$inv_id = 	$this->request->post["InvId"];
		$shp_item = $this->request->post["Shp_item"];
		$crc = 		$this->request->post["SignatureValue"];

		$crc = strtoupper($crc);

		$mrh_login = $this->config->get('robokassa_shop_login');

		$my_crc1 = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));
		$my_crc2 = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
		$my_crc3 = strtoupper(md5("$mrh_login:$out_summ:$inv_id:$mrh_pass2"));
		$my_crc4 = strtoupper(md5("$mrh_login:$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

		if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'):  metka-4 '.$crc.'|'.$my_crc1.'|'.$my_crc2.'|'.$my_crc3.'|'.$my_crc4);

		if( $my_crc1 == $crc || 
			$my_crc2 == $crc || 
			$my_crc3 == $crc || 
			$my_crc4 == $crc
		)
		{
			if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): metka-5');

			$this->load->model('checkout/order');

			if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): metka-6');

			if( $this->config->get('robokassa_confirm_status')=='before' )
			{
				$order_info = $this->model_checkout_order->getOrder($this->request->post["InvId"]);
				$comment = '';

				if( !empty($order_info['language_id']) )
				{
					$this->load->model('localisation/language');
					$lang = $this->model_localisation_language->getLanguage($order_info['language_id']);

					if( !empty($lang['code']) && $this->config->get('robokassa_confirm_comment') )
					{
						$comment_arr = unserialize($this->config->get('robokassa_confirm_comment'));

						if( !empty($comment_arr[$lang['code']]) )
						{
							$comment = $comment_arr[$lang['code']];
						}
					}
				}

				$this->model_checkout_order->update( $inv_id,$this->config->get('robokassa_order_status_id'), $comment, $this->config->get('robokassa_confirm_notify') );
			}
			else
			{
				$this->model_checkout_order->confirm($inv_id, $this->config->get('robokassa_order_status_id'));
			}

			if( $IS_DEBUG )
				$log->write('RESULT('.$this->request->post["InvId"].'): metka-7');

			echo "OK$inv_id\n";
		}

		if( $IS_DEBUG )
			$log->write('RESULT('.$this->request->post["InvId"].'): metka-end');		
	}

	public function fail() 
	{
		$this->language->load('payment/robokassa');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array(); 

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => false
		); 

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/cart'),
			'text'      => $this->language->get('text_basket'),
			'separator' => $this->language->get('text_separator')
		);

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
					
		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/robokassa/fail'),
			'text'      => $this->language->get('text_fail'),
			'separator' => $this->language->get('text_separator')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_message'] = $this->language->get('text_message');

		$this->load->model('payment/robokassa');

		$data['text_message'] = str_replace("%1", $this->url->link('checkout/checkout', '', 'SSL'), $data['text_message']);

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		/*		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'			
		);*/


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data);
		} else {
			return $this->load->view('default/template/common/success.tpl.tpl', $data);
		}
	}
}
?>