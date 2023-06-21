import router from './router';
import { getToken } from './utils/getToken';
import getPageTitle from './utils/getPageTitle';

const whiteList = ['/login', '/register', '/remind-password', '/working-time', '/compare-face'];

router.beforeEach(async(to, from, next) => {
  document.title = getPageTitle(to.meta.title);

  const TOKEN = getToken();

  if (TOKEN) {
    if (to.path === '/login') {
      next({ path: '/enrollment' });
    } else {
      next();
    }
  } else {
    if (whiteList.indexOf(to.matched[0] ? to.matched[0].path : '') !== -1) {
      next();
    } else {
      next(`/login?redirect=${to.path}`);
    }
  }
});
