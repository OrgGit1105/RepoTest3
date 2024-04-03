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
  getDetailPermission: template`/viam_rds/detail-permission?user_id=${'user_id'}&rds_manager_id=${'rds_manager_id'}&database_name=${'database_name'}`,
  updateRdsRole: template`/viam_rds/${'user_id'}`,
  deleteRdsRole: template`/viam_rds/${'user_id'}?rds_manager_id=${'rds_manager_id'}&database_id=${'database_id'}`,
  uploadFileHandler: template`/upload`,
  urlGETOneRds: template`/rds_manager/${'id'}`,
  urlPUTOneRds: template`/rds_manager/${'id'}`,
  deleteOneRds: template`/rds_manager/${'id'}`,
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
  return request.postRequest(urlAPI.urlPostRds(), params);
}

export function getDetailPermission(user_id, rds_manager_id, database_name) {
  return request.getRequest(urlAPI.getDetailPermission({ user_id, rds_manager_id, database_name }));
}

export function updateRdsRole(user_id, formData) {
  return request.putRequest(urlAPI.updateRdsRole({ user_id }), formData);
}

export function deleteRdsRole(user_id, rds_manager_id, database_id) {
  return request.deleteRequest(urlAPI.deleteRdsRole({ user_id, rds_manager_id, database_id }));
}

export function uploadFileHandler(formData) {
  return request.postRequest(urlAPI.uploadFileHandler(), formData);
}

export function getOneRds(id, data) {
  return request.getRequest(urlAPI.urlGETOneRds({ id: id }), data);
}

export function updateOneRds(id, data) {
  return request.putRequest(urlAPI.urlPUTOneRds({ id: id }), data);
}

export function deleteOneRds(id) {
  return request.deleteRequest(urlAPI.deleteOneRds({ id: id }));
}
