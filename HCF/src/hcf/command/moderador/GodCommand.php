<?php

declare(strict_types = 1);

namespace hcf\command\moderador;

use hcf\Loader;
use hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\GameMode;
use hcf\StaffMode\Good;
/**
* Class GodCommand
* @package hcf\command
*/
class GodCommand extends Command
{

  /**
  * GodCommand construct.
  */
  public function __construct() {
    parent::__construct('god', 'Use command for god');
    $this->setPermission('god.command');
  }

  /**
  * @param CommandSender $sender
  * @param string $commandLabel
  * @param array $args
  */
  public function execute(CommandSender $sender, string $commandLabel, array $args): void
  {
    if (!$sender instanceof Player)
      return;

    if (!$this->testPermission($sender))
      return;

      Loader::getInstance()->getStaffModeManager()->toggleGod($sender);
  }
}