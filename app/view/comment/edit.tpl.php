<div class='comment-form'>
    <form method=post>
        <input type=hidden name="redirect" value="<?=$pagekey == 'smalltalk' ? $this->url->create('smalltalk') : $this->url->create('')?>">
        <input type=hidden name="pagekey" value="<?=$pagekey?>">
        <fieldset>
        <legend>Ändra kommentaren</legend>
        <p><label>Name:<br/><input type='text' name='name' value='<?=$comment['name']?>'/></label></p>
        <p><label>Text:<br/><textarea name='content'><?=$comment['content']?></textarea></label></p>
        <p><label>Hemsida:<br/><input type='text' name='web' value='<?=$comment['web']?>'/></label></p>
        <p><label>Email:<br/><input type='text' name='mail' value='<?=$comment['mail']?>'/></label></p>
        <p class=buttons>
            <input type='submit' name='doEdit' value='Uppdatera' onClick="this.form.action = '<?=$this->url->create('comment/edit/' . $id . '/' . $pagekey)?>'"/>
            <input type='submit' name='doRemoveOne' value='Radera kommentar' onClick="this.form.action = '<?=$this->url->create('comment/delete/' . $id . '/' . $pagekey)?>'"/>
            <input type='reset' value='Återställ'/>
            <input type='submit' name='doGoBack' value='Avbryt' onClick="this.form.action = '<?=$pagekey == 'smalltalk' ? $this->url->create('smalltalk') : $this->url->create('')?>'">
        </p>
        <output></output>
        </fieldset>
    </form>
</div>