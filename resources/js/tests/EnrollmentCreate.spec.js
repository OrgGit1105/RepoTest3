/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import EnrollmentCreate from '@/views/Enrollment/create.vue';
// import { createEnrollment } from '@/api/enrollment';

describe('Test Component EnrollmentCreate', () => {
  // const onSubmit = jest.fn();
  const localVue = createLocalVue();
  const wrapper = shallowMount(EnrollmentCreate, {
    localVue,
    store,
  });
  const dataCorrect = {
    form: {
      interview_date: '2021/07/01',
      company_branch_id: '2',
      candidate_name: 'University',
      joining_age: '22',
      spouse: '1',
      dependents: '1',
      worked_years: '2',
      final_education: 'Cao Dang',
      shortest_service: '2',
    },
  };
  wrapper.setData({ ...dataCorrect });

  test('Case 1: Check component name EnrollmentCreate', async() => {
    expect(wrapper.vm.$options.name).toMatch('EnrollmentCreate');
  });

  test('Case 2: Input form', async() => {
    const fields = Object.keys(dataCorrect.form);
    for (let i = 0; i < fields.length; i++) {
      expect(wrapper.vm.form[fields[i]]).toBe(dataCorrect.form[fields[i]]);
    }
  });
  test('Case 3: Validate fields empty', async() => {
    const fields = [
      'interview_date',
      'company_branch_id',
      'candidate_name',
      'joining_age',
      'spouse',
      'dependents',
      'worked_years',
      'final_education',
      'shortest_service',
    ];
    for (let i = 0; i < fields.length; i++) {
      wrapper.vm.error[fields[i]] = false;
      await wrapper.find('.btn-simulation').trigger('click');
      expect(wrapper.vm.error[fields[i]]).toBe(false);
    }
  });
  // test('Case 3: Check call function onSubmit', async() => {
  //   await wrapper.find('.btn_submit').trigger('click');
  //   expect(createEnrollment).toHaveBeenCalledWith(dataCorrect.form);
  // });

  // test('Case 7: Check function call api and return success', async () => {
  //   wrapper.setData({ ...dataCorrect });
  //   await wrapper.vm.onSubmit($event);
  //   expect(wrapper.vm.isSuccess).toBe(true);
  //   console.log('isSuccess ==>', wrapper.vm.isSuccess);
  // });
});
