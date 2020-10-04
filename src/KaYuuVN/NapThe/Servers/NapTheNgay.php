<?php

namespace KaYuuVN\NapThe\Servers;

use pocketmine\Player;
use KaYuuVN\NapThe\{
	NapThe,
	Form
};

Class NapTheNgay
{
	public function __construct(NapThe $plugin, Player $player, int $type, $seri, $pin, $amount)
    {
        $this->plugin = $plugin;
        $this->player = $player;

        $this->type = $type;
        $this->seri = $seri;
        $this->pin = $pin;
        $this->amount = $amount;

        //Du lieu lay tu config setting.yml
        $this->api_url = $this->plugin->config->get('api-url');
        $this->merchant_id = $this->plugin->config->get('merchant-id');
        $this->email = $this->plugin->config->get('email');
        $this->secure_code = $this->plugin->config->get('secure-code');
        $this->trans_id = time();

    }

    public function CallToHost()
    {
    	$arrayPost = array(
	        'merchant_id'=>intval($this->merchant_id),
	        'api_email'=>trim($this->email),
	        'trans_id'=>trim($this->trans_id),
	        'card_id'=>trim($this->type),
	        'card_value'=> intval($this->amount),
	        'pin_field'=>trim($this->pin),
	        'seri_field'=>trim($this->seri),
	        'algo_mode'=>'hmac'
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

		$this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->result = json_decode($data, true);
		$this->sendCallback();

		
    }

    public function sendCallback()
    {
    	$form = new Form($this->plugin, $this->player);
    	if ($this->status == 200) {
    		$amount = $this->result['amount'];
			switch ($this->type) {
				case 1: $type = "Viettel"; break;
				case 2: $type = "Mobiphone"; break;
				case 3: $type = "Vinaphone"; break;
				case 4: $type = "Zing"; break;
				case 5: $type = "Gate"; break;
				case 6: $type = "VCoin"; break;
			}
			if ($this->result['code'] == 100) {
				$form->NapThe($this->plugin->Replace($this->plugin->config->get('accept_callback'), [
					"TYPE" => $type,
					"AMOUNT" => $amount
				]));
				$this->plugin->token->addPoint($this->player, $this->plugin->config->get('amount_callback')[$amount] * $this->plugin->config->get('multip'));
	    	} else {
				$form->NapThe($this->plugin->Replace($this->plugin->config->get('deny_callback'), [
					"REASON" => $this->result['msg'],
					"CODE" => $this->result['code']
				]));
			}
		} else { 
			$form->NapThe($this->plugin->Replace($this->plugin->config->get('error_host_callback'), [
					"CODE" => $this->status,
				])); 
		}
    }
}