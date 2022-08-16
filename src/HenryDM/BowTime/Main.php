<?php 

namespace HenryDM\BowTime;

# Entity Events
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\ProjectileLaunchEvent;

# Login and Quit Event
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

# Other Events
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use function str_replace;

class Main extends PluginBase implements Listener  {

    /*** @var array */
    private array $cooldown = [];

    /*** @var array */
    private array $settings = [];

    public function onEnable() : void {
		
        $this->settings = $this->getConfig()->getAll();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
	    $this->saveDefaultConfig();
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event) : void {
        $this->cooldown[$event->getPlayer()->getName()] = "0.00";
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event) : void{
        unset($this->cooldown[$event->getPlayer()->getName()]);
    }
	
    /**
     * @param ProjectileLaunchEvent $event
     */
    public function onLaunch(ProjectileLaunchEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof Arrow) {
            $player = $entity->getOwningEntity();
            if ($player instanceof Player) {
                $time = microtime(true) - (float)$this->cooldown[$player->getName()];
                $settings = $this->settings;
                if ($time < (float)$settings["cooldown"]) {
                    $settings["pop-up"] ? $player->sendPopup(str_replace("{TIME}", $time, $settings["message"])) : $player->sendMessage(str_replace("{TIME}", $time, $settings["message"]));
                    $event->cancel();
                    return;
                }
                $this->cooldown[$player->getName()] = (string)microtime(true);
            }
        }
    }
}
