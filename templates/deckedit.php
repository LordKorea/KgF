<!DOCTYPE html>
<html>
  <head>
    <title>Karten gegen Flopsigkeit</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <?= $this->mUser->getThemeLoader() ?>
  </head>
  <body>
    <div class="cupboard">
      <a href="?logout">Log Out</a> -
      <a href="/global.php?page=dashboard">Close editor</a>
      <div class="rightFloat">
        <a href="#" id="lightLabel">
          Lights are being checked...
        </a>
      </div>
      <div class="clearAfterFloat"></div>
    </div>
    <?= $this->getStatusFormat() ?>

    <div class="match-box">
      <input type="file" id="deckinput" accept=".tsv">
      <div class="rightFloat">
        <button class="largeTextButton" id="openDeckButton">
          Open deck
        </button>
        <button class="largeTextButton" id="addCardButton">
          Add card
        </button>
        <button class="largeTextButton" id="sortCardsButton">
          Sort by type
        </button>
        <button class="largeTextButton" id="exportDeckButton">
          Export deck
        </button>
      </div>
      <div class="clearAfterFloat"></div>
    </div>

    <div class="card-container" id="deck-display">
    </div>

    <div class="card-editor-container">
      <div class="card-editor-cover">&nbsp;</div>
      <div class="card-editor">
        <div class="card-editor-card-display"></div>
        <b>Underscore (_):</b> Represents a gap for statement cards (at most 3 per card)<br>
        <b>Pipe (|):</b> Represents a hyphenation point for long words<br>
        <input type="text" size="70" maxlength="250" id="card-text-input" class="card-text-edit"><br>
        <button class="largeTextButton" id="closeEditorButton">
          Save card
        </button>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="/js/download.js"></script>
    <script type="text/javascript" src="/js/card.js"></script>
    <script type="text/javascript" src="/js/deck.js"></script>
    <script type="text/javascript" src="/js/theme.js"></script>
  </body>
</html>
