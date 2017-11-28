<?php
  /**
   * Represents a participant of a match
   */
  class Participant {
    /**
     * Prepared SQL queries
     */
    private static $sql_queries;
    /**
     * The ID->Participant cache
     */
    private static $id_cache;
    /**
     * The PlayerID->Participant cache
     */
    private static $player_cache;
    /**
     * The ID of this participant
     */
    private $id;
    /**
     * The ID of the player
     */
    private $player_id;
    /**
     * The name of the player
     */
    private $player_name;
    /**
     * The match this participant participates in
     */
    private $match;
    /**
     * The score of this participant
     */
    private $score;
    /**
     * Whether this participant is currently picking
     */
    private $picking;
    /**
     * The point in time when this participant will be kicked due to timeout
     */
    private $timeout;

    /**
     * Used to provide a DB handle and to initialize all the queries
     */
    public static function provideDB($dbh) {
      self::$sql_queries = array(
        "housekeeping_find" => $dbh->prepare(
          "SELECT * ".
          "FROM `kgf_match_participant` ".
          "WHERE `mp_timeout` <= UNIX_TIMESTAMP()"
        ),
        "housekeeping" => $dbh->prepare(
          "DELETE FROM `kgf_match_participant` ".
          "WHERE `mp_timeout` <= UNIX_TIMESTAMP()"
        ),
        "all_for_match" => $dbh->prepare(
          "SELECT * ".
          "FROM `kgf_match_participant` ".
          "WHERE `mp_match` = :matchid ".
          "ORDER BY `mp_id` ASC"
        ),
        "get_participant" => $dbh->prepare(
          "SELECT * ".
          "FROM `kgf_match_participant` ".
          "WHERE `mp_player` = :playerid"
        ),
        "add_participant" => $dbh->prepare(
          "INSERT INTO `kgf_match_participant` ".
            "(`mp_id`, `mp_player`, `mp_name`, `mp_match`, `mp_score`, ".
              "`mp_picking`, `mp_timeout`) ".
          "VALUES (NULL, :playerid, :playername, :matchid, 0, 0, :timeout)"
        ),
        "fetch_latest" => $dbh->prepare(
          "SELECT * ".
          "FROM `kgf_match_participant` ".
          "ORDER BY `mp_id` DESC ".
          "LIMIT 1"
        ),
        "abandon" => $dbh->prepare(
          "DELETE FROM `kgf_match_participant` ".
          "WHERE `mp_id` = :partid"
        ),
        "heartbeat" => $dbh->prepare(
          "UPDATE `kgf_match_participant` ".
          "SET `mp_timeout` = :timeout ".
          "WHERE `mp_id` = :partid"
        )
      );
    }

    /**
     * Performs housekeeping tasks
     */
    public static function perform_housekeeping() {
      $q = self::$sql_queries["housekeeping_find"];
      $q->execute();
      $rows = $q->fetchAll();
      foreach ($rows as $part) {
        Match::get_by_id($part["mp_match"])->get_chat()->send_message("SYSTEM",
          "<b>".$part["mp_name"]." timed out</b>");
      }

      $q = self::$sql_queries["housekeeping"];
      $q->execute();
    }

    /**
     * Loads the participants of the given match
     */
    public static function load_for_match($match) {
      $q = self::$sql_queries["all_for_match"];
      $q->bindValue(":matchid", $match->get_id(), PDO::PARAM_INT);
      $q->execute();
      $rows = $q->fetchAll();

      $parts = array();
      foreach ($rows as $part) {
        $id = $part["mp_id"];
        $pid = $part["mp_player"];
        if (!isset(self::$id_cache[$id])) {
          self::$id_cache[$id] = new Participant($part["mp_id"],
            $part["mp_player"], $part["mp_name"], $match,
            intval($part["mp_score"]), intval($part["mp_picking"]) != 0,
            intval($part["mp_timeout"]));
          self::$player_cache[$pid] = self::$id_cache[$id];
        }
        $parts[] = self::$id_cache[$id];
      }
      return $parts;
    }

    /**
     * Fetches the participant for this player
     */
    public static function get_participant($player) {
      if (isset(self::$player_cache[$player])) {
        return self::$player_cache[$player];
      }

      $q = self::$sql_queries["get_participant"];
      $q->bindValue(":playerid", $player, PDO::PARAM_STR);
      $q->execute();
      $row = $q->fetch();
      if (!$row) {
        return null;
      }

      $id = $row["mp_id"];
      $pid = $player;
      self::$id_cache[$id] = new Participant($id, $pid, $row["mp_name"],
        Match::get_by_id($row["mp_match"]), intval($row["mp_score"]),
        intval($row["mp_picking"]) != 0, intval($row["mp_timeout"]));
      self::$player_cache[$pid] = self::$id_cache[$id];
      return self::$id_cache[$id];
    }

    /**
     * Creates a participant from an User and a Match
     */
    public static function create_from_user_and_match($user, $match, $timeout) {
      $q = self::$sql_queries["add_participant"];
      $q->bindValue(":playerid", $user->get_id(), PDO::PARAM_STR);
      $q->bindValue(":playername", $user->get_nickname(), PDO::PARAM_STR);
      $q->bindValue(":matchid", $match->get_id(), PDO::PARAM_INT);
      $q->bindValue(":timeout", $timeout, PDO::PARAM_INT);
      $q->execute();

      // We're in a transaction, so this should be the one we just created
      $q = self::$sql_queries["fetch_latest"];
      $q->execute();

      $row = $q->fetch();
      $id = $row["mp_id"];
      $pid = $row["mp_player"];

      self::$id_cache[$id] = new Participant($id, $pid, $row["mp_name"], $match,
        $row["mp_score"], intval($row["mp_picking"]) != 0, $row["mp_timeout"]);
      self::$player_cache[$pid] = self::$id_cache[$id];
      return self::$id_cache[$id];
    }

    /**
     * Private constructor to prevent instance creation
     */
    private function __construct($id, $player_id, $name, $match, $score,
      $picking, $timeout) {
      $this->id = intval($id);
      $this->player_id = $player_id;
      $this->player_name = $name;
      $this->match = $match;
      $this->score = intval($score);
      $this->picking = $picking;
      $this->timeout = $timeout;
    }

    /**
     * Leaves the match (and thus destroys this participant)
     */
    public function leave_match() {
      $this->match->get_chat()->send_message("SYSTEM",
        "<b>".$this->get_name()." left</b>");

      $q = self::$sql_queries["abandon"];
      $q->bindValue(":partid", $this->id, PDO::PARAM_INT);
      $q->execute();
      unset(self::$id_cache[$this->id]);
      unset(self::$player_cache[$this->player_id]);
    }

    /**
     * Registers a heartbeat (i.e. the timeout will be reset) for the given
     * amount of seconds
     */
    public function heartbeat($seconds) {
      $q = self::$sql_queries["heartbeat"];
      $q->bindValue(":timeout", time() + $seconds, PDO::PARAM_INT);
      $q->bindValue(":partid", $this->id, PDO::PARAM_INT);
      $q->execute();
    }

    /**
     * ID getter
     */
    public function get_id() {
      return $this->id;
    }

    /**
     * Name getter
     */
    public function get_name() {
      return $this->player_name;
    }

    /**
     * Match getter
     */
    public function get_match() {
      return $this->match;
    }

    /**
     * Score getter
     */
    public function get_score() {
      return $this->score;
    }
  }
?>