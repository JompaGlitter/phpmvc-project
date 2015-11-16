<!-- Question -->
<h2><?=$question->title?></h2>
<p><?=$question->text?></p>
<p>
    Taggar: <?php 
                foreach ($tags as $tag) {
                    echo "<a href='" . $this->url->create('forum/view-tag/' . $tag->tag_id) . "'>" . $tag->tag_name . "</a> ";
                } 
            ?>
    <br>
    av <a href="<?=$this->url->create('users/id/' . $question->user_id)?>"><?=$question->username?></a> &raquo <?=$question->created?>
</p>


<!-- Comments related to the question, if present -->
<?php
    if (isset($q_comments) && !empty($q_comments)) {
        $html = "<ul>";
        foreach ($q_comments as $comment) {
            $html .= "<li>" . $comment->text . "<br> - <a href='" . $this->url->create('users/id/' . $comment->user_id) . "'>" . $comment->username . "</a> &raquo " . $comment->created . "</li>";
        }
        $html .= "</ul>";
        
        echo $html;
    }
?>
<p><a href="<?=$this->url->create('forum/add-question-comment/' . $question->id)?>">Kommentera frågan</a></p>


<!-- Answers related to question, if present -->
<strong>Svar:</strong> (<?=count($answers)?>)
<hr>
<?php
    if (isset($answers) && !empty($answers)) {
        $html = "<ul>";
        foreach ($answers as $answer) {
            $html .= "<li>" . $answer->text . "<br> - <a href='" . $this->url->create('users/id/' . $answer->user_id) . "'>" . $answer->username . "</a> &raquo " . $answer->created . "</li>";
            
            /* 
             * Comments related to answer, if present
             */
            $html .= "<ul>";
            if (isset($a_comments) && !empty($a_comments)) {
                foreach ($a_comments as $comment) {
                    if ($comment->answer_id == $answer->id) {
                        $html .= "<li>" . $comment->text . "<br> - <a href='" . $this->url->create('users/id/' . $comment->user_id) . "'>" . $comment->username . "</a> &raquo " . $comment->created . "</li>";
                    }
                }
            }
            $html .= "<li><a href='" . $this->url->create('forum/add-answer-comment/' . $answer->id) . "'>Kommentera svaret</a></li>";
            $html .= "</ul>";
        }
        $html .= "</ul>";
        
        echo $html;
    
    } else {
        echo "<p>Denna fråga har ej besvarats än.</p>";
    }
?>
<a href="<?=$this->url->create('forum/add-answer/' . $question->id)?>">Svara på frågan</a>   
