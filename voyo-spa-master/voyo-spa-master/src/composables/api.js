import axios from 'axios';
import { ref } from 'vue';
export const api = 'http://85.215.229.145:1080/api/';
export const token = sessionStorage.getItem('voyo-token')
export const profile = ref({})

export function createUser(email, password, firstName, lastName) {
  var bodyFormData = new FormData();
  bodyFormData.append('email', email);
  bodyFormData.append('password', password);
  bodyFormData.append('firstname', firstName);
  bodyFormData.append('lastname', lastName);

  return axios.post(api + 'create-user', bodyFormData);
}

export function loginUser(email, password) {
  return axios.post(
    api + 'login_check',
    {
      email: email,
      password: password,
    },
    {
      headers: {
        'Content-Type': 'application/json',
      },
    }
  );
}

export function getRoles(currentToken = token) {
  const config = {
    headers: { Authorization: `Bearer ${currentToken}` }
  };

  return axios.get(api + 'get-roles', config)
}

export function getRKey(email) {
  var bodyFormData = new FormData();
  bodyFormData.append('email', email);

  axios
    .post(api + 'send-resetKey', bodyFormData)
    .then(function (response) {
      console.log(response.data);
      return response.data['redirect_to'];
    })
    .catch(function (error) {
      console.error(error);
      return false;
    });
}

export function newPassword(password) {
  var bodyFormData = new FormData()
  bodyFormData.append('newPassword', password)

  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .post(api + 'reset-password', bodyFormData, config)
    .then(function (response) {
      console.log(response.data);
      return response;
    })
    .catch(function (error) {
      console.error(error);
      return false;
    });
}

export function editProfile(bodyFormData) {
  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .post(api + 'profile/edit', bodyFormData, config)
    .then(function (response) {
      console.log(response.data);
      return response.data;
    })
    .catch(function (error) {
      console.error(error);
      return false;
    });
}

export function visitorRequest(hourlyRate, address, phone) {
  var bodyFormData = new FormData();
  bodyFormData.append('hourlyRate', hourlyRate);
  bodyFormData.append('address', address);
  bodyFormData.append('phone', phone);

  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .post(api + 'visitor-request/create', bodyFormData, config)
    .then(function (response) {
      console.log(response.data);
      return response.data;
    })
    .catch(function (error) {
      console.error(error);
      return false;
    });
}

export function visitorRequestWithCode(code) {
  var bodyFormData = new FormData();
  bodyFormData.append('code', code);

  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .post(api + 'visitor-request/verify-sms-code', bodyFormData, config)
    .then(function (response) {
      console.log(response.data);
      return response.data;
    })
    .catch(function (error) {
      console.error(error);
      return false;
    });
}

export function sendMessage(senderId, recipientId, content) {
  var bodyFormData = new FormData();
  bodyFormData.append('senderId', senderId);
  bodyFormData.append('recipientId', recipientId);
  bodyFormData.append('content', content);

  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .post(api + 'messages/send', bodyFormData, config)
    .then(function (response) {
      console.log(response.data);
      return response.data;
    })
    .catch(function (error) {
      console.error(error);
      return false;
    });
}

export async function retrieveMessages() {
  const config = {
    headers: { Authorization: `Bearer ${sessionStorage.getItem('voyo-token')}` }
  };

  try {
    const response = await axios.get(api + 'messages', config)
    return response.data;
  } catch (error) {
    console.error(error.response.data);
  }
}

export async function getUserProfileImg() {
  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .get(api + 'profile/image', config)
    .then(function (response) {
      const blob = new Blob([response.data], { type: 'image/jpeg' });
      console.log('img', blob)
      const imageUrl = URL.createObjectURL(blob);
      console.log('imageUrl', imageUrl)
      return imageUrl;
    })
    .catch(function (error) {
      console.error(error);
      return false;
    });
}

export async function searchUsers(query, check) {
  let config = null
  if (query === null && check === false) {
    config = {
      headers: {
        'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
      }
    }
  } else {
    config = {
      params: {
        "query": query,
        "onlyVisitors": check
      },
      headers: {
        'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
      }
    };
  }


    try {
      const response = await axios.get(api + 'search-users', config);
      return response.data;
    } catch (error) {
      // Gérer les erreurs ici
      console.log("echec")
      console.error(error.response.data);
      throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
    }
}

export async function searchvisit(query, state) {
  let config = null
  if (query === null && state === "Toutes") {
    config = {
      headers: {
        'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
      }
    }
  } else {
    config = {
      params: {
        "query": query,
        "state": state
      },
      headers: {
        'Authorization': `Bearer ${token}`, // Ajouter le token dans l'en-tête Authorization
      }
    };
  }


  try {
    const response = await axios.get(api + 'visit-requests', config);
    return response.data;
  } catch (error) {
    // Gérer les erreurs ici
    console.log("echec")
    console.error(error.response.data);
    throw error; // Renvoyer l'erreur pour la gérer dans la fonction onLoad()
  }
}

export async function getProfile() {
  const targetUserId = sessionStorage.getItem('voyo-target-profile');
  if (targetUserId) {
    const config = {
      headers: { Authorization: `Bearer ${token}` }
    };

    try {
      const response = await axios.get(api + 'profile?id=' + targetUserId, config)
      return response.data;
    } catch (error) {
      console.error(error.response.data);
    }
  }
}

export function getOwnProfile() {
  if (sessionStorage.getItem('voyo-token')) {
    const config = {
      headers: { Authorization: `Bearer ${sessionStorage.getItem('voyo-token')}` }
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

function getOwnVisit() {
  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  axios
    .get(api + 'my-visit-requests', config)
    .then(function (response) {
      console.log(response)
    })
    .catch(function (error) {
      console.error(error)
      return false
    });

}

getOwnProfile()
