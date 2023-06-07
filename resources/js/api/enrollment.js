import request from '../utils/request.js';
import { template } from './template.js';
import * as RequestApi from './request';
export const urlAPI = {
  urlEnrollment: template`/enrollment`,
  urlGetResult: template`/enrollment/${'id'}`,
};

export function createEnrollment(data) {
  return request.postRequest(urlAPI.urlEnrollment(), data);
}

export function getAllEnrollment(data) {
  return request.getRequest(urlAPI.urlEnrollment(), data);
}
export function getCandidateResult(id, data) {
  return request.getRequest(urlAPI.urlGetResult({ id }), data);
}
// API Test
export function getResult(data) {
  return RequestApi.getOne(data);
}
export function getList(data) {
  return RequestApi.getAll(data);
}
