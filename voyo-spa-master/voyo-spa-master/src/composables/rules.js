const rules = {
  required: (value) => {
    return !!String(value).trim() || 'Ce champ est nécessaire.';
  },
  number: (value) => {
    return value > 0 || 'Ce champ doit contenir un chiffre valide.';
  },
  price: (value) => {
    return value > 0 || 'Ce champ doit contenir un prix valide.';
  },
  phone: (value) => {
    return value.length === 9 || 'Ce champ doit contenir un numéro de téléphone valide.';
  },
  email: (value) => {
    return (
      /[a-z0-9]+@[a-z]+\.[a-z]{2,3}/.test(value) || 'Rentrez un email valide.'
    );
  },
  password: (value) => {
    return (
      value.length >= 8 ||
      'Le mot de passe doit contenir au moins 8 charactères.'
    );
  },
  codePostal: (value) => {
    return (
      (!isNaN(value) &&
        (function (x) {
          return (x | 0) === x;
        })(parseFloat(value))) ||
      'Rentrez un code postal valide.'
    );
  },
  codePostalSize: (value) => {
    return value.length === 5 || 'Le code postal doit contenir 5 chiffres.';
  },
};

export default rules;
