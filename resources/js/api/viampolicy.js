import request from '../utils/request.js';
import { template } from './template.js';
import * as RequestApi from './request';
const urlAPI = {
  urlGETUser: template`/policy`,
  urlPOSTOneUser: template`/policy`,
  urlGETOneUser: template`/policy/${'id'}`,
  urlPUTOneUser: template`/policy/${'id'}`,
  urlDELETEOneUser: template`/policy/${'id'}`,
};

export function getAllUser(url) {
  return request.getRequest(url);
}

export function getAllPolicy(url) {
  return request.getRequest(url);
}
// export function getAllUser(params) {
//     if (params.url) {
//       return request.getRequest(params.url, params);
//     }
//     return request.getRequest(urlAPI.urlGETUser(), params);
//   }

export function postOneUser(data) {
  return request.postRequest(urlAPI.urlPOSTOneUser(), data);
}

export function getOneUser(id, data) {
  return request.getRequest(urlAPI.urlGETOneUser({ id: id }), data);
}

export function putOneUser(id, data) {
  return request.putRequest(urlAPI.urlPUTOneUser({ id: id }), data);
}

export function deleteOneUser(id) {
  return request.deleteRequest(urlAPI.urlDELETEOneUser({ id: id }));
}
// Test
export function getAllUserTest(data) {
  return RequestApi.getAll(data);
}
