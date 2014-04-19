<?php

/*
 * $Id$
 * minestat.php - A Minecraft server status checker
 * Copyright (C) 2014 Lloyd Dilley
 * http://www.devux.org/projects/minestat/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

class MineStat
{
  private $address
  private $port
  private $online;          // online or offline?
  private $version;         // Minecraft server version
  private $motd;            // message of the day
  private $current_players; // current number of players online
  private $max_players;     // maximum player capacity

  public function __construct($address, $port)
  {
    $this->address = $address;
    $this->port = $port;

    try
    {
      $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      if($socket === false)
      {
        $this->online = false;
        return;
      }
      $result = socket_connect($socket, $address, $port);
      if($result === false)
      {
        $this->online = false;
        return;
      }
      $payload = "\xFE\x01";
      socket_write($socket, $payload, strlen($payload));
      $raw_data = socket_read($socket, 512);
      socket_close($socket);
    }
    catch(Exeption $e)
    {
      $this->online = false;
      return;
    }

    $this->online = true;
    $server_info = explode("\x00\x00\x00", $raw_data);
    $this->version = $server_info[2];
    $this->motd = $server_info[3];
    $this->current_players = $server_info[4];
    $this->max_players = $server_info[5];
  }

  public function get_address()
  {
    return $this->address;
  }

  public function get_port()
  {
    return $this->port;
  }

  public function is_online()
  {
    return $this->online;
  }

  public function get_version()
  {
    return $this->version;
  }

  public function get_motd()
  {
    return $this->motd;
  }

  public function get_current_players()
  {
    return $this->current_players;
  }

  public function get_max_players()
  {
    return $this->max_players;
  }
}

?>
