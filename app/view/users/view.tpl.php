<!-- User info -->
<img src="<?=$user->gravatar?>" title="<?=$user->username?>" alt="<?=$user->username?>">

<h2><?=$user->username?></h2>
<?=$user->about?>

<p>
    <small>
    <strong>E-mail:</strong> <a href="mailto:<?=$user->email?>"><?=$user->email?></a><br>
    <strong>Hemsida:</strong> <a href="<?=$user->homepage?>"><?=$user->homepage?></a><br>
    <strong>Medlem sedan </strong> <?=$user->created?>
    </small>
</p>

<p>
    <i class="fa fa-pencil-square-o"></i> <a href="<?=$this->url->create('users/update/' . $user->id)?>">Uppdatera</a>
</p>


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
