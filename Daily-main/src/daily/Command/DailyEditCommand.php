<?php

namespace daily\Command;

use daily\Main;

use daily\Utils\Npc;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use daily\Utils\Inventorys;
use daily\Cooldowns\Cooldown;
use daily\Utils\Utils;

class DailyEditCommand extends Command {

    private $plugin;
    private $cooldown;

    public function __construct($plugin) {
        parent::__construct("dailyedit", "comando para editar los items de el daily");
        $this->setPermission("use.player.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args):void{
        if(!$sender instanceof Player)
            return;
        
            $playerName = $sender->getName();

            if (empty($args[0])) return;
            
            if ($sender instanceof Player) {
                $config = Utils::getItemsConfig();
                $contents = $sender->getInventory()->getContents();
                switch (strtolower($args[0])) {
                    case '1':
                        $content = [];
                        foreach ($contents as $item){
                            $content[] = Utils::serialize($item);
                        }
                        $config->set("1", $content);
                        $config->save();
                        break;
                    case '2':
                        $content = [];
                        foreach ($contents as $item){
                            $content[] = Utils::serialize($item);
                        }
                        $config->set("2", $content);
                        $config->save();
                        break;
                    case '3':
                        $content = [];
                        foreach ($contents as $item){
                            $content[] = Utils::serialize($item);
                        }
                        $config->set("3", $content);
                        $config->save();
                        break;
                    case '4':
                        $content = [];
                        foreach ($contents as $item){
                            $content[] = Utils::serialize($item);
                        }
                        $config->set("4", $content);
                        $config->save();
                        break;
                    case '5':
                        $content = [];
                        foreach ($contents as $item){
                            $content[] = Utils::serialize($item);
                        }
                        $config->set("5", $content);
                        $config->save();
                        break;
                    case 'npc':
                        $entity = Npc::create($sender);
                        $entity->spawnToAll();
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
    }
}