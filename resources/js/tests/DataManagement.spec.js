/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import DataManagement from '@/views/DataManagement/index.vue';
import { getAllData } from '@/api/employee';
import { expect, test } from '@jest/globals';

describe('Test Component Data Management', () => {
  const localValue = createLocalVue();
  const wrapper = shallowMount(DataManagement, {
    localValue,
    store,
    fields: [
      { key: 'employee_code', sortable: true },
      { key: 'employee_name', sortable: true },
      { key: 'company_branch', sortable: true },
      { key: 'total_worked', sortable: true },
      { key: 'date_joining_company', sortable: true },
      { key: 'date_out_company', sortable: true },
      { key: 'joining_age_company', sortable: true },
      { key: 'spouse', sortable: true },
      { key: 'dependents', sortable: true },
      { key: 'worked_year', sortable: true },
      { key: 'final_education', sortable: true },
      { key: 'shortest_service', sortable: true },
    ],
  });

  const data = {
    dataManagement: [],
    pagination: {
      current_page: 1,
      per_page: 20,
      total_records: 0,
    },
  };

  test('Case 1: Check component name DataManagement', async() => {
    expect(wrapper.vm.$options.name).toMatch('DataManagement');
  });

  test('Case 2: initializes with correct elements', () => {
    expect(wrapper.findAll('h1').length).toEqual(1);
    expect(wrapper.findAll('h1').at(0).text()).toMatch('LANGUAGES.TEXT_DATA_MANAGEMENT');
  });
  test('Case 3: Should data management with pagination', async() => {
    const params = {
      url: '/data',
      page: data.pagination.current_page,
      per_page: data.pagination.per_page,
    };
    await getAllData(params)
      .then(res => {
        data.dataManagement = res.data.result;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log('err ==>', err);
      });
    await store.dispatch('app/saveDataManagement', data.dataManagement);
    expect(store.getters.dataManagement).toBe(data.dataManagement);
  });
});
