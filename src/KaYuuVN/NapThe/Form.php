<?php

namespace KaYuuVN\NapThe;

use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use KaYuuVN\NapThe\Servers\NapTheNgay;
use jojoe77777\FormAPI\{
    CustomForm, 
    SimpleForm, 
    ModalForm
};

Class Form {
    private $plugin;
    private $content;

	/**
     * Form constructor.
     * @param NapThe $plugin
     */

    public function __construct(NapThe $plugin, Player $player) {
        $this->plugin = $plugin;
        $this->player = $player;
    }

    public function sendMenu() {
        $form = new SimpleForm(function(Player $player, $data) {
            if ($data === null) {
                return true;
            }

            if ($data == 0) {
                return $this->NapThe();
            }
        });
        $form->setTitle($this->plugin->config->get('title'));
        $form->setContent($this->Contents());
        $form->addButton("Submit");
        $this->player->sendForm($form);

    }

    public function Contents() {
        $lists = array();
        foreach ($this->plugin->config->get('contents') as $line) {
            $this->content .= $line."\n";
        }
        foreach ($this->plugin->dropdown_list_name as $id => $val) {
            $lists[$id] = $this->plugin->Status($id);  
        }
        $lists['PLAYER'] = $this->player->getName();
        var_dump($lists);
        return $this->plugin->Replace($this->content, $lists);
    }

    public function NapThe($message = "") {
        $form = new CustomForm(function(Player $player, $data) {
            if ($data === null) {
                return true;
            }
            $server = new NapTheNgay($this->plugin, $player, $data[1] + 1, $data[2], $data[3], $this->plugin->dropdown_list_amount[$data[4]]);
            return $server->send();
        });
        $form->setTitle($this->player->getName());
        $form->addLabel($message);
        $form->addDropdown($this->plugin->config->get('type_content'), array_values($this->plugin->dropdown_list_name));
        $form->addInput($this->plugin->config->get('seri_content'));
        $form->addInput($this->plugin->config->get('pin_content'));
        $form->addDropdown($this->plugin->config->get('amount_content'), $this->plugin->dropdown_list_amount);
        $this->player->sendForm($form);
    }
}
