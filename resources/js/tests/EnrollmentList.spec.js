/* eslint-disable quotes */
/* eslint-disable indent */
/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
import { shallowMount, createLocalVue } from "@vue/test-utils";
import store from '@/store';
import EnrollmentList from '@/views/Enrollment/index.vue';
import Filter from '@/views/Enrollment/fillter.vue';
import router from '@/router';
import { UserRoleId } from '@/configs';
import { postLogin } from '@/api/login';
import * as EnrollmentApi from '@/api/enrollment';
describe('Test Component EnrollmentUserList', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(EnrollmentList, {
    localVue,
    store,
    router,
    globals: {
      EnrollmentApi: true,
    },
  });
  const wrapperFilter = shallowMount(Filter, {
    localVue,
    store,
    router,
    globals: {
      EnrollmentApi: true,
    },
  });

  const data = {
    listEnrollment: [],
    pagination: {},
  };
  const params = {
    url: '/enrollment',
    page: 1,
    per_page: 20,
  };
  let TOKEN = '';
  let PROFILE = {};
  let USER = {};

  test('Case 1: Check component name EnrollmentList', async() => {
    expect(wrapper.vm.$options.name).toMatch('EnrollmentList');
  });

  test('Case 2: Check Component Enrollment List', async() => {
    await wrapper.setData({
      isExit: null,
    });

    const FillterList = wrapper.findComponent({ name: 'FillterList' });
    const EnrollmentPdf = wrapper.findComponent({ name: 'EnrollmentPdf' });

    expect(FillterList.exists()).toBe(true);
    expect(EnrollmentPdf.exists()).toBe(true);
  });
  test('Case 3: Submit Login', async() => {
    const account = {
      url: '/auth/login',
      user_name: 'test@gmail.com',
      password: '12345678',
    };
    await postLogin(account)
      .then(res => {
        // console.log('Data return', res);
        TOKEN = res.data.access_token;
        PROFILE = res.data.profile;
        console.log('PROFILE ==>', PROFILE);
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
        store.dispatch('user/saveLogin', { USER, TOKEN });
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log(err);
      });
    expect(TOKEN).not.toBeNull();
  });
  test('Case 4: Check function call Api Filter', async() => {
    // Case 4: Call api get Result
    await EnrollmentApi.getList(params)
      .then(res => {
        // console.log('res.data', res);
        data.listEnrollment = res.data.result;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log('loi o day ==>', err);
      });
    // Case 5: Check Data Store ListEnrollment
    await store.dispatch('app/saveListEnrollment', data.listEnrollment);
    expect(store.getters.listEnrollment).toBe(data.listEnrollment);
  });
  test('Case 5: Check function call Api Filter', async() => {
    params.candidate_name = 'test';
    params.company_branch_id = 1;
    params.start_date = '2021-06-26';
    params.end_date = '2021-06-28';
    // Case 4: Call api get Result
    await EnrollmentApi.getList(params)
      .then(res => {
        // console.log('res.data', res);
        data.listEnrollment = res.data.result;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log('loi o day ==>', err);
      });
    // Case 5: Check Data Store ListEnrollment when add Query
    await store.dispatch('app/saveListEnrollment', data.listEnrollment);
    expect(store.getters.listEnrollment).toBe(data.listEnrollment);
  });
  test('Case 6: Check Pagination', async() => {
    params.page = 2;
    params.per_page = 2;
    // Case 4: Call api get Result
    await EnrollmentApi.getList(params)
      .then(res => {
        // console.log('Case 6', res);
        data.pagination = res.data.pagination;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log('loi o day ==>', err);
      });
    // Case 5: Check Data Store ListEnrollment when add Query
    await store.dispatch('app/savePagination', data.pagination);
    expect(store.getters.pagination).toBe(data.pagination);
  });
  test('Case 7: check show form with role', async() => {
    // If Headquater
    await wrapperFilter.setData({
      roleId: UserRoleId.HEAD_QUARTER,
    });
    expect(wrapperFilter.find('.show-by-role').isVisible()).toBe(true);
    // If Department
    await wrapperFilter.setData({
      roleId: UserRoleId.DEPARTMENT,
    });
    expect(wrapperFilter.find('.show-by-role').isVisible()).toBe(false);
  });
  test('Case 8: check show form select when click checkbox in Interview date', async() => {
    await wrapperFilter.setData({
      statusInterviewDate: 'accepted',
    });
    expect(wrapperFilter.find('.interview-status').isVisible()).toBe(true);
    await wrapperFilter.setData({
      statusInterviewDate: 'not_accepted',
    });
    expect(wrapperFilter.find('.interview-status').isVisible()).toBe(false);
  });
  test('Case 9: check show form select when click checkbox in Interview Departure', async() => {
    await wrapperFilter.setData({
      roleId: UserRoleId.HEAD_QUARTER,
      statusInterviewDeparture: 'accepted',
    });
    expect(wrapperFilter.find('.interview-departure-status').isVisible()).toBe(true);
    await wrapperFilter.setData({
      statusInterviewDeparture: 'not_accepted',
    });
    expect(wrapperFilter.find('.interview-departure-status').isVisible()).toBe(false);
  });

  test('Case 10: check show form select when click checkbox in Candidate Name', async() => {
    await wrapperFilter.setData({
      statusCandidateName: 'accepted',
    });
    expect(wrapperFilter.find('.candidate-name-status').isVisible()).toBe(true);
    await wrapperFilter.setData({
      statusCandidateName: 'not_accepted',
    });
    expect(wrapperFilter.find('.candidate-name-status').isVisible()).toBe(false);
  });
});
