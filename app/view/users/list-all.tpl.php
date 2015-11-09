<h1><?=$title?></h1>

<table>
<?php foreach ($users as $user) : ?>
    <tr>
        
        <td><img src="<?=$user->gravatar . '?s=40'?>" title="<?=$user->username?>" alt="<?=$user->username?>"></td>
        <td>
            <a href="<?=$this->url->create('users/id/' . $user->id)?>"><?=$user->username?></a><br>
            <small><i>Medlem sedan <?=$user->created?></i></small>
        </td>
        
    </tr> 
<?php endforeach; ?>
</table>

<p><i class="fa fa-user-plus"></i> <a href="<?=$this->url->create('users/add')?>">Skapa ny användare</a></p>