<?php
class Fournisseur extends TableObject {

    function getOption(){
        echo '<option value="'.$this->code_frs.'">'.$this->nom.'</option>';
    }

}