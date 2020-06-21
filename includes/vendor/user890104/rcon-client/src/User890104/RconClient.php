<?php
namespace User890104;

use Exception;

class RconClient {
	private $socket;
	private $password;
	private $challengeNumber = null;
	private $splitPackets = [];
	
	const PACKET_SIZE = 1400;
	
	const READ_TIMEOUT = 2;
	const WRITE_TIMEOUT = 1;
	
	const PACKET_HEADER = -1;
	const PACKET_HEADER_SPLIT = -2;
	
	const CHALLENGE_PLACEHOLDER = -1;
	
	const RESPONSE_CHALLENGE = 0x41;
	
	const REQUEST_A2S_INFO = 0x54;
	const RESPONSE_A2S_INFO = 0x49;
	
	const REQUEST_A2S_PLAYER = 0x55;
	const RESPONSE_A2S_PLAYER = 0x44;
	
	const REQUEST_A2S_RULES = 0x56;
	const RESPONSE_A2S_RULES = 0x45;
	
	const RESPONSE_TEXT = 0x6c;
	
	public function __construct($host, $password, $port = 27015, $readTimeout = null, $writeTimeout = null) {
		$this->socket = new Socket('udp://' . $host, $port);
		
		if (is_null($readTimeout)) {
			$this->socket->setReadTimeout(static::READ_TIMEOUT);
		}
		else {
			$tv_sec = floor($readTimeout);
			$tv_usec = ($readTimeout - $tv_sec) * 1e6;
			$this->socket->setReadTimeout($tv_sec, $tv_usec);
		}
		
		if (is_null($writeTimeout)) {
			$this->socket->setWriteTimeout(static::WRITE_TIMEOUT);
		}
		else {
			$tv_sec = floor($writeTimeout);
			$tv_usec = ($writeTimeout - $tv_sec) * 1e6;
			$this->socket->setWriteTimeout($tv_sec, $tv_usec);
		}
		
		$this->password = $password;
	}
		
	private static function encode($cmd) {
		return pack('Va*', static::PACKET_HEADER, $cmd);
	}
	
	private static function decode($msg) {
		$reader = new ByteStreamReader($msg);
		$header = $reader->readInt();

		if ($header === static::PACKET_HEADER) {
			return $reader;
		}
		
		if ($header === static::PACKET_HEADER_SPLIT) {
			$id = $reader->readInt();
			$num = $reader->readUnsignedChar();
			$packetNumber = $num >> 4;
			$packetCount = $num & 0x0F;
			
			// TODO
			var_dump($id, $packetNumber, $packetCount);
			throw new Exception('Unsupported');
			return null;
		}
	}
	
	private function communicate($cmd) {
		$this->socket->send(self::encode($cmd));
		
		$response = [];

		while (true) {
			$data = $this->socket->receive(static::PACKET_SIZE);
			
			if ($data === false) {
				break;
			}

			$decoded = static::decode($data);
			
			if (!is_null($decoded)) {
				$response[] = $decoded;
			}
		}
		
		return $response;
	}
	
	// Text command API
	private function challenge() {
		$response = $this->communicate('challenge rcon');

		if (count($response) !== 1) {
			throw new Exception('Invalid number of packets');
		}
		
		$text = rtrim($response[0]->readString());
		
		if (preg_match('/^challenge rcon (\d+)$/', $text, $matches) !== 1) {
			throw new Exception('Invalid challenge response');
		}
		
		$this->challengeNumber = intval($matches[1]);
	}
	
	private function buildCommandString($cmd) {
		return 'rcon ' . $this->challengeNumber . ' "' . $this->password . '" ' . $cmd;
	}
	
	public function exec($cmd) {
		if (is_null($this->challengeNumber)) {
			$this->challenge();
		}
		
		$response = $this->communicate($this->buildCommandString($cmd));
		
		if (count($response) === 0) {
			throw new Exception('No response received');
		}
		
		$reader = &$response[0];
		$header = $reader->readUnsignedChar();
		
		if ($header !== static::RESPONSE_TEXT) {
			throw new Exception('Invalid response');
		}
		
		$text = $reader->readString();
		
		if ($text === 'Bad rcon_password.') {
			throw new Exception('Bad rcon_password');
		}
		
		return $text;
	}
	
	// Binary API
	private function execBinary($cmd) {
		if (is_null($this->challengeNumber)) {
			$response = $this->communicate(pack('CV', $cmd, static::CHALLENGE_PLACEHOLDER));

			if (count($response) !== 1) {
				throw new Exception('Invaid response packet');
			}
			
			$reader = &$response[0];
			$header = $reader->readUnsignedChar();
			
			if ($header !== static::RESPONSE_CHALLENGE) {
				throw new Exception('Invaid challenge response');
			}
			
			$this->challengeNumber = $reader->readUnsignedIntLE();
		}
		
		return $this->communicate(pack('CV', $cmd, $this->challengeNumber));
	}
	
	public function getInfo() {
		$response = $this->communicate(chr(static::REQUEST_A2S_INFO) . 'Source Engine Query' . chr(0));
		
		foreach ($response as $reader) {
			$header = $reader->readUnsignedChar();

			if ($header !== static::RESPONSE_A2S_INFO) {
				continue;
			}

			$info = [
				'Protocol'		=> $reader->readUnsignedChar(),
				'Name'			=> $reader->readString(),
				'Map'			=> $reader->readString(),
				'Folder'		=> $reader->readString(),
				'Game'			=> $reader->readString(),
				'AppID'			=> $reader->readUnsignedShortLE(),
				'Players'		=> $reader->readUnsignedChar(),
				'MaxPlayers'	=> $reader->readUnsignedChar(),
				'Bots'			=> $reader->readUnsignedChar(),
				'ServerType'	=> $reader->readAsciiLetter(),
				'Environment'	=> $reader->readAsciiLetter(),
				'Visibility'	=> $reader->readUnsignedChar(),
				'VAC'			=> $reader->readUnsignedChar(),
				'Version'		=> $reader->readString(),
				'EDF'			=> $reader->readUnsignedChar(),
			];
			
			if ($info['EDF'] & 0x80) {
				$info['Port'] = $reader->readUnsignedShortLE();
			}
			
			if ($info['EDF'] & 0x10) {
				$info['SteamID'] = $reader->readUnsignedLongLE();
			}
			
			if ($info['EDF'] & 0x40) {
				$info['SourceTVPort'] = $reader->readUnsignedShortLE();
				$info['SourceTVName'] = $reader->readString();
			}
			
			if ($info['EDF'] & 0x20) {
				$info['Keywords'] = $reader->readString();
			}
			
			if ($info['EDF'] & 0x01) {
				$info['GameID'] = $reader->readUnsignedLongLE();
			}
			
			return $info;
		}
		
		return false;
	}
	
	public function getPlayers() {
		$response = $this->execBinary(static::REQUEST_A2S_PLAYER);
		
		if (count($response) !== 1) {
			throw new Exception('Invaid response packet');
		}
		
		$reader = &$response[0];
		$header = $reader->readUnsignedChar();
		
		if ($header !== static::RESPONSE_A2S_PLAYER) {
			throw new Exception('Invaid response packet header');
		}
		
		$numPlayers = $reader->readUnsignedChar();
		$players = [];
		
		for ($i = 0; $i < $numPlayers; ++$i) {
			$players[] = [
				'Index' => $reader->readUnsignedChar(),
				'Name' => $reader->readString(),
				'Score' => $reader->readInt(),
				'Duration' => $reader->readFloat(),
			];
		}
		
		return $players;
	}
	
	public function getRules() {
		$response = $this->execBinary(static::REQUEST_A2S_RULES);
		
		if (count($response) !== 1) {
			throw new Exception('Invaid response packet');
		}
		
		$reader = &$response[0];
		$header = $reader->readUnsignedChar();
		
		if ($header !== static::RESPONSE_A2S_RULES) {
			throw new Exception('Invaid response packet header');
		}
		
		$numRules = $reader->readUnsignedShortLE();
		$rules = [];
		
		for ($i = 0; $i < $numRules; ++$i) {
			$rules[$reader->readString()] = $reader->readString();
		}
		
		return $rules;
	}
}
