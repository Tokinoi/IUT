<?php
// Représentation d'un contact
class Contact {
     public $id;
     public $nom;
     public $prénom;
     public $tél;

    public function __construct(int $id, string $n, string $p, string $t) {
        $this->id = $id;
        $this->nom = $n;
        $this->prénom = $p;
        $this->tél = $t;
    }

     public function __tostring() {
         return "$this->id : $this->nom $this->prénom - $this->tél";
     }

     public function toTableRow(){
        return "<tr>
                    <td>". $this->id ."</td>
                    <td>". $this->nom ."</td>
                    <td>". $this->prénom ."</td>
                    <td>". $this->tél ."</td>
                </tr>";
     }
     public function toForm(){
        return 
        '<form action="" method="post">
        <p class="form">
            <input type="hidden" name="id" id="id" value="' . $this->id . '"required>
            <input type="text" name="name" id="name" value="' . $this->nom . '" required>
            <input type="text" name="surname" id="surname" value="' . $this->prénom . '" "required>
            <input type="tel" name="tel" id="tel" value="' . $this->tél . '" "required>
            <input type="image" name="modif[]" src="img/modif.png" alt="Modification"/>
            <input type="image" name="supp[]" src="img/supp.png" alt="Supression"/>
            </p>
        </form>';
     }
}
