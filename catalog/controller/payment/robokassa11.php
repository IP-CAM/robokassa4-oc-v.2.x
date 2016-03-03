<?php
class ControllerPaymentRobokassa11 extends Controller {

	private $INDEX = 11;

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
			$data['action'] = "https://auth.robokassa.ru/Merchant/Index.aspx?isTest=1";
		}
		else
		{
			$data['action'] = "https://auth.robokassa.ru/Merchant/Index.aspx";
		}
		
		$mrh_pass1 = $this->config->get('robokassa_password1');
		$data['mrh_login'] = $this->config->get('robokassa_shop_login');

		$mrh_login = $data['mrh_login'];

		$out_summ = round($order_info['total'],2);

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

		$data['robokassa_confirm_status'] = $this->config->get('robokassa_confirm_status');

		$in_curr = $robokassa_currencies[$this->INDEX];

		$inv_id = $this->session->data['order_id'];
		$data['out_summ'] = $out_summ;
		$data['inv_id'] =  $this->session->data['order_id'];
		$data['inv_desc'] = $order_info['store_name'];

		$data['crc'] = hash($this->config->get('robokassa_hash'), "$mrh_login:$out_summ:$inv_id:$mrh_pass1");


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
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/robokassa.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/robokassa.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/robokassa.tpl', $data);
		}
	}
}
?>