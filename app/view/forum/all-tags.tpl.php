<p>
<?php foreach ($tags as $tag) : ?>
        <strong><a href="<?=$this->url->create('forum/view-tag/' . $tag->id)?>"><?=$tag->tag?></a></strong> (<?=$tag->countOf?>)<br>
<?php endforeach; ?>
</p>