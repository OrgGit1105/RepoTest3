import request from '../utils/request.js';
import { template } from './template.js';
const urlAPI = {
  urlGETImageByUserId: template`/image_face?user_id=${'id'}`,
  urlDELETEImageByUserId: template`/image_face/${'id'}`,
  urlCREATEImageByUserId: template`/image_face`,
};

export function getImageByUserId(id) {
  return request.getRequest(urlAPI.urlGETImageByUserId({ id }));
}

export function deleteImageByUserId(id) {
  return request.deleteRequest(urlAPI.urlDELETEImageByUserId({ id }));
}

export function createImage(data) {
  return request.postRequest(urlAPI.urlCREATEImageByUserId(), data);
}
