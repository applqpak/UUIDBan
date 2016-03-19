<?php

  namespace ClientBan;

  use pocketmine\plugin\PluginBase;
  use pocketmine\event\Listener;
  use pocketmine\event\player\PlayerPreLoginEvent;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  use pocketmine\utils\Config;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\Player;

  class Main extends PluginBase implements Listener
  {

    public function dataPath()
    {

      return $this->getDataFolder();

    }

    public function onEnable()
    {

      $this->getServer()->getPluginManager()->registerEvents($this, $this);

      @mkdir($this->dataPath());

      $this->cfg = new Config($this->dataPath() . "banned-users.txt", Config::ENUM, array("player_uuids" => array()));

    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
    {

      if(strtolower($cmd->getName()) === "clientban")
      {

        if(!(isset($args[0])))
        {

          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /clientban <player> < reason >");

          return true;

        }
        else
        {

          $name = $args[0];

          $player = $this->getServer()->getPlayer($name);

          if($player === null)
          {

            $sender->sendMessage(TF::RED . "Player " . $name . " could not be found.");

            return true;

          }
          else
          {

            $player_name = $player->getName();

            $banned_uuids = $this->cfg->get("player_uuids");

            $player_uuid = $player->getClientSecret();

            if(in_array($player_uuid, $banned_uuids))
            {

              $sender->sendMessage(TF::RED . "Player " . $player_name . " is already banned.");

              return true;

            }
            else
            {

              array_push($banned_uuids, $player_uuid);

              $this->cfg->set("player_uuids", $banned_uuids);

              $this->cfg->save();

              $sender->sendMessage(TF::GREEN . "Successfully banned " . $player_uuid . " belonging to " . $player_name . ".");

              return true;

            }

          }

        }

      }

    }

  }

?>
