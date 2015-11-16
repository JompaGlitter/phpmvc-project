<!-- All questions, if present -->
<strong>Frågor</strong> (<?=count($questions)?>)
<br>
<hr>
<?php if (empty($questions) || is_null($questions)) : ?>
    <p>Användaren har inte ställt någon fråga än.</p>

<?php else : ?>
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
        <!-- Creation date -->
        <i>Skapad &raquo <?=$question->created?></i>
        </small>
    </p>
    <?php endforeach; ?>
<?php endif; ?>



<!-- All answers, if present -->
<strong>Svar</strong> (<?=count($answers)?>)
<br>
<hr>
<?php if (empty($answers) || is_null($answers)) : ?>
    <p>Användaren har inte svarat på någon fråga än.</p>

<?php else : ?>
    <?php foreach ($answers as $answer) : ?>
    <p>
        <!-- Title -->
        <strong><a href="<?=$this->url->create('forum/view-question/' . $answer->question_id)?>"><?=$answer->question_title?></a></strong><br>
        <!-- Creation date -->
        <small><i>Svarat &raquo <?=$answer->answer_created?></i></small>
    </p>
    <?php endforeach; ?>
<?php endif; ?>

