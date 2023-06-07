/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import UserCreate from '@/views/User/create.vue';
// import * as UserApi from '@/api/user';

describe('Test Component UserCreate', () => {
  // const onSubmit = jest.fn();
  const localVue = createLocalVue();
  const wrapper = shallowMount(UserCreate, {
    localVue,
    store,
  });
  const dataCorrect = {
    form: {
      role_id: '2',
      department_id: '1',
      username: 'thubkit',
      email: 'thubkit.hut@gmail.com',
      password: '12345678',
    },
  };
  wrapper.setData({ ...dataCorrect });
  test('Case 1: Input form', async() => {
    expect(wrapper.vm.form.username).toBe(dataCorrect.form.username);
  });
  test('Case 2: Validate fields empty', async() => {
    const fields = ['username', 'email', 'password'];
    for (let i = 0; i < fields.length; i++) {
      wrapper.vm.error[fields[i]] = false;
      await wrapper.find('.btn_submit').trigger('click');
      expect(wrapper.vm.error[fields[i]]).toBe(false);
    }
  });
  // test('Case 3: Check call function onSubmit', async () => {
  //   await wrapper.find('.btn_submit').trigger('click');
  //   expect(onSubmit).toHaveBeenCalled();
  // });
  //   test('Case 5: Validate email', async () => {
  //     const f = 'email';
  //     wrapper.setData({ ...dataCorrect });
  //     wrapper.vm[f] = 'wrong email';
  //     await wrapper.find('.btn_submit').trigger('click');
  //     expect(wrapper.vm.error).toContain('Email invalid!');
  //   });

  // test('Case 7: Check function call api and return success', async () => {
  //   wrapper.setData({ ...dataCorrect });
  //   await wrapper.vm.onSubmit($event);
  //   expect(wrapper.vm.isSuccess).toBe(true);
  //   console.log('isSuccess ==>', wrapper.vm.isSuccess);
  // });
});
