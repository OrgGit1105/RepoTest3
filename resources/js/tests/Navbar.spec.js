/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import Navbar from '@/layout/components/Navbar/index';
import router from '@/router';
describe('Test Component Navbar', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(Navbar, {
    localVue,
    store,
    router,
  });

  test('Case 1: Check component name Navbar', async() => {
    expect(wrapper.vm.$options.name).toMatch('Navbar');
  });
  test('Case 2: Test BTN Logout Exits', () => {
    const CHECKBOX = wrapper.find('.btn-logout');

    expect(CHECKBOX.exists()).toBeTruthy();
  });
  test('Case 3: Test click BUTTON Logout', () => {
    const LOGOUT = wrapper.find('.btn-logout');

    const spy = jest.spyOn(wrapper.vm, 'doLogout');

    LOGOUT.trigger('click');

    expect(spy).toHaveBeenCalled();
  });
});
