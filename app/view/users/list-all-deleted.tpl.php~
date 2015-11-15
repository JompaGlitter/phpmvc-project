<h1><?=$title?></h1>


<table>
<?php foreach ($users as $user) : ?>
    <tr>
        <!-- <pre><?=var_dump($user->getProperties())?></pre> -->
        
        <!-- User acronym and name -->
        <td><a href="<?=$this->url->create('users/id/' . $user->id)?>"><?=$user->acronym?></a></td>
        <td>(<?=$user->name?>)</td>
        
        <!-- Is the user active or inactive? -->
        <td><?=$user->deleted ? 'Raderad' : ($user->active ? 'Aktiv' : 'Inaktiv')?></td>
        
        <!-- Delete user permanently? -->
        <td><i class="fa fa-times"></i> <a href="<?=$this->url->create('users/delete/' . $user->id)?>">Radera permanent</a></td>
        
    </tr> 

<?php endforeach; ?>
</table>

<p><i class="fa fa-user-plus"></i> <a href="<?=$this->url->create('users/add')?>">Skapa ny användare</a></p>

<hr>

<table>
    <tr>
        <td><i class="fa fa-users"></i><a href="<?=$this->url->create('users')?>">Alla användare</a></td>
        <td><i class="fa fa-ban"></i><a href="<?=$this->url->create('users/is-inactive')?>">Inaktiva</a></td>
        <td><i class="fa fa-trash-o"></i><a href="<?=$this->url->create('users/soft-deleted')?>">Papperskorgen</a></td>
    </tr>
</table>

<p><i class="fa fa-refresh"></i> <a href="<?=$this->url->create('setup')?>">Återställ användardatabasen</a></p>