<?php

namespace KaYuuVN\NapThe\Servers;

use pocketmine\Player;
use KaYuuVN\NapThe\{
	NapThe,
	Form
};

class NapTheNgay {

    private $type;
    private $seri;
    private $pin;
    private $amount;

    private $api_url;
    private $merchant_id;
    private $email;
    private $secure_code;
    private $trans_id;

    public function __construct(NapThe $plugin, Player $player, int $type, $seri, $pin, $amount) {
        $this->plugin = $plugin;
        $this->player = $player;

        $this->type = $type;
        $this->seri = $seri;
        $this->pin = $pin;
        $this->amount = $amount;

        //Du lieu lay tu config setting.yml
        $this->api_url = $this->plugin->config->get('api_url');
        $this->merchant_id = $this->plugin->config->get('merchant_id');
        $this->mail = $this->plugin->config->get('mail');
        $this->secure_code = $this->plugin->config->get('secure_code');
        $this->trans_id = time();

    }

    public function send() {
    	$arrayPost = array(
	        'merchant_id' => intval($this->merchant_id),
	        'api_email' => trim($this->mail),
	        'trans_id' => trim($this->trans_id),
	        'card_id' => trim($this->type),
	        'card_value' => intval($this->amount),
	        'pin_field' => trim($this->pin),
	        'seri_field' => trim($this->seri),
	        'algo_mode' => 'hmac'
        );
        $data_sign = hash_hmac('SHA1', implode('',$arrayPost), $this->secure_code);
        $arrayPost['data_sign'] = $data_sign;
        $curl = curl_init($this->api_url);

        curl_setopt_array($curl, array(
	        CURLOPT_POST=>true,
	        CURLOPT_HEADER=>false,
	        CURLINFO_HEADER_OUT=>true,
			CURLOPT_TIMEOUT=>120,
			CURLOPT_RETURNTRANSFER=>true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POSTFIELDS=>http_build_query($arrayPost)
		));
		$data = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$result = json_decode($data, true);

		$form = new Form($this->plugin, $this->player);
    	if ($status == 200) {
    		$amount = $result['amount'];
    		foreach ($this->plugin->dropdown_list_name as $id => $val) {
    			if ($this->type == $id) {
    				$type = $val;
    				break;
    			}
    		}
			if ($result['code'] == 100) {
				$form->NapThe($this->plugin->Replace($this->plugin->config->get('accept_callback'), [
					"TYPE" => $type,
					"AMOUNT" => $amount
				]));
				$multip = ($this->plugin->config->get('multip') == 0) ? 1 : $this->plugin->config->get('multip');
				$this->plugin->token->addMoney($this->player, $this->plugin->config->get('amount_callback')[$amount] * $multip);
	    	} else {
				$form->NapThe($this->plugin->Replace($this->plugin->config->get('deny_callback'), [
					"REASON" => $result['msg'],
					"CODE" => $result['code']
				]));
			}
		} else { 
			$form->NapThe($this->plugin->Replace($this->plugin->config->get('error_host_callback'), [
					"CODE" => $status,
				])); 
		}
    }
}
