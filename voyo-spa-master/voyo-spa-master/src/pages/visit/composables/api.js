import axios from 'axios';
import { ref } from 'vue';
export const api = 'http://85.215.229.145:1080/api/';
export const token = sessionStorage.getItem('voyo-token')
export const profile = ref({})

export function createVisitRequest(adresse, ville, codePostal, prix, date) {

  var bodyFormData = new FormData();
  bodyFormData.append('propertyAddress', adresse);
  bodyFormData.append('propertyCity', ville);
  bodyFormData.append('propertyPostalCode', codePostal);
  bodyFormData.append('price', prix);
  bodyFormData.append('scheduledAt', date);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  // Effectuer la requête POST avec les données et la configuration
  return axios.post(api + 'visit-request/create', bodyFormData, config);
}

export function editVisitRequest(id, adresse, ville, codePostal, prix, date, verifPoints) {
  let formatedverifPoints = JSON.stringify(verifPoints)

  const bodyFormData = new FormData();
  bodyFormData.append('id', id);
  bodyFormData.append('propertyAddress', adresse);
  bodyFormData.append('propertyCity', ville);
  bodyFormData.append('propertyPostalCode', codePostal);
  bodyFormData.append('price', prix);
  bodyFormData.append('scheduledAt', date);
  bodyFormData.append('verificationPoints', formatedverifPoints);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  // Effectuer la requête POST avec les données et la configuration
  return axios.post(api + 'visit-request/edit', bodyFormData, config);
}

export async function viewVisitRequest(id) {
  if (id) {
    const config = {
      headers: {
        'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
      }
    };

    try {
      const response = await axios.get(api + 'visit-request/view?id=' + id, config);
      return response.data;
    } catch (error) {
      // Gérer les erreurs ici
      console.log('échec')
      console.error(error.response.data);
      throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
    }
  }
}

export async function acceptOrDecline(id, decision) {

  const bodyFormData = new FormData();
  bodyFormData.append('id', id);
  bodyFormData.append('decision', decision.toString());

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  console.log('test111', bodyFormData, config)
  try {
    const response = await axios.post(api + 'visit-request-solicitation/decide', bodyFormData, config);
    console.log(decision)
    console.log(response.data)
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('échec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function editReport(id, decision) {

  const bodyFormData = new FormData();
  bodyFormData.append('id', id);
  bodyFormData.append('report', decision);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  try {
    const response = await axios.post(api + 'visit-request/edit-report', bodyFormData, config);
    console.log(decision)
    console.log(response.data)
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('échec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function validateParticularDemand(id, comment) {
  const bodyFormData = new FormData();
  bodyFormData.append('id', id);
  bodyFormData.append('comment', comment);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  try {
    const response = await axios.post(api + 'verification-point/validate', bodyFormData, config);
    console.log(decision)
    console.log(response.data)
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('échec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function payVisitRequest(id) {

  const bodyFormData = new FormData();
  bodyFormData.append('id', id);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  try {
    const response = await axios.post(api + 'visit-request/pay', bodyFormData, config);
    console.log(response.data)
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('échec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}



export function deleteVisitRequest(id) {

  var bodyFormData = new FormData();
  bodyFormData.append('id', id);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  // Effectuer la requête POST avec les données et la configuration
  return axios.post(api + 'visit-request/annulate', bodyFormData, config);
}

export function terminateVisit(id) {

  var bodyFormData = new FormData();
  bodyFormData.append('id', id);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  // Effectuer la requête POST avec les données et la configuration
  return axios.post(api + 'visit-request/complete', bodyFormData, config);
}

export async function myVisitsRequests(){

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };


  try {
    const response = await axios.get(api + 'my-visit-requests', config);
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('echec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function myVisitSoli(){

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };


  try {
    const response = await axios.get(api + 'visit-request-solicitations', config);
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('echec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function searchVisitors(ville, jour, hour) {
  const config = {
    params: {
      'address' : ville,
      'availabilityDate' : jour,
      'availabilityHour' : hour
    },
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  try {
    const response = await axios.get(api + 'search-visitors', config);
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('echec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function sendVisitRequest(visitRequest, visitorId) {

  var bodyFormData = new FormData();
  bodyFormData.append('visitRequestId', visitRequest);
  bodyFormData.append('visitorId', visitorId);

  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  try {
    const response = await axios.post(api + 'visit-request-solicitation/send', bodyFormData, config);
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log('echec')
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function getOwnVisit() {
  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .get(api + 'visit-requests', config)
    .then(function (response) {
      console.log(response.data)
      return response.data
    })
    .catch(function (error) {
      console.error(error)
      return []
    });
}

export async function addVerificationPoint(id, verifPoints) {
  const bodyFormData = new FormData();
  bodyFormData.append('id', id);
  bodyFormData.append('verificationPoints', JSON.stringify(verifPoints));
  const config = {
    headers: {
      'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
    }
  };

  // Effectuer la requête POST avec les données et la configuration
  return axios.post(api + 'visit-request/edit', bodyFormData, config);
}

function getOwnProfile() {
  if (token) {
    const config = {
      headers: { Authorization: `Bearer ${token}` }
    };

    axios
      .get(api + 'profile', config)
      .then(function (response) {
        profile.value = response.data
      })
      .catch(function (error) {
        console.error(error)
        return false
      });
  }
}

getOwnProfile()
