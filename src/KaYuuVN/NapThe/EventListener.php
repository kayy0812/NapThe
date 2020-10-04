<?php
namespace KaYuuVN\NapThe;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\{
	PlayerJoinEvent
};

/**
 * Class EventListener
 * @package KaYuuVN\NapThe
 */
Class EventListener implements Listener
{
    
    /** @var NapThe */
    private $plugin;
    
    /**
    * EventListener constructor.
    * @param NapThe $plugin
    */
    public function __construct(NapThe $plugin)
    {
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $event)
	{
	}
}
