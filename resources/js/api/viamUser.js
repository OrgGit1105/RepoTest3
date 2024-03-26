import request from '../utils/request.js';
import { template } from './template.js';
import * as RequestApi from './request';
const urlAPI = {
  urlGETUser: template`/viam_user`,
  urlPOSTOneUser: template`/viam_user`,
  urlGETOneUser: template`/viam_user/${'id'}`,
  urlPUTOneUser: template`/viam_user/${'id'}`,
  urlDELETEOneUser: template`/viam_user/${'id'}`,
  urlGETDatabasesByRdsId: template`/viam_rds/${'id'}`,
  urlGETViamRds: template`/viam_rds?rds_manager_id=${'rds_id'}&database_name=${'database_name'}`,
  urlPOSTConfigRole: template`/viam_rds`,
  urlGETListRDS: template`/rds_manager`,
  urlPostRds: template`/rds_manager`,
};

export function getAllRole() {
  return request.getRequest(urlAPI.urlGETUser());
}
// export function getAllUser(params) {
//   if (params.url) {
//     return request.getRequest(params.url, params);
//   }
//   return request.getRequest(urlAPI.urlGETUser(), params);
// }
export function getAllUser(url) {
  return request.getRequest(url);
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

export function getListRDS(url) {
  return request.getRequest(url);
}

export function getListDatabases(id, data) {
  return request.getRequest(urlAPI.urlGETDatabasesByRdsId({ id: id }), data);
}

export function getListViamRds(rds_id, database_name) {
  return request.getRequest(urlAPI.urlGETViamRds({ rds_id, database_name }));
}

export function createRdsRole(params) {
  return request.postRequest(urlAPI.urlPOSTConfigRole(), params);
}

export function getAllRDS(params) {
  return request.getRequest(urlAPI.urlGETListRDS(), params);
}

export function postOneRDS(params) {
  console.log('params params==>', params);
  return request.postRequest(urlAPI.urlPostRds(), params);
}
