<?php

namespace KaYuuVN\NapThe;

use pocketmine\plugin\PluginBase;
use pocketmine\{
	Server,
	Player
};
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use KaYuuVN\NapThe\Commands\NapTheCommand;

class NapThe extends PluginBase
{
	public $config = [];
	public $dropdown_list_type = [
    	"Viettel",
    	"Mobiphone",
    	"Vinaphone",
    	"Zing",
    	"Gate"
    ];
    public $dropdown_list_amount = [
    	"10000",
    	"20000",
    	"50000",
    	"100000",
    	"200000",
    	"500000",
    	"1000000"
    ];

	public function onEnable(): void 
	{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register("napthe", new NapTheCommand($this));
		$files = array("setting.yml");
		foreach ($files as $file) {
			if (!file_exists($this->getDataFolder().$file)) {
				@mkdir($this->getDataFolder());
				file_put_contents($this->getDataFolder().$file, $this->getResource($file));
			}
		}
		$this->config = new Config($this->getDataFolder(). "setting.yml", Config::YAML);
		$this->token = $this->getServer()->getPluginManager()->getPlugin('PointAPI');
	}

	public function Replace(string $subject, $replace = []): string
	{
		foreach ($replace as $var => $code) {
			$subject = str_replace("{".$var."}", $code, $subject);
		}
		return $subject;
	}

	public function CallStatus($id)
	{
		$json = file_get_contents('http://api.napthengay.com/Status.php');
		$decode = json_decode($json, true);
		foreach ($decode as $data) {
			if ($data['id'] == $id) {
				return ($data['status'] == 1) ? "Hoạt Động" : "Bảo Trì";
			}
		}
	}
}
		
