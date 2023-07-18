import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Layout from '../layout';

export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    redirect: { name: 'redirect' },
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('../views/Redirect/index.vue'),
        name: 'redirect',
      },
    ],
  },
  {
    path: '/',
    // redirect: '/enrollment/create',
    redirect: '/working-time/index',
    // meta: {
    //   title: 'routes.enrollment',
    //   icon: 'icofont-institution',
    // },
    component: () => import('../views/WorkingTime/index'),
    name: 'WorkingTimeManagement',
    meta: {
      title: 'routes.working-time',
      icon: 'icofont-ui-user',
    },
    hidden: true,
  },
  {
    path: '/enrollment',
    component: Layout,
    meta: {
      title: 'routes.enrollment',
      icon: 'icofont-institution',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/Enrollment/index.vue'),
        name: 'ENROLLMENT',
        meta: {
          title: 'routes.enrollment',
          icon: 'icofont-institution',
        },
      },
      {
        path: 'create',
        component: () => import('../views/Enrollment/create'),
        name: 'EnrollmentCreate',
        meta: {
          title: 'routes.enrollment',
        },
        hidden: true,
      },
      {
        path: 'result/:id',
        component: () => import('../views/Enrollment/candidateResult'),
        name: 'EnrollmentCandidateResult',
        hidden: true,
        meta: {
          title: 'routes.user',
          icon: 'icofont-ui-user',
        },
      },
    ],
  },
  {
    path: '/datamanagement',
    redirect: '/datamanagement/index',
    component: Layout,
    meta: {
      title: 'routes.data',
      icon: 'icofont-crown',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/DataManagement/index'),
        name: 'DataManagement',
        meta: {
          title: 'routes.data',
          icon: 'icofont-crown',
        },
      },
    ],
  },
  {
    path: '/retirement',
    redirect: '/retirement/index',
    component: Layout,
    meta: {
      title: 'routes.retirement',
      icon: 'icofont-crown',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/Retirement/index'),
        name: 'Retirement',
        meta: {
          title: 'routes.retirement',
        },
      },
    ],
  },
  {
    path: '/employee',
    redirect: '/employee/index',
    component: Layout,
    meta: {
      title: 'routes.employee',
      icon: 'icofont-crown',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/Employee/index'),
        name: 'EmployeeList',
        meta: {
          title: 'routes.employee',
        },
      },
      {
        path: 'chart/:id',
        component: () => import('../views/Employee/chart'),
        name: 'EmployeeChart',
        meta: {
          title: 'routes.employee',
        },
      },
    ],
  },
  {
    path: '/datadigitaco',
    redirect: '/datadigitaco/index',
    component: Layout,
    meta: {
      title: 'routes.datadigitaco',
      icon: 'icofont-crown',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/DataDigitaco/index'),
        name: 'DataDigitacoList',
        meta: {
          title: 'routes.datadigitaco',
        },
      },
      {
        path: 'detail/:id/:type',
        component: () => import('../views/DataDigitaco/detail'),
        name: 'DataDigitacoDetail',
        meta: {
          title: 'routes.datadigitaco',
        },
      },
    ],
  },
  {
    path: '/csv',
    redirect: '/csv/index',
    component: Layout,
    meta: {
      title: 'routes.csv',
      icon: 'icofont-crown',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/CSV_Import/index'),
        name: 'CSV',
        meta: {
          title: 'routes.csv',
          icon: 'icofont-crown',
        },
      },
    ],
  },
  {
    path: '/user',
    redirect: '/user/index',
    component: Layout,
    meta: {
      title: 'routes.user',
      icon: 'icofont-ui-user',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/User/index'),
        name: 'UserManagement',
        meta: {
          title: 'routes.employee',
          icon: 'icofont-ui-user',
        },
      },
      {
        path: 'create',
        component: () => import('../views/User/create'),
        name: 'CreateUser',
        hidden: true,
        meta: {
          title: 'routes.employee',
          icon: 'icofont-ui-user',
        },
      },
      {
        path: 'edit/:id',
        component: () => import('../views/User/edit'),
        name: 'EditUser',
        hidden: true,
        meta: {
          title: 'routes.employee',
          icon: 'icofont-ui-user',
        },
      },
    ],
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login/index.vue'),
    hidden: true,
  },
  {
    path: '/compare-face',
    name: 'CompareFace',
    component: () => import('../views/CompareFace/index.vue'),
    hidden: true,
    meta: {
      title: 'routes.user',
    },
  },
  {
    path: '/working-time',
    redirect: '/working-time/index',
    component: Layout,
    meta: {
      title: 'routes.working-time',
      icon: 'icofont-ui-user',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/WorkingTime/index'),
        name: 'WorkingTimeManagement',
        meta: {
          title: 'routes.working-time',
          icon: 'icofont-ui-user',
        },
      },
      {
        path: 'detail/:id',
        component: () => import('../views/WorkingTime/detail'),
        name: 'WorkingTimeManagementDetail',
        hidden: true,
        meta: {
          title: 'routes.working-time',
          icon: 'icofont-ui-user',
        },
      },
    ],
  },
  {
    path: '/analytics',
    redirect: '/analytics/index',
    component: Layout,
    meta: {
      title: 'routes.user',
      icon: 'icofont-ui-user',
    },
    children: [
      {
        path: 'index',
        component: () => import('../views/Analytics/index'),
        name: 'AnalyticsManagement',
        meta: {
          // title: 'routes.user',
          icon: 'icofont-ui-user',
        },
      },
      {
        path: 'detail/:id',
        component: () => import('../views/Analytics/detail'),
        name: 'AnalyticsDetail',
        meta: {
          title: 'routes.analytics',
          icon: 'icofont-ui-user',
        },
      },
    ],
  },
];

export const asyncRoutes = [];

const createRouter = () =>
  new VueRouter({
    mode: 'history',
    scrollBehavior: () => ({ y: 0 }),
    // base: process.env.MIX_LARAVEL_PATH,
    routes: constantRoutes,
  });

const router = createRouter();

export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher;
}

export default router;
