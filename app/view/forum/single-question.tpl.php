<!-- Question -->
<h2><?=$question->title?></h2>
<p><?=$question->text?></p>
<p>
    Taggar: <?php 
                foreach ($tags as $tag) {
                    echo $tag->tag_name . " ";
                } 
            ?>
    <br>
    Skapad: <?=$question->created?>
</p>


<!-- Comments related to the question, if present -->
<?php
    if (isset($q_comments) && !empty($q_comments)) {
        $html = "<ul>";
        foreach ($q_comments as $comment) {
            $html .= "<li>" . $comment->text . "<br> - " . $comment->username . " (" . $comment->created . ")</li>";
        }
        $html .= "</ul>";
        
        echo $html;
    }
?>
<a href="<?=$this->url->create('forum/add-comment/' . $question->id)?>">Skriv kommentar</a>


<!-- Answers related to question, if present -->
<h3>Svar:</h3>
<?php
    if (isset($answers) && !empty($answers)) {
        $html = "<ul>";
        foreach ($answers as $answer) {
            $html .= "<li>" . $answer->text . "<br> - " . $answer->username . " (" . $answer->created . ")</li>";
            
            /* 
             * Comments related to answer, if present
             */
            if (isset($a_comments) && !empty($a_comments)) {
                $html .= "<ul>";
                foreach ($a_comments as $comment) {
                    if ($comment->answer_id == $answer->id) {
                        $html .= "<li>" . $comment->text . "<br> - " . $comment->username . " (" . $comment->created . ")</li>";
                    }
                }
                $html .= "<li><a href='" . $this->url->create('forum/add-comment/' . $answer->id) . "'>Skriv kommentar</a></li>";
                $html .= "</ul>";
            }
        }
        $html .= "</ul>";
        
        echo $html;
    }
?>
<a href="<?=$this->url->create('forum/add-answer/' . $question->id)?>">Svara på frågan</a>   
