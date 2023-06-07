import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlGETDataDigitaco: template`/digitaco_data`,
  urlGETOneDataDigitaco: template`/digitaco_data/detail/${'id'}/${'type'}`,
};

export function getAllDataDigitaco(data) {
  return request.getRequest(urlAPI.urlGETDataDigitaco(), data);
}

export function getOneDataDigitaco(id, type, pagination) {
  return request.getRequest(urlAPI.urlGETOneDataDigitaco({ id, type }), pagination);
}

