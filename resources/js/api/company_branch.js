import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlData: template`/company_branch`,
  urlDataUser: template`/company_branch/user`,
};

export function getAllCompanyBranch(data) {
  return request.getRequest(urlAPI.urlData(), data);
}
export function getCompanyBranchByUser(data) {
  return request.getRequest(urlAPI.urlDataUser(), data);
}
