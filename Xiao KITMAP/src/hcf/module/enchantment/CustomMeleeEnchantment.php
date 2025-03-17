<?php

namespace hcf\module\enchantment;

use pocketmine\entity\Entity;

interface CustomMeleeEnchantment {
	public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel, float $finalDamage): void;
}