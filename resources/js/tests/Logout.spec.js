/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import Navbar from '@/layout/components/Navbar/index.vue';

describe('Test Component Logout', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(Navbar, { localVue, store });
  const doLogout = jest.fn();
  wrapper.setMethods({ doLogout });
  const TOKEN = '';

  test('Case 1: Check call function doLogout', async() => {
    wrapper.find('.btn-logout').trigger('click');
    expect(doLogout).toHaveBeenCalled();
  });

  test('Case 2: Check function dispatch logout', async() => {
    await store.dispatch('user/logout');
    expect(store.getters.token).toBe(TOKEN);
  });
});
