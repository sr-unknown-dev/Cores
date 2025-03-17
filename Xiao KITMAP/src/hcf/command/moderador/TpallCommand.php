<?php

namespace hcf\command\moderador;

use hcf\Loader;
use hcf\player\Player;

use pocketmine\utils\TextFormat as TE;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\world\sound\PopSound;

class TpallCommand extends Command {

	/**
	 * TpaCommand Constructor.
	 */
	public function __construct(){
		parent::__construct("tpa", TE::YELLOW."Teleport all players", "tpa");
		$this->setPermission('moderador.command');
	}

	/**
	 * @param CommandSender $sender
	 * @param String $label
	 * @param Array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(!$sender->hasPermission('moderador.command')){
			$sender->sendMessage("".TE::RED."You do not have permission to use this command");
			return;
		}
		foreach(Loader::getInstance()->getServer()->getOnlinePlayers() as $players){
			$location = $players->getLocation();
			$location->getWorld()->addSound($location, new PopSound());
			$players->teleport($sender->getLocation());
		}
	}
}

?>
