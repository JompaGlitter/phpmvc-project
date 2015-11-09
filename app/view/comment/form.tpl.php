<div class='comment-form'>
    <form method=post>
        <input type=hidden name="redirect" value="<?=$pagekey == 'smalltalk' ? $this->url->create('smalltalk') : $this->url->create('')?>">
        <input type=hidden name="pagekey" value="<?=$pagekey?>">
        <fieldset>
        <legend>Lägg till en kommentar</legend>
        <p><label>Name:<br/><input type='text' name='name' value='<?=$name?>'/></label></p>
        <p><label>Text:<br/><textarea name='content'><?=$content?></textarea></label></p>
        <p><label>Hemsida:<br/><input type='text' name='web' value='http://<?=$web?>'/></label></p>
        <p><label>Email:<br/><input type='text' name='mail' value='<?=$mail?>'/></label></p>
        <p class=buttons>
            <input type='submit' name='doCreate' value='Kommentera' onClick="this.form.action = '<?=$this->url->create('comment/add')?>'"/>
            <input type='reset' value='Återställ'/>
            <input type='submit' name='doRemoveAll' value='Radera alla kommentarer' onClick="this.form.action = '<?=$this->url->create('comment/remove-all/' . $pagekey)?>'"/>
        </p>
        <output></output>
        </fieldset>
    </form>
</div>
