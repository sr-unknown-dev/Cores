<?php



namespace hcf\abilities\items;

use hcf\abilities\entity\FrezzerGunEntity;
use hcf\abilities\entity\SwitcherEntity;
use hcf\Loader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class PortableRogue implements Listener
{
    private array $focus = [];
    public $hits = [];

    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $victim = $event->getEntity();
        $item = $damager->getInventory()->getItemInHand();
        if ($damager instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "PortableRogue") {
                    $event->cancel();
                    if ($damager->getSession()->getCooldown('ability.PortableRogue') === null) {
                        if ($damager->getSession()->getCooldown('ability.global') === null) {
                            if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($damager->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                                $damager->sendMessage("§6You have activated §7PortableRogue");
                                $victim->setHealth($victim->getHealth() - 8);
                                $damager->getInventory()->setItemInHand(VanillaItems::AIR());
                                $damager->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 3, 0));
                                $damager->sendMessage(TextFormat::colorize("&cCooldown active of &7PortableRogue &r&cby 3 minute"));
                                $damager->getSession()->addCooldown('ability.PortableRogue', ' §l§7×§r §7PortableRogue§r§f: §f', 180);
                                $damager->getSession()->addCooldown('ability.global', ' §l§7×§r &5Partner Item§r§f: §f', 5);
                                $damager->sendMessage("§cHas activated §7PortableRogue§r§c");
                                $damagerName = $damager->getName();

                        } else {
                            $damager->sendMessage("§c§6you have cooldown of §dPartner Item §f" . Timer::format($damager->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $damager->sendMessage("§cNo puedes usar el §7PortableRogue§c por " . Timer::format($damager->getSession()->getCooldown("ability.PortableRogue")->getTime()));
                    }
                }
            }
        }
}