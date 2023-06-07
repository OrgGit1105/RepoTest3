import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlData: template`/employee`,
  urlEmployee: template`/employee/all`,
  urlGetById: template`/employee/detail`,
};

export function createDataImport(data) {
  return request.postRequest(urlAPI.urlData(), { data });
}

export function getAllData(data) {
  return request.getRequest(urlAPI.urlData(), data);
}
export function getAllEmployee(data) {
  return request.getRequest(urlAPI.urlEmployee(), data);
}

export function getEmployeeById(data) {
  return request.getRequest(urlAPI.urlGetById(), data);
}

// Api Test
export function getEmployeeId(id) {
  return request.getRequest(urlAPI.urlGetById({ id }));
}
