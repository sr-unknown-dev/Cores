<?php

declare(strict_types=1);

namespace hcf\koth;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\Loader;
use hcf\player\Player;
use hcf\utils\Utils;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use itoozh\crates\Main;

/**
 * Class Koth
 * @package hcf\koth
 */
class Koth
{
    
    /** @var Player|null */
    private ?Player $capturer = null;
    
    /** @var string */
    private string $name;
    /** @var int */
    private int $time, $progress;
    /** @var int */
    private int $points;
    /** @var int */
    private int $keyCount;
    
    /** @var string|null */
    private ?string $coords;
    
    /** @var KothCapzone|null */
    private ?KothCapzone $capzone = null;
    
    /**
     * Koth construct.
     * @param string $name
     * @param int $time
     * @param int $points
     * @param string $key
     * @param int $keyCount
     * @param string|null $coords
     * @param array|null $claim
     * @param array|null $capzone
     */
    public function __construct(string $name, int $time, int $points, ?string $coords, ?array $claim, ?array $capzone)
    {
        $this->name = $name;
        $this->time = $time;
        $this->points = $points;
        $this->progress = $time;
        $this->coords = $coords;
        
        if ($claim !== null)
            Loader::getInstance()->getClaimManager()->createClaim($name, 'koth', (int) $claim['minX'], (int) $claim['maxX'], (int) $claim['minZ'], (int) $claim['maxZ'], $claim['world']);
        
        if ($capzone !== null)
            $this->capzone = new KothCapzone((int) $capzone['minX'], (int) $capzone['maxX'], (int) $capzone['minY'], (int) $capzone['maxY'], (int) $capzone['minZ'], (int) $capzone['maxZ'], $capzone['world']);
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
    
    /**
     * @return int
     */
    public function getProgress(): int
    {
        return $this->progress;
    }
    
    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }
    
    /**
     * @return string|null
     */
    public function getCoords(): ?string
    {
        return $this->coords;
    }
    
    /**
     * @return KothCapzone|null
     */
    public function getCapzone(): ?KothCapzone
    {
        return $this->capzone;
    }
    
    /**
     * @param int $time
     */
    public function setTime(int $time): void
    {
        $this->time = $time;
    }
    
    /**
     * @param int $time
     */
    public function setProgress(int $time): void
    {
        $this->progress = $time;
    }
    
    /**
     * @param int $points
     */
    public function setPoints(int $points): void
    {
        $this->points = $points;
    }
    
    /**
     * @param string|null $coords
     */
    public function setCoords(?string $coords): void
    {
        $this->coords = $coords;
    }
    
    /**
     * @param KothCapzone $capzone
     */
    public function setCapzone(KothCapzone $capzone): void
    {
        $this->capzone = $capzone;
    }
    
    public function update(): void
    {
        if ($this->capturer === null) {
            $world = Loader::getInstance()->getServer()->getWorldManager()->getWorldByName($this->getCapzone()->getWorld());
            
            if ($world !== null) {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    if ($player instanceof Player) {
                        if ($this->getCapzone()->inside($player->getPosition()) && $player->getSession()->getFaction() !== null && ($player->getSession()->getCooldown('pvp.timer')  === null && $player->getSession()->getCooldown('starting.timer') === null)) {
                            $this->capturer = $player;
                            
                            if ($this->getName() !== "Citadel") {
                                Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize('&7[&4KoTh&7] &r&g' . $player->getName() . ' &fis capturing &e' . $this->getName()));
                                Utils::PlaySound($player, "note.pling", 1, 1);
                            } else {
                                Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize('&r&6[Citadel] &r&e' . $player->getName() . ' &fis capturing &3 ' . $this->getName()));
                                Utils::PlaySound($player, "note.pling", 1, 1);
                            }
                            break;
                        }
                    }
                }
            }
        } else {
            if (!$this->capturer->isOnline() || !$this->getCapzone()->inside($this->capturer->getPosition())) {
                $this->progress = $this->time;
                $this->capturer = null;
                return;
            }
            
            if ($this->getProgress() === 0) {

                Loader::getInstance()->getFactionManager()->getFaction($this->capturer->getSession()->getFaction())->setPoints(Loader::getInstance()->getFactionManager()->getFaction($this->capturer->getSession()->getFaction())->getPoints() + $this->getPoints());
                Loader::getInstance()->getFactionManager()->getFaction($this->capturer->getSession()->getFaction())->setKothCaptures(Loader::getInstance()->getFactionManager()->getFaction($this->capturer->getSession()->getFaction())->getKothCaptures() + 1);

                if ($this->getName() !== "Citadel") {
                    $webHook = new Webhook(Loader::getInstance()->getConfig()->get('koth.webhook'));

                    $msg = new Message();

                    $totalpoints = Loader::getInstance()->getFactionManager()->getFaction($this->capturer->getSession()->getFaction())->getPoints();

                    $embed = new Embed();
                    $embed->setTitle("KotH " . "{$this->getName()}" . " ha terminado 🏔️");
                    $embed->setColor(0xD87200);
                    $embed->addField("Fue capturado por 👤", "{$this->capturer->getName()}");
                    $factionName = Loader::getInstance()->getFactionManager()->getOriginalFactionName($this->capturer->getSession()->getFaction());
                    $embed->addField("Facción 👥", "{$factionName}", true);
                    $embed->addField("Puntos Totales 🍎", "{$totalpoints}", true);
                    $embed->setFooter("");
                    $msg->addEmbed($embed);

                    $webHook->send($msg);
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&8█&7███████&8█"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7███&4█&7██"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7██&4█&7███       &r&6".$this->getName()." &r&7has been capeed by:"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7█&4█&7████       &r&a× ".$this->capturer->getName()));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4██&7█████"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7█&4█&7████"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7██&4█&7███"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&4█&7███&4█&7██"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&8█&7███████&8█"));
                    Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Koth")->giveKey($this->capturer, 5);
                    Utils::kothcontroller();
                    

                } else {
                    $webHook = new Webhook(Loader::getInstance()->getConfig()->get('koth.webhook'));

                    $msg = new Message();

                    $totalpoints = Loader::getInstance()->getFactionManager()->getFaction($this->capturer->getSession()->getFaction())->getPoints();

                    $embed = new Embed();
                    $embed->setTitle("Citadel has finished 🌌");
                    $embed->setColor(0x4F0075);
                    $embed->addField("Was captured by 👤", "{$this->capturer->getName()}");
                    $embed->addField("Faction 👥", "{$this->capturer->getSession()->getFaction()}", true);
                    $embed->addField("Total Points 🍎", "{$totalpoints}", true);
                    $embed->setFooter("");
                    $msg->addEmbed($embed);

                    $webHook->send($msg);
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6[Citadel]"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&econtrolled by"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6[&e" . $this->capturer->getSession()->getFaction() . "&6] " . $this->capturer->getName() . "&e!"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
                    Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
                    Loader::getInstance()->getHandlerManager()->getCrateManager()->getCrate("Citadel")->giveKey($this->capturer, 5);
                    Utils::kothcontroller();

                }
                $this->progress = $this->time;
                $this->capturer = null;
                Loader::getInstance()->getKothManager()->setKothActive(null);
                return;
            }
            $this->progress--;
        }
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'time' => $this->getTime(),
            'points' => $this->getPoints(),
            'coords' => $this->getCoords(),
            'claim' => null,
            'capzone' => null
        ];
        
        if (($claim = Loader::getInstance()->getClaimManager()->getClaim($this->getName())) !== null)
            $data['claim'] = [
                'minX' => $claim->getMinX(),
                'maxX' => $claim->getMaxX(),
                'minZ' => $claim->getMinZ(),
                'maxZ' => $claim->getMaxZ(),
                'world' => $claim->getWorld()
            ];
        
        if ($this->getCapzone() !== null)
            $data['capzone'] = [
                'minX' => $this->getCapzone()->getMinX(),
                'maxX' => $this->getCapzone()->getMaxX(),
                'minY' => $this->getCapzone()->getMinY(),
                'maxY' => $this->getCapzone()->getMaxY(),
                'minZ' => $this->getCapzone()->getMinZ(),
                'maxZ' => $this->getCapzone()->getMaxZ(),
                'world' => $this->getCapzone()->getWorld()
            ];
        return $data;
    }
}