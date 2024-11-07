import { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('pages/LoginPage.vue'),
  },
  // Let that up there

  {
    path: '/login',
    component: () => import('pages/LoginPage.vue'),
  },
  {
    path: '/register',
    component: () => import('pages/RegisterPage.vue'),
  },
  {
    path: '/forgottenPassword',
    component: () => import('pages/ForgottenPassword.vue'),
  },
  {
    path: '/home',
    component: () => import('pages/HomePage.vue'),
  },
  {
    path: '/contacts',
    component: () => import('pages/ContactsPage.vue'),
  },
  {
    path: '/profil',
    component: () => import('pages/ProfilPage.vue'),
  },
  {
    path: '/profil/view',
    component: () => import('pages/TargetProfil.vue'),
  },
  {
    path: '/home/visitor',
    component: () => import('pages/visitor/VisitorHome.vue'),
  },
  {
    path: '/profil/visitor',
    component: () => import('pages/visitor/VisitorProfilPage.vue'),
  },
  {
    path: '/visit',
    component: () => import('pages/visit/pages/CreateVisit.vue'),
  },
  {
    path: '/visit/update',
    component: () => import('pages/visit/pages/UpdateVisit.vue'),
  },
  {
    path: '/userlist',
    component: () => import('pages/UserList.vue'),
  },
  {
    path: '/visitlist',
    component: () => import('pages/VisitList.vue'),
  },
  {
  path: '/viewVisit',
  component: () => import('pages/visit/pages/UpdateView.vue'),
},
  {
    path: '/visitorReport',
    component: () => import('pages/visitorReport.vue'),
  },
  {
  path: '/terminatedVisit',
  component: () => import('pages/terminatedVisit.vue'),
},


  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
