<?php foreach ($questions as $question) : ?>
    <p>
        <!-- Title
             TODO: link to single question view) 
             -->
        <strong><?=$question->title?></strong><br>
        
        <!-- Related tags 
             TODO: link to view with questions related to the tag
             -->
        <small>
        Taggar: 
        <?php
        foreach ($tags as $tag) {
            if ($tag->question_id == $question->id) {
                echo $tag->tag_name . " ";
            }
        }
        ?>
        <br>
        
        <!-- Creator and creation date-->
        <i>av <?=$question->username?> &raquo <?=$question->created?></i>
        </small>
    </p>
<?php endforeach; ?>