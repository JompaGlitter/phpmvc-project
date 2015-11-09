<h1><?=$title?><em><?=$user->getProperties()['acronym']?></em></h1>

<!--<pre><?=var_dump($user->getProperties())?></pre>--> 


<!-- User info -->
<p><strong>Namn:</strong> <?=$user->name?></p>
<p><strong>E-mail:</strong> <?=$user->email?></p>
<p><strong>Skapad:</strong> <?=$user->created?></p>
<p><strong>Status:</strong> <?=$user->deleted ? 'Raderad (' . $user->deleted . ')' : ($user->active ? 'Aktiv' : 'Inaktiv')?></p>


<p>
<!-- Options based on user status -->
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


    
<p><i class="fa fa-home"></i> <a href='<?=$this->url->create('users')?>'>Tillbaka</a></p>
