<?php foreach ($questions as $question) : ?>
    <p>
        <!-- Title -->
        <strong><a href="<?=$this->url->create('forum/view-question/' . $question->id)?>"><?=$question->title?></a></strong><br>
        
        <!-- Related tags -->
        <small>
        Taggar: 
        <?php
        foreach ($tags as $tag) {
            if ($tag->question_id == $question->id) {
                echo "<a href='" . $this->url->create('forum/view-tag/' . $tag->id) . "'>" . $tag->tag . "</a> ";
            }
        }
        ?>
        <br>
        
        <!-- Creator and creation date-->
        <i>av <?=$question->username?> &raquo <?=$question->created?></i>
        </small>
    </p>
<?php endforeach; ?>