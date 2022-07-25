<?php namespace Taco\BC;

use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use function str_replace;

class Main extends PluginBase implements Listener  {

    /*** @var array */
    private array $cd = [];

    /*** @var array */
    private array $settings = [];

    public function onEnable() : void {
        $this->saveConfig();
        $this->settings = $this->getConfig()->getAll();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event) : void {
        $this->cd[$event->getPlayer()->getName()] = "0.00";
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event) : void {
        unset($this->cd[$event->getPlayer()->getName()]);
    }

    /**
     * @param ProjectileLaunchEvent $event
     */
    public function onLaunch(ProjectileLaunchEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof Arrow) {
            $player = $entity->getOwningEntity();
            if ($player instanceof Player) {
                $time = microtime(true) - (float)$this->cd[$player->getName()];
                $settings = $this->settings;
                if ($time < (float)$settings["cd"]) {
                    $settings["popup"] ? $player->sendPopup(str_replace("{time}", $time, $settings["message"])) : $player->sendMessage(str_replace("{time}", $time, $settings["message"]));
                    $event->setCancelled(true);
                    return;
                }
                $this->cd[$player->getName()] = (string)microtime(true);
            }
        }
    }

}