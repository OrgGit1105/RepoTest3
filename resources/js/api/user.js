import request from '../utils/request.js';
import { template } from './template.js';
import * as RequestApi from './request';
const urlAPI = {
  urlGETUser: template`/api/user`,
  urlPOSTOneUser: template`/api/user`,
  urlGETOneUser: template`/api/user/${'id'}`,
  urlPUTOneUser: template`/api/user/${'id'}`,
  urlDELETEOneUser: template`/api/user/${'id'}`,
};

export function getAllUser(params) {
  if (params.url) {
    return request.getRequest(params.url, params);
  }
  return request.getRequest(urlAPI.urlGETUser(), params);
}

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
