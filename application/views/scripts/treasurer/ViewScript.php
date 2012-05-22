
<form action="<?php echo $this->escape($this->element->getAction()) ?>"
      method="<?php echo $this->escape($this->element->getMethod()) ?>"
      class="twocol form-horizontal"
      onsubmit="javascript:return validate_new(this)">

<fieldset class="row-fluid">
    <legend>Add New SVDP Member</legend>
    
    <div class="span6">
        <?php echo $this->element->firstname ?>
    </div>
    
    <div class="span6">
        <?php echo $this->element->lastname ?>
    </div>   
     
    <div class="span6">           
        <?php echo $this->element->home ?>
    </div>

    <div class="span6">
        <?php echo $this->element->cell ?>
    </div>
</fieldset>
<fieldset class="row-fluid">
    <div class="span6">
        <?php echo $this->element->email ?>
    </div>

    <div class="span6">
        <?php echo $this->element->role ?>
    </div>

</fieldset>
    
    <?php echo $this->element->submit ?>
</form>