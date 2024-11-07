
/* Les contacts tels qu'extraits de la base, pour tester l'affichage du select sans avoir à écrire la requête AJAX */

var xhr = new XMLHttpRequest()
xhr.open("GET", "retrieve.php", false)
xhr.send(null)
var lesContacts = JSON.parse(xhr.responseText)

// id du contact séléctionné mis à jour par rafraîchir, appelée par onchange du SELECT
var idContactCourant;

// Création d'un contact vide (pour le formulaire d'ajout)
function contactVide() {
    return { 'id': -1, 'nom': "", 'prénom': "", 'tél': "" };
}

// Remplissage du select des contacts à partir d'un tableau de contacts
function populateSelect(mesContacts) {
    var contacts = document.getElementById('contacts');
    for (var item in mesContacts) {
        var newOption = document.createElement("option");
        newOption.setAttribute("value", mesContacts[item]['id']);
        newOption.appendChild(document.createTextNode(mesContacts[item]['nom'] + " " + mesContacts[item]['prénom']));
        contacts.appendChild(newOption);
    }
}

// Remplissage du formulaire avec les données du contact : contact.id, contact.nom...
function populateForm(contact) {
    document.getElementById('nom').setAttribute("value", contact['nom']);
    document.getElementById('prénom').setAttribute("value", contact['prénom']);
    document.getElementById('tél').setAttribute("value", contact['tél']);
}

// Rafraîchissement de l'affichage lors du changement du contact sélectionné
// Reçoit l'identifiant du contact
function rafraichir(idContact) {
    idContactCourant = parseInt(idContact); // idContact vient du HTML, c'est donc du texte
    afficheForm();
    afficheBoutons()
}

// Chargement de la liste des contacts et initialisation du select
function init() {
    idContactCourant = -2;
    // Masque le formulaire et les boutons puisqu'ici idContactCourant == -2
    afficheForm(); afficheBoutons();
    // Dans un deuxième temps, on ira chercher les contacts avec une requête AJAX, on se contente pour l'instant
    // d'afficher la liste préchargée
    populateSelect(lesContacts);
}

// Affichage du formulaire contenant les informations d'un utilisateur
function afficheForm() {
    var xhr = new XMLHttpRequest()
    xhr.open("GET", "retrieve.php?id=" + idContactCourant, false)
    xhr.send(null)
    console.log(xhr.responseText)
    if (idContactCourant != -2 && idContactCourant != -1) {
        var contacts = JSON.parse(xhr.responseText)
        populateForm(contacts)
        document.getElementById('tForm').style.visibility = "visible"
    }
    if (idContactCourant == -2) {

        document.getElementById('tForm').style.visibility = "hidden"
    }
    if (idContactCourant == -1) {
        document.getElementById('tForm').style.visibility = "visible"
        document.getElementById('nom').setAttribute("value", "");
        document.getElementById('prénom').setAttribute("value", "");
        document.getElementById('tél').setAttribute("value", "");

    }
}

// Affichage du ou des boutons de validation du formulaire
function afficheBoutons() {
    valider = document.getElementById("pBoutons").children[0];
    effacer = document.getElementById("pBoutons").children[1];
    if (idContactCourant != -2 && idContactCourant != -1) {
        valider.style.visibility = "visible"
        effacer.style.visibility = "visible"
    }
    if (idContactCourant == -2) {
        valider.style.visibility = "hidden"
        effacer.style.visibility = "hidden"
    }
    if (idContactCourant == -1) {
        valider.style.visibility = "visible"
        effacer.style.visibility = "hidden"

    }
}

// Enregistrement des données du formulaire
function validerForm(choix) {
    if (choix === "valider") {
        alert("Contact validé")
        validation()
    }
    if (choix == "effacer") {
        alert("Contact effacé")
        supprimer()
    }


}

function validation() {
    var xhr1 = new XMLHttpRequest()
    xhr1.open("GET", "retrieve.php?id=" + idContactCourant, false)
    xhr1.send(null)
    contact= xhr1.responseText


    var xhr2 = new XMLHttpRequest()
    xhr2.open("POST", "update.php", false)
    xhr2.send('id='+idContactCourant +'&nom='+document.getElementById('nom').lastChild+'&prénom='+document.getElementById('prénom').lastChild+'&tél='+document.getElementById('tél').lastChild)
    alert(xhr2.responseText)
}
function supprimer() { }
