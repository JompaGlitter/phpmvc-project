<h2>Tagg: <?=$tag->tag?></h2>
<p>Nedan listas frågor med taggen <strong><?=$tag->tag?></strong>.</p>

<!-- Loop out tag related questions -->
<?php foreach ($questions as $question) : ?>
    <p>
        <!-- Title -->
        <strong><a href="<?=$this->url->create('forum/view-question/' . $question->q_id)?>"><?=$question->q_title?></a></strong><br>
        
        <!-- Related tags -->
        <small>
        Taggar: 
        <?php
        foreach ($q_tags as $q_tag) {
            if ($q_tag->question_id == $question->q_id) {
                echo "<a href='" . $this->url->create('forum/view-tag/' . $q_tag->id) . "'>" . $q_tag->tag . "</a> ";
            }
        }
        ?>
        <br>
        
        <!-- Creator and creation date-->
        <i>av <?=$question->q_username?> &raquo <?=$question->q_created?></i>
        </small>
    </p>
<?php endforeach; ?>