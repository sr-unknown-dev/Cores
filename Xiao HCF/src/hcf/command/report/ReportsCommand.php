<?php

namespace hcf\command\report;

use CortexPE\Commando\BaseCommand;
use hcf\Loader;
use hcf\player\Player;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class ReportsCommand extends BaseCommand
{
    private Config $reports;

    public function __construct(string $name, string $description = "")
    {
        parent::__construct(Loader::getInstance(), $name, $description);
        $this->reports = new Config(Loader::getInstance()->getDataFolder() . "reports.yml", Config::YAML);
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Este comando solo puede ser usado por jugadores.");
            return;
        }
        $this->showReportsMenu($sender);
    }

    private function showReportsMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

        foreach ($this->reports->getAll() as $reportId => $report) {
            $reportItem = VanillaBlocks::MOB_HEAD()->asItem();
            $reportItem->setCustomName(TextFormat::colorize("&r{$report['target']}"));
            $reportItem->setLore([
                TextFormat::colorize("&7Razón: &g{$report['reason']}"),
                TextFormat::colorize("&7Reportado por: &g{$report['player']}")
            ]);
            $reportItem->getNamedTag()->setString("report_id", $reportId);
            $menu->getInventory()->addItem($reportItem);
        }

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $reportId = $transaction->getItemClicked()->getNamedTag()->getString("report_id");

            if ($reportId !== "") {
                $report = $this->reports->get($reportId);
                $targetPlayer = $player->getServer()->getPlayerExact($report['target']);

                if ($targetPlayer === null) {
                    $player->sendMessage(TextFormat::RED . "El jugador reportado no está en línea.");
                } else {
                    $player->teleport($targetPlayer->getPosition());
                    $player->sendMessage(TextFormat::GREEN . "Te has teletransportado a " . $targetPlayer->getName());
                }
            }

            return $transaction->discard();
        });
        $menu->send($player);
    }
}