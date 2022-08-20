const enterprise = [
  {
    path: '/enterprise',
    name: 'enterprise',
    component: () => import('pages/Enterprises/EnterprisesList'),
  },
  {
    path: '/enterprise/create',
    name: 'enterprise_create',
    component: () => import('pages/Enterprises/EnterprisesForm'),
  },
  {
    path: '/enterprise/update/:id',
    name: 'enterprise_update',
    component: () => import('pages/Enterprises/EnterprisesForm'),
  }
]
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Enterprises/EnterprisesList') },
      ...enterprise
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
