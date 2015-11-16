<!-- Question -->
<h3><?=$question->title?></h3>
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