<?php



/**

 * ██╗░░██╗░█████╗░██╗░░██╗██╗░░░██╗░██████╗██╗░░██╗██╗██████╗░░█████╗░

 * ██║░██╔╝██╔══██╗██║░██╔╝██║░░░██║██╔════╝██║░░██║██║██╔══██╗██╔══██╗

 * █████═╝░██║░░██║█████═╝░██║░░░██║╚█████╗░███████║██║██████╦╝██║░░██║

 * ██╔═██╗░██║░░██║██╔═██╗░██║░░░██║░╚═══██╗██╔══██║██║██╔══██╗██║░░██║

 * ██║░╚██╗╚█████╔╝██║░╚██╗╚██████╔╝██████╔╝██║░░██║██║██████╦╝╚█████╔╝

 * ╚═╝░░╚═╝░╚════╝░╚═╝░░╚═╝░╚═════╝░╚═════╝░╚═╝░░╚═╝╚═╝╚═════╝░░╚════╝░

 * 

 * Copyright (c) 2024 Kokushibo

 * 

 * This program is free software: you can redistribute it and/or modify

 * it under the terms of the GNU General Public License as published by

 * the Free Software Foundation, either version 3 of the License, or

 * (at your option) any later version.

 *

 * This program is distributed in the hope that it will be useful,

 * but WITHOUT ANY WARRANTY; without even the implied warranty of

 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the

 * GNU General Public License for more details.

 *

 * You should have received a copy of the GNU General Public License

 * along with this program. If not, see <http://www.gnu.org/licenses/>.

 *

 * Discord: 01_kokushibo_01

 */



namespace hcf\handler\kit;


use hcf\handler\kit\Kit;
use pocketmine\item\Item;
use pocketmine\item\Minecart;
use pocketmine\item\VanillaItems;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;



class KitsPortable {



    public static function createPortable(Kit $kit): Item {



        $cart = VanillaItems::MINECART();

        $cart->setCustomName(TextFormat::colorize($kit->getNameFormat() . " §r§7Kit"));



        $cart->setLore([

            TextFormat::colorize("&aClick to receive kit")

        ]);



        $nbt = $cart->getNamedTag();

        $nbt->setString("PortableKit", $kit->getName());



        return $cart;



    }

    public static function isPortable(Item $item): bool {

        if(!($item instanceof Minecart)) return false;

        $nbt = $item->getNamedTag();

        return $nbt->getTag("PortableKit") !== null;

    }

    public static function getPortable(Item $item): ?string {

        if(!self::isPortable($item)) return null;

        return $item->getNamedTag()->getString("PortableKit");

    }

    public static function givePortable(Player $player, Kit $kit): void {

        $portable = self::createPortable($kit);

        if($player->getInventory()->canAddItem($portable)) {

            $player->getInventory()->addItem($portable);

        }else{

            $player->getWorld()->dropItem($player->getPosition()->asVector3(), $portable);

        }

    }

}