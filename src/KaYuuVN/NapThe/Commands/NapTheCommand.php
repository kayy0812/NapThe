<?php
namespace KaYuuVN\NapThe\Commands;

use pocketmine\command\{
    Command,
    PluginCommand,
    CommandSender
};
use pocketmine\Player;
use KaYuuVN\NapThe\{
	NapThe,
	Form
};

/**
 * Class NapTheCommand
 * @package KaYuuVN\NapThe\Commands
 */
Class NapTheCommand extends PluginCommand
{
    
    /**
    * NapTheCommand constructor.
    * @param NapThe $plugin
    */
    public function __construct(NapThe $plugin) {
        parent::__construct('napthe', $plugin);
        $this->setAliases(['donate']);
        $this->setDescription('');
        $this->plugin = $plugin;
    }
    
   /**
    * @param CommandSender $sender
    * @param string $commandLabel
    * @param array $args
    *
    * @return bool
    */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if($sender instanceof Player) {
            $form = new Form($this->plugin, $sender->getPlayer());
            $form->sendMenu();
            return true;
        }   
    }
}
