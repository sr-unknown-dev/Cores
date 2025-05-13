<?php

namespace hcf\module\enchantment\defaults;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\Server;
use hcf\utils\Utils;
use hcf\module\enchantment\Enchantment;
use hcf\module\enchantment\CustomMeleeEnchantment;

class ZeusEnchantment extends Enchantment implements CustomMeleeEnchantment {
    
    /**
     * ZeusEnchantment construct.
     */
    public function __construct()
    {
        parent::__construct('Zeus', Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 2);
    }
    
	public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel, float $finalDamage): void {
		if(!$attacker instanceof Human) return;
        if (!$victim instanceof Human) return;
		if(lcg_value() > (0.25 * $enchantmentLevel)) return;
        $player = $victim;
        Utils::playLight($player->getPosition());
        $player->setHealth($player->getHealth() - 0.8);
	}
}