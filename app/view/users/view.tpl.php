<!-- User info -->
<img src="<?=$user->gravatar?>" title="<?=$user->username?>" alt="<?=$user->username?>">

<h2><?=htmlentities($user->username)?></h2>
<?=$this->textFilter->doFilter($user->about, 'nl2br, markdown')?>

<p>
    <small>
    <strong>E-mail:</strong> <a href="mailto:<?=htmlentities($user->email)?>"><?=htmlentities($user->email)?></a><br>
    <strong>Hemsida:</strong> <a href="<?=htmlentities($user->homepage)?>"><?=htmlentities($user->homepage)?></a><br>
    <strong>Medlem sedan </strong> <?=htmlentities($user->created)?>
    </small>
</p>

<?php if ($user->username == $loggedIn) : ?>
<p>
    <i class="fa fa-pencil-square-o"></i> <a href="<?=$this->url->create('users/update/' . $user->id)?>">Uppdatera</a>
</p>
<?php endif; ?>


<p>
<!-- Options based on user status 
    
<?php if ($user->deleted) : ?>
    <i class="fa fa-repeat"></i> <a href="<?=$this->url->create('users/undo-soft-delete/' . $user->id)?>">Återställ</a>
<?php elseif ($user->active) : ?>
    <i class="fa fa-pencil-square-o"></i> <a href="<?=$this->url->create('users/update/' . $user->id)?>">Uppdatera</a><br>  
    <i class="fa fa-ban"></i> <a href="<?=$this->url->create('users/deactivate/' . $user->id)?>">Inaktivera</a><br>
    <i class="fa fa-trash-o"></i> <a href="<?=$this->url->create('users/soft-delete/' . $user->id)?>">Radera</a>
<?php else : ?>
    <i class="fa fa-pencil-square-o"></i> <a href="<?=$this->url->create('users/update/' . $user->id)?>">Uppdatera</a><br>
    <i class="fa fa-check"></i> <a href="<?=$this->url->create('users/activate/' . $user->id)?>">Aktivera</a><br>
    <i class="fa fa-trash-o"></i> <a href="<?=$this->url->create('users/soft-delete/' . $user->id)?>">Radera</a>
<?php endif; ?>

-->
<p><i class="fa fa-home"></i> <a href='<?=$this->url->create('users')?>'>Tillbaka</a></p>
