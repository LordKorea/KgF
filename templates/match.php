<!DOCTYPE html>
<html>
    <head>
        <title>Karten gegen Flopsigkeit</title>
        <link rel="stylesheet" type="text/css" href="/css/main.css">
        <?= $this->user->get_theme_loader() ?>
    </head>
    <body>
        <div class="cupboard">
            <a href="?logout">Log Out</a> &mdash;
            <a href="/global.php?page=match&action=abandon">Abandon match</a>
            <div style="float: right;">
                <a href="#" onclick="toggleLights()" id="lightLabel">Lights are being checked...</a>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="match-container">
            <div class="match-view">
                <?= $this->get_status_format() ?>
                <div class="match-status">
                    The match will start in <b>27</b> seconds...
                </div>
                <div class="part-container">
                    <?php
                        $parts = $this->match->get_participants();
                        foreach ($parts as $part) {
                    ?>
                    <div class="part">
                        <div class="part-name"><?= $part->get_name() ?></div>
                        <div class="part-score"><b><?= $part->get_score() ?>pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <?php
                        }
                    ?>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                    <div class="part">
                        <div class="part-name">ALongNameThatIsStillPlausible123</div>
                        <div class="part-score"><b>99pts</b></div>
                        <div class="part-type"><i>Memester</i></div>
                        <div class="part-status"><i>Waiting...</i></div>
                    </div>
                </div>
                
                <div class="card-area">
                    <div class="card-area-statement">
                        <div class="card-base statement-card sticky-card">
                            This is a statement about <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>.
                            <div class="card-id">
                                #1234
                            </div>
                        </div>
                    </div>
                    <div class="card-area-played">
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                        <div class="card-area-set">
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                            <div class="card-base object-card">Words<div class="card-id">#1234</div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="match-chat">
                <input type="text" class="chat-line" spellcheck="false">
                <div class="chat-messages">
                    <div><img src="/img/bang.svg" class="chat-svg"><span class="chat-msg">Match was created</span></div>
                    <div><img src="/img/bang.svg" class="chat-svg"><span class="chat-msg">This is a long text containing a bunch of stuff just for testing the behavior of this chatbox in different settings.</span></div>
                    <div><img src="/img/bang.svg" class="chat-svg"><span class="chat-msg">Another message</span></div>
                    <div><img src="/img/message.svg" class="chat-svg"><span class="chat-msg">ALongNameThatIsStillPlausible123: hello i am a person</span></div>
                    <div><img src="/img/message.svg" class="chat-svg"><span class="chat-msg">OtherPerson: me too</span></div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="/js/matchview.js"></script>
        <script type="text/javascript" src="/js/theme.js"></script>
    </body>
</html>
