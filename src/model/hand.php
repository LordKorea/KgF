<?php
  /**
   * Represents a collection of hand cards
   */
  class Hand {
    /**
     * The number of cards per type on the hand (excluding STATEMENT)
     */
    const HAND_CARD_PER_TYPE = 6;
    /**
     * The cards in this hand
     */
    private $mHandCards;
    /**
     * The participant this hand belongs to
     */
    private $mParticipant;

    /**
     * Loads the hand of a participant
     */
    public static function loadHand(Participant $participant) {
      return new Hand(HandCard::loadHandCards($participant), $participant);
    }

    /**
     * Private constructor to prevent instance creation
     */
    private function __construct(array $cards, Participant $participant) {
      $this->mHandCards = $cards;
      $this->mParticipant = $participant;
    }

    /**
     * Hand card getter
     */
    public function getHandCards() {
      return $this->mHandCards;
    }

    /**
     * Replenishes the hand
     */
    public function replenish() {
      $counts = array(
        "OBJECT" => self::HAND_CARD_PER_TYPE,
        "VERB" => self::HAND_CARD_PER_TYPE
      );
      foreach ($this->mHandCards as $handCard) {
        $counts[$handCard->getCard()->getType()]--;
      }
      foreach ($counts as $type => $needed) {
        HandCard::replenishHandFor($this->mParticipant, $type, $needed);
      }
      $this->mHandCards = HandCard::loadHandCards($this->mParticipant);
    }

    /**
     * Toggles a hand card from picked to not picked and vice versa
     */
    public function togglePicked($handId) {
      $handId = intval($handId);
      if (!isset($this->mHandCards[$handId])) {
        // This hand card does not exist in this hand...
        return;
      }

      $hc = $this->mHandCards[$handId];
      if ($hc->isPicked()) {
        $pick = $hc->getPickId();
        foreach ($this->mHandCards as $handCard) {
          if ($handCard->isPicked() && $handCard->getPickId() >= $pick) {
            $handCard->unpick();
          }
        }
      } else {
        $maxPicked = $this->mParticipant->getMatch()->getCardGapCount();
        $nPicked = 0;
        $nextPickId = 0;
        foreach ($this->mHandCards as $handCard) {
          if ($handCard->isPicked()) {
            $nPicked++;
            $nextPickId = max($nextPickId, $handCard->getPickId() + 1);
          }
        }
        if ($nPicked >= $maxPicked) {
          // Can't pick new cards, too many on hand
          return;
        }
        $hc->pick($nextPickId);
      }
    }

    /**
     * Returns the number of picked cards on this hand
     */
    public function getPickCount() {
      $n = 0;
      foreach ($this->mHandCards as $handCard) {
        if ($handCard->isPicked()) {
          $n++;
        }
      }
      return $n;
    }

    /**
     * Unpicks all cards in this hand
     */
    public function unpickAll() {
      foreach ($this->mHandCards as $handCard) {
        if ($handCard->isPicked()) {
          $handCard->unpick();
        }
      }
    }

    /**
     * Deletes all picked cards
     */
    public function deletePicked() {
      $delIds = array();
      foreach ($this->mHandCards as $handId => $handCard) {
        if ($handCard->isPicked()) {
          $delIds[] = $handId;
          $handCard->delete();
        }
      }
      foreach ($delIds as $id) {
        unset($this->mHandCards[$id]);
      }
    }

    /**
     * Fetches the information about the picked cards in this hand
     */
    public function getPickData($redacted) {
      $redacted = boolval($redacted);
      $data = array();
      foreach ($this->mHandCards as $handId => $handCard) {
        if ($handCard->isPicked()) {
          if ($redacted) {
            $data[] = array(
              "redacted" => true
            );
          } else {
            $data[] = array(
              "type" => $handCard->getCard()->getType(),
              "text" => $handCard->getCard()->getText()
            );
          }
        }
      }
      return $data;
    }
  }
?>
