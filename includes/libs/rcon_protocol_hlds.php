<?php

// ************************************************************************
//PHPrcon - PHP script collection to remotely administrate and configure Halflife and HalflifeMod Servers through a webinterface
//Copyright (C) 2002  Henrik Beige
//
//This library is free software; you can redistribute it and/or
//modify it under the terms of the GNU Lesser General Public
//License as published by the Free Software Foundation; either
//version 2.1 of the License, or (at your option) any later version.
//
//This library is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//Lesser General Public License for more details.
//
//You should have received a copy of the GNU Lesser General Public
//License along with this library; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// ************************************************************************

class Rcon
{

  var $challenge_number;
  var $connected;
  var $server_ip;
  var $server_password;
  var $server_port;
  var $socket;


  //Constructor
  function Rcon()
  {
    $this->challenge_number = 0;
    $this->connected = true;
    $this->server_password = '';
    $this->server_password = 27015;
    $this->server_password = '';
  }


  //Open socket to gameserver
  function Connect($server_ip, $server_port, $server_password = '')
  {
    //store server data
    $this->server_ip = gethostbyname($server_ip);
    $this->server_port = $server_port;
    $this->server_password = $server_password;

    //open connection to gameserver
    $fp = fsockopen('udp://' . $this->server_ip, $this->server_port, $errno, $errstr, 5);
    if($fp)
      $this->connected = true;
    else
    {
      $this->connected = false;
      return false;
    }

    //store socket
    $this->socket = $fp;

    //return success
    return true;

  } //function Connect($server_ip, $server_port, $server_password = "")


  //Close socket to gameserver
  function Disconnect()
  {
    //close socket
    fclose($this->socket);
    $connected = false;

  } //function Disconnect()


  //Is there an open connection
  function IsConnected()
  {
    return $this->connected;
  } //function IsConnected()


  //Get detailed player info via rcon
  function ServerInfo()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //get server information
    $status = $this->RconCommand("status");

    //If there is no open connection return false
    //If there is bad rcon password return "Bad rcon_password."
    if(!$status || trim($status) == "Bad rcon_password.")
      return $status;

   //format global server info
    $line = explode("\n", $status);
    $map = substr($line[3], strpos($line[3], ":") + 1);
    $players = trim(substr($line[4], strpos($line[4], ":") + 1));
    $active = explode(" ", $players);

    $result["ip"] = trim(substr($line[2], strpos($line[2], ":") + 1));
    $result["name"] = trim(substr($line[0], strpos($line[0], ":") + 1));
    $result["map"] = trim(substr($map, 0, strpos($map, "at:")));
    $result["mod"] = "Counterstrike " . trim(substr($line[1], strpos($line[1], ":") + 1));
    $result["game"] = "Halflife";
    $result["activeplayers"] = $active[0];
    $result["maxplayers"] = substr($active[2], 1);

    //format player info
    for($i = 1; $i <= $result["activeplayers"]; $i++)
    {
      //get possible player line
      $tmp = $line[$i + 6];

      //break if no more players are left
      if(substr_count($tmp, "#") <= 0)
        break;

      //name
      $begin = strpos($tmp, "\"") + 1;
      $end = strrpos($tmp, "\"");
      $result[$i]["name"] = substr($tmp, $begin, $end - $begin);
      $tmp = trim(substr($tmp, $end + 1));

      //ID
      $end = strpos($tmp, " ");
      $result[$i]["id"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //WonID
      $end = strpos($tmp, " ");
      $result[$i]["wonid"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Frag
      $end = strpos($tmp, " ");
      $result[$i]["frag"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Time
      $end = strpos($tmp, " ");
      $result[$i]["time"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Ping
      $end = strpos($tmp, " ");
      $result[$i]["ping"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Loss
      $tmp = trim(substr($tmp, $end));

      //Adress
      $result[$i]["adress"] = $tmp;

    } //for($i = 1; $i < $result["activeplayers"]; $i++)

    //return formatted result
    return $result;

  } //function ServerInfo()


  //Get all maps in all directories
  function ServerMaps($pagenumber = 0)
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //Get list of maps
    $maps = $this->RconCommand("maps *", $pagenumber);

    //If there is no open connection return false
    //If there is bad rcon password return "Bad rcon_password."
    if(!$maps || trim($maps) == "Bad rcon_password.")
      return $maps;

    //Split Maplist in rows
    $line = explode("\n", $maps);
    $count = sizeof($line) - 4;

    //format maps
    for($i = 0; $i <= $count; $i++)
    {
      $text = $line[$i];

      //at directory output sorted map list
      if(strstr($text, "Dir:"))
      {
        //reset counter
        $mapcount = 0;

        //parse directory name
        $directory = strstr($text, " ");

      } //if(strstr($text, "Dir:"))

      else if(strstr($text, "(fs)"))
      {
        //parse mappath
        $mappath = strstr($text, " ");

        //parse mapname
        //if no "/" is included in the "maps * " result
        if(!($tmpmap = strrchr($mappath, "/")))
          $tmpmap = $mappath;

        //parse mapname without suffix (.bsp)
        $result[$directory][$i] = substr($tmpmap, 1, strpos($tmpmap, ".") - 1);

      } //else if(strstr($text, "(fs)"))

    } //for($i = 1; $i <= $count; $i++)


    //return formatted result
    return $result;

  } //function ServerMaps()


  //Get server info via info protocoll
  function Info()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //send info command
    $command = "\xff\xff\xff\xffinfo\x00";
    $buffer = $this->Communicate($command);

    //If no connection is open
    if(trim($buffer) == "")
    {
      $this->connected = false;
      return false;
    }

    //build info array
    $buffer = explode("\x00", $buffer);

    $result["ip"] = substr($buffer[0], 5);
    $result["name"] = $buffer[1];
    $result["map"] = $buffer[2];
    $result["mod"] = $buffer[3];
    $result["game"] = $buffer[4];
    $result["activeplayers"] = (strlen($buffer[5]) > 1)?ord($buffer[5][0]):"0";
    $result["maxplayers"] = (strlen($buffer[5]) > 1)?ord($buffer[5][1]):"0";

    //return formatted result
    return $result;

  } //function Info()


  //Get players via info protocoll
  function Players()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //send players command
    $command = "\xff\xff\xff\xffplayers\x00";
    $buffer = $this->Communicate($command);

    //If no connection is open
    if(trim($buffer) == "")
    {
      $this->connected = false;
      return false;
    }

    //get number of online players
    $buffer = substr($buffer, 1);

    //build players array
    for($i = 1; strlen($buffer) > 0; $i++)
    {
      //playername
      $tmp = strpos($buffer, "\x00");
      $result[$i]["name"] = substr($buffer, 1, $tmp);

      //frag count
      $result[$i]["frag"] = ord($buffer[$tmp + 1]) +
                           (ord($buffer[$tmp + 2]) << 8) +
                           (ord($buffer[$tmp + 3]) << 16) +
                           (ord($buffer[$tmp + 4]) << 24);

      //online time
      $tmptime = @unpack('ftime', substr($buffer, $tmp + 5, 4));
      $result[$i]["time"] = date('i:s', round($tmptime['time'], 0) + 82800);

      $buffer = substr($buffer, $tmp + 9);
    } //for($i = 1; $i <= $count; $i++)

    //return formatted result
    return $result;

  } //function Players()


  //Get server rules via info protocoll
  function ServerRules()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //build info command
    $command = "\xff\xff\xff\xffrules\x00";
    $buffer = $this->Communicate($command);

    //If no connection is open
    if(trim($buffer) == "")
    {
      $this->connected = false;
      return false;
    }

    //seperate rules
    $buffer = substr($buffer, 2);
    $buffer = explode("\x00", $buffer);
    $buffer_count = floor(sizeof($buffer) / 2);

    //build rules array
    for($i = 0; $i < $buffer_count; $i++)
    {
      $result[$buffer[2 * $i]] = $buffer[2 * $i + 1];
    }

    //sort rules
    ksort($result);

    //return formatted result
    return $result;

  } //function ServerRules()


  //Execute rcon command on open socket $fp
  function RconCommand($command, $pagenumber = 0, $single = true)
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //get challenge number
    if($this->challenge_number == "")
    {
      //send request of challenge number
      $challenge = "\xff\xff\xff\xffchallenge rcon\n";
      $buffer = $this->Communicate($challenge);

      //If no connection is open
      if(trim($buffer) == "")
      {
        $this->connected = false;
        return false;
      }

      //get challenge number
      $buffer = explode(" ", $buffer);
      $this->challenge_number = trim($buffer[2]);
    }

    //build command
    $command = "\xff\xff\xff\xffrcon $this->challenge_number \"$this->server_password\" $command\n";

    //get specified page
    $result = "";
    $buffer = "";
    while($pagenumber >= 0)
    {
      //send rcon command
      $buffer .= $this->Communicate($command);

      //get only one package
      if($single == true)
        $result = $buffer;

      //get more then one package and put them together
      else
        $result .= $buffer;

      //clear command for higher iterations
      $command = "";

      $pagenumber--;

    } //while($pagenumber >= 0)

    //return unformatted result
    return trim($result);

  } //function RconCommand ($command)


  //Communication between PHPrcon and the Gameserver
  function Communicate($command)
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;


    //write command on socket
    if($command != "")
      fputs($this->socket, $command, strlen($command));

    //get results from server
    $buffer = fread ($this->socket, 1);
    $status = socket_get_status($this->socket);
    $buffer .= fread($this->socket, $status["unread_bytes"]);


    //If there is another package waiting
    if(substr($buffer, 0, 4) == "\xfe\xff\xff\xff")
    {
      //get results from server
      $buffer2 = fread ($this->socket, 1);
      $status = socket_get_status($this->socket);
      $buffer2 .= fread($this->socket, $status["unread_bytes"]);

      //If the second one came first
      if(strlen($buffer) > strlen($buffer2))
        $buffer = substr($buffer, 14) . substr($buffer2, 9);
      else
        $buffer = substr($buffer2, 14) . substr($buffer, 9);

    }

    //In case there is only one package
    else
      $buffer = substr($buffer, 5);


    //return unformatted result
    return $buffer;

  } //function Communicate($buffer)

}

?>