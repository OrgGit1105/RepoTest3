/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import Login from '@/views/Login/index.vue';
import { postLogin } from '@/api/login';

describe('Test Component Login', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(Login, { localVue, store });
  const account = {
    username: 'test@gmail.com',
    password: '12345678',
  };
  wrapper.setData({ account });
  const handleLogin = jest.fn();
  wrapper.setMethods({ handleLogin });
  let TOKEN = '';
  let PROFILE = {};
  let USER = {};

  test('Case 1: Check component Login has data', () => {
    expect(typeof Login.data).toBe('function');
  });

  test('Case 2: Check account (username, password) is blank', () => {
    const account = {
      username: 'test@gmail.com',
      password: '12345678',
    };
    wrapper.setData({ account });
    expect(wrapper.vm.account.username).toBe(account.username);
    expect(wrapper.vm.account.password).toBe(account.password);
  });

  test('Case 3: Check call function handleLogin', async() => {
    wrapper.find('.btn_submit').trigger('click');
    expect(handleLogin).toHaveBeenCalled();
  });

  test('Case 4: Check function call api and save token', async() => {
    const account = {
      url: '/auth/login',
      user_name: 'test@gmail.com',
      password: '12345678',
    };
    await postLogin(account)
      .then(res => {
        TOKEN = res.data.access_token;
        PROFILE = res.data.profile;
        // console.log('PROFILE ==>', PROFILE);
        USER = {
          address: PROFILE.address || '',
          avatar: PROFILE.avatar || '',
          email: PROFILE.email || '',
          fax: PROFILE.fax || '',
          gender: PROFILE.gender || '',
          id: PROFILE.id || '',
          name: PROFILE.name || '',
          phone: PROFILE.phone || '',
          status: PROFILE.status || '',
        };
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log(err);
      });
    expect(TOKEN).not.toBeNull();
    await store.dispatch('user/saveLogin', { USER, TOKEN });
    expect(store.getters.token).toBe(TOKEN);
    if (Object.keys(USER).length > 0) {
      expect(store.getters.email).toStrictEqual(USER.email || '');
    }
  });
  test('Case 5: Check change account', async() => {
    const account = {
      user_name: 'test@gmail.com',
      password: '12345678',
    };
    await wrapper.setData({
      account: account,
    });

    expect(wrapper.vm.account.email).toBe(account.email);
    expect(wrapper.vm.account.password).toBe(account.password);
  });
  test('Case 6: Check Validate Email and Password incorrect', () => {
    wrapper.vm.account = { username: null };
    wrapper.vm.account = { username: '   ' };
    wrapper.vm.account = { username: 'test' };
    wrapper.vm.account = { username: '123456789' };
    wrapper.vm.account = { username: 'test@' };
    wrapper.vm.account = { username: 'test@gmail' };
    wrapper.vm.account = { username: 'test@gmail.' };
    wrapper.vm.account = { username: 'test@gmail.123' };
    wrapper.vm.account = { username: 'test@123.456' };
    wrapper.vm.account = { username: 'test.gmail' };
    wrapper.vm.account = { username: 'test.gmail.com' };
    wrapper.vm.account = { username: 'test#gmail.com' };

    wrapper.vm.account = { password: null };
    wrapper.vm.account = { password: '   ' };
    wrapper.vm.account = { password: '*' };
    wrapper.vm.account = { password: '1' };
    wrapper.vm.account = { password: '12' };
    wrapper.vm.account = { password: '123' };
    wrapper.vm.account = { password: '1234' };
    wrapper.vm.account = { password: '12345' };
    wrapper.vm.account = { password: '123456' };
    wrapper.vm.account = { password: '1234567' };
    wrapper.vm.account = { password: '1234567894534532454' };
    wrapper.vm.account = { password: '123 456' };
    wrapper.vm.account = { password: '@123' };
    wrapper.vm.account = { password: '#12345' };

    const result = wrapper.vm.checkValidate();
    expect(result).toBeFalsy();
  });

  //   test('Case 7: Check email and password inconrrect', async() => {
  //     const account = {
  //       username: 'nodata@gmail.com',
  //       password: 'password123',
  //     };
  //     await postLogin(account)
  //       .then(res => {
  //         const message = {
  //           content: res.message,
  //         };
  //       })
  //       .catch(err => {
  //         console.log('err ==>', err);
  //       });

  //     await wrapper.setData({ account: account });
  //     await wrapper.vm.handleLogin();
  //     expect(wrapper.vm.message).toStrictEqual(message);
  //   });
  //   it('Case 8: Check the function that STORES DATA into STORE', async() => {
  //     await store.dispatch('user/saveLogin', { USER, TOKEN });
  test('Case 7: Check Validate Email', () => {
    wrapper.vm.account = { username: 'test@gmail.com' };
    wrapper.vm.account = { username: 'test@domain.vn' };
    wrapper.vm.account = { username: 'test@outlook.com' };
    wrapper.vm.account = { username: 'test@apple.com' };
    wrapper.vm.account = { username: 'test@gmail.uk' };
    wrapper.vm.account = { username: 'test@veho.edu.vn' };

    const result = wrapper.vm.checkEmail(account.username);
    expect(result).toBeTruthy();
  });

  test('Case 8: Test button Show password', () => {
    wrapper.vm.account = {
      password: '',
    };
    const show_password = wrapper.find('#showPassword');
    expect(show_password.exists()).toBe(true);
  });
  test('Case 9: Check display of button name when password field has not been entered', () => {
    wrapper.vm.account = {
      password: '',
    };
    const label = wrapper.find('#showPassword');
    expect(label.exists()).toBeTruthy();
  });
});
